<?

class Recieversdelete extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
		
			$request = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."recievers` 
			WHERE `id` = '".intval($_GET['id'])."'";
			$this->mysqlQuery($request);

			$this->redirect($this->root_path.'?mod=recievers&success');
	}
}
?>