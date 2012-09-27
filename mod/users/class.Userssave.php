<?

class UsersSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id'])!='0') {
			if($_POST['password']!='') $add_pass = "`pass_code` = '".md5($_POST['password'])."',";
			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."usrs` SET  `log_name` = '".mysql_real_escape_string($_POST['login'])."', `email`='".mysql_real_escape_string($_POST['email'])."', ".$add_pass."
						`type` = '".intval($_POST['userType'])."'";
										
			if(intval($_POST['userType'])=='2')	$request .= ", `u_id` = '".intval($_POST['u_id'])."'";
			else $request .= ", `u_id` = '0'";
			
			if(intval($_POST['userType'])=='8')	$request .= ", `t_id` = '".intval($_POST['t_id'])."'";
			else $request .= ", `t_id` = '0'";
			
			if(intval($_POST['userType'])=='9')	$request .= ", `e_id` = '".intval($_POST['e_id'])."'";
			else $request .= ", `e_id` = '0'";
		
			$request .= " WHERE `id` = '".intval($_GET['id'])."'";
			$this->mysqlQuery($request);
		
			if(mysql_errno()=='1062') $error = '&error='.mysql_real_escape_string($_POST['login']);
			else $error = '&success';
		
			$this->redirect($this->root_path.'?mod=users&sw=form&id='.intval($_GET['id']).$error);
		}
		else $this->redirect($this->root_path.'?mod=users');
	}
}
?>