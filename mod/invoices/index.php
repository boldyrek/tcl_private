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

$module = 'invoices';

if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.Invoices'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.InvoicesList.php');

switch($_GET['sw']) {
	case 'form':
		CheckAdmin();
		$page = new InvoicesForm();
		break;
	case 'file':
		$page = new InvoicesFile();
		break;
	case 'mail':
		$page = new InvoicesMail();
		break;
	case 'save':
		CheckAdmin();
		$page = new InvoicesSave();
		break;
	case 'delete':
		CheckAdmin();
		$page = new InvoicesDelete();
		break;
	case 'carsforclient':
		$page = new carsForCustomer();
		break;
	
	default:$page = new InvoicesList();break;
}

function CheckAdmin()
{
	//print_r ($_SESSION);
	if ($_SESSION['user_type']!='1' and $_SESSION['user_type']!='7')
	die ("Not allow");
}

$page -> drawContent();

?>