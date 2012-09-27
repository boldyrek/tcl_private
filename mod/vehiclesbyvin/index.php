<?
//error_reporting(0);
header("Expires: Tue, 1 Jul 2003 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
session_start();
if(isset($_GET['mod'])) $mod = $_GET['mod'];
else $mod = '';

if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='8') header('Location: /adm_transporters');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='9') header('Location: /adm_expeditors');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='10') header('Location: /view'); 

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');
if(isset($_SESSION['authorised'])) {
	if(Proto::exists($mod)) {
		$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$mod.'/index.php';
		if(file_exists($file)) require_once($file);
		else die('пїЅпїЅпїЅпїЅ '.$file.' пїЅпїЅ пїЅпїЅпїЅпїЅпїЅпїЅ!');
		
	}
	elseif($_SESSION['authorised']) {
		require_once($_SERVER['DOCUMENT_ROOT'].'/mod/clients/index.php');
	}
}
else {
	require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.starter.php');
	$authoriser = new starter();
	$authoriser->drawContent();
}
?>