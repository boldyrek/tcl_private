<?

class PaymentsSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		//if($_POST['payment_status']=='on') $status = '1';
		//else $status = '0';
		
		$status = '1';
		
		$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."payments` SET  `client` = '".mysql_real_escape_string($_POST['client'])."',
					`amount` = '".intval($_POST['amount'])."',
					`comment` = '".mysql_real_escape_string(strtoupper($_POST['comment']))."',
					`date` = '".mysql_real_escape_string($_POST['date'])."',
					`user_added` = '".$_SESSION['login_id']."',
					`status` = '".$status."',
					`last_edited` = NOW(),
					`car` = '".intval($_POST['car'])."'
					 WHERE `id`=".intval($_GET['id'])." LIMIT 1";
		
		$this->mysqlQuery($request);
		
		$client_info = mysql_fetch_array($this->mysqlQuery("
		SELECT dealer 
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE `id` = '".intval($_POST['client'])."'"));
		
		updateBalance($_POST['client'], $client_info['dealer']);
			
		$this->redirect($this->root_path.'?mod=payments&sw=form&payment='.intval($_GET['id']).'&success');
	}
}
?>