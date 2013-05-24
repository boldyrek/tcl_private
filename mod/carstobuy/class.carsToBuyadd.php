<?

class carsToBuyadd extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

        protected function GetCarsListByVIN($vin_code) {
                if (trim($vin_code) != '') {
                    $founds_by_vin = array();
                    
                    $check_request = "SELECT id, model, vin FROM `ccl_".ACCOUNT_SUFFIX."carstobuy` WHERE vin LIKE '%".mysql_real_escape_string($_POST['vin'])."%' ORDER BY model";
                    $result = $this->mysqlQuery($check_request);
                    while ($row = mysql_fetch_array($result)) {
                        $row['table_name'] = 'carstobuy';
                        $founds_by_vin[] = $row;
                    }
                    
                    $check_request = "SELECT id, model, frame FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE frame LIKE '%".mysql_real_escape_string($_POST['vin'])."%' ORDER BY model";
                    $result = $this->mysqlQuery($check_request);
                    while ($row = mysql_fetch_array($result)) {
                        $row['table_name'] = 'cars';
                        $founds_by_vin[] = $row;
                    }
                    
                    return $founds_by_vin;
                } else {
                    return array();
                }
        }
        
        protected function SaveFormData($founds_by_vin) {
                $_SESSION['ccl_carstobuy_add_FormData']['founds_by_vin'] = $founds_by_vin;
                $_SESSION['ccl_carstobuy_add_FormData']['post']          = $_POST;
        }
        
	private function Process() {
                if (!isset($_POST['vin'])) $_POST['vin'] = '';
                if (!isset($_GET['second']) || strtoupper(trim(urldecode($_GET['second']))) != strtoupper(trim(urldecode($_POST['vin'])))) {
                    $founds_by_vin = $this->GetCarsListByVIN($_POST['vin']);
                } else {
                    $founds_by_vin = array();
                }
                
                if (count($founds_by_vin) == 0) {
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
                } else {
                    $this->SaveFormData($founds_by_vin);
                    $this->redirect($this->root_path.'?mod=carstobuy&sw=form&add');
                }
	}
}
?>