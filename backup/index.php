<?
session_start();
//if (
//    isset($_SESSION["user_type"]) && 
//    isset($_SERVER["REDIRECT_QUERY_STRING"]) &&
//    is_numeric($_SERVER["REDIRECT_QUERY_STRING"]) &&
//    ($_SESSION["user_type"]==1 || $_SESSION["user_type"]==7))
if (
    isset($_SESSION["user_type"]) && 
    isset($_SERVER["QUERY_STRING"]) &&
    is_numeric($_SERVER["QUERY_STRING"]) &&
    ($_SESSION["user_type"]==1 || $_SESSION["user_type"]==7))
{
    if (is_file("./".$_SERVER["QUERY_STRING"].".sql"))
	$F="./".trim($_SERVER["QUERY_STRING"]).".sql";
    elseif (is_file("./".trim($_SERVER["QUERY_STRING"]).".auto.sql"))
	$F="./".$_SERVER["QUERY_STRING"].".auto.sql";

    header('Content-type: text/plain');
    header('Content-Disposition: attachment; filename="backup_'.date("Ymd-His",$_SERVER["QUERY_STRING"]).'.sql"');
    if(@$F) { readfile("./".$F); exit; }
}
print "%)";
?>