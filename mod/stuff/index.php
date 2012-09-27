<?
/*
	Индексная страница раздела клиентов.
	Ниже выполняется проверка прав доступа пользователя к данному разделу.
	
	Проверка выполняется не по разграниченным правилам из файла lib/access.php,
	а просто путём проверки типа залогиненного пользователя.
	
	Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/

if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');

if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/stuff/class.Stuff'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/stuff/class.StuffList.php');

switch($_GET['sw']) {
	case 'form':
		$page = new StuffForm();
		break;
	case 'save':
		$page = new StuffSave();
		break;
	case 'add':
		$page = new StuffAdd();
		break;	
	case 'delete':
		$page = new StuffDelete();
		break;	
	case 'sell':
		$page = new StuffSell();
		break;
	case 'forsale':
		$page = new StuffForSale();
		break;
	case 'comment':
		$page = new StuffComment();
		break;	
	case 'settopphoto':
		$page = new setTopPhoto();
		break;		
	case 'allphotos':
		$page = new Stuffallphotos();
		break;
	default:
		$page = new StuffList();
		break;
}

$page -> drawContent();

?>