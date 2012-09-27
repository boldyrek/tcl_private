<?

class ClientsSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		require_once($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		if($_POST['dealer']=='on') { 
			$dealer = 1;
			$mydealer = 0;
		}
		else { 
			$dealer = 0;
			$mydealer = mysql_real_escape_string($_POST['mydealer']);
		}

		if($_POST['spam']=='on') $spam = '1';
		else $spam='0';
		$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."customers` SET  `name` = '".mysql_real_escape_string(preg_replace('/^\"*$/','',$_POST['name']))."',
					`contacts` = '".mysql_real_escape_string($_POST['contacts'])."',
					`address` = '".mysql_real_escape_string($_POST['address'])."',
					`passport` = '".mysql_real_escape_string($_POST['passport'])."',
					`email` = '".mysql_real_escape_string($_POST['email'])."',
					`dealer` = '".$dealer."',
					`mydealer` = '".$mydealer."',
					`name_en` = '".mysql_real_escape_string($_POST['name_en'])."',
					`address_en` = '".mysql_real_escape_string($_POST['address_en'])."',
					`allowspam` = '".$spam."',
                                        `autocheck` = '".intval(isset($_POST['autocheck']))."'
					 WHERE `id`=".mysql_real_escape_string($_GET['id'])." LIMIT 1";
					
			$this->mysqlQuery($request);
		
			updateBalance(intval($_GET['id']), $dealer);
			if($_POST['login']!='')
			{
				$report = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."usrs` WHERE `u_id` = '".intval($_GET['id'])."'");
				
				if(mysql_num_rows($report)=='0') 
				{
					if($_POST['password']!='') $add_pass = " '".md5($_POST['password'])."',";
					$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."usrs` ( `id` , `log_name` , `pass_code` , `type` , `u_id`, `lang`)
					VALUES (LAST_INSERT_ID(), '".mysql_real_escape_string($_POST['login'])."',".$add_pass." '2', '".intval($_GET['id'])."', 'eng')";
				}
				else 
				{
					if($_POST['password']!='') $add_pass = ", `pass_code` = '".md5($_POST['password'])."'";
					$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."usrs` SET `log_name` = '".mysql_real_escape_string($_POST['login'])."'".$add_pass." 
					 WHERE `u_id` = '".mysql_real_escape_string($_GET['id'])."' LIMIT 1";
				}
		
				$this->mysqlQuery($request);	
		
				if(mysql_errno()=='1062') $error = '&error='.mysql_real_escape_string($_POST['login']);
			}
			
			if($_POST['invite_user']=='on') {
				
				require_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.Carsadd.php');				
				
				$inv = new CarsAdd();
				
				if(intval($_GET['id'])!=0 and intval($_GET['id'])!='') {
				if(!$inv->clientHasLogin(intval($_GET['id']))) {
					// определяем имя пользователя для данного клиента
					$user_info = $inv->ownerData(intval($_GET['id']));
					
					if(strlen($user_info['email'])>6 and strlen($user_info['name'])>2) {
					
						$new_login = $inv->makeName($user_info['name']);
						$new_password = $inv->makePasswd();
						
						$login_insert = $new_login;
						
						while(!$inv->insertNewUser($login_insert, $new_password, intval($_GET['id']))) {
							$login_insert = $new_login.rand(0,99);
						}
						$message = $inv->informUser(array('login'=>$login_insert, 'password'=>$new_password));
						if($inv->sMail($user_info['email'], $message)) {
							$invitation = 'sent';
						}
						else $invitation = 'notsent';
					}
					else $invitation = 'error';
				}
				else $invitation = 'exists';
			}
			else echo 'Error! Unknown buyer id!';
			}
		
		$this->redirect($this->root_path.'?mod=clients&sw=form&customer_id='.intval($_GET['id']).'&success&inv='.$invitation.$error.(isset($_GET['hidemenu'])?'&hidemenu':''));

	}
}
?>