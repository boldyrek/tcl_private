<?

class CarsForSale extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		$spamit=false;
		
		if(isset($_POST['sell']) and $_POST['sell']=='on' and $_POST['sell_id']==0) {
			$sql = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."forsale`
			(`car`, `price`, `comment`, `date`, `active_through`, `sold`)
			VALUES ( 
			'".intval($_GET['car_id'])."', 
			'".intval($_POST['sellPrice'])."', 
			'".mysql_real_escape_string($_POST['sellComment'])."', 
			'".date('Y-m-d')."',
			'".mysql_real_escape_string($_POST['post_till'])."',
			'".($_POST['sold']=='on'?'1':'0')."')";
			$spamit=true;
			
		} elseif ($_POST['sell_id']!=0 and $_POST['sell']=='on') {
			$sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."forsale`
			SET `price`='".intval($_POST['sellPrice'])."',
			`comment`='".mysql_real_escape_string($_POST['sellComment'])."',
			`sold` = '".($_POST['sold']=='on'?'1':'0')."',
			`active_through` = '".mysql_real_escape_string($_POST['post_till'])."'
			WHERE id='".intval($_POST['sell_id'])."'";
		} else {
			$sql = "DELETE 
			FROM `ccl_".ACCOUNT_SUFFIX."forsale` 
			WHERE id='".intval($_POST['sell_id'])."'";
		}
		
 		$this->mysqlQuery($sql);
 		$new_id = mysql_insert_id();
 		if($spamit) {
 			$spamcar = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE id=".intval($_GET['car_id']);
 			$spamcar = $this->mysqlQuery($spamcar);
 			$carinfo=mysql_fetch_assoc($spamcar);
 			$carinfo["endprice"]=intval($_POST['sellPrice']);
 			$carinfo["endcomment"]=$_POST['sellComment'];
 			
 			$spamadr = "SELECT name, email FROM `ccl_".ACCOUNT_SUFFIX."customers`	WHERE allowspam=1";
 			$spamadr = $this->mysqlQuery($spamadr);
 			$txt="";
			while($l=mysql_fetch_row($spamadr)) {
				if($l[1] && eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$",$l[1])) {
 					require_once 'templates/sellmail.tpl.php';
					$this->XMail(
						"MakmalAuto.com <info@makmalauto.com>",
						"=?windows-1251?B?".base64_encode($l[0])."?= <{$l[1]}>",
						'=?windows-1251?B?'.base64_encode($SELLMAILtitle).'?=',
						$SELLMAILtxt);
				}
			}
		}

		$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_GET['car_id']));
	}
	
	private function XMail($from, $to, $subj, $text) {
		$un        = strtoupper(uniqid(time()));
		$head      = "From: $from\n";
		$head     .= "Subject: $subj\n";
		$head     .= "X-Mailer: PHPMail Tool\n";
		$head     .= "Reply-To: $from\n";
		$head     .= "Mime-Version: 1.0\n";
		$head	  .= "Content-Type:text/html; charset=utf-8\n";
		$zag=$text;

		return mail("$to", "$subj", $zag, $head);
	}
}
?>