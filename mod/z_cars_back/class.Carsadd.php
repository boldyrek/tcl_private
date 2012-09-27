<?

class CarsAdd extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		require_once($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
	
		//проверка наличия машины с таким вин кодом в базе
		$carsExist = $this->mysqlQuery("
		SELECT ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.frame, ccl_".ACCOUNT_SUFFIX."customers.name
		FROM `ccl_".ACCOUNT_SUFFIX."cars`
		RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."customers` ON ( ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."cars.buyer )
		WHERE 1
		ORDER BY ccl_".ACCOUNT_SUFFIX."cars.id DESC");
		
		$i = 0;
		$num = mysql_num_rows($carsExist);
		while($i<$num) {
			$line = mysql_fetch_array($carsExist);
			if($_POST['frame'] == $line['frame']) {
				$_SESSION['car_exists']['frame'] = $line['frame'];
				$_SESSION['car_exists']['model'] = $line['model'];
				$_SESSION['car_exists']['buyer'] = $line['name'];
				
				//данные формы
				$_SESSION['carForm']['buyer_id'] = $_POST['buyer'];
				$_SESSION['carForm']['reciever'] = $_POST['reciever'];
				$_SESSION['carForm']['model'] = $_POST['model'];
				$_SESSION['carForm']['frame'] = $_POST['frame'];
				$_SESSION['carForm']['year'] = $_POST['year'];
				$_SESSION['carForm']['engine'] = $_POST['engine'];
				$_SESSION['carForm']['FOB'] = $_POST['FOB'];
				$_SESSION['carForm']['transporter'] = $_POST['transporter'];
				$_SESSION['carForm']['weight'] = $_POST['weight'];
				$_SESSION['carForm']['total'] = $_POST['total'];
				$_SESSION['carForm']['invoice'] = $_POST['invoice'];
				$_SESSION['carForm']['price_jp'] = $_POST['price_jp'];
				$_SESSION['carForm']['buy_date'] = $_POST['buy_date'];
				$_SESSION['carForm']['prepay'] = $_POST['prepay'];
				$_SESSION['carForm']['volume'] = $_POST['volume'];
				$_SESSION['carForm']['milage'] = $_POST['milage'];
				$_SESSION['carForm']['notice'] = $_POST['notice'];
				$_SESSION['carForm']['place_id1'] = $_POST['place_id1'];
				$_SESSION['carForm']['place_id2'] = $_POST['place_id2'];
				$_SESSION['carForm']['place_id3'] = $_POST['place_id3'];
				$_SESSION['carForm']['aucfee'] = $_POST['aucfee'];
				$_SESSION['carForm']['dealer_comission'] = $_POST['dealer_comission'];
				$_SESSION['carForm']['cost_to_port'] = $_POST['cost_to_port'];
				$_SESSION['carForm']['cost_to_destination'] = $_POST['cost_to_destination'];
				$_SESSION['carForm']['unload'] = $_POST['unload'];
				$_SESSION['carForm']['insurance'] = $_POST['insurance'];
				$_SESSION['carForm']['other'] = $_POST['other'];
				$_SESSION['carForm']['inspection'] = $_POST['inspection'];
				$_SESSION['carForm']['auction'] = $_POST['auction'];
				$_SESSION['carForm']['ready'] = $_POST['ready'];
				$_SESSION['carForm']['date_ready'] = $_POST['date_ready'];
				$_SESSION['carForm']['port'] = $_POST['port'];
				$_SESSION['carForm']['type'] = intval($_POST['type']);
			
				$carAlreadyExists = 1;
			}
			$i++;
		}
		
		if($carAlreadyExists == 1) {
			$this->redirect($this->root_path.'?mod=cars&sw=form&add&exists');
			break;
		}
		
		$isdealer = 'isDealer'.$_POST['buyer']; //определяем является ли текущий владелец машины дилером
		$mydealer = 'myDealer'.$_POST['buyer']; //дилер текущего владельца
		
		if($_POST[$isdealer]==0) $dealer = $_POST[$mydealer];
		else $dealer = $_POST['buyer'];
		
				
		if($_POST['ready']=='on') { 
			$readiness = '1';
			$date_ready = $_POST['date_ready'];
		}
		else {
			$readiness = '0';
			$date_ready = '0000-00-00';
		}
		
		$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."cars` (
					`id`, 
					`buyer`, 
					`reciever`, 
					`model`, 
					`frame`, 
					`year`, 
					`engine`, 
					`price_jp`, 
					`invoice`, 
					`total`, 
					`container`, 
					`delivered`, 
					`transporter`, 
					`dealer`, 
					`port`, 
					`weight`, 
					`prepay`, 
					`buy_date`,
					`volume`,
					`milage`, 
					`created`, 
					`notice`, 
					`place_id1`, 
					`place_id2`, 
					`place_id3`,
					`aucfee`,
					`dealer_comission`,
					`cost_to_port`,
					`cost_to_destination`,
					`unload`,
					`insurance`,
					`other`,
					`inspection`,
					`auction`,
					`ready`,
					`date_ready`,
					`type`) 
					VALUES (LAST_INSERT_ID(), 
					'".intval($_POST['buyer'])."', 
					'".intval($_POST['reciever'])."',
					'".mysql_real_escape_string(strtoupper($_POST['model']))."', 
					'".mysql_real_escape_string(strtoupper($_POST['frame']))."', 
					'".mysql_real_escape_string($_POST['year'])."', 
					'".mysql_real_escape_string($_POST['engine'])."', 
					'".mysql_real_escape_string($_POST['price_jp'])."', 
					'".mysql_real_escape_string($_POST['invoice'])."', 
					'".mysql_real_escape_string($_POST['total'])."', 
					'".intval($_POST['container'])."', 
					'0', 
					'".intval($_POST['transporter'])."', 
					'".mysql_real_escape_string($dealer)."', 
					'".intval($_POST['port'])."', 
					'".intval($_POST['weight'])."', 
					'".intval($_POST['prepay'])."',
					'".mysql_real_escape_string($_POST['buy_date'])."',
					'".mysql_real_escape_string($_POST['volume'])."',
					'".intval($_POST['milage'])."',
					NOW(),
					'".mysql_real_escape_string($_POST['notice'])."',
					'".intval($_POST['place1'])."',
					'".intval($_POST['place2'])."',
					'".intval($_POST['place3'])."',
					'".intval($_POST['aucfee'])."',
					'".intval($_POST['dealer_comission'])."',
					'".intval($_POST['cost_to_port'])."',
					'".intval($_POST['cost_to_destination'])."',
					'".intval($_POST['unload'])."',
					'".intval($_POST['insurance'])."',
					'".intval($_POST['other'])."',
				 	'".intval($_POST['inspection'])."',
				 	'".intval($_POST['auction'])."',
				 	'".$readiness."',
				 	'".$date_ready."',
				 	'".intval($_POST['type'])."'
				 	)";
		


		
		$this->mysqlQuery($request);
		$invitation = 'no';
		if($_POST['invite_user']=='on') {
			
			if(intval($_POST['buyer'])!=0 and intval($_POST['buyer'])!='') {
				if(!$this->clientHasLogin(intval($_POST['buyer']))) {
					// определяем имя пользователя для данного клиента
					$user_info = $this->ownerData(intval($_POST['buyer']));
					
					if(strlen($user_info['email'])>6 and strlen($user_info['name'])>2) {
					
						$new_login = $this->makeName($user_info['name']);
						$new_password = $this->makePasswd();
						
						$login_insert = $new_login;
						
						while(!$this->insertNewUser($login_insert, $new_password, intval($_POST['buyer']))) {
							$login_insert = $new_login.rand(0,99);
						}
						$message = $this->informUser(array('login'=>$login_insert, 'password'=>$new_password));
						if($this->sMail($user_info['email'], $message)) {
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
		
		//обновление баланса клиента
		if($_POST['buyer']!=0) updateBalance($_POST['buyer'], $_POST[$isdealer]);
		
		//обновление баланса поставщика
		if($_POST['transporter']!=0) updateSupplierBalance($_POST['transporter']);
		
		if(mysql_error()=='') $added_id = mysql_fetch_array($this->mysqlQuery("
		SELECT max(id) AS id FROM `ccl_".ACCOUNT_SUFFIX."cars`"));
	
		$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.$added_id['id'].'&success&inv='.$invitation); 
	}
	
	function ownerData($u_id) {
		$info = mysql_fetch_array($this->mysqlQuery("SELECT name, email FROM `ccl_".ACCOUNT_SUFFIX."customers` WHERE `id` = '".$u_id."'"));
		return $info;
		
	}
	
	function makeName($in) {
		
		$name = strtolower($in);
		$i = 0;
		$word = '';
		$char = '';
		while(strlen($name)>$i and $i<10) {
			$char = substr($name,$i,1);
			if($char!=' ') {
				if(!preg_match('[^\W]', $char)) $word = $word.$char;
			}
			else break;
			$i++;
		}
		$lat_name = ruslat($word);
		return $lat_name;
	
	}
	
	function makePasswd() {
		$passw='';
		$chars = array('ba','fa','ga','da','la','na','sa','go','ho','mo','po','wo','zo','tu','gu','ru','vu','mu','ni','si','ti','bi','gi', 'ke','me','ne','fe','le','be','ze','de','pe');
		$chars2 = array('br','tr','wr','vr','gr','sr');
		$chars3 = array('af','am','as','al','ap','az','ak','el','er','et','em','ef','ew','ev','op','ot','ok','om','on','ob','ut','ur','uw','ug','uh','ud','uv','uz');
		
		$passw.=$chars[rand(0,(count($chars)-1))].$chars2[rand(0, (count($chars2)-1))].$chars3[rand(0,(count($chars3)-1))];
		
		return $passw.rand(10,99);
		
	}
	
	function insertNewUser($login, $password, $id) {
		$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."usrs`
			(`log_name`,`pass_code`, `type`, `u_id`, `lang`)
			VALUES ('".$login."', '".md5($password)."', '2', '".$id."','eng')");
		if(!mysql_error()) return true;
		else return false;
	}
	
	function informUser($data) {
		
		$text = '<h3>Уважаемый клиент!</h3>
		<p>Для вас заведен пользователь в нашей базе данных "Cars&Clients".</p>
		<br>
		<p><b>Ваши реквизиты:</b><br>
		<blockquote>логин: '.$data['login'].'<br>
		пароль: '.$data['password'].'
		</blockquote>
		<br>
		Вы можете войти в нашу базу по адресу <a href="http://tcl.makmalauto.com" target="_blank">http://tcl.makmalauto.com</a>, используя указанные выше данные.
		<br>
		<p>Добро пожаловать и удачного дня!</p>
		<br>
		<p>
		Это сообщение автоматическое, на него не нужно отвечать.<br>
		Пожалуйста, храните предоставленные вам конфиденциальные данные в безопасности.</p>
		<p>С уважением,<br>
		Makmal Auto - North America<br>
		<a href="http://www.makmalauto.com">http://www.makmalauto.com</a>';
		return $text;
		
	}
	
	function sMail($to, $text) {
		$subj = '=?windows-1251?B?'.base64_encode('База данных Makmal-Auto').'?=';
		$from = 'Makmal-Auto <dmitrii@makmalauto.com>';
		
		$head      = "From: $from\n";
		$head     .= "Subject: $subj\n";
		$head     .= "X-Mailer: PHPMail Tool\n";
		$head     .= "Reply-To: $from\n";
		$head     .= "Mime-Version: 1.0\n";
		$head	  .= "Content-Type:text/html; charset=utf-8\n";
		$zag = $text;

		return mail("$to", "$subj", $zag, $head);
	}
	
	function clientHasLogin($id) {
		$check = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."usrs` WHERE `u_id` = '".$id."' and `type` = '2'");
		if(mysql_num_rows($check)>0) return true;
		else return false;
	}
}
?>