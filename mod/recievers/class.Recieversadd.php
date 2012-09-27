<?

class Recieversadd extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
		
			$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."recievers` 
			(`name`, `phone`, `address`, `passport`)
			VALUES ('".mysql_real_escape_string($_POST['name'])."', '".mysql_real_escape_string($_POST['phone'])."', '".mysql_real_escape_string($_POST['address'])."', '".mysql_real_escape_string($_POST['passport'])."')";
			$this->mysqlQuery($request);

			$this->redirect($this->root_path.'?mod=recievers&success');
	}
}
?>