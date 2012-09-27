<?
/*
	Индексная страница раздела клиентов.
	Ниже выполняется проверка прав доступа пользователя к данному разделу.
	
	Проверка выполняется не по разграниченным правилам из файла lib/access.php,
	а просто путём проверки типа залогиненного пользователя.
	
	Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');
if($_SESSION['user_type']=='6') header('Location: /?mod=con_reports');

// Проверка подраздела и подключение соответствующего файла
if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/clients/class.Clients'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/clients/class.ClientsList.php');

// Проверка подраздела и создание соответствующего класса
switch($_GET['sw']) {
	case 'form':$page = new ClientsForm();break;
	case 'detail':$page = new ClientsDetail();break;
	case 'save':$page = new ClientsSave();break;
	case 'add':$page = new ClientsAdd();break;
	case 'delete':$page = new ClientsDelete();break;
	case 'recalcbalance':$page = new ClientsRecalcBalance();break;
		
	default:
		$page = new ClientsList();
		break;
}

// Вызов главного оперирующего метода нового класса
$page -> drawContent();

?>