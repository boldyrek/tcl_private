<?

class CertificatesForm extends Proto {
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		
		$certificate = $this->mysqlQuery("
		SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."certificates`
		WHERE `car` = '".intval($_GET['car'])."'");
		
		$car = @mysql_fetch_array($this->mysqlQuery("
		SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		WHERE `id` = '".intval($_GET['car'])."'"));
		
		$supplier = mysql_fetch_array($this->mysqlQuery("
		SELECT ccl_".ACCOUNT_SUFFIX."suppliers.name, 
		ccl_".ACCOUNT_SUFFIX."suppliers.address 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."suppliers` 
		ON(ccl_".ACCOUNT_SUFFIX."suppliers.id = ccl_".ACCOUNT_SUFFIX."cars.supplier) 
		WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".intval($_GET['car'])."'"));
		
		require($_SERVER['DOCUMENT_ROOT']."/mod/certificates/certificate.php");
		$this->page .= $print;
	}
}

?>