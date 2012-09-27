<?
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');

if(isset($_GET['mod']) and $_GET['mod']!='') {
	$file = $_SERVER['DOCUMENT_ROOT'].'/adm_expeditors/lib/class.Expeditors'.$_GET['mod'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/adm_expeditors');
	
}
else {
	$default = $_SERVER['DOCUMENT_ROOT'].'/adm_expeditors/lib/class.Expeditorscars.php';
	require_once($default);
}
switch($_GET['mod']) {
	case 'cars':
		$page = new ExpeditorsCars();
		break;
	default:
		$page = new ExpeditorsCars();
		break;
}
if($page) {
	$page->makePage();
}


?>