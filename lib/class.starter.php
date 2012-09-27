<?

class starter extends Proto {
	
	function drawContent() {
		
	    //$this->sMail('work@weltkind.com', 'Привет мир', 'Тестовая отправка');
	    
	    $this->page .= $this->templates['header'];
		$this->createLoginForm();
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
}

?>