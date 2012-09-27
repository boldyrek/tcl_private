<?

class auctionsadd extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}

	private function Process() {

			$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."auctions` 
						(name, address, phones, comment)
						VALUES(
						'".mysql_real_escape_string($_POST['name'])."',
						'".mysql_real_escape_string($_POST['address'])."',
						'".mysql_real_escape_string($_POST['phones'])."',
						'".mysql_real_escape_string($_POST['comment'])."'
						)";
			
			$this->mysqlQuery($request);

			$this->redirect($this->root_path.'?mod=auctions&success');
	}
}
?>