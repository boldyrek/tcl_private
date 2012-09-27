<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
$module = 'marka';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.markaList.php');

switch(@$_GET['sw']) {
	case 'form':$page = new MarkaForm();break;
	case 'save':$page = new MarkaSave();break;
	case 'delete':$page = new MarkaDelete();break;

	default:$page = new MarkaList();break;
}

$page -> drawContent();

?>