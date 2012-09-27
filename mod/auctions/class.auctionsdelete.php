<?

class auctionsdelete extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}

	private function Process() {

			$request = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."auctions` WHERE id = '".intval($_GET['id'])."'";
			
			$this->mysqlQuery($request);

			$this->redirect($this->root_path.'?mod=auctions&deleted');
	}
}
?>