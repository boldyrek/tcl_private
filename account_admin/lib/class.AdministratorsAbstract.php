<?

	require_once ( $_SERVER['DOCUMENT_ROOT'] . '/lib/functions.php' );
	require_once($_SERVER['DOCUMENT_ROOT'].'/Zend/Translate.php');
	require_once($_SERVER['DOCUMENT_ROOT'].'/Zend/Registry.php');


abstract class AdministratorsAbstract {

	const MODULE_NAME	= 'account_admin';

	const TABLE_ADMINISTRATORS	= 'accounts_admin';
	const TABLE_ACCOUNTS	= 'accounts';

	const MIN_PASSWORD_LENGTH	= 3;

	const COOKIE_NAME_AUTHORISED	= 'morozov';
	const COOKIE_NAME_LOGIN	= 'pavlik';
	const COOKIE_NAME_PASSWORD	= 'ivanovich';


	private $current_version = '1.9.1b';
	private $last_updated = '31 march 2010, 13:30 GMT+6';

	public $config;

	protected $login;
	private $password;

	private $location;

	public $per_page = 30;

	// пользователь
	private $user_id;
	private $login_id;
	private $main_menu = array();
	protected $permissions = array();

	
	// язык пользователя
	public $lang = array();
	public $translate;

	// база данных
	protected $db_host;
	public $db_name;
	private $db_user;
	private $db_pass;
	private $db_conn;

	protected $root_path='/';
	// сессия
	private $logon_time;
	private $session_lifetime;
	private $query_stack = array();
	private $errors = array();
	private $page_started;
	private $page_finished;
	private $authorised;
	private $auth_cookies;

	// безопасность
	private $user_hash; // идентификатор пользователя, кодированный в MD5
	private $auth_tries; // количество неудачных попыток авторизации

	// тестирование
	protected $debug_mode;
	protected $exec_time;
	protected $track_queries;

	// шаблоны
	private $templates_file;
	public $templates = array();

	// страница
	public $page;

	// построение списков
	protected $sortNow;
	
	//###########################

	function __construct () {
		setlocale ( LC_CTYPE, 'ru_RU.utf8' );
		setlocale ( LC_TIME, 'ru_RU.utf8' );
		setlocale ( LC_COLLATE, 'ru_RU.utf8' );
		
		if ( isset ( $_GET['exit'] ) ) $this -> logoff ();
		$this->page_started = Start_Timer();

		$this->config = $_SERVER['DOCUMENT_ROOT'].'/inc/config.php';
		$this->getConfig();
		$this->connectDB();

		//$this->chooseLang();
		$this->initLang();
		
		$this->getTemplates();

		if(isset($_POST['auth'])) {
			if($this->checkLoginData()) {
				$this->authorise();
			}
		}
	}

	function __destruct() {
	}

    protected function initLang(){
		if(isset($_SESSION['lang']))
		{
			if($_SESSION['lang']=='0') $_SESSION['lang'] = 'eng'; // Setting default language to ENGlish
			$this->user_lang = $_SESSION['lang'];
			if($this->user_lang=='eng')
				$this->translate = new Zend_Translate('gettext',$_SERVER['DOCUMENT_ROOT'].'/translations/en.mo','en');
			elseif($this->user_lang=='rus')
				$this->translate = new Zend_Translate('gettext',$_SERVER['DOCUMENT_ROOT'].'/translations/ru.mo','ru');
			Zend_Registry::set('translation', $this->translate);
		}
    }

/*
	private function chooseLang(){
		$str=$_SERVER['DOCUMENT_ROOT'].$this->root_path.'inc/languages.php';
		require_once ( $str );
		if( isset ( $_SESSION['lang'] ) && $_SESSION['lang'] ) {
			$this -> lang	= $LangHash[$_SESSION['lang']];
		}
		else
		{/*?>
			<table id="back" style="background-color:#000000;opacity:0.4;z-index:1000;position:absolute;top:0;left:0;width:100%;height:200%;display:none"><tr><td></td></tr></table>
			<div id="lng" style="background-color:#ffffff;z-index:1005;position:absolute;top:100;left:300;width:300;height:180;display:none;border:solid 2px #22e;" align="center">Пожалуйста<br>выберите язык отображения сайта:<br /><br /><br />
			<select style="width:150px;">
			<option selected="selected" value="rus">Русский</option>
			</select>
			<br/><br/>
			<input type="button" value="Выбрать" onclick="document.getElementById('back').style.display='none';document.getElementById('lng').style.display='none';"/>
			</div>

		<?*//*}

	}
*/
	protected function publish() {
		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		else echo $this->page;
		if($this->debug_mode) $this->debugQueries();
		if($this->exec_time) $this->timePassed();
		if($this->track_queries) $this->saveQueries();
	}

	private function connectDB() {
		if(!$this->db_conn = @mysql_connect($this->db_host, $this->db_user, $this->db_pass)) $this->errorHandler('Нет соединения с базой данных', 2);
		else mysql_query('SET NAMES utf8');

		if(!@mysql_select_db($this->db_name, $this->db_conn)) {
			$this->errorHandler('Не могу выбрать базу данных!', 2);
			return false;
		}
		else return true;
	}

