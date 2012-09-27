<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
$module = 'model';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.modelList.php');

switch(@$_GET['sw']) {
	case 'form':$page = new ModelForm();break;
	case 'save':$page = new ModelSave();break;
	case 'delete':$page = new ModelDelete();break;

	default:$page = new ModelList();break;
}

$page -> drawContent();

?>