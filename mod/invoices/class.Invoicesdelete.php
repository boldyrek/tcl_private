<?

class InvoicesDelete extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

	}

	private	function Process() {

		// удаление инвойса
		if(intval($_GET['id'])!=0 and intval($_GET['id'])!='') {
			$sql="DELETE FROM `ccl_".ACCOUNT_SUFFIX."invoices` WHERE id = '".intval($_GET['id'])."'";
			if ($this->mysqlQuery($sql))
			{
				$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."invoices_services` WHERE `invoice_id` = '".intval($_GET['id'])."'");
			}
		}

		$this->redirect($this->root_path.'?mod=invoices');
	}
}

?>