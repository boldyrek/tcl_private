<?

class info extends Proto {
	
	private $totals;
	private $class = "rowA";
	
	public function drawContent() {
		
		if($this->checkAuth()) {
			$this->getContent();
		}

		$this->publish();
	}
	
	function getContent() {
		if(intval($_GET['car'])!=0) {
			$car = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car'])."'"));
			$out = 'аукцонный сбор: '.$car['acufee'];
		}
		else $out = '';
		echo $out;
	}
}
	
?>