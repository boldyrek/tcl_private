<?

class auctionssave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}

	private function Process() {

			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."auctions` SET 
						name 	= '".mysql_real_escape_string($_POST['name'])."',
						address 	= '".mysql_real_escape_string($_POST['address'])."',
						phones 	= '".mysql_real_escape_string($_POST['phones'])."',
						comment 	= '".mysql_real_escape_string($_POST['comment'])."'						
						WHERE `id` 	= '".intval($_GET['id'])."' LIMIT 1";
			$this->mysqlQuery($request);

			$this->redirect($this->root_path.'?mod=auctions&sw=form&id='.intval($_GET['id']).'&success');
	}
}
?>