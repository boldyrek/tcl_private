<?
require_once($_SERVER['DOCUMENT_ROOT'].'/Zend/Registry.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/functions.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Mainview.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/Zend/Translate.php');

abstract class Proto {

    public $config;
    private $current_version = '1.9.5b';
    private $last_updated = '13 january 2011, 16:10 GMT+6';


    protected $login; // поступает от пользователя вместе с паролем
    private $password;
    /*Account id. Также поступает вместе с паролем.*/
    private $_accId = '';
    private $_accSuffix = '';

    private $location; // название текущего модуля

    public $per_page = 30; // количество записей на странице в списках

    // пользователь
    private $user_id; // получаем из базы при успешной авторизации, для клиентов
    private $login_id; // id записи пользователя
    private $user_type; // тип пользователя, получаем из базы после успешной авторизации
    private $main_menu = array(); // главное меню для данного типа пользователя
    protected $permissions = array(); // права доступа для данного типа пользователя
    protected $user_folder; // путь, после корневого, в котором работает данный пользователь
    protected $user_types; // типы пользователей

    // язык пользователя
    //	public $lang = array();
    public $user_lang;
    public $translate;

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

    protected $root_path='/'; // путь установки системы, от корневого

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
    public $place;// = array(1=>"На аукционе",2=>"В экспедиторской компании",3=>"В Makmal North America",4=>"У продавца",5=>"У покупателя", 6=>"Тайтл выслан покупателю", 8=>"В пути в Порт/к Эспедитору", 9=>"В автосалоне Ямато");
    //	public $place = array(1=>"На аукционе",2=>"В экспедиторской компании",4=>"У водителя",3=>"В Makmal North America",4=>"У продавца",5=>"У покупателя", 6=>"Загружена в контейнер", 7=>"Доставлена на место назначения", 8=>"В пути в Порт/к Эспедитору");    , 9=>"В автосалоне Ямато"

    public $place_in_amerika = array(1,2,3,4);

    // Места нахождение !(товара) тайтла?!?!
    public $tplace;// = array(1=>"У продавца",2=>"У покупателя",3=>"Загружена в контейнер",4=>"Отправлена почтой в место назначения",5=>"В контейнере");

    public $purposes = null;

    // Settings properties
    public $company_name;
    public $company_bank_account;
    public $company_credentials;

    public $mail_template1;
    // - End of settings

    //###########################

