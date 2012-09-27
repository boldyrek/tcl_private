<?

class Stuffallphotos extends Proto {
	
	public function drawContent() {
		
		if($this->checkAuth()) {
				$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		$stuff_id = intval($_GET['stuff_id']);
		if($stuff_id!=0 and $stuff_id!='') {
			
			$photos = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff_photos` WHERE `stuff` = '".$stuff_id."' ORDER BY id DESC");
			$info = mysql_fetch_array($this->mysqlQuery("SELECT name FROM `ccl_".ACCOUNT_SUFFIX."stuff` WHERE `id` = '".$stuff_id."'"));
			
			$this->page .= '<html>
				<head><title>'.$info['name'].'</title></head>
				<body>
				<h3>'.$info['name'].'</h3>';
			
			while($line = mysql_fetch_array($photos)) {
				$this->page .= '<img src="/photos/stuff/'.ACCOUNT_SUFFIX.$stuff_id.'/'.$line['file'].'" vspace="10"><br>';
			}
			
		}
	
	}

}

?>