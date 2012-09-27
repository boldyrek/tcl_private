<?php
header('Location: /');
die();

require_once('inc/baseconf.php');

define('ACCOUNT_SUFFIX', '');

echo 'connecting to <b>['.$db_host.']</b><br><br>';
$conn = mysql_connect($db_host,$db_user,$db_pass) or die ("Error connecting to DataBase!!!");
mysql_select_db($db_base);
mysql_query("SET CHARACTER SET 'utf8'");

$carq = mysql_query("SELECT id FROM ccl_".ACCOUNT_SUFFIX."cars WHERE created > '2009-07-01'");
while($r = mysql_fetch_assoc($carq)){
	$id = $r['id'];
	echo "<span style='color:blue'>id: $id ... </span>";
	$full_pays = mysql_fetch_assoc(mysql_query("
		SELECT SUM(amount) as 'total_paid'
		FROM ccl_".ACCOUNT_SUFFIX."accounting
		WHERE type = 1 AND car = ".intval($id)));
	if(mysql_errno ()) echo mysql_error ();
	$total_paid = isset($full_pays['total_paid']) ? $full_pays['total_paid'] : '0';
	$update_payments = mysql_query("UPDATE ccl_".ACCOUNT_SUFFIX."cars SET paid_total = ".$total_paid." WHERE id='".intval($id)."' LIMIT 1");
	if(mysql_errno())
		echo "<span style='color:red'>error!</span> <br>\n<span style='color:orange'>".  mysql_error() ."</span><br>\n";
	else
		echo "<span style='color:green'>done!</span> <br>\n";
	flush();
}
?>