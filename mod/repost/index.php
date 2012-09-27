<?

require($_SERVER['DOCUMENT_ROOT'].'/diesel/class.rePost.php');
$obj = new rePost();
$obj->setId($_GET['sale'], $_GET['parent']);
$obj->process($_GET['type']);

?>