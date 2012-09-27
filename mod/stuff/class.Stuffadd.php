<?

class StuffAdd extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
		require_once($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');

		//проверка наличия товара с таким uid в базе
		$carsExist = $this->mysqlQuery("SELECT id FROM `ccl_".ACCOUNT_SUFFIX."stuff` WHERE uid ='".intval($_POST['uid'])."' LIMIT 1");
		
		// если  уид в базе есть  тогда  редиректим обратно на форму с данными в сессии
		if ($carsExist && mysql_num_rows($carsExist)>0){

			$_SESSION['stuffForm']['buyer'] = $_POST['buyer'];
			$_SESSION['stuffForm']['name'] = $_POST['name'];
			$_SESSION['stuffForm']['count'] = $_POST['count'];
			$_SESSION['stuffForm']['transporter_id'] = $_POST['transporter_id'];
			$_SESSION['stuffForm']['port_id'] = $_POST['port_id'];
			$_SESSION['stuffForm']['date_buy'] = $_POST['date_buy'];
			$_SESSION['stuffForm']['post_id'] = $_POST['post_id'];
			$_SESSION['stuffForm']['place_in'] = $_POST['place_in'];
			$_SESSION['stuffForm']['uid'] = $_POST['uid'];
			$_SESSION['stuffForm']['serials'] = $_POST['serials'];
			$_SESSION['stuffForm']['price'] = $_POST['price'];
			$_SESSION['stuffForm']['delivery'] = $_POST['delivery'];
			$_SESSION['stuffForm']['other'] = $_POST['other'];
			$_SESSION['stuffForm']['total'] = $_POST['total'];
			$_SESSION['stuffForm']['paid'] = $_POST['paid'];
			$_SESSION['stuffForm']['balance'] = $_POST['balance'];
			$_SESSION['stuffForm']['invoice'] = $_POST['invoice'];
			$_SESSION['stuffForm']['current_place'] = $_POST['current_place'];
			$_SESSION['stuffForm']['deliveried'] = $_POST['deliveried'];
			
			$this->redirect($this->root_path.'?mod=stuff&sw=form&add&exists');
			break;
		}

		$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."stuff`
						SET buyer = '".intval($_POST['buyer'])."',
						name 		= '".mysql_real_escape_string(strtoupper($_POST['name']))."',
						count 		= '".intval($_POST['count'])."',
						serials		= '".mysql_real_escape_string($_POST['serials'])."',
						transporter_id 	= '".intval($_POST['transporter_id'])."',
						port_id 	= '".intval($_POST['port_id'])."',												
						date_buy 	= '".mysql_real_escape_string($_POST['date_buy'])."',
						post_id 	= '".($_POST['post_id']=='-1'?'0':intval($_POST['post_id']))."',							
						place_in 	= '".intval($_POST['place_in'])."',
						uid 	= '".mysql_real_escape_string($_POST['uid'])."',							
						price  	= '".floatval($_POST['price'])."',							
						delivery  	= '".floatval($_POST['delivery'])."',							
						other  	= '".floatval($_POST['other'])."',							
						invoice  	= '".floatval($_POST['invoice'])."',							
						current_place  	= '".intval($_POST['current_place'])."',							
						deliveried  	= '".intval($_POST['deliveried'])."',
						inspection 	= '".intval($_POST['inspection'])."',													
						allow_inspection = '".intval($_POST['allow_inspection'])."',
						allow_codocs = '".intval($_POST['allow_codocs'])."'
				 	";




		$this->mysqlQuery($request);
		$added_id=mysql_insert_id();
		/*		$invitation = 'no';
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


		*/
		//обновление баланса клиента
		if($_POST['buyer']!=0) updateStuffBalance($_POST['buyer'], intval($_POST['count'])*intval($_POST['price']));

		//обновление баланса поставщика
/*		if($_POST['transporter']!=0) updateSupplierBalance($_POST['transporter']);
		if(mysql_error()=='') $added_id = mysql_insert_id();*/
		
		$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.$added_id.'&success');
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
			(`log_name`,`pass_code`, `type`, `u_id`)
			VALUES ('".$login."', '".md5($password)."', '2', '".$id."')");
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


	function clientHasLogin($id) {
		$check = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."usrs` WHERE `u_id` = '".$id."' and `type` = '2'");
		if(mysql_num_rows($check)>0) return true;
		else return false;
	}
}
?>