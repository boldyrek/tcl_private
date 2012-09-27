<?

class Carsallphotos extends Proto {
	
	public function drawContent() {
		
		if($this->checkAuth()) {
				$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		$car_id = intval($_GET['car_id']);
		if($car_id!=0 and $car_id!='') {
			
			$photos = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE `car` = '".$car_id."' ORDER BY id DESC");
			$info = mysql_fetch_array($this->mysqlQuery("SELECT model, year, frame FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".$car_id."'"));
			
			$this->page .= '<html>
				<head><title>'.$info['model'].' - '.$info['frame'].'</title></head>
				<body>
				<h3>'.$info['model'].' - '.$info['year'].' - '.$info['frame'].'</h3>';
			
			while($line = mysql_fetch_array($photos)) {
				$this->page .= '<img src="/photos/'.$car_id.'/'.$line['file'].'" vspace="10"><br>';
			}
			
		}
	
	}

}

?>