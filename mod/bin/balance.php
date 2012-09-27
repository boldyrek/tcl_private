<?

function updateBalance($id,$dealer)
{
	if($dealer==0) {
		$cars = mysql_fetch_array(mysql_query("SELECT SUM(total) as total, COUNT(id) as cars FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `buyer` = '".intval($id)."'"));
		
		$payments = mysql_fetch_array(mysql_query("SELECT SUM(amount) as total FROM `ccl_".ACCOUNT_SUFFIX."accounting` WHERE `client` = '".$id."' and `status` = '1' and type = '1'"));
		
		$balance = $payments['total']-$cars['total'];
		
		mysql_query("UPDATE `ccl_".ACCOUNT_SUFFIX."customers` SET `balance`='".$balance."' WHERE id='".intval($id)."'");
		
		$myDealer = mysql_fetch_array(mysql_query("SELECT mydealer FROM `ccl_".ACCOUNT_SUFFIX."customers` WHERE `id` = '".mysql_real_escape_string($id)."'"));
		
		if($myDealer['mydealer']!=0) updateBalance($myDealer['mydealer'], 1);

	}
	elseif($dealer==1) {
		$cars = mysql_query("SELECT SUM(total) as total FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `buyer` = '".intval($id)."' or `dealer` = '".intval($id)."'");
		
		$mypayments = mysql_fetch_array(mysql_query("SELECT SUM(amount) as total FROM `ccl_".ACCOUNT_SUFFIX."payments` WHERE `client` = '".intval($id)."' and `status` = '1'"));
		
		$payments = mysql_query("SELECT DISTINCT SUM(ccl_".ACCOUNT_SUFFIX."accounting.amount) as total
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."accounting`
		ON (ccl_".ACCOUNT_SUFFIX."accounting.client = ccl_".ACCOUNT_SUFFIX."customers.id)
		WHERE ccl_".ACCOUNT_SUFFIX."customers.mydealer = '".intval($id)."' and ccl_".ACCOUNT_SUFFIX."accounting.status = '1' and type = '1' ");

		$balance = $payments['total']+$mypayments['total']-$cars['total'];
			
		mysql_query("UPDATE `ccl_".ACCOUNT_SUFFIX."customers` SET `balance`='".$balance."' WHERE id='".intval($id)."'");
	}

}
function updateStuffBalance($id, $count)
{
		$payments = mysql_fetch_array(mysql_query("SELECT SUM(amount) as total FROM `ccl_".ACCOUNT_SUFFIX."accounting` WHERE `client` = '".$id."' and `status` = '1' and  type = '1'"));
		$balance = $payments['total']-$count;
		mysql_query("UPDATE `ccl_".ACCOUNT_SUFFIX."customers` SET `balance`='".$balance."' WHERE id='".intval($id)."'");
}

//подсчет полей стоимость машины и предоплата
function countSums($cars) {

	$num = mysql_num_rows($cars);
	$j=1;
	$delivered = 0; // доставленные автомобили	
	$notdelivered = 0;	// не доставленные автомобили
	$total = 0; // общая стоимость автомобилей
	while($j<=$num)
	{
		$line = mysql_fetch_array($cars);
		if($line['delivered']=='1') {
			$delivered = $delivered+$line['total'];		
		}
		else {
		$prepaid = $prepaid+$line['prepay'];
		}
		$total = $total + $line['total'];
		$j++;
	}
	$out['delivered'] = $delivered;
	$out['prepaid'] = $prepaid;
	$out['total'] = $total;
	return $out;
}


function updateDealerBalance($id, $isdealer) {
	if($isdealer == 1) {
		$mypays = mysql_fetch_array(mysql_query("SELECT SUM(amount) AS total FROM `ccl_".ACCOUNT_SUFFIX."payments` WHERE `client` = '".$id."'"));
	}
	elseif($isdealer  == 0) {
		
	}
}

function updateSupplierBalance($id)
{
	$cars = mysql_fetch_array(mysql_query("SELECT SUM(FOB) as total FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `transporter`='".mysql_real_escape_string($id)."'"));
	$services = mysql_fetch_array(mysql_query("SELECT SUM(sum) as total FROM `ccl_".ACCOUNT_SUFFIX."sup_serv` WHERE `from`='".mysql_real_escape_string($id)."'"));
	$paid = mysql_fetch_array(mysql_query("SELECT SUM(sum) as total FROM `ccl_".ACCOUNT_SUFFIX."sup_pay` WHERE `to`='".mysql_real_escape_string($id)."'"));

	$balance = $paid['total']-$cars['total']-$services['total'];
	$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."transporters` SET `balance`='".$balance."' WHERE id='".mysql_real_escape_string($id)."'";
	mysql_query($request);
}


function updateTransporterBalance($id)
{
	$pays = mysql_fetch_array(mysql_query("SELECT SUM(sum) as total from `ccl_".ACCOUNT_SUFFIX."sup_pay` WHERE `to`='".$id."'"));
	$services = mysql_fetch_array(mysql_query("SELECT SUM(sum) as total from `ccl_".ACCOUNT_SUFFIX."sup_serv` WHERE `from`='".$id."'"));
//	$cars = mysql_fetch_array(mysql_query("SELECT SUM(cost_to_port) as total from `ccl_".ACCOUNT_SUFFIX."cars` WHERE `transporter`='".$id."'"));
	
	$cars = mysql_fetch_array(mysql_query("SELECT SUM(ccl_".ACCOUNT_SUFFIX."accounting.amount) as total FROM ccl_".ACCOUNT_SUFFIX."accounting,ccl_".ACCOUNT_SUFFIX."cars
		WHERE ccl_".ACCOUNT_SUFFIX."accounting.car = ccl_".ACCOUNT_SUFFIX."cars.id
		AND ccl_".ACCOUNT_SUFFIX."cars.transporter = '".$id."'
		AND ccl_".ACCOUNT_SUFFIX."accounting.purpose = 4"));
	$balance = $cars['total']+$services['total'] - $pays['total'];
	$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."transporters` SET `balance` = '".$balance."' WHERE `id` = '".$id."'";

	mysql_query($request);
}

function updateExpeditorBalance($id)
{
	$containers = mysql_fetch_array(mysql_query("SELECT SUM(price) as total from `ccl_".ACCOUNT_SUFFIX."containers` WHERE `expeditor`='".$id."'"));
	$services = mysql_fetch_array(mysql_query("SELECT SUM(sum) as total from `ccl_".ACCOUNT_SUFFIX."exp_serv` WHERE `from`='".$id."'"));
	$paid = mysql_fetch_array(mysql_query("SELECT SUM(sum) as total from `ccl_".ACCOUNT_SUFFIX."exp_pay` WHERE `to`='".$id."'"));
	
	$balance = $paid['total']-$containers['total']-$services['total'];
	
	mysql_query("UPDATE `ccl_".ACCOUNT_SUFFIX."expeditors` SET `balance`='".$balance."' WHERE id='".$id."'");
}

function updateContainerBalance($cars)
{
	$request = "SELECT buyer FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE ";
	foreach ($cars as $k => $v) {
		if($v!='' and $v!='0') $add_cars .= "`id` = '".$car[$j]."' OR ";
	}
	
	if($add_cars != '')
	{
		$cars = mysql_query($request.rtrim($add_cars, ' OR '));
		$num = mysql_num_rows($cars);
		$j=1;
		while($j<=$num)
		{
			$line = mysql_fetch_array($cars);
			updateBalance($line['buyer'],0);
			$j++;
		}
	}
}


?>