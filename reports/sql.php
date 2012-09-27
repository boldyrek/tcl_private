<?
	$conn=mysql_connect(localhost,$db_user,$db_pass) or die ("Нет соединения с базой");
	mysql_select_db($db,$conn);
	if(isset($_POST['to_date'])) {
	
	$to_date = $_POST['to_date'];
	
	if(isset($_POST['from_date'])) {
		$from_date = $_POST['from_date'];
	}
	if($_POST['supplier']!='0') $sup = $_POST['supplier'];
	else $sup = '';
	
	if(isset($_POST['arrived'])) {
		switch($_POST['arrived']) {
		case 0:
		$addon = "";
		break;
		case 1:
		$addon = " and `delivered` = '1'";
		break;
		case 2:
		$addon = " and `delivered` = '0'";
		}
	}
	
	$query_result = array();
	
	$condition = ltrim(($sup!=""?"`supplier` = '".$sup."'":"").
		($from_date!=""?" and `buy_date`>='".$from_date."'":"").
		" and `buy_date` <= '".$to_date."'", " and");
		
		$main_request = "SELECT id,model,frame,buy_date 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		WHERE ".$condition.$addon."
		ORDER BY `buy_date` ASC";
	
	$query_result = mysql_query($main_request);
		
	}
	
	function getSuppliers() {
		$list = mysql_query("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."suppliers` WHERE 1");
		return $list;
	}

?>