<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
$module = 'services';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.ServicesList.php');

switch(@$_GET['sw']) {
	case 'form':$page = new ServicesForm();break;
	case 'save':$page = new ServicesSave();break;
	case 'delete':$page = new ServicesDelete();break;

	default:$page = new ServicesList();break;
}

$page -> drawContent();

?>