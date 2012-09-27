<?

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Mainview.php');

abstract class Proto {
	
	public $config;
	private $current_version = '1.4.0b';
	private $last_updated = '18 апреля 2008 года 15:55 GMT+6';
	
	
	protected $login; // поступает от пользователя вместе с паролем
	private $password;
	
	private $location; // название текущего модуля
	
	public $per_page = 20; // количество записей на странице в списках
	
	// пользователь
	private $user_id; // получаем из базы при успешной авторизации, для клиентов
	private $login_id; // id записи пользователя 
	private $user_type; // тип пользователя, получаем из базы после успешной авторизации
	private $main_menu = array(); // главное меню для данного типа пользователя
	protected $permissions = array(); // права доступа для данного типа пользователя
	protected $user_folder; // путь, после корневого, в котором работает данный пользователь
	protected $user_types; // типы пользователей
	
	// база данных
	protected $db_host;
	private $db_name;
	private $db_user;
	private $db_pass;
	private $db_conn;
	
	// FTP
	protected $ftp_host;
	protected $ftp_user;
	protected $ftp_pass;
	
	protected $root_path; // путь установки системы, от корневого
	
	// сессия
	private $logon_time;
	private $session_lifetime;
	private $query_stack = array();
	private $errors = array(); // ошибки возникшие в результате выполнения скриптов
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
	public $page; // HTML страницы
	
	// построение списков
	protected $sortNow;
	
	// папки для фотографий
	protected $photo_folders;
	
	// места нахождения машины или тех пасспорта
	public $place = array(1=>"На аукционе",2=>"В экспедиторской компании",4=>"У водителя",3=>"В Makmal North America",4=>"У продавца",5=>"У покупателя", 6=>"Загружена в контейнер", 7=>"Доставлена на место назначения", 8=>"В пути в Порт/к Эспедитору");
	
	//###########################

	function __construct() {
		setlocale(LC_ALL, 'ru_RU.cp1251');
		//$this->buildSession();
		if(isset($_GET['exit'])) $this->logoff();
		$this->page_started = Start_Timer();	
				
		$this->config = $_SERVER['DOCUMENT_ROOT'].'/inc/config.php';
		$this->getConfig();
		$this->connectDB();
		
		$this->getTemplates();
		
		if(isset($_POST['auth'])) {
			if($this->checkLoginData()) {
				$this->authorise();
			}
		}
			
		//if($this->user_type=='2') $this->redirect($this->root_path.'public');
		if($this->user_type=='8') $this->redirect($this->root_path.'adm_transporters');
		if($this->user_type=='8') $this->redirect($this->root_path.'adm_expeditors');
	}
	
	function __destruct() {
// 		$this->unlinkDB();
	}
	
	private function buildSession() {
	/*	session_start();*/
	}
	
	protected function publish() {
		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		else echo $this->page;
		if($this->debug_mode) $this->debugQueries();
		if($this->exec_time) $this->timePassed();
		if($this->track_queries) $this->saveQueries();
	}
	
	private function connectDB() {
			// соединяемся с базой данных
			if(!$this->db_conn = @mysql_connect($this->db_host, $this->db_user, $this->db_pass)) $this->errorHandler('Нет соединения с базой данных', 2);
			else mysql_query('SET NAMES cp1251');
			
			if(!@mysql_select_db($this->db_name, $this->db_conn)) {
				$this->errorHandler('Не могу выбрать базу данных!', 2);
				return false;
			}
			
			else return true;
			
	}
	
	private function logon() {
		
		$sql_info = $this->mysqlQuery("
		SELECT * 
		FROM `ccl_usrs` 
		WHERE log_name = '".$this->login."'");
		$query_data = mysql_fetch_array($sql_info);
		
		$pass_check = md5($this->password);
		if($pass_check==$query_data['pass_code']) {
		
			$_SESSION['user_name'] = $this->login;
			$_SESSION['user_type'] = $query_data['type'];
			if($query_data['u_id']) $_SESSION['user_id'] = $query_data['u_id'];
			if($query_data['t_id']) $_SESSION['user_id'] = $query_data['t_id'];
			if($query_data['e_id']) $_SESSION['user_id'] = $query_data['e_id'];
			$_SESSION['login_id'] = $query_data['id'];
			$_SESSION['user_identity'] = $this->user_hash;
			
			if($_POST['save_login']=='on') $this->setCookies();
			return true;
		}
		else { 
			$this->errorHandler('Неверный логин или пароль!', 1);
			return false;
		}
	}
	
	private function logoff() {
		session_destroy();
		$this->setCookies(-3600);
		$this->redirect('/');
	}
	
	private function checkLoginData() {
		
		if(isset($_POST['login']) and $_POST['login']!='') $this->login = $_POST['login'];
		if(isset($_POST['password']) and $_POST['password']!='') $this->password = $_POST['password'];
		
		return $this->logon();
	}
	
	protected function checkAuth() {
		$this->authorised = $_SESSION['authorised'];
		return $this->authorised;
		
	}
	
	private function authorise() {
		$_SESSION['authorised'] = true;
		//$this->authorised = $_SESSION['authorised'];
		$this->redirect($this->root_path);
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
					$out .= '<span style="font-size:1'.$key.';">'.$value.'</span><br>';
				}
			}
			echo '<div style="padding:5px; border:1px solid #000000;width:500px; background-color:#fcc;">'.$out.'</div>';
			
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
			$root_path = '';
			$user_folders[$_SESSION['user_type']] = '';
			$user_types = '';
			
