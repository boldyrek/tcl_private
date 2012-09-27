<?

class StuffForSale extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		$spamit=false;
		$diesel = false;
		
		if(isset($_POST['sell']) and $_POST['sell']=='on' and $_POST['sell_id']==0) {
			$sql = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."stuff_forsale`
			(`stuff`, `price`, `comment`, `date`, `active_through`, `sold`)
			VALUES ( 
			'".intval($_GET['stuff_id'])."', 
			'".intval($_POST['sellPrice'])."',
			'".mysql_real_escape_string($_POST['sellComment'])."', 
			'".date('Y-m-d')."',
			'".mysql_real_escape_string($_POST['post_till'])."',
			'".($_POST['sold']=='on'?'1':'0')."')";
			$spamit=true;
			$diesel = true;
			
		} elseif ($_POST['sell_id']!=0 and $_POST['sell']=='on') {
			$sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."stuff_forsale`
			SET `price`='".intval($_POST['sellPrice'])."',
			`comment`='".mysql_real_escape_string($_POST['sellComment'])."',
			`sold` = '".($_POST['sold']=='on'?'1':'0')."',
			`active_through` = '".mysql_real_escape_string($_POST['post_till'])."'
			WHERE id='".intval($_POST['sell_id'])."'";
		} else {
			$sql = "DELETE 
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_forsale` 
			WHERE id='".intval($_POST['sell_id'])."'";
		}
		
 		$this->mysqlQuery($sql);
 		$new_id = mysql_insert_id();
 		if($diesel) $this->diesel($new_id);
		$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_GET['stuff_id']));
	}
	
	private function diesel($new_id) {
		
		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff` WHERE `id` = '".intval($_GET['stuff_id'])."'"));
		$title = 'ПРОДАЮ '.$carinfo['name'];
		$subtitle = 'цена: '.intval($_POST['sellPrice']).'$';
		
		$text = 'Продаю 
		[b]'.$carinfo['name'].'[/b]
		цена: '.intval($_POST['sellPrice']).'$

		'.mysql_real_escape_string($_POST['sellComment']).'
				 
		[url="http://www.makmalauto.com/go.php?id='.intval($new_id).'"]Фотографии на сайте www.MakmalAuto.com.[/url]
		
		По вопросам приобретения звоните 996-312-902-600, либо пишите dmitrii@makmalauto.com ';
		//echo $text;
		require($_SERVER['DOCUMENT_ROOT'].'/diesel/class.Diesel.php');		
		
		$obj = new diesel();
		$obj->setForumNumber(13);
		$obj->getData($title, $subtitle, $text);
		$obj->process();
	}
}
?>