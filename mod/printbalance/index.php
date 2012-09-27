<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');

require_once($_SERVER['DOCUMENT_ROOT'].'/mod/printbalance/class.PrintBalance.php');

$page = new PrintBalance();

$page -> drawContent();

?>