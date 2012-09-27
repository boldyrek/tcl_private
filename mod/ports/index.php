<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
$module = 'ports';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.portsList.php');

switch(@$_GET['sw']) {
	case 'form':$page = new PortsForm();break;
	case 'save':$page = new PortsSave();break;
	case 'delete':$page = new PortsDelete();break;

	default:$page = new PortsList();break;
}

$page -> drawContent();

?>