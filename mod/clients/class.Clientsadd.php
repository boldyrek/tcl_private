<?

class ClientsAdd extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		//проверям отмечен ли чек-бокс "дилер"
		if($_POST['dealer']=='on') { 
			$dealer = 1;
			$mydealer = 0;
		}
		else { 
			$dealer = 0;
			$mydealer = mysql_real_escape_string($_POST['mydealer']);
		}
		//#######################
		
		$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."customers` (`id`, `name`, `contacts` , `address`, `name_en`, `address_en`, `passport`, `email`, `balance`, `real_balance`, `cars_delivered`, `cars` , `total_cars` , `payments`, `dealer`, `mydealer`, `scan`)
		VALUES (LAST_INSERT_ID(), 
		'".mysql_real_escape_string($_POST['name'])."', 
		'".mysql_real_escape_string($_POST['contacts'])."', 
		'".mysql_real_escape_string($_POST['address'])."',
		'".mysql_real_escape_string($_POST['name_en'])."', 		
		'".mysql_real_escape_string($_POST['address_en'])."', 
		'".mysql_real_escape_string($_POST['passport'])."', 
		'".mysql_real_escape_string($_POST['email'])."', 
		'0', 
		'0',
		'0', 
		'0', 
		'0', 
		'0',
		'".$dealer."',
		'".$mydealer."',
			'')";
		$this->mysqlQuery($request);
		
		if(mysql_error()=='') $added_id = mysql_fetch_array($this->mysqlQuery("SELECT max(id) as id from `ccl_".ACCOUNT_SUFFIX."customers`"));
		
		if($_POST['addreciever']=='on') {
			$this->mysqlQuery("
			INSERT INTO `ccl_".ACCOUNT_SUFFIX."recievers`
			(`name`, `phone`, `address`, `passport`)
			VALUES
			('".mysql_real_escape_string($_POST['name_en'])."', '".mysql_real_escape_string($_POST['contacts'])."', '".mysql_real_escape_string($_POST['address_en'])."', '".mysql_real_escape_string($_POST['passport'])."')");
		}
		
		if($_POST['invite_user']=='on') {
		
		require_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.Carsadd.php');				
		
		$inv = new CarsAdd();
		
		if(intval($added_id['id'])!=0 and intval($added_id['id'])!='') {
		if(!$inv->clientHasLogin($added_id['id'])) {
			// определяем имя пользователя для данного клиента
			$user_info = $inv->ownerData(intval($added_id['id']));
			
			if(strlen($user_info['email'])>6 and strlen($user_info['name'])>2) {
			
				$new_login = $inv->makeName($user_info['name']);
				$new_password = $inv->makePasswd();
				
				$login_insert = $new_login;
				
				while(!$inv->insertNewUser($login_insert, $new_password, intval($added_id['id']))) {
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
		
		$this->redirect($this->root_path.'?mod=clients&sw=form&customer_id='.$added_id['id'].'&inv='.$invitation.(isset($_GET['hidemenu'])?'&hidemenu':'')); 

	}
}
?>