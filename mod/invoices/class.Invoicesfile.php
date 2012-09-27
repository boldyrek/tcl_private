<?
class InvoicesFile extends Proto {
	var $inv_id;
	var $info;
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}

	private	function Process() {
		// файл инвойса
		$this->setInv_id();
		$this->getInfo();
		$this->getServ();
		$this->checkAuthor();
		if(ACCOUNT_ID == '0')
			require_once("templates/printInvoice.php");
		else
			require_once("templates/printInvoice_all.php");
	}

	private function checkAuthor()
	{
		if ($_SESSION['user_type']!='1' and $_SESSION['user_type']!='7')
		{
			if ($this->info['client']!=$_SESSION['user_id'] || !$this->info['access']) die ("not allowed");
			return ;
		}

	}
	private function setInv_id(){
		if (!isset($_REQUEST['inv_id']))
		die("incorrect link");
		else $this->inv_id=intval($_REQUEST['inv_id']);
	}
	private function getInfo()
	{
		$sql="SELECT ccl_".ACCOUNT_SUFFIX."invoices.*, ccl_".ACCOUNT_SUFFIX."customers.name_en as name from `ccl_".ACCOUNT_SUFFIX."invoices` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers`
		ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."invoices.client)
		WHERE ccl_".ACCOUNT_SUFFIX."invoices.id='".$this->inv_id."' LIMIT 1";
		
		if ($res=$this->mysqlQuery($sql)){
			$tmp=mysql_fetch_assoc($res);
			if (!empty($tmp))
			{
				$this->info=$tmp;
				return;
			}
		}
		die("Invoice was deleted");

	}
	private function getServ()
	{
		$sql="SELECT t1.*, t2.`item` from `ccl_".ACCOUNT_SUFFIX."invoices_services` as t1 LEFT JOIN  `ccl_".ACCOUNT_SUFFIX."services` as t2 ON (t1.item_id=t2.id) WHERE `invoice_id`='".$this->inv_id."' ORDER BY t1.num";
		
		if ($res=$this->mysqlQuery($sql))
		while ($tmp=mysql_fetch_assoc($res))
			$this->info['serv_list'][]=$tmp;
		
	}
	private function getBuyer($car_id)
	{
		$sql="SELECT car.buyer as `buyer`, vl.name as `name` FROM ccl_".ACCOUNT_SUFFIX."cars as car, ccl_".ACCOUNT_SUFFIX."customers as vl  WHERE car.buyer=vl.id and car.id={$car_id}";
		if ($res=mysql_query($sql))
		$info=mysql_fetch_assoc($res);
		return $info;
	}
}
?>