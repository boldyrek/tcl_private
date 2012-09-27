<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');

if(isset($_GET['sw'])) $switch = $_GET['sw'];
else $switch = '';

if(Proto::exists($switch)) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/view/mod/clients/class.Clients'.$switch.'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/view/mod/clients/class.ClientsList.php');

switch($switch) {
	case 'form':$page = new ClientsForm();break;
	case 'detail':$page = new ClientsDetail();break;
	case 'save':$page = new ClientsSave();break;
	case 'add':$page = new ClientsAdd();break;
	case 'delete':$page = new ClientsDelete();break;
		
	default:
		$page = new ClientsList();
		break;
}

$page -> drawContent();

?>