			require_once($this->config);
			$DBvars['host']!=''?$this->db_host = $DBvars['host']:$this->errorHandler('Не указан хост базы данных', 2);
			$DBvars['name']!=''?$this->db_name = $DBvars['name']:$this->errorHandler('Не указано название базы данных', 2);
			$DBvars['user']!=''?$this->db_user = $DBvars['user']:$this->errorHandler('Не указано имя пользователя базы данных', 2);
			$this->db_pass = $DBvars['pass'];
			
			$this->ftp_host = $ftpserver;
			$this->ftp_user = $ftpuser;
			$this->ftp_pass = $ftppass;
			
			$this->templates_file = $current['main_templates'];
			$this->user_hash = $user_identificator;
			$this->debug_mode = $debug_mode;
			$this->track_queries = $track_queries;
			$this->exec_time = $show_execution_time;
			$this->root_path = $root_path;
			$this->user_folder = isset($_SESSION['user_type'])?$user_folders[$_SESSION['user_type']]:false;
			$this->user_types = $user_types;
			
			$this->photo_folders = $photo_folders;
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
	
	protected function checkDataType($var, $type='string') {
		return $var;
	}
	
	private function unlinkDB() {
		if(isset($conn)) mysql_close($this->db_conn);
	}
	
	protected function createLoginForm() {
		$form = $this->templates['login_form'];
		$form = str_replace('[%user_identificator%]', $this->user_hash, $form);
		$form = str_replace('[%login%]', $this->login, $form);
		//$form = str_replace('[%warnings%]', $this->errorsPublisher(), $form);
		
		if($this->checkCookies()) {
			if($this->logon()) $this->authorise();// $pseudo_pass = '******';
		}
		$pseudo_pass = '';
		
		$form = str_replace('[%password%]', $pseudo_pass, $form);
		
		$this->page .= $form;
	}
	
	private function setCookies($cookie_expire = 2592000) {
		setcookie("elmundo", $this->login, time()+$cookie_expire);
		setcookie("teqiero", $this->password, time()+$cookie_expire);
		setcookie("muchachos", 1, time()+$cookie_expire);
	}
	
	public function checkCookies() {
		if(isset($_COOKIE['muchachos']) and $_COOKIE['muchachos']=='1') {
			$this->login = $_COOKIE['elmundo'];
			$this->password = $_COOKIE['teqiero'];
			$this->auth_cookies = true;
			return true;
		}
		else {
			$this->auth_cookies = false;
			return false;
		}
	}
	
	protected function seedMaker() {
		$symb = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890,./?;:()-=+_!@#$%^&*{}[]';
		
		$j = 1;
		while($j<=6) {
			$char .= substr($symb, rand(1,strlen($symb)),1);
			$j++;
		}
		return $char;
	}

