<?

class UsersAdd extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		if($_POST['userType']!='2') $u_id = '0'; else $u_id = intval($_POST['u_id']);
		if($_POST['userType']!='8') $t_id = '0'; else $t_id = intval($_POST['t_id']);
		if($_POST['userType']!='9') $e_id = '0'; else $e_id = intval($_POST['e_id']);
				
				$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."usrs` (`id`, `log_name`, `email`, `pass_code`, `type`, `u_id`,`t_id`,`e_id`,`lang`) 
				VALUES (LAST_INSERT_ID(), 
				'".mysql_real_escape_string($_POST['login'])."', 
				'".mysql_real_escape_string($_POST['email'])."', 
				'".md5($_POST['password'])."', 
				'".intval($_POST['userType'])."', 
				'".$u_id."','".$t_id."','".$e_id."',
				'rus')";
				
				$this->mysqlQuery($request);
		
		$this->redirect($this->root_path.'?mod=users'); 		
	}
}
?>