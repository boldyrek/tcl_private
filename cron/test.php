<?
include($_SERVER['DOCUMENT_ROOT'].'/inc/baseconf.php');
$h=mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbbase);


$q='SELECT * FROM `ccl_cars` WHERE `id` > 1000';
$r=mysql_query($q);
echo mysql_num_rows($r);
?>