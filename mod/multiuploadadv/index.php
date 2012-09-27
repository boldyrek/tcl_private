<?
set_time_limit(300);
require_once('class.multiupload.php');
$page = new multiUpload();
$page->getFileList();

?>