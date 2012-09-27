<?

// база данных

	$DBvars['host'] = 'localhost';
	$DBvars['name'] = 'boldyrek_db1';
	$DBvars['user'] = 'root';
	$DBvars['pass'] = 'ieSeTiengae7Sh';


//работа с FTP
	
	$ftpserver = '88.214.207.23';
	//$ftpserver = '88.214.192.132';
	$ftpuser = 'boldyrek_ftp0';
	$ftppass = '3clb1Ffd';

$root_path = '/';

$user_identificator = md5($_SERVER['REMOTE_ADDR'].'$'.$_SERVER['HTTP_USER_AGENT'].'$'.$_SERVER['SERVER_NAME'].'$'.gethostbyaddr($_SERVER['REMOTE_ADDR']));

$debug_mode = false;
$track_queries = true;
$show_execution_time = false;

$current['main_templates'] = $_SERVER['DOCUMENT_ROOT'].'/lib/main_templates.php';

$user_folders['1'] = '';
$user_folders['2'] = 'public';
$user_folders['3'] = '';
$user_folders['4'] = '';
$user_folders['5'] = '';
$user_folders['7'] = '';
$user_folders['10'] = 'view/';
$user_folders['11'] = 'public';

//типы пользователей
$user_types[1] = 'Administrator';	//Администратор';
$user_types[2] = 'Client / Dealer';	//Клиент / Дилер';
$user_types[3] = 'Operator';	//Оператор';
$user_types[4] = 'Manager';	//Менеджер';
$user_types[5] = 'Supplier';	//Поставщик';
$user_types[6] = 'Reports';	//'Отчеты';
$user_types[8] = 'Transporter';	//Транспортник';
$user_types[9] = 'Expeditor';	//'Экспедитор';
$user_types[10] = 'Broker';	//'Брокер';
$user_types[11] = 'Yamato base Admin';	//'Администратор базы Ямато';

// папки для фотографий

$photo_folders[1] = 'Pics at the place of purchase';	//при ПОКУПКЕ
$photo_folders[2] = 'Pics before loading into container';	//при ПОГРУЗКЕ

set_include_path($_SERVER['DOCUMENT_ROOT'] . PATH_SEPARATOR . $_SERVER['DOCUMENT_ROOT'].'/Zend/');

?>
