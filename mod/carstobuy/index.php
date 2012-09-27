<?
/*
	Индексная страница раздела клиентов.
	Ниже выполняется проверка прав доступа пользователя к данному разделу.
	
	Проверка выполняется не по разграниченным правилам из файла lib/access.php,
	а просто путём проверки типа залогиненного пользователя.
	
	Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/

if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');

if(isset($_GET['sw'])) $switch = $_GET['sw'];
else $switch = '';

if(Proto::exists($switch)) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/carstobuy/class.carsToBuy'.$switch.'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/carstobuy/class.carsToBuylist.php');

switch($switch) {
	case 'form':
		$page = new carsToBuyform();
		break;
	case 'save':
		$page = new carsToBuysave();
		break;
	case 'add':
		$page = new carsToBuyadd();
		break;	
	case 'delete':
		$page = new carsToBuydelete();
		break;
        case 'checkvin':
		$page = new carsToBuycheckvin;
		break;
			
	default:
		$page = new carsToBuyList();
		break;
}

$page -> drawContent();

?>