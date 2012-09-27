<?

class MarkaDelete extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

	}

	private	function Process() {

		if(intval($_GET['id'])!=0 and intval($_GET['id'])!='') {
			$this->mysqlQuery("
			DELETE FROM `ccl_".ACCOUNT_SUFFIX."marka` 
			WHERE id = '".intval($_GET['id'])."'");
		}
		$this->redirect($this->root_path.'?mod=marka');
	}

}


?>