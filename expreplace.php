<?

header('Location: /');
die();

	require_once('inc/baseconf.php');

	echo 'connecting to <b>['.$db_host.']</b><br><br>';
	$conn = mysql_connect($db_host,$db_user,$db_pass) or die ("Error connecting to DataBase!!!");
	mysql_select_db($db_base);
	mysql_query("SET CHARACTER SET 'utf8'");
	
	$rec=array(
		0 => 'other',
		1 => 'price_jp',
		2 => 'aucfee',
		3 => 'inspection',
		4 => 'cost_to_port',
		5 => 'cost_to_destination',
		6 => 'unload',
		19 => 'dealer_comission',
		101 => 'insurance'
	);
	
	// Replacing old expenses
	echo 'Starting replacing <b>Old Expenses</b>:<br>';
	$exps=mysql_query('SELECT * FROM ccl_'.ACCOUNT_SUFFIX.'cars WHERE id < 487');
	while($p=mysql_fetch_assoc($exps))
	{
		foreach($rec as $purpose=>$amount)
		{
			if($purpose>100) $purpose=0;
			if(isset($p[$amount]) and $p[$amount]!='0')
			{
				$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."accounting` (`client`, `amount`, `comment`, `date`, `user_added`, `status`, `car`, `paid`, `signer`, `purpose`, `stuff`, `type`) 
				VALUES ( 
				'".$p['buyer']."', '".intval($p[$amount])."', 'Old-converted (no comments)', '".$p['buy_date']."','175',
				'0', '".intval($p['id'])."', '1', 'auto', '".$purpose."', '0', '2')";
				$e=mysql_query($request);
				if($e) echo '<span style="color:green">*</span> '; else echo '<span style="color:red">*</span> ';
			}
		}
	}

	echo '<br><br><b>Finished!</b><br><br>';
	
	// If error - show it
	echo '<div style="color:#aa0000"><b>';
	if(mysql_errno()) echo 'Error occured!!!<br><br>'.mysql_error();
	echo '</b></div>';
?>