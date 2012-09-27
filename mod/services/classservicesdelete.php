<?

class InvoicesDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
		
	}
	
	private	function Process() {
		
		// удаление инвойса	
		if(intval($_GET['id'])!=0 and intval($_GET['id'])!='') {
			$this->mysqlQuery("
			DELETE FROM `ccl_".ACCOUNT_SUFFIX."invoices` 
			WHERE id = '".intval($_GET['id'])."'");
		
			if(intval($_GET['container'])!=0 and intval($_GET['container'])!='') {
				$this->mysqlQuery("
				UPDATE `ccl_".ACCOUNT_SUFFIX."containers` 
				SET `invoice`='0' 
				WHERE id='".intval($_GET['container'])."'");
			}
		}
		
		$this->redirect($this->root_path.'?mod=invoices');
	}
}

?>