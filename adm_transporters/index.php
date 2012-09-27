<?
session_start();

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');
if(isset($_GET['mod']) and $_GET['mod']!='') {
	$file = $_SERVER['DOCUMENT_ROOT'].'/adm_transporters/lib/class.Transporters'.$_GET['mod'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/adm_transporters');
	
}
else {
	$default = $_SERVER['DOCUMENT_ROOT'].'/adm_transporters/lib/class.Transporterscars.php';
	require_once($default);
}
switch($_GET['mod']) {
	case 'cars':
		$page = new TransportersCars();
		break;
	default:
		$page = new TransportersCars();
		break;
}
if($page) {
	$page->makePage();
}


?>