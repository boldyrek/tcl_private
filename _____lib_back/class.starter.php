<?

class starter extends Proto {
	
	function drawContent() {
		$this->page .= $this->templates['header'];
		$this->createLoginForm();
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
}

?>
