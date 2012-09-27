<?

require($_SERVER['DOCUMENT_ROOT'].'/bb.ct.kz/class.rePost.php');
$obj = new rePost();
$obj->setId($_GET['sale'], $_GET['parent']);
$obj->setPrice($_GET['price']);
$obj->process($_GET['type']);

?>