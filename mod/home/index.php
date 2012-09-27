<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
//if(isset($_SESSION['user_type']) and $_SESSION['user_type']!='1') header('Location: /');

$module = 'home';
$tmodule = 'Home';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$tmodule.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.HomeMain.php');

switch(@$_GET['sw']) {
	case 'lastcomments':$page = new LastComments();break;

	default: $page = new HomeMain();break;
}

$page -> drawContent();

?>