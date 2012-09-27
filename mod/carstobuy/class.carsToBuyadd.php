<?

class carsToBuyadd extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {

			$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."carstobuy` 
						(model, body, auctionname, lane, run, time, vin, salon, maxprice, url, years, prepay, client, other, date)
						VALUES(
						'".mysql_real_escape_string($_POST['model'])."',
						'".mysql_real_escape_string($_POST['body'])."',
                                                '".mysql_real_escape_string(strtoupper($_POST['auctionname']))."',
                                                '".mysql_real_escape_string(strtoupper($_POST['lane']))."',
                                                '".intval($_POST['run'])."',
                                                '".mysql_real_escape_string(strtoupper($_POST['time']))."',
                                                '".mysql_real_escape_string($_POST['vin'])."',
                                                '".mysql_real_escape_string(strtoupper($_POST['salon']))."',
						'".intval($_POST['maxprice'])."',
						'".mysql_real_escape_string($_POST['url'])."',
						'".mysql_real_escape_string($_POST['years'])."',
						'".intval($_POST['prepay'])."',
						'".intval($_POST['client'])."',
						'".mysql_real_escape_string($_POST['other'])."',
						'".mysql_real_escape_string($_POST['date'])."'
						)";
			$this->mysqlQuery($request) or die('<p>' . mysql_error());

			$this->redirect($this->root_path.'?mod=carstobuy&success');
	}
}
?>