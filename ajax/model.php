<?php
header("content-type: text/html; charset=windows-1251");
session_start();
if ($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='6')
{
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');

$link = mysql_connect($DBvars['host'], $DBvars['user'], $DBvars['pass']);
if (!$link) {die('error: db' . mysql_error());}
$db=mysql_select_db($DBvars['name'], $link);
if (!$db) {die('error: table' . mysql_error());}

// init

$marka_id = (isset($_GET['marka_id'])?intval($_GET['marka_id']):die('error: no id'));

$res = mysql_query("SELECT * FROM ccl_".ACCOUNT_SUFFIX."model WHERE marka_id='{$marka_id}' ORDER BY name");
if ($res && mysql_num_rows($res)>0)
{
	$buff = '';
	while ($arr = mysql_fetch_assoc($res))
	{
		$buff .= "<option value='{$arr['id']}'>{$arr['name']}</option>";
	}
	echo $buff;
} else {echo '<option value=0>не выбрано</option>';}

}
else die ("Not allow");
?>