    function __construct() {
        setlocale(LC_CTYPE, 'ru_RU.utf8');
        setlocale(LC_TIME, 'ru_RU.utf8');
        setlocale(LC_COLLATE, 'ru_RU.utf8');

        //$this->buildSession();
        if(isset($_GET['exit'])) $this->logoff();
        $this->page_started = Start_Timer();

        $this->config = $_SERVER['DOCUMENT_ROOT'].'/inc/config.php';
        $this->getConfig();
        $this->connectDB();

        $this->chooseLang();	// Вызов функции выбора языка
        $this->initLang();

        $this->getSettings();

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

    // Выбор языка пользователя
    protected function chooseLang(){
        if(isset($_POST['switch_lang']))	// and $this->checkAuth()
        {
            echo $this->login;
            switch($_POST['switch_lang'])
            {
                case 'rus':
                    $this->mysqlQuery("UPDATE ccl_".ACCOUNT_SUFFIX."usrs SET lang='rus' WHERE id='".$_SESSION['login_id']."' LIMIT 1");
                    $_SESSION['lang']='rus';
                    $this->user_lang='rus';
                    break;
                case 'eng':
                    $this->mysqlQuery("UPDATE ccl_".ACCOUNT_SUFFIX."usrs SET lang='eng' WHERE id='".$_SESSION['login_id']."' LIMIT 1");
                    $_SESSION['lang']='eng';
                    $this->user_lang='eng';
                    break;
            }
            //if(mysql_errno()) echo '<span style="color: red">Language switching error happened!</span> : '.mysql_error();
            $this->redirect($_SERVER['REQUEST_URI']);
            exit();
        }
        else
        {
            if(isset($_SESSION['lang']) and $_SESSION['lang']!='0')
            {
                $this->user_lang = $_SESSION['lang'];
            }
        }
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

    // Get arrays stored for each account from database
    protected function getSettings(){
        if($_SESSION['authorised']){
            // Company settings
            $r = mysql_fetch_assoc($this->mysqlQuery('SELECT data from ccl_'.ACCOUNT_SUFFIX.'settings WHERE name=\'company_name\' LIMIT 1'));
            $this->company_name = $r['data'];

            Zend_Registry::set('company_name', $this->company_name);
            // Arrays settings
            $r = mysql_fetch_assoc($this->mysqlQuery('SELECT data from ccl_'.ACCOUNT_SUFFIX.'settings WHERE name=\'car_location\' LIMIT 1'));
            $this->place = unserialize($r['data']);
            $rt = mysql_fetch_assoc($this->mysqlQuery('SELECT data from ccl_'.ACCOUNT_SUFFIX.'settings WHERE name=\'title_location\' LIMIT 1'));
            $this->tplace = unserialize($rt['data']);
            $rp = mysql_fetch_assoc($this->mysqlQuery('SELECT data from ccl_'.ACCOUNT_SUFFIX.'settings WHERE name=\'purposes\' LIMIT 1'));
            $this->purposes = unserialize($rp['data']);
            Zend_Registry::set('car_location', $this->place);
            Zend_Registry::set('title_location', $this->tplace);
            Zend_Registry::set('purposes', $this->purposes);
        }
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
        else mysql_query('SET NAMES utf8');

        if(!@mysql_select_db($this->db_name, $this->db_conn)) {
            $this->errorHandler('Не могу выбрать базу данных!', 2);
            return false;
        }
        else return true;
    }

    private function logon() {

        if ($this->_accId>0){
            $sql = "SELECT * FROM `accounts` WHERE `id`='".$this->_accId."'";
            $res  = $this->mysqlQuery($sql);
            $info = @mysql_fetch_assoc($res);
            if (empty($info)){
                $this->errorHandler('Данной учетной записи не существует!', 1);
                return false;
            }

            if ($info['active']==0){
                $this->errorHandler('Ваша учетная запись не активна. Свяжитесь с администратором.', 1);
                return false;
            }
        }
        $sql = "
		SELECT u.*, c.autocheck AS `autocheck_allowed`
		FROM `ccl_".$this->_accSuffix."usrs` AS u
                LEFT JOIN `ccl_".$this->_accSuffix."customers` AS c
                ON (u.u_id = c.id)
		WHERE u.log_name = '".$this->login."'";

        $sql_info = $this->mysqlQuery($sql);
        $query_data = mysql_fetch_array($sql_info);


        $pass_check = md5($this->password);
        if($pass_check==$query_data['pass_code']) {

            $this->_defineAccount();

            $_SESSION['user_name'] = $this->login;
            $_SESSION['user_type'] = $query_data['type'];
            if($query_data['u_id']) $_SESSION['user_id'] = $query_data['u_id'];
            if($query_data['t_id']) $_SESSION['user_id'] = $query_data['t_id'];
            if($query_data['e_id']) $_SESSION['user_id'] = $query_data['e_id'];
            $_SESSION['login_id'] = $query_data['id'];
            $_SESSION['user_identity'] = $this->user_hash;
            $_SESSION['lang'] = $query_data['lang'];	// Добавляем в массив сессии язык пользователя
            $_SESSION['autocheck_allowed'] = (bool) $query_data['autocheck_allowed'];

            if($_POST['save_login']=='on') $this->setCookies();
            return true;
        }
        else {
            $this->errorHandler('Неверный логин или пароль!', 1);
            return false;
        }
    }

    /*Если авторизация прошла успешно, обозначаем аккаунт*/
    protected function _defineAccount(){
        $_SESSION['acc_id'] = $this->_accId;

        if (!defined('ACCOUNT_ID')){
            define('ACCOUNT_ID', $this->_accId);
            if (ACCOUNT_ID===0){
                define('ACCOUNT_SUFFIX', '');
            }
            else{
                define('ACCOUNT_SUFFIX', '_'.ACCOUNT_ID);
            }
        }
    }
    private function logoff() {
        session_destroy();

        /*Убираем куки*/
        setcookie("elmundo", null, time());
        setcookie("teqiero", null, time());
        setcookie("muchachos", null, time());
        setcookie("acc_id", null, time());


        // For correct log-outing trying to nullify session vars needed to keep logged on


        $this->redirect('/');
    }

    private function checkLoginData() {

        if(isset($_POST['login']) and $_POST['login']!='') $this->login = addslashes($_POST['login']);
        if(isset($_POST['password']) and $_POST['password']!='') $this->password = $_POST['password'];
        if(isset($_POST['acc_id'])) $this->_accId = intval($_POST['acc_id']);

        if ($this->_accId>0){
            $this->_accSuffix = $this->_accId.'_';
        }

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
            $out='';
            foreach ($this->errors as $v) {
                foreach ($v as $key => $value) {
                    $out .= '<span style="font-size:1'.$key.';"><b>'.$value.'</b></span><br>';
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
            $user_folders[@$_SESSION['user_type']] = '';
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
        $form = str_replace('[%account_id%]', $this->_accId, $form);
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
        setcookie("acc_id", ACCOUNT_ID, time()+$cookie_expire);
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
            $menu_element = '<td class="menuItem[%button%]" onclick="document.location=\''.$this->root_path.$this->user_folder.'?mod=[%item%]\';"><a href="'.$this->root_path.$this->user_folder.'?mod=[%item%]">[%item_text%]</a></td>';
            
            $this->page .= '
            <style>
               #admin-menu td {padding-right:5px}
               #admin-menu dl {margin:0; padding:0}
               #admin-menu dl dd, #admin-menu dl dt {margin:0; padding:0 0 3px 0}
            </style>
            <div class="admMenu">
            <table width="100%" border="0" cellspacing="0" cellpadding="0" id="admin-menu">
            <tr valign="top">
               <td style="width:150px">
                  '.$this->translate->_('Привет').', '.$_SESSION['user_name'].'!
                  <br /><a href="'.$this->root_path.'?exit">'.$this->translate->_('выйти').'</a>
               </td>';

            // СТАВКИ
            if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4')
            {
                //if((time() - $_SESSION['last_stakes_counted'])>60) $new = $this->getNewStakesNumber();
                //else $new = $_SESSION['new_stakes_counted'];

                //if($new>0) $new = '<img src="'.$this->root_path.'img/stakes/alert.gif" align="absmiddle" hspace="5"> (+'.$new.')';
                //else $new = '';
            }

            // Машины на аукционах для админов
            if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7')
            {
               $this->page .= '<td><dl>';
               $this->page .= '<dd><a href="?mod=carscom">'.$this->translate->_('Cars.com').'</a></dd>';
               $this->page .= '<dd><a href="?mod=grabber">'.$this->translate->_('Grabber').'</a></dd>';
               $this->page .= '<dd><a href="?mod=mds">'.$this->translate->_('Market Days Supply').'</a></dd>';
               $this->page .= '<dd><a href="?mod=ptm">'.$this->translate->_('Price To Market').'</a></dd>';
               $this->page .= '</dl></td>';
            }
            // Продажа автомобилей для всех кроме 8 и 9
            if($_SESSION['user_type']!='8' && $_SESSION['user_type']!='9')
            {
               $this->page .= '<td><dl>';
               $this->page .= '<dd><a href="?mod=sale">'.$this->translate->_('продажа автомобилей').'</a></dd>';

               if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7')
               {
                  $this->page .= '<dd><a href="/?mod=tobuilt">Tobuilt</a></dd>';
                  $this->page .= '<dd><a href="/?mod=realtor">Realtor</a></dd>';
               }

               if ($_SESSION['user_type'] == 2 AND $_SESSION['autocheck_allowed'])
               {
                  $this->page .= '<dd><a href="/public/?mod=autocheck">Autocheck</a></dd>';
                  $this->page .= '<dd><a href="/public/?mod=datebyvin">Date by VIN</a></dd>';
                  $this->page .= '<dd><a href="http://www.makmalauto.com/dealersentrance11672/">Диллерские цены</a></dd>';
               }

               $this->page .= '</dl></td>';
            }
            
            // дополнительное админское меню
            if ($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7')
            {
               $this->page .= '
               <td>
                  <dl>
                     <dd><a href="'.$this->root_path.'?mod=auctions">'.$this->translate->_('аукционы').'</a></dd>
                  </dl>
               </td>
               <td>
                  <dl>
                     <dt>'.$this->translate->_('отчеты').':</dt>
                        <dd><a href="'.$this->root_path.'?mod=acc_reports">'.$this->translate->_('бухгалтерия').'</a></dd>
                        <dd><a href="'.$this->root_path.'?mod=con_reports">'.$this->translate->_('контейнеры').'</a></dd>
                  </dl>
               </td>
               <td>
                  <dl>
                     <dd><a href="'.$this->root_path.'?mod=users">'.$this->translate->_('пользователи').'</a></dd>
                     <!-- <a href="'.$this->root_path.'?mod=backup">'.$this->translate->_('резервное копирование').'</a> -->
                     <dd><a href="'.$this->root_path.'?mod=fileshare">'.$this->translate->_('файлы').'</a></dd>
                     <dd><a href="'.$this->root_path.'?mod=profile">'.$this->translate->_('настройки профиля').'</a></dd>
                  </dl>
               </td>
               ';
            }
            //#################

            if ($_SESSION['user_type'] == '1' or $_SESSION['user_type'] == '2' or $_SESSION['user_type'] == '7')
            {
               $this->page .= '
               <td>
                  <form action="'.$_SERVER[REQUEST_URI].'" method="POST" name="language_switcher" id="language_switcher">
                     <select style="width:80px;font-size:9px;" name="switch_lang" onchange="document.language_switcher.submit()">
                        <option'.($this->user_lang == 'eng' ? ' selected=selected' : '').' value="eng">English</option>
                        <option'.($this->user_lang == 'rus' ? ' selected=selected' : '').' value="rus">Русский</option>
                     </select>
                  </form>
               </td>
               ';
            }

            $this->page .= '
            </tr>
            </table>
            </div>';

            $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="topmenu">
		 	<tr>
		    <td width="141"><a href="'.$this->root_path.$this->user_folder.'"><img src="'.$this->root_path.'img/ccl/logo_sm.gif" width="141" height="70" border="0" alt="'.$this->translate->_('На первую страницу').'" /></a></td>
		    <td align="left" valign="top"><table cellspacing="0" cellpadding="0" height="70"><tr>'.
            $this->menuMaker($menu_element).'</tr></table></td>
			</tr></table>';
        }
        else $this->errorHandler($this->translate->_('У вас нет доступа для просмотра этой страницы!'),1);
    }

    private function menuMaker($template) {
        $out=false;
        $this->getMenu();
        $this->location = $_GET['mod'];
        $menu = $this->main_menu[$this->permissions['type']];
        if(count($menu)==0) $this->errorHandler($this->translate->_('Для данного типа пользователя меню не существует!'), 1);
        else {
            foreach ($menu as $k=>$v) {
                if($v['item']=='stuff' and ACCOUNT_ID!='0') continue;
                if($v['item']=='contracts' and ACCOUNT_ID!='0') continue;
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
        else $this->errorHandler($this->translate->_('Не найден файл содержащий меню пользователей'), 2);
    }

    private function getPermissions() {
        $file_path = $_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/access.php';
        if(file_exists($file_path)) {
            require_once($file_path);
            $this->permissions = $matrix[$_SESSION['user_type']];
        }
        else $this->errorHandler($this->translate->_('Не найден файл с правами пользователей!'), 2);

    }

    public function exists($var) {
        if(isset($var) and $var!='') return true;
        else return false;
    }

    public function redirect($location) {
        @header('Location: '.$location);
    }

    protected function getCustomersList() {
        $sql = "SELECT name, id, dealer, mydealer
			FROM `ccl_".ACCOUNT_SUFFIX."customers`
			WHERE 1	ORDER BY `name` ASC";
        return $this->mysqlQuery($sql);
    }

    /** 20080209 **/
    protected function getTransportersList() {
        $sql = "SELECT name, id FROM `ccl_".ACCOUNT_SUFFIX."transporters` ORDER BY `name` ASC";
        return $this->mysqlQuery($sql);
    }
    protected function getExpeditorsList() {
        $sql = "SELECT name, id FROM `ccl_".ACCOUNT_SUFFIX."expeditors` ORDER BY `name` ASC";
        return $this->mysqlQuery($sql);
    }
    /** /20080209 **/

    protected function getPlacesList() {
        $sql = "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."places`
			WHERE 1	ORDER BY `name` ASC";
        return $this->mysqlQuery($sql);
    }
    protected function getPortList() {
        $sql = "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."ports`
			WHERE 1	ORDER BY `name` ASC";
        return $this->mysqlQuery($sql);
    }
    public function buildList($request,$cols,$list,$module,$item,$limit, $dop='')
    {
        //сортировка
        if($cols[1]['name']=='date' or $cols[1]['name']=='save_date') $sort_dir = 'DESC';
        else $sort_dir = 'ASC';
        $order_list = $this->defineSort('sort_'.$list, '`'.$cols[1]['name'].'` '.$sort_dir); //добавляем сортировку в запрос
        $this->sortDeco('sort_'.$list); //выводим указатель того, что сейчас сортируется и направление сортировки
        $request = $request." ORDER BY ".$order_list.$limit;
        //echo $request; die;
        $content = $this->mysqlQuery($request);

        $num = mysql_num_rows($content);
        $i=1;
        $class="rowA rowB";
        $out.='
		<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
			<tr class="title sortButtons">';
        while($i<=count($cols))
        {
            $out .= $this->sorterTD($module, $cols[$i]['name'], $cols[$i]['caption'], $cols[$i]['width'],$dop);

            $i++;
        }
        $out.='</tr>';
        $i=1;
        while ($i<=$num)
        {
            $line = mysql_fetch_array($content);
            if((!isset($_GET['mod']) or $_GET['mod']=='clients') and ($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4')) {
                $onclick_location = 'document.location=\''.$this->root_path.$this->user_folder.'?mod='.$list.'&sw=detail&id='.$line['id'].$dop.'\'';
            }
            else $onclick_location = 'document.location=\''.$this->root_path.$this->user_folder.'?mod='.$list.'&sw=form&'.$item.'='.$line['id'].$dop.'\'';

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
        if($page>1) $out['print'] .= '<td><a href="'.$this->root_path.$this->user_folder.'?'.$location.'page=1'.($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_to_first.gif" border="0" alt="на первую страницу" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_to_first_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_to_first.gif\'"></a>&nbsp;</td>';

        // предыдущая страница
        if($out['next_page'] > 2) $out['print'] .= '<td>
		<a href="/'.$this->user_folder.'?'.$location.'page='.$out['prev_page'].($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_prev_page.gif" border="0" alt="на предыдущую страницу" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_prev_page_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_prev_page.gif\'"></a>
		&nbsp;&nbsp;</td>';

        // ссылки не ПРЕДЫДУЩИЕ страницы
        $i = 1;
        while($i<=$maxPagesLinks) {

            if($i<$page) {
                $current_page_link = $page-$i;
                $backLinks = '<td><a href="'.$this->root_path.$this->user_folder.'?'.$location.'page='.$current_page_link.($sub_get!=''?$sub_get:'').'"><div id="pagenum">'.$current_page_link.'</div></a></td>'.$backLinks;
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
                $forward_links = $forward_links.'<td><a href="'.$this->root_path.$this->user_folder.'?'.$location.'page='.$next_page.($sub_get!=''?$sub_get:'').'"><div id="pagenum">'.$next_page.'</div></a></td>';
            }
            else break;
            $i++;
        }

        $out['print'] .= $backLinks.'<td><div id="curpage">'.$page.'</div></td>'.$forward_links;


        // следующая страница
        if($page*$this->per_page < $total) $out['print'] .= '<td>&nbsp;&nbsp;<a href="'.$this->root_path.$this->user_folder.'?'.$location.'page='.$out['next_page'].($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_next_page.gif" border="0" alt="'.$this->translate->_('на следующую страницу').'" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_next_page_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_next_page.gif\'"></a></td>';

        // ссылка в конец
        if($page<$total_pages) $out['print'] .= '<td>&nbsp;<a href="'.$this->root_path.$this->user_folder.'?'.$location.'page='.$total_pages.($sub_get!=''?$sub_get:'').'"><img src="/img/ccl/button_to_last.gif" border="0" alt="'.$this->translate->_('на последнюю страницу').'" align="absmiddle" onMouseOver="this.src=\'/img/ccl/button_to_last_hov.gif\'" onMouseOut="this.src=\'/img/ccl/button_to_last.gif\'"></a></td>';

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

    public function sorterTD($module, $item, $caption, $width, $dop='') {

        if(isset($_GET['ref'])) $type = 'ref';
        elseif(isset($_GET['mod'])) $type = 'mod';
        return '<td width="'.$width.'" onclick="document.location=\''.$this->root_path.$this->user_folder.'?'.$type.'='.$module.'&sort='.$item.'&sdir='.$this->sortNow['resort'][$item].$dop.'\'" onMouseOver="this.className=\'sortButtonsHover\'" onMouseOut="this.className=\'\'" style="cursor:pointer;">'.$caption.' '.$this->sortNow['image'][$item].'</td>';
    }

    public function getRootPath() {
        return $this->root_path;
    }

    protected function getNewStakesNumber() {

        $new = mysql_fetch_array($this->mysqlQuery("
		SELECT COUNT(id) as total FROM `ccl_".ACCOUNT_SUFFIX."stakes_list`
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

    public function sMail($to, $text, $subj='', $from = false) {

        static $separators = array(' ', ';', ',', "\n");

        require_once($_SERVER['DOCUMENT_ROOT'].'/lib/phpmailer/class.phpmailer.php');
        require_once($_SERVER['DOCUMENT_ROOT'].'/lib/phpmailer/class.smtp.php');
        require_once($_SERVER['DOCUMENT_ROOT'].'/lib/phpmailer/class.pop3.php');

        $mail = new PHPMailer(false); // the true param means it will throw exceptions on errors, which we need to catch
    //    $mail->IsSMTP(); // telling the class to use SMTP
        $mail->CharSet = 'utf-8';

        try {
            $mail->Host       = "mail.makmalauto.com"; // SMTP server
            //$mail->SMTPDebug  = 2;                     // enables SMTP debug information (for testing)
            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
            $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
            $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
            $mail->Username   = "no-reply@makmalauto.com";  // GMAIL username
            $mail->Password   = "test12345";            // GMAIL password


            foreach ($separators as $separator){
                if (strstr($to, $separator)){
                    $to = explode($separator, $to);
                }
            }

            if (is_string($to)){
                $mail->AddAddress($to);
            }


            if (is_array($to)){
                foreach ($to as $t){
                    if (trim($t)!=''){
                        $mail->AddAddress(trim($t));
                    }

                }
            }


            if ($from){
                $mail->SetFrom($from);
            }
            else{
                $mail->SetFrom('no-reply@makmalauto.com', 'Auto informer');
            }


            if ($subj){
                $mail->Subject = $subj;
            }
            else{
                if (is_object($this->translate)){
                    $mail->Subject = $this->translate->_('Система уведомлений Makmal-Auto');
                }
                else{
                    $mail->Subject = 'Система уведомлений Makmal-Auto';
                }

            }
            $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
            $mail->MsgHTML($text);
            $mail->Send();
            return true;
            //echo "Message Sent OK</p>\n";

        } catch (phpmailerException $e) {
            return false;
            //echo $e->errorMessage(); //Pretty error messages from PHPMailer
        } catch (Exception $e) {
            return false;
            //echo $e->getMessage(); //Boring error messages from anything else!
        }


        /*if ($subj=='') {$subj=$this->translate->_('Система уведомлений Makmal-Auto');}
        $subj = '=?utf-8?B?'.base64_encode($subj).'?=';

        $from = 'Makmal-Auto <info@makmalauto.com>';
        $head      = "From: $from\n";
        $head     .= "Subject: $subj\n";
        $head     .= "X-Mailer: PHPMail Tool\n";
        $head     .= "Reply-To: $from\n";
        $head     .= "Mime-Version: 1.0\n";
        $head	  .= "Content-Type:text/html; charset=utf-8\n";
        $zag = $text;

        return mail("$to", "$subj", $zag, $head);*/


    }

    function send_mail($mail, $module, $value, $template, $subj='')
    {
        $body='';
        $text='';
        if ($text=$this->make_value_for_template($module, $template, $value, ''))
        {
            if ($body=$this->make_mail_teplate($text, ''))
            {
                $this->sMail($mail, $body, $$subj);
            }
        }
    }

    function make_value_for_template($module, $template, $value, $sub='../')
    {
        $text='';
        if (!include("{$sub}mod/$module/templates/$template.php")) return  false;
        foreach ($value as $key=>$value)
        {
            $text=str_replace("||{$key}||", $value, $text);
        }
        return $text;
    }

    function make_mail_teplate($text, $sub='../')
    {
        if (!include("{$sub}template/mail_template.php")) return  false;
        $body=str_replace("||txt_body||", $text, $mail_html);
        return $body;
    }

    public function arrayFromSql($query) {
        if($query!='') {
            $from_sql = $this->mysqlQuery($query);
            $out = array();

            $i = 0;
            while($line = mysql_fetch_array($from_sql)) {
                $keys = array_keys($line);
                foreach($keys as $k=>$v) {
                    if(!is_integer($v)) {
                        $out[$i][$v] = $line[$v];
                    }
                }
                $i++;
            }
            return $out;
        }
        else return false;
    }
}

?>