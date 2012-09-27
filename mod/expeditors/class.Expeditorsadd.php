<?

class ExpeditorsAdd extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."expeditors` (`id`, `name`, `address`, `phone`, `email`, `balance`) 
		VALUES (LAST_INSERT_ID(), 
		'".mysql_real_escape_string(strtoupper($_POST['name']))."', 
		'".mysql_real_escape_string(strtoupper($_POST['address']))."', 
		'".mysql_real_escape_string(strtoupper($_POST['phone']))."', 
		'".mysql_real_escape_string($_POST['email'])."', 
		'0')";
		$this->mysqlQuery($request);

		$this->redirect($this->root_path.'?mod=expeditors'); 
	}
}
?>