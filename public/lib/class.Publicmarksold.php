<?php
class markSold extends Proto {
	
	var $car_id = '';

	public function makePage() {
		$this->setCarId();
		if($this->checkAuth()) {
			$this->markAsSold();
		}
		else echo 'reloadpage';
	}

	public function markAsSold() {
		$responce = '';
		if($this->car_id!='0' and $this->car_id!='') {
			$content = mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."cars.*,ccl_".ACCOUNT_SUFFIX."cars.sold as car_is_sold, ccl_".ACCOUNT_SUFFIX."containers.sent, ccl_".ACCOUNT_SUFFIX."containers.portdate, ccl_".ACCOUNT_SUFFIX."containers.station, ccl_".ACCOUNT_SUFFIX."containers.rail, ccl_".ACCOUNT_SUFFIX."containers.number, ccl_".ACCOUNT_SUFFIX."containers.bishkek, ccl_".ACCOUNT_SUFFIX."forsale.id as sell_id,
			ccl_".ACCOUNT_SUFFIX."forsale.price as sell_price, ccl_".ACCOUNT_SUFFIX."forsale.comment as sell_comment,
			ccl_".ACCOUNT_SUFFIX."places.name as destination,
			ccl_".ACCOUNT_SUFFIX."auctions.name as auction_name
			FROM `ccl_".ACCOUNT_SUFFIX."cars`
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."containers`
			ON ( ccl_".ACCOUNT_SUFFIX."containers.id = ccl_".ACCOUNT_SUFFIX."cars.container )
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."forsale`
			ON (ccl_".ACCOUNT_SUFFIX."forsale.car = ccl_".ACCOUNT_SUFFIX."cars.id)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."auctions`
			ON (ccl_".ACCOUNT_SUFFIX."auctions.id = ccl_".ACCOUNT_SUFFIX."cars.auction)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."places`
			ON (ccl_".ACCOUNT_SUFFIX."places.id = ccl_".ACCOUNT_SUFFIX."cars.place_id3)
			WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".$this->car_id."' LIMIT 1"));

			if($this->validateOwnership($content))
			{
				$state = intval($_GET['set']) >= 1 ? 1 : 0;
				$r=mysql_query("UPDATE ccl_".ACCOUNT_SUFFIX."cars SET sold = ".$state." WHERE id = ".$this->car_id);
				if(!$r){
					echo 'error';
					return;
				}
				$r=@mysql_fetch_assoc(mysql_query("SELECT id,sold FROM ccl_".ACCOUNT_SUFFIX."cars WHERE id = ".$this->car_id));
				if($r['sold']=='1')	echo 'marked';
				else echo 'unmarked';
			}
			else $responce = 'reloadpage';
		}
		else $responce = 'reloadpage';
	}

	private function validateOwnership($content) {
		if($_SESSION['user_type']!='11'){
			if($content['buyer']==$_SESSION['user_id'] or $content['dealer']==$_SESSION['user_id'] or $content['reciever']==$_SESSION['user_id']) return true;
			else return false;
		} else return true;
	}
	
	private function setCarId() {
		if(isset($_GET['carid'])) $id = intval($_GET['carid']);
		else $id = '';
		$this->car_id = $id;
	}
}
?>
