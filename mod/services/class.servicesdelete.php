<?

class ServicesDelete extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

	}

	private	function Process() {

		// удаление инвойса
		if(intval($_GET['id'])!=0 and intval($_GET['id'])!='') {
			$this->mysqlQuery("
			DELETE FROM `ccl_".ACCOUNT_SUFFIX."services` 
			WHERE id = '".intval($_GET['id'])."'");
		}
		$this->redirect($this->root_path.'?mod=services');
	}

}


?>