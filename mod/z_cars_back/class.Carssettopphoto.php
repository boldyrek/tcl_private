<?

class setTopPhoto extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}
	
	function Process() {
		if(intval($_GET['car']!=0)) {
			$this->mysqlQuery("
			UPDATE `ccl_".ACCOUNT_SUFFIX."cars`
			SET `top_photo` = '".intval($_GET['top_photo'])."' 
			WHERE `id` = '".intval($_GET['car'])."' LIMIT 1");
		}
	}
}