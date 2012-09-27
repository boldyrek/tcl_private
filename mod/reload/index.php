<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
$module = 'reload';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.reloadList.php');

switch(@$_GET['sw']) {
	case 'form':$page = new ReloadForm();break;
	case 'save':$page = new ReloadSave();break;
	case 'delete':$page = new ReloadDelete();break;

	default:$page = new ReloadList();break;
}

$page -> drawContent();

?>