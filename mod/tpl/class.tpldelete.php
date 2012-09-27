<?
class tplDelete extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

	}

	private	function Process() {

		$type=htmlspecialchars($_GET['type']);
		if(intval($_GET['id'])!=0 and intval($_GET['id'])!='') {
			$this->mysqlQuery("
			DELETE FROM `ccl_".ACCOUNT_SUFFIX."tpl` 
			WHERE id = '".intval($_GET['id'])."'");
		}
		$this->redirect($this->root_path.'?mod=tpl&type='.$type);
	}

}


?>