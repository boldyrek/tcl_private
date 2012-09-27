<?

class carsForCustomer extends Proto {
	var $customer;
	
	function drawContent() {
		if($this->checkAuth()) {
			if(intval($_GET['client'])!='' and intval($_GET['client'])!=0) $this->getCarSelect();
		}
		
	}
	
	function getCarsForCustomer() {
		
/*		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars`
		WHERE `buyer` = '".$this->customer."'
		OR `reciever` = '".$this->customer."' ORDER BY `model` ASC";
*/
/*		:) fun queries
		SELECT * FROM `ccl_cars` AS `car` INNER JOIN `ccl_invoices` AS `inv` ON car.id=inv.carid ORDER BY car.id DESC LIMIT 10;
		SELECT * FROM TABLE1 WHERE TABLE1.NAME = (SELECT TABLE2.NAME FROM TABLE2 WHERE TABLE2.ID = MyID)
		SELECT * FROM `ccl_cars` AS `car` WHERE car.id <> (SELECT `ccl_invoices`.carid FROM `ccl_invoices` WHERE `ccl_invoices`.carid = 0);
*/
		$tmp[] = array();
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` ORDER BY `id` DESC";
		$q1=mysql_query($sql);
		while($r1=mysql_fetch_assoc($q1))
		{
			$sql2 = mysql_query("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."invoices` WHERE `carid` = ".$r1['id']);
			if(mysql_num_rows($sql2)==0) $tmp[] = $r1;//array_push($tmp,$r1);
		}
/*		$out = $this->mysqlQuery($sql);
		while($tmp=mysql_fetch_assoc($out))*/
		$arr[]=$tmp;
		return $arr;
	}
	
	function getCarSelect()
	{
		header("Content-type: text/html; charset=utf-8");
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` ORDER BY `id` DESC";
		$q1=mysql_query($sql);
		$text= "<select name='carId' id='carId' style=width:auto>";
		$text.="	<option value=\"0\">".$this->translate('Выберите машину')." </option>\n";
		while($r1=mysql_fetch_assoc($q1))
		{
			$sql2 = mysql_query("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."invoices` WHERE `carid` = ".$r1['id']);
			if(mysql_num_rows($sql2)==0)
			$text.='<option value="'.$r1['id'].'" '.($r1['id']==$car_id?'selected="selected"':'').">[ ".$r1['model']." ] -   ".$r1['frame']."</option>\n";
		}
		$text.="</select>";
//		echo iconv('WINDOWS-1251', 'UTF-8', $text);
		echo $text;
	}
/*
	function getCarSelect()
	{
		$this->customer = intval($_GET['client']);
		$cars = $this->getCarsForCustomer();
		if(count($cars)>0) {
			$text= "<select name='carId' id='carId' style=width:auto>";
			$text.="<option value=0>Выберите машину</option>";
			foreach ($cars as $num=>$arr)
			$text.="<option value='".$arr['id']."' ".($arr['id']==$car_id?"selected":"").">".$arr['model']." - ".$arr['frame']."</option>\n";
			$text.="</select>";
		}
		else $text = 'у этого клиента нет автомобилей';
		echo iconv('WINDOWS-1251', 'UTF-8', $text);
	}	
*/
}

?>