	// modified! l0ver [27.11.2009]
	private function logon() {
		$sql	= "SELECT * FROM `%s` WHERE login = '%s'";
		$sql	= sprintf ( $sql, self::TABLE_ADMINISTRATORS, $this -> login );
		$query	= $this -> mysqlQuery ( $sql );
		$data	= mysql_fetch_array ( $query );

		if ( $data['password'] == md5 ( $this -> password ) ) {
			list ( $_SESSION['name'], $_SESSION['id'], $_SESSION['login'], $_SESSION['lang'] )	= array ( $data['name'], $data['id'], $this -> login, $data['lang'] );

			if ( $_POST['save_login'] == 'on' ) $this -> setCookies ();
			return true;
		}
		else {
			$this -> errorHandler ( 'Неверный логин или пароль!', 1 );
			return false;
		}
	}

	private function logoff () {
		setcookie ( self::COOKIE_NAME_AUTHORISED, false );
		setcookie ( self::COOKIE_NAME_LOGIN, null );
		setcookie ( self::COOKIE_NAME_PASSWORD, null );

		session_destroy ();
		$this -> redirect ( $this -> root_path . self::MODULE_NAME );
	}

	protected function errorHandler($error, $rate) {
		// уровни ошибок
		// 0 - низкий, почти уведомление
		// 1 - средний, но не критичный
		// 2 - критический, влияет на работу системы

		if(strlen($error)>0) array_push($this->errors, array($rate => $error));
	}

	protected function errorsPublisher() {
		if(count($this->errors)>0) {
			foreach ($this->errors as $v) {
				foreach ($v as $key => $value) {
					$out .= '<span style="font-size:1'.$key.';"><b>'.$value.'</b></span><br />';
				}
			}
			echo '<div style="padding:5px; border:1px solid #000000;width:90%; background-color:#fcc;">'.$out.'</div>';

		}
	}

	private function getConfig() {
		if(file_exists($this->config)) {
			$DBvars['host'] = '';
			$DBvars['name'] = '';
			$DBvars['user'] = '';
			$DBvars['pass'] = '';
			$ftpserver = '';
			$ftppass = '';
			$ftpuser = '';
			$current['main_templates'] = '';
			$user_identificator = '';
			$debug_mode = '';
			$track_queries = '';
			$show_execution_time = '';
			$root_path = '/';
			$user_types = '';

			require_once($this->config);
			$DBvars['host']!=''?$this->db_host = $DBvars['host']:$this->errorHandler('Не указан хост базы данных', 2);
			$DBvars['name']!=''?$this->db_name = $DBvars['name']:$this->errorHandler('Не указано название базы данных', 2);
			$DBvars['user']!=''?$this->db_user = $DBvars['user']:$this->errorHandler('Не указано имя пользователя базы данных', 2);
			$this->db_pass = $DBvars['pass'];

			$current['main_templates'] = $_SERVER['DOCUMENT_ROOT'].'/account_admin/lib/main_templates.php';
			
			$this->templates_file = $current['main_templates'];
			$this->user_hash = $user_identificator;
			$this->debug_mode = $debug_mode;
			$this->track_queries = $track_queries;
			$this->exec_time = $show_execution_time;
			$this->root_path = $root_path;
		}
		else $this->errorHandler('Не найден конфигурационный файл '.$config, 2);
	}

	private function getTemplates() {
		if(file_exists($this->templates_file)) {
			require_once($this->templates_file);
			$this->templates['header'] = $header;
			$this->templates['footer'] = $footer;
			$this->templates['login_form'] = $login_form;
		}
		else $this->errorHandler('Не найден основной файл шаблонов '.$this->templates_file, 1);
	}

	private function checkLoginData () {
		if(isset($_POST['login']) and $_POST['login']) $this->login = $_POST['login'];
		if(isset($_POST['password']) and $_POST['password']) $this->password = $_POST['password'];

		return $this -> logon ();
	}

	protected function checkAuth () {
		$this -> authorised	= $_SESSION['logged'];
		return $this -> authorised;
	}

	private function authorise () {
		$_SESSION['logged']	= true;

		// согласно ТЗ "фиксируем дату и время последней авторизации" (l0ver)
		$sql	= "UPDATE %s SET last_login = NOW() WHERE login = '%s'";
		$sql	= sprintf ( $sql, self::TABLE_ADMINISTRATORS, $this -> login );
		$query	= $this -> mysqlQuery ( $sql );

		$this -> redirect ( $this -> root_path . self::MODULE_NAME . '/' );
	}

	protected function createLoginForm () {
		$form	= $this -> templates['login_form'];
		$form	= str_replace ( '[%user_identificator%]', $this -> user_hash, $form );
		$form	= str_replace ( '[%login%]', $this -> login, $form );

		if( $this -> checkCookies () ) {
			if ( $this -> logon () ) $this -> authorise ();
		}
		$pseudo_pass = '';

		$form	= str_replace ( '[%password%]', $pseudo_pass, $form );

		$this -> page .= $form;
	}

