<?
mb_internal_encoding("UTF-8");
header("Expires: Tue, 1 Jul 2003 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: text/html; charset=utf-8");
session_start();

// Registering Zend_Registry
require_once($_SERVER['DOCUMENT_ROOT'].'/Zend/Registry.php');

if(isset($_GET['mod'])) $mod = $_GET['mod'];
else $mod = '';
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');

// custom access for client: upload & delete photos
if (isset($_SESSION['user_type']) and $_SESSION['user_type']=='2')
{
   $customAccessAllowed = ($mod == 'multiupload' || ($mod == 'cars' && $_GET['sw'] == 'delete' && $_GET['what'] == 'photo'));

   if (! $customAccessAllowed)
   {
      header('Location: /public');
   }
}

if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='8') header('Location: /adm_transporters');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='9') header('Location: /adm_expeditors');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='10') header('Location: /view');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');
if(isset($_SESSION['authorised'])) {
	if(Proto::exists($mod)) {
		$file = $_SERVER['DOCUMENT_ROOT'].'/mod/'.$mod.'/index.php';
		if(file_exists($file)) require_once($file);
		else die('Coudn\'t find file: '.$file.' !');

	}
        elseif($_SESSION['authorised']) {
		require_once($_SERVER['DOCUMENT_ROOT'].'/mod/home/index.php');
	}
}
else {
	require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.starter.php');
	$authoriser = new starter();
	$authoriser->drawContent();
}
?>