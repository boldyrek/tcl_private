<?

class Sign extends Proto {
	
	private $totals;
	private $class = "rowA";
	
	public function drawContent() {
		
		if($this->checkAuth()) {
			$this->getContent();
		}

		$this->publish();
	}
	
	function getContent() {
		$msg='';
		if(intval($_GET['car'])!='0') {
			$car = mysql_fetch_array($this->mysqlQuery("SELECT counted FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car'])."' LIMIT 1"));
			if($car['counted']=='0')
			{
				$this->mysqlQuery("UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET counted = '1' WHERE `id` = '".intval($_GET['car'])."' LIMIT 1");
				if(mysql_errno()) $msg='error';
				else $msg='set';
			}
			else
			{
				$this->mysqlQuery("UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET counted = '0' WHERE `id` = '".intval($_GET['car'])."' LIMIT 1");
				if(mysql_errno()) $msg='error';
				else $msg='unset';
			}
			$out = $msg;
		}
		else $out = '';
		echo $msg;
//		echo iconv('cp1251','utf8',$out);
	}
}
	
?>