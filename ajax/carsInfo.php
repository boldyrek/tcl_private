<?
header("content-type: text/html; charset=utf-8");
session_start();
if ($_SESSION['user_type']!='1' and $_SESSION['user_type']!='7')
die ("Not allow");
require_once($_SERVER['DOCUMENT_ROOT'].'/inc/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');
$link = mysql_connect($DBvars['host'], $DBvars['user'], $DBvars['pass']);
if (!$link) {die('Could not connect: ' . mysql_error());}
$db=mysql_select_db($DBvars['name'], $link);
$info='';
if (isset($_GET['id']) and intval($_GET['id'])!=0)
{
	$id=intval($_GET['id']);
	mysql_query('SET NAMES utf8');
	$sql="SELECT car.year, car.price_jp, car.buy_date, vl.name, vl.id FROM ccl_".ACCOUNT_SUFFIX."cars as car, ccl_".ACCOUNT_SUFFIX."customers as vl  WHERE car.buyer=vl.id and car.id={$id}";
	if ($res2=mysql_query($sql))
	{
		list($year, $price, $date, $name, $uid)=mysql_fetch_array($res2);
		$info.="Purchaser: $name <a href='/?mod=clients&sw=detail&id=$uid'><img src='/img/ccl/more_info.gif' border=0 align='absmiddle'></a><br>";
		$info.="Year: $year<br>";
		$info.="Price: $price<br>";
		$info.="Date of purchase: $date<br>";
	}
}
echo $info;
mysql_close($link);


?>