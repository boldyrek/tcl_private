<?

class PaymentsAdd extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		// защита от случайного двойного добавления платежа, таймаут 3 секунды
		if((mktime() - $_SESSION['last_added_payment'])<3 and $_SESSION['last_added_payment']!='' and $_SESSION['last_added_payment']!='0') {
					header('Location: '.$this->root_path.'?mod=payments'); 
					break;
			}
				
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $status = '1';
		else $status = '0';
		
		if (isset($_POST['car'])) $what='car';
		elseif (isset($_POST['stuff'])) $what='stuff';
		$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."payments` (`id`, `client`, `amount`, `comment`, `date`, `user_added`, `status`, `last_edited`, `$what`) 
		VALUES (LAST_INSERT_ID(), 
		'".mysql_real_escape_string($_POST['client'])."', 
		'".intval($_POST['amount'])."', 
		'".mysql_real_escape_string(strtoupper($_POST['comment']))."', 
		'".mysql_real_escape_string($_POST['date'])."',
		'".intval($_SESSION['login_id'])."',
		'".$status."',
		NOW(),
		'".intval($_POST[$what])."')";
		$this->mysqlQuery($request);
		
		$_SESSION['last_added_payment'] = mktime();
		
		$client_info = mysql_fetch_array($this->mysqlQuery("
		SELECT dealer 
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE `id` = '".intval($_POST['client'])."'"));

		updateBalance(intval($_POST['client']), $client_info['dealer']);
		
		if(isset($_GET['return'])) $this->redirect($this->root_path.'?mod='.($what=='car'?'cars':'stuff').'&sw=form&'.$what.'_id='.intval($_GET['return']));
		else $this->redirect($this->root_path.'?mod=payments'); 
	}
}
?>