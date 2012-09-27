<?
if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/fileshare/class.Fileshare'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/fileshare/class.FileshareList.php');

switch($_GET['sw']) {

	case 'add':
		$page = new FileshareAdd();
		break;
	default:
		$page = new FileshareList();
		break;
}

$page -> drawContent();

?>