<?

class CarsSaveOve extends Proto {

	private $_csvName = false;

	private $_url = 'http://tcl.makmalauto.com';

	private $_fields = array(




	'prefields'=>array(
	'Vin'=>'vin', 'Make'=>'make', 'Model'=>'model', 'Trim'=>'trim', 'Year'=>'year', 'Odometer'=>'odometer', 'OdometerUnits'=>'odometerunit',
	'ExteriorColor'=>'exterior_color', 'InteriorColor'=>'interior_color', 'StockNumber'=>'manheim_stock_number',
	'PhysicalLocationInd'=>'vehicle_location', 'PhysicalLocationCode'=>'vehicle_location_id', 'FacilitatedAuctionCode'=>'facilitation_service_provider_id',
	'TitleStatus'=>'title_status_id', 'TitleState'=>'state_id', 'EngineCylinders'=>'engine_option', 'Transmission'=>'transmission_option', 'Drivetrain'=>'drive_train_option',
	'FuelType'=>'fuel_option', 'AudioType'=>'audio_type_option', 'BodyStyle'=>'body_type_option', 'TopStyle'=>'top_type_option', 'InteriorType'=>'interior_type_option', 'Doors'=>'door_option'),

	'options'=>array(
	'AirBagSideCurtain', 'Air Conditioning', 'Airbags', 'Alloy Wheels', 'AntilockBrakes', 'ChildSeat', 'ChildSeatAnchors', 'CruiseControl', 'DualClimateControl',
	'DVD', 'Navigation', 'HeatedSeats', 'PowerBrakes', 'PowerDoorLocks', 'PowerMirrors', 'PowerSeats', 'PowerSteering', 'PowerWindows', 'RearWindowDefroster',
	'TiltTeleWheel', 'TintedGlass'
	),

	'lastfields'=>array(
	'FrameDamage'=>'frame_damage', 'PriorPaint'=>'prior_paint', 'PriorPaintDetails'=>'prior_paint_details'
	),

	'extdamages'=>array(
	'ExteriorDamages', 'ExtDesc1', 'ExtCond1', 'ExtSev1'

	),

	'intdamages'=>array(
	'InteriorDamages', 'IntDesc1', 'IntCond1', 'IntSev1'
	),

	'tires' => array(
	'AllTiresMatch'=>'all_tires_match', 'LtFrontTireCond'=>'left_front_tire_condition_id',
	'RtFrontTireCond'=>'right_front_tire_condition_id',	'LtRearTireCond'=>'left_rear_tire_condition_id',
	'RtRearTireCond'=>'right_rear_tire_condition_id', 'SpareTireCond'=>'spare_tire_condition_id',

	),

	'images'=>array('IMAGEURL'),

	'bids'=>array('StartingBidPrice'=>'starting_bid_price', 'BidIncrement'=>'bid_increment', 'FloorPrice'=>'floor_price', 'BuyNowPrice'=>'buy_now_price'),

	'date'=>array('PreviewStartDate','StartDate','EndDate'),

	'other'=>array('ActionFlag','ResultsEmail')


	);









	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
		if(!isset($_POST['listing']['equipment_ids']))
		{
			$_POST['listing']['equipment_ids'] = array();
		}

		$data = @mysql_fetch_array($this->mysqlQuery("SELECT * FROM ccl_".ACCOUNT_SUFFIX."ove WHERE car_id='".intval($_GET['car_id'])."' LIMIT 1"));

		if (!empty($data)){
			if ($data['csv']!=='')
			$this->_csvName = $data['csv'];

		}


		$sql="REPLACE INTO `ccl_".ACCOUNT_SUFFIX."ove` VALUES ('".intval($_GET['car_id'])."', '".serialize($_POST)."','','0', '".$this->_getCsvName()."')";


		$this->mysqlQuery($sql);

		$this->_makeExcel();
		//die;



		header('Location: '.$this->root_path.'?mod=cars&sw=oveform&&car_id='.intval($_GET['car_id']).'&success');


