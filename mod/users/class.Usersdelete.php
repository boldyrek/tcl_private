<?

class UsersDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id'])!=0) {
			$request = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."usrs` WHERE id=".intval($_GET['id']);
			$this->mysqlQuery($request);
		}
		$this->redirect($this->root_path.'?mod=users');	
	}
}
?>