	private function setCookies ( $cookie_expire = 2592000 ) {
		setcookie ( self::COOKIE_NAME_LOGIN, $this -> login, time () + $cookie_expire );
		setcookie ( self::COOKIE_NAME_PASSWORD, $this -> password, time () + $cookie_expire );
		setcookie ( self::COOKIE_NAME_AUTHORISED, 1, time() + $cookie_expire );
	}

	public function checkCookies () {
		if( isset ( $_COOKIE['morozov'] ) and $_COOKIE['morozov'] == '1' ) {
			$this -> login	= $_COOKIE['pavlik'];
			$this -> password	= $_COOKIE['ivanovich'];
			$this -> auth_cookies	= true;
			return true;
		}
		else {
			$this -> auth_cookies	= false;
			return false;
		}
	}

	public function mysqlQuery($request) {
		if($request!='') {
			$result = mysql_query($request);
			if(mysql_error()) $status = 0;
			else $status = 1;

			if($this->debug_mode) array_push($this->query_stack, array($status => $request));
		}
		return $result;
	}

	protected function debugQueries() {
		echo '<hr><div style="background-color:#fff; font-size:10px; border:1px solid #ccc;padding:5px;width:700px;">';
		if(count($this->query_stack)>0) {
			foreach ($this->query_stack as $k=>$v) {
				foreach($v as $key => $value) {
					if($key=='0') $style = ' style="color:#ff0000;"';
					else $style = '';

					echo '<p'.$style.'>'.($k+1).': '.$value.'</p>';
				}
			}
		}
		echo '</div>';

	}

	private function saveQueries() {
		$passed = End_Timer($this->page_started);
		if($passed>'0.1') {
			if(count($this->query_stack)>0) {
				foreach ($this->query_stack as $k=>$v) {
					foreach($v as $key => $value) {
						$queries .= mysql_real_escape_string($value).'
						';	
					}
				}
				$request = "INSERT INTO `ccl_logs`
				(`id`, `date`, `time`, `queries`, `file`)
				VALUES
				(LAST_INSERT_ID(),
				NOW(),
				'".$passed."',
				'".mysql_real_escape_string($queries)."',
				'".mysql_real_escape_string($_SERVER['QUERY_STRING'])."')";

				mysql_query($request);
			}
		}
	}

	private function timePassed() {
		$time = End_Timer($this->page_started);

		echo '<div style="padding:5px; border:1px solid #ccc; width:180px; font-size:10px;color:#aaa;">executed in: '.
		$time.' sec.</div>';
	}


	public function makeTopMenu()
	{
		$this -> page	.= '<div class="admMenu"><table width="98%" border="0" cellspacing="0" cellpadding="0" style="margin:3px">
		<tr><td>' . $this->translate->_('Привет').', ' . $_SESSION['name'] . '! &nbsp;&nbsp;<a href="' . $this -> root_path . self::MODULE_NAME . '/?exit">' . $this ->translate->_('выйти') . '</a></td>
		<td align="right">
		 <select style="width:60px;font-size:9px;">
			<option>Русский
			<option>English
		 </select>
		</td></tr>
		</table></div>

		<table width="970" border="0" cellspacing="0" cellpadding="0" class="topmenu">
	 	<tr>
	    <td width="141"><a href="' . $this -> root_path . self::MODULE_NAME . '/"><img src="' . $this -> root_path . 'img/ccl/logo_sm.gif" width="141" height="70" border="0" alt="На первую страницу" /></a></td>
	    <td align="left" valign="top"><table cellspacing="0" cellpadding="0" height="70"><tr>

<td class="menuItemoff" onclick="document.location=\'' . $this -> root_path . self::MODULE_NAME . '/?mod=admin\';"><a href="' . $this -> root_path . self::MODULE_NAME . '/?mod=admin">АДМИНИСТ-РЫ</a></td>
<td class="menuItemoff" onclick="document.location=\'' . $this -> root_path . self::MODULE_NAME . '/?mod=account\';"><a href="' . $this -> root_path . self::MODULE_NAME . '/?mod=account">АККАУНТЫ</a></td>

</tr></table></td>
		</tr></table>';
	}


	public function redirect ( $location ) {
		header ( 'Location: ' . $location );
	}


	public function changePassword ( $table, $entry ) {
		if ( isset ( $_POST['pwd1'] ) && strlen ( $_POST['pwd1'] ) >= self::MIN_PASSWORD_LENGTH ) {
			if ( $_POST['pwd1'] == $_POST['pwd2'] ) {
				$sql	= "UPDATE %s SET password = MD5('%s') WHERE id = '%d'";
				$sql	= sprintf ( $sql, $table, $_POST['pwd1'], $entry );

				$query	= $this -> mysqlQuery ( $sql );
			} else {
				$this -> errorHandler ( 'Пароль и подтверждение пароля не совпадают. Пароль изменен не будет.', 0 );
			}
		}
	}

}

	// ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~