		//				echo "<pre>";
		//				print_r($_POST);
		//				echo "</pre>";
		exit;
	}

	private function _makeExcel(){
		$path = $_SERVER['DOCUMENT_ROOT'].'/photos/ove/';

		if (!is_dir($path)){
			mkdir($path);
			chmod($path, 0777);
		}

		$file = $path.$this->_getCsvName();
		if (is_file($file)){
			@unlink($file);
		}


		file_put_contents($file, $this->_getCsvContent());



	}

	private function _getCsvContent(){
		$result = array();


		foreach ($this->_fields['prefields'] as $key=>$field){
			$result[$key]=$this->_filter($_POST['listing'][$field]);
		}



		foreach ($this->_fields['options'] as $option){
			if (in_array($option, $_POST['listing']['equipment_ids'])){
				$result[$option] = 1;
			}
			else{
				$result[$option] = 0;
			}
		}

		foreach ($this->_fields['lastfields'] as $key=>$field){
			$result[$key]=$this->_filter($_POST['listing'][$field]);
		}

		$result['ExteriorDamages'] = $_POST['listing']['no_exterior_damage']?0:1;


		/*ExteriorDamages*/
		if (!empty($_POST['listing']['interior_disclosed_damages'])){
			$total = count($_POST['listing']['interior_disclosed_damages']);
			$total = $total/3;

			if ($total>=3){
				for ($i=0; $i<$total; $i++){
					$ind = ($i+1);
					$k = $i*3;
					$result['ExtDesc'.($ind)] = $_POST['listing']['exterior_disclosed_damages'][$k]['damage_description_id'];
					$result['ExtCond'.($ind)] = $_POST['listing']['exterior_disclosed_damages'][$k+1]['damage_condition_id'];
					$result['ExtSev'.($ind)] = str_replace('`', '"',$_POST['listing']['exterior_disclosed_damages'][$k+2]['damage_severity_id']);
				}
			}
		}


		$result['InteriorDamages'] = $_POST['listing']['no_interior_damage']?0:1;


		/*InteriorDamages*/

		if (!empty($_POST['listing']['interior_disclosed_damages'])){
			$total = count($_POST['listing']['interior_disclosed_damages']);
			$total = $total/3;

			if ($total>=3){
				for ($i=0; $i<$total; $i++){
					$ind = ($i+1);
					$k = $i*3;
					$result['IntDesc'.($ind)] = $_POST['listing']['interior_disclosed_damages'][$k]['damage_description_id'];
					$result['IntCond'.($ind)] = $_POST['listing']['interior_disclosed_damages'][$k+1]['damage_condition_id'];
					$result['IntSev'.($ind)] = str_replace('`', '"',$_POST['listing']['interior_disclosed_damages'][$k+2]['damage_severity_id']);
				}
			}
		}


		/*Tires*/
		foreach ($this->_fields['tires'] as $key=>$field){
			$result[$key]=str_replace("`", '"', $_POST['listing'][$field]);
		}

		/*Photos*/
		if (!empty($_POST['photos'])){
			$pTo = count($_POST['photos']);
			if ($pTo>10){
				$pTo=10;
			}

			for ($i=0; $i<$pTo; $i++){
				$result['IMAGEURL'.$i] = $this->_url.$_POST['photos'][$i];
			}
		}

		if ($_POST['presenter']['allow_dealers_to_bid'] && $_POST['presenter']['allow_dealers_to_buy_now']){
			$result['ListingType'] = 'BOTH';
		}
		elseif(!$_POST['presenter']['allow_dealers_to_bid'] && $_POST['presenter']['allow_dealers_to_buy_now']){
			$result['ListingType'] = 'BUY';
		}
		else{
			$result['ListingType'] = 'BID';
		}
		
		/*Bids*/
		foreach ($this->_fields['bids'] as $key=>$field){
			$result[$key]=intval($_POST['listing'][$field]);
		}

		/*date*/

		//$result['PreviewStartDate'] = date("m/d/Y H:i:s", time()-60*60*24);

		$startTime = (strtotime($_POST['presenter']['start_time_date'].' '.$_POST['presenter']['start_time']. ':00'))-100;

		if ($startTime<time()){
			$result['StartDate'] = date("m/d/Y H:i:s", time()-60*60*24);
		}
		else{
		$result['StartDate'] = $this->_filter($_POST['presenter']['start_time_date'].' '.$_POST['presenter']['start_time']. ':00');
		}


		$result['EndDate'] = $this->_filter($_POST['presenter']['end_time_date'].' '.$_POST['presenter']['end_time'].':00');

		$result['ActionFlag'] = '';

		$result['ResultsEmail'] = $this->_filter($_POST['ove_email']);


		
		//print_r ($result); die;
		require_once($_SERVER['DOCUMENT_ROOT'].'/lib/csv.php');

		//echo CSV::Array2Csv($result); die;
		return CSV::Array2Csv($result);

	}

	private function _filter($value){
		$value = str_replace(",", ";", $value);
		return mysql_real_escape_string(strip_tags($value));
	}



	private function _getCsvName(){

		if (false===$this->_csvName){
			list($usec, $sec) = explode(" ", microtime());
			$this->_csvName = time()."_".substr($usec,2).'.csv';
		}
		return $this->_csvName;
	}
}
?>