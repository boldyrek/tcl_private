<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');

if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/client_cars/class.ClientCars'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/client_cars/class.ClientCarsList.php');

switch($_GET['sw']) {
	
	default:
		$page = new ClientCarsList();
		break;
}

$page -> drawContent();

?>