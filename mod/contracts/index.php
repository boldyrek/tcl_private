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

$module = 'contracts';

if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.Contracts'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.ContractsList.php');


switch($_GET['sw']) {
	case 'form':$page = new ContractsForm();break;
	case 'show':$page = new ContractsShow();break;
	case 'act':$page = new ContractsAct();break;
	
	case 'save':$page = new ContractsSave();break;
	case 'delete':$page = new ContractsDelete();break;
	
	case 'cars':$page = new ContractsCars();break;
	
	default:$page = new ContractsList();break;
}

$page -> drawContent();

?>