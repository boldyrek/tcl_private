<?
require_once 'bin/balance.php';
class ClientsRecalcBalance extends Proto {
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
			$this->page .= $this->module_content;
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		$q = $this->mysqlQuery("SELECT id as customer,dealer FROM ccl_".ACCOUNT_SUFFIX."customers ORDER BY id");
		while($r = mysql_fetch_assoc($q)){
			updateBalance($r['customer'], $r['dealer']);
		}
		$this->redirect('/?mod=clients');
	}
}

?>