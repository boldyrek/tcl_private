<?

class TransportersSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		// сохраняем данные поставщика
			if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id'])!='0')
			{
			$ports = '';
			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."transporters` SET  `name` = '".mysql_real_escape_string(strtoupper($_POST['name']))."',
						`address` = '".mysql_real_escape_string(strtoupper($_POST['address']))."',
						`phone` = '".mysql_real_escape_string($_POST['phone'])."',
						`email` = '".mysql_real_escape_string($_POST['email'])."'
						 WHERE `id`=".intval($_GET['id'])." LIMIT 1";
		
		 	$this->mysqlQuery($request);
			$this->redirect($this->root_path.'?mod=transporters&sw=form&sup_id='.intval($_GET['id']).'&success');
			}
		elseif(isset($_GET['add'])) {
			$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."transporters` (`id`, `name`, `address`, `phone`, `email`, `balance`) 
				VALUES (LAST_INSERT_ID(), 
				'".mysql_real_escape_string(strtoupper($_POST['name']))."', 
				'".mysql_real_escape_string(strtoupper($_POST['address']))."', 
				'".mysql_real_escape_string(strtoupper($_POST['phone']))."', 
				'".mysql_real_escape_string($_POST['email'])."', 
				'0')";
				$this->mysqlQuery($request);
		
			$this->redirect($this->root_path.'?mod=transporters'); 
		}

	}
}
?>