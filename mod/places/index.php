<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
$module = 'places';

if(@Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.'.$module.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/'.$module.'/class.placesList.php');

switch(@$_GET['sw']) {
	case 'form':$page = new PlacesForm();break;
	case 'save':$page = new PlacesSave();break;
	case 'delete':$page = new PlacesDelete();break;

	default:$page = new PlacesList();break;
}

$page -> drawContent();

?>