	protected function hashMaker($seed, $time) {
		return  md5(md5($seed).md5($time));
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
	
	//верхнее меню
	public function makeTopMenu()
	{
		$this->getPermissions();
		
		if($this->permissions['type']!='0' and $this->permissions['type']!='')
		{
			$menu_element = '
			<td class="menuItem[%button%]" onclick="document.location=\''.$this->root_path.$this->user_folder.'?mod=[%item%]\';"><a href="'.$this->root_path.$this->user_folder.'?mod=[%item%]">[%item_text%]</a></td>';
			
			$this->page .= '<div class="admMenu"><table width="98%" border="0" cellspacing="0" cellpadding="0" style="margin:3px">
			<tr><td>Привет, '.$_SESSION['user_name'].'! &nbsp;&nbsp;<a href="'.$this->root_path.'?exit">выйти</a></td>
			<td align="right">';
			
			// СТАВКИ
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4') {
			 	if((time() - $_SESSION['last_stakes_counted'])>60) $new = $this->getNewStakesNumber();
			 	else $new = $_SESSION['new_stakes_counted'];
			 	
			 	if($new>0) $new = '<img src="'.$this->root_path.'img/stakes/alert.gif" align="absmiddle" hspace="5"> (+'.$new.')';
			 	else $new = '';
			}
			//#################
			if($_SESSION['user_type']!='8' && $_SESSION['user_type']!='9')
				$this->page .= '<a href="?mod=sale">продажа автомобилей</a> &nbsp;&nbsp;&nbsp;';
			
			// дополнительное админское меню
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7')
			 $this->page .= '
			 <a href="'.$this->root_path.'?mod=auctions">список аукционов</a>&nbsp;&nbsp;&nbsp;
			 <a href="'.$this->root_path.'?mod=reports">отчеты</a>&nbsp;&nbsp;&nbsp;
			 <a href="'.$this->root_path.'?mod=users">пользователи</a>&nbsp;&nbsp;&nbsp;
			 <a href="'.$this->root_path.'?mod=backup">резервное копирование</a>&nbsp;&nbsp;&nbsp;
			 <a href="'.$this->root_path.'?mod=fileshare">файлы</a>';
			//#################
			
			$this->page .= '</td></tr>
			</table></div>';
	
			$this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="topmenu">
		 	<tr>
		    <td width="141"><a href="'.$this->root_path.$this->user_folder.'"><img src="'.$this->root_path.'img/ccl/logo_sm.gif" width="141" height="70" border="0" alt="На первую страницу" /></a></td>
		    <td align="left" valign="top"><table cellspacing="0" cellpadding="0" height="70"><tr>'.
		 	$this->menuMaker($menu_element).'</tr></table></td>
			</tr></table>';
		}
		else $this->errorHandler('У вас нет доступа для просмотра этой страницы!',1);
	}
	
	private function menuMaker($template) {
		$out=false;
		$this->getMenu();
		$this->location = $_GET['mod'];
		$menu = $this->main_menu[$this->permissions['type']];
		if(count($menu)==0) $this->errorHandler('Для данного типа пользователя меню не существует!', 1);
		else {
			foreach ($menu as $k=>$v) {
				$tmp = $template;
				
				if($this->permissions[$v['item']]=='1') {
					$tmp = str_replace('[%item_text%]',$v['text'], $tmp);
					$tmp = str_replace('[%item%]',$v['item'],$tmp);
					if($v['item']==$this->location) $button = 'on';
					else $button = 'off';
					
					$tmp = str_replace('[%button%]',$button,$tmp);
				}
				else $tmp ='';	
				if($tmp!='') $out .= $tmp;
			}
		}
		
		return $out;
	}
	
	private function getMenu() {
		$path = $_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/menu.php';
		if(file_exists($path)) {
			require_once($path);
			$this->main_menu = $menu;
		}
		else $this->errorHandler('Не найден файл содержащий меню пользователей', 2);
	}
	
	private function getPermissions() {
		$file_path = $_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/access.php';	
		if(file_exists($file_path)) {
			require_once($file_path);
			$this->permissions = $matrix[$_SESSION['user_type']];
		}
		else $this->errorHandler('Не найден файл с правами пользователей!', 2);
		
	}
	
	public function exists($var) {
		if(isset($var) and $var!='') return true;
		else return false;
	}
	
	public function redirect($location) {
		header('Location: '.$location);
	}
	
	protected function getCustomersList() {
			$sql = "SELECT name, id, dealer, mydealer 
			FROM `ccl_customers`
			WHERE 1	ORDER BY `name` ASC";
			return $this->mysqlQuery($sql);		
	}
	
	/** 20080209 **/
	protected function getTransportersList() {
		$sql = "SELECT name, id FROM `ccl_transporters` ORDER BY `name` ASC";
		return $this->mysqlQuery($sql);		
	}
	protected function getExpeditorsList() {
		$sql = "SELECT name, id FROM `ccl_expeditors` ORDER BY `name` ASC";
		return $this->mysqlQuery($sql);		
	}
	/** /20080209 **/
	
	protected function getPlacesList() {
			$sql = "SELECT * 
			FROM `ccl_places`
			WHERE 1	ORDER BY `name` ASC";
			return $this->mysqlQuery($sql);		
	}
	
