<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');

if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/view/mod/containers/class.Containers'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/view/mod/containers/class.ContainersList.php');

switch($_GET['sw']) {
	case 'form':$page = new ContainersForm();break;
	case 'save':$page = new ContainersSave();break;
	case 'add':$page = new ContainersAdd();break;
	case 'delete':$page = new ContainersDelete();break;
	case 'cprint':$page = new ContainersEdit();break;
	case 'print':$page = new ContainersPrint();break;
		
	default:
		$page = new ContainersList();
		break;
}

$page -> drawContent();

?>