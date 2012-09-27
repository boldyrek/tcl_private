<?
session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');

$mod = '';
if(isset($_GET['mod'])) $mod = strtolower($_GET['mod']);

if($mod!='') {
	$file = $_SERVER['DOCUMENT_ROOT'].'/view/mod/'.$mod.'/index.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/view');
	
}
else {
	require_once($_SERVER['DOCUMENT_ROOT'].'/view/mod/clients/index.php');
}


?>