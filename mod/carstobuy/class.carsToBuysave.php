<?

class carsToBuysave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
			if($_POST['bought']=='on') $bought = '1';
			else $bought = '0';
			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."carstobuy` SET 
						model 	= '".mysql_real_escape_string($_POST['model'])."',
						body 	= '".mysql_real_escape_string($_POST['body'])."',
                                                lane 	= '".mysql_real_escape_string($_POST['lane'])."',
                                                run 	= '".intval($_POST['run'])."',
						auctionname 	= '".mysql_real_escape_string($_POST['auctionname'])."',
                                                vin 	= '".mysql_real_escape_string($_POST['vin'])."',
                                                time 	= '".mysql_real_escape_string($_POST['time'])."',
                                                salon 	= '".mysql_real_escape_string($_POST['salon'])."',
						maxprice 		= '".intval($_POST['maxprice'])."',
						url 	= '".mysql_real_escape_string($_POST['url'])."',
						years 	= '".mysql_real_escape_string($_POST['years'])."',
						prepay 	= '".intval($_POST['prepay'])."',
						client 	= '".intval($_POST['client'])."',
						other 	= '".mysql_real_escape_string($_POST['other'])."',
						date 	= '".mysql_real_escape_string($_POST['date'])."',
						status 	= '".$bought."'
						WHERE `id` 	= '".intval($_GET['id'])."' LIMIT 1";
			$this->mysqlQuery($request);

			$this->redirect($this->root_path.'?mod=carstobuy&sw=form&car_id='.intval($_GET['id']).'&success');
	}
}
?>