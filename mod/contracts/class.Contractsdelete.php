<?

class ContractsDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
		
	}
	
	function Process() {
		// удаляем контракт

		if(intval($_GET['id'])!=0 and  intval($_GET['id'])!='') {
					$this->mysqlQuery("
					DELETE FROM `ccl_".ACCOUNT_SUFFIX."contracts` 
					WHERE id = '".intval($_GET['id'])."'");
				}
		
		$this->redirect($this->root_path.'?mod=contracts');
	}
}
?>