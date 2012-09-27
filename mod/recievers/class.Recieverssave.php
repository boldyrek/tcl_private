<?

class Recieverssave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
		
			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."recievers` SET 
						name 	= '".mysql_real_escape_string($_POST['name'])."',
						phone 	= '".mysql_real_escape_string($_POST['phone'])."',
						address 	= '".mysql_real_escape_string($_POST['address'])."',
						passport 		= '".mysql_real_escape_string($_POST['passport'])."'
						WHERE `id` 	= '".intval($_GET['id'])."' LIMIT 1";
			$this->mysqlQuery($request);

			$this->redirect($this->root_path.'?mod=recievers&sw=form&id='.intval($_GET['id']).'&success');
	}
}
?>