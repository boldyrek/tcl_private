<?

class CertificatesPrint extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
		
	}
	
	function Process() {
		if(!$this->exists($_GET['id'])) $this->redirect($this->root_path);
		
		$cert = mysql_fetch_array($this->mysqlQuery("
		SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."certificates` 
		WHERE `car` = '".intval($_GET['id'])."'"));
		
		$car = @mysql_fetch_array($this->mysqlQuery("
		SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		WHERE `id` = '".intval($_GET['id'])."'"));
		
		require($_SERVER['DOCUMENT_ROOT']."/mod/certificates/templates/cert_file.php");
		
		echo $cert_file;
	}
}

?>