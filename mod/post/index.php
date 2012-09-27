<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
$module = 'post';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.postList.php');

switch(@$_GET['sw']) {
	case 'form':$page = new PostForm();break;
	case 'save':$page = new PostSave();break;
	case 'delete':$page = new PostDelete();break;

	default:$page = new PostList();break;
}

$page -> drawContent();

?>