	public function buildList($request,$cols,$list,$module,$item,$limit)
	{
		//сортировка
		if($cols[1]['name']=='date' or $cols[1]['name']=='save_date') $sort_dir = 'DESC';
		else $sort_dir = 'ASC';
		$order_list = $this->defineSort('sort_'.$list, '`'.$cols[1]['name'].'` '.$sort_dir); //добавляем сортировку в запрос
		$this->sortDeco('sort_'.$list); //выводим указатель того, что сейчас сортируется и направление сортировки
		$request = $request." ORDER BY ".$order_list.$limit;
	
		$content = $this->mysqlQuery($request);
			
		$num = mysql_num_rows($content);
		$i=1; 
		$class="rowA rowB";
		$out.='
		<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
			<tr class="title sortButtons">';
		while($i<=count($cols))
		{
			$out .= $this->sorterTD($module, $cols[$i]['name'], $cols[$i]['caption'], $cols[$i]['width']);
						 
			$i++;
		}
		$out.='</tr>';
		$i=1; 
		while ($i<=$num)
		{
			$line = mysql_fetch_array($content);
			if((!isset($_GET['mod']) or $_GET['mod']=='clients') and ($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4')) {
				$onclick_location = 'document.location=\''.$this->root_path.'?mod='.$list.'&sw=detail&id='.$line['id'].'\'';
			}
			else $onclick_location = 'document.location=\''.$this->root_path.'?mod='.$list.'&sw=form&'.$item.'='.$line['id'].'\'';
			
		$out .= '
		<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" onclick="'.$onclick_location.'">';
			$j=1;
			while($j<=count($cols))
			{
				$out.='
				<td>'.cleanContent($line[$cols[$j]['name']]).'&nbsp;</td>';
				$j++;
			}
			$out .= '
			</tr>';
			$i++;
			if ($class=="rowA") $class="rowA rowB"; 
			else $class="rowA";
		}
		$out .= '</table>';
		
		return $out;
	}
	
	//постраничный переход
	public function pageBrowse($page, $module, $total, $sub_get = '') {
		
		$maxPagesLinks = 5; // количество ссылок на следующие и/или предыдущие страницы
		
		if($page>1 and $page!=0)
		{
			$start = ($page*$this->per_page)-$this->per_page;
			$out['qlimit'] = " LIMIT ".$start.", ".$this->per_page;
			$out['next_page'] = $page+1;
			$out['prev_page'] = $page-1;
		}
	
		else {
			$out['qlimit'] = " LIMIT 0, ".$this->per_page;
			$out['next_page'] = 2;
			$page = '1';
		}
		$out['print'] = '<div class="pages"><center>
		<table cellspacing="2" cellpadding="0">
		<tr>';
		
		if($module!='') $location = 'mod='.$module.'&';
		
		// ссылка в начало
		if($page>1) $out['print'] .= '<td><a href="'.$this->root_path.'?'.$location.'page=1'.($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_to_first.gif" border="0" alt="на первую страницу" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_to_first_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_to_first.gif\'"></a>&nbsp;</td>';
		
		// предыдущая страница
		if($out['next_page'] > 2) $out['print'] .= '<td>
		<a href="?'.$location.'page='.$out['prev_page'].($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_prev_page.gif" border="0" alt="на предыдущую страницу" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_prev_page_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_prev_page.gif\'"></a>
		&nbsp;&nbsp;</td>';
		
		// ссылки не ПРЕДЫДУЩИЕ страницы
		$i = 1;
		while($i<=$maxPagesLinks) {
			
			if($i<$page) {
				$current_page_link = $page-$i;
				$backLinks = '<td><a href="'.$this->root_path.'?'.$location.'page='.$current_page_link.($sub_get!=''?$sub_get:'').'"><div id="pagenum">'.$current_page_link.'</div></a></td>'.$backLinks;
			}
			else break;
			$i++;
		}
		
		// ссылки на СЛЕДУЮЩИЕ СТРАНИЦЫ
		$total_pages = intval($total/$this->per_page); // максимально страниц для этой таблицы
		if((($total/$this->per_page)-$total_pages)>0) $total_pages++;
		$i = 1;
		while($i<=$maxPagesLinks) {
			$next_page = $page+$i;
			if($next_page<=$total_pages) {
				$forward_links = $forward_links.'<td><a href="'.$this->root_path.'?'.$location.'page='.$next_page.($sub_get!=''?$sub_get:'').'"><div id="pagenum">'.$next_page.'</div></a></td>';
			}
			else break;
			$i++;
		}
		
		$out['print'] .= $backLinks.'<td><div id="curpage">'.$page.'</div></td>'.$forward_links;
		
		
		// следующая страница
		if($page*$this->per_page < $total) $out['print'] .= '<td>&nbsp;&nbsp;<a href="'.$this->root_path.'?'.$location.'page='.$out['next_page'].($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_next_page.gif" border="0" alt="на следующую страницу" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_next_page_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_next_page.gif\'"></a></td>';	
		
		// ссылка в конец
		if($page<$total_pages) $out['print'] .= '<td>&nbsp;<a href="'.$this->root_path.'?'.$location.'page='.$total_pages.($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_to_last.gif" border="0" alt="на последнюю страницу" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_to_last_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_to_last.gif\'"></a></td>';
		
		$out['print'] .= '
		</tr>
		</table>
		</center>
		</div>';
		return $out;
	}
	
	//построение выпадающего списка
	public function buildSelect($table, $name, $condition, $empty, $tabindex, $onchange = '') {
		$num = mysql_num_rows($table);
		$i=1;
		if($num>0) {
			$out = '
			<select name="'.$name.'" tabindex="'.$tabindex.'" id="List'.$name.'"';
			if($onchange!='') $out .= ' onchange="'.$onchange.'"';
			$out .= '>
			<option value="0"';
			if($condition==0) $out .= ' selected="selected"';
			$out .= '>'.$empty.'</option>';
			while($i<=$num) {
				$line = mysql_fetch_array($table);
				$out .= '
				<option value="'.$line['id'].'"';
				if($condition==$line['id']) $out .= ' selected="selected"';
				$out .= '>'.stripslashes($line['name']).'</option>';
				$i++;
			}
			$out .= '</select>';
		}
		else $out = '';
		
		return $out;
	}
	
	//сортировка списков
	public function defineSort($list, $default) {
		
		if($this->exists($_GET['sort']) and isset($_GET['sdir'])) {
			if($_GET['sdir']=='up') $sdir = 'ASC';
			elseif($_GET['sdir']=='down' or $_GET['sdir']=='') $sdir = 'DESC';
			else $sdir = 'DESC';
			$_SESSION[$list] = mysql_real_escape_string($_GET['sort']);
			$_SESSION[$list.'_dir'] = $sdir;
		}
				
		if(isset($_SESSION[$list])) $order = $_SESSION[$list].' '.$_SESSION[$list.'_dir'];
		else $order = $default;
		
		return $order;
	}
	
	public function sortDeco($list) {
	
		if($_SESSION[$list.'_dir'] == 'ASC') { 
			$sdir = 'asc';
			$this->sortNow['resort'][$_SESSION[$list]] = 'down';
		}
		elseif($_SESSION[$list.'_dir'] == 'DESC') {
			$sdir = 'desc';
			$this->sortNow['resort'][$_SESSION[$list]] = 'up';
		}
		
		$this->sortNow['image'][$_SESSION[$list]] = '<img src="'.$this->root_path.'img/ccl/'.$sdir.'.gif" style="position:absolute;margin-top:2px; margin-left:2px;">';
	}
	
	public function sorterTD($module, $item, $caption, $width) {
		
		if(isset($_GET['ref'])) $type = 'ref';
		elseif(isset($_GET['mod'])) $type = 'mod';
		return '<td width="'.$width.'" onclick="document.location=\''.$this->root_path.$this->user_folder.'?'.$type.'='.$module.'&sort='.$item.'&sdir='.$this->sortNow['resort'][$item].'\'" onMouseOver="this.className=\'sortButtonsHover\'" onMouseOut="this.className=\'\'" style="cursor:pointer;">'.$caption.' '.$this->sortNow['image'][$item].'</td>';
	}
	
	public function getRootPath() {
		return $this->root_path;
	}
	
	protected function getNewStakesNumber() {
		
		$new = mysql_fetch_array($this->mysqlQuery("
		SELECT COUNT(id) as total FROM `stakes_list`
		WHERE `status` = '0'"));
		$_SESSION['last_stakes_counted'] = time();
		$_SESSION['new_stakes_counted'] = $new['total'];
				
		return $new['total'];
	}
	function wrapFile($in, $bg='') {
		if($bg!='') $wrap_bg = 'background-color:'.$bg;
		return '<div style="float:left; width:128px; padding:2px; border:1px solid #ddd;text-align:center; margin-right:4px; margin-bottom:5px;'.$wrap_bg.'">
		'.$in.'</div>';
	}
}

?>