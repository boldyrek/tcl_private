<?
//
//
//
//	Ставки сделаны, ставок больше нет!
//
//
//

require_once('class.Stakes.php');

if(!isset($_GET['sw']) or $_GET['sw']!='archive') {
	$page = new Stakes();
	$page -> drawContent();
}
elseif($_GET['sw']=='archive') {
	require_once('class.stakesArchive.php');
	$page = new stakesArchive();
	$page -> drawArchive();
}

?>