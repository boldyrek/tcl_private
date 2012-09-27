<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']!='1') header('Location: /');
$module = 'profile';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.profileform.php');

switch(@$_GET['sw']) {
	case 'form':$page = new ProfileForm();break;
	case 'save':$page = new ProfileSave();break;

	default:$page = new ProfileForm();break;
}

$page -> drawContent();

?>