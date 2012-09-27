<?
if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.Cars'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.CarsList.php');

switch($_GET['sw']) {
	case 'form':
		$page = new CarsForm();
		break;
	case 'save':
		$page = new CarsSave();
		break;
	case 'add':
		$page = new CarsAdd();
		break;	
	case 'delete':
		$page = new CarsDelete();
		break;	
	case 'sell':
		$page = new CarsSell();
		break;
	case 'forsale':
		$page = new CarsForSale();
		break;
	case 'comment':
		$page = new CarsComment();
		break;	
	case 'settopphoto':
		$page = new setTopPhoto();
		break;		
	case 'allphotos':
		$page = new Carsallphotos();
		break;
	default:
		$page = new CarsList();
		break;
}

$page -> drawContent();

?>