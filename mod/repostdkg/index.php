<?

require($_SERVER['DOCUMENT_ROOT'].'/dieselkg/class.rePost.php');
$obj = new rePost();
$obj->setId($_GET['sale'], $_GET['parent']);
$obj->process($_GET['type']);

?>