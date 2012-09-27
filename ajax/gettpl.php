<?
header("content-type: text/html; charset=windows-1251");
session_start();
if ($_SESSION['user_type']!='1' and $_SESSION['user_type']!='7')
	die ("Not allow");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');

$link = mysql_connect($DBvars['host'], $DBvars['user'], $DBvars['pass']);
if (!$link) {die('Could not connect: ' . mysql_error());}
$db=mysql_select_db($DBvars['name'], $link);

$id = (isset($_GET['id'])?intval($_GET['id']):0);

if (isset($_GET['id']) and intval($_GET['id'])!=0)
{
	$id=intval($_GET['id']);
	mysql_query('SET NAMES cp1251');
	$sql="SELECT id, txt FROM `ccl_".ACCOUNT_SUFFIX."tpl` WHERE id='{$id}'";
	$res = mysql_query($sql);
	if ($res && mysql_num_rows($res)>0)
	{
		list($aid, $txt) = mysql_fetch_array($res);	
		echo $txt;
	}
}
mysql_close($link);

?>