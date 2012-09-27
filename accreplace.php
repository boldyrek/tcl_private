<?

header('Location: /');
die();

/*Need reengenering to multy accounts!*/	
require_once('inc/baseconf.php');
/*
$db_host='localhost';
$db_base='boldyrek_db0';
$db_user='root';
$db_pass='root';
*/
	echo 'connecting to <b>['.$db_host.']</b><br><br>';
	$conn = mysql_connect($db_host,$db_user,$db_pass) or die ("Error connecting to DataBase!!!");
	mysql_select_db('boldyrek_db1');
	mysql_query("SET CHARACTER SET 'cp1251'");
	
	echo 'Truncating (Clearing) accounting table!<br><br>';
	mysql_query('TRUNCATE ccl_accounting;');
	
	// Replacing payments
	echo 'Starting replacing <b>Payments</b>:<br>';
	$pays=mysql_query('SELECT * FROM ccl_payments');
	while($p=mysql_fetch_assoc($pays))
	{
		$request = "INSERT INTO `ccl_accounting` (`client`, `amount`, `comment`, `date`, `user_added`, `status`, `last_edited`, `car`, `paid`, `signer`, `purpose`, `stuff`, `type`) 
		VALUES ( 
		'".$p['client']."', 
		'".intval($p['amount'])."', 
		'".$p['comment']."', 
		'".$p['date']."',
		'".intval($p['user_added'])."',
		'".$p['status']."',
		'".$p['last_edited']."',
		'".intval($p['car'])."',
		'1',
		'auto"./*$p['signer'].*/"',
		'0',
		'0',
		'1'
		)";
		$e=mysql_query($request);
		if($e) echo '<span style="color:green">*</span> '; else echo '<span style="color:green">*</span> ';
	}

	// Replacing expenses
	echo '<br><br>Starting replacing <b>Expenses</b>:<br>';
	$pays=mysql_query('SELECT * FROM ccl_expenses');
	while($p=mysql_fetch_assoc($pays))
	{
		$request = "INSERT INTO `ccl_accounting` (`client`, `amount`, `comment`, `date`, `user_added`, `status`, `last_edited`, `car`, `paid`, `signer`, `purpose`, `stuff`, `type`) 
		VALUES ( 
		'".$p['client']."', 
		'".intval($p['amount'])."', 
		'".$p['comment']."', 
		'".$p['date']."',
		'".intval($p['user_added'])."',
		'".$p['status']."',
		'".$p['last_edited']."',
		'".intval($p['car'])."',
		'".$p['paid']."',
		'".$p['signer']."',
		'".intval($p['purpose'])."',
		'0',
		'2'
		)";
		$e=mysql_query($request);
		if($e) echo '<span style="color:green">*</span> '; else echo '<span style="color:green">*</span> ';
	}
	
	echo '<br><br><b>Finished!</b><br><br>';
	
	// If error - show it
	echo '<div style="color:#aa0000"><b>';
	if(mysql_errno()) echo 'Error occured!!!<br><br>'.mysql_error();
	echo '</b></div>';
?>