<?

class Webcarssave extends Proto {
	const SECTER_WORD = 'ivolga';
	private $car_info, $car_id, $_post, $files, $photolist, $full_data;
	private $_result;
	//const SEND_URI = 'http://makmal/api';
	const SEND_URI = 'http://webcars.kg/api';

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {

		if (isset($_GET['mode']) && $_GET['mode']=='delete'){
			$this->_delete();
			return ;
		}

		// if mode is "autopost" - modify _post property
		if ($_GET['mode']=='autopost'){
			$this->autopost();
			$this->_post = $this->full_data;
		}  else {
			if (empty($_POST)){
				die ('No post parametrs');
			}
			$this->_post = $_POST;

			$this->carInfo();

			if (!empty($_POST['options'])){
				$this->_post['options'] = implode(';', $_POST['options']);
			}

			$fullPhotos = array();
			if (!empty($_POST['photos'])){
				foreach ($_POST['photos'] as $num=>$photo){
					if ($photo!=''){
						$fullPhotos[]='http://'.$_SERVER['HTTP_HOST'].'/photos/'.$this->car_id.'/'.$photo;
					}
				}
			}

			$this->_post['photos'] = implode(';', $fullPhotos);
		}



/*		if ($this->_post['color']!=''){
			$this->_post['color'] = iconv('windows-1251', 'utf-8', $this->_post['color']);
		}

		if ($this->_post['additional']!=''){
			$this->_post['additional'] = iconv('windows-1251', 'utf-8', $this->_post['additional']);
		}
  */
		unset($this->_post['save']);
//		print '<pre>';
//		print_r($this->_post);
//		print '</pre>';
		$this->_send();
		$this->_result();
	}

	private function autopost(){
		$this->carInfo();
		$saleinfo = mysql_fetch_assoc($this->mysqlQuery("SELECT * FROM ccl_".ACCOUNT_SUFFIX."forsale WHERE `car` = ".$this->car_id));
		// getting model id and brand id associated with webcars.kg
		$wc_query = mysql_fetch_assoc($this->mysqlQuery("SELECT webcars_id FROM ccl_".ACCOUNT_SUFFIX."webcars_marka WHERE `tcl_id` = ".$this->car_info['car_marka']));
		$wc_brand = $wc_query['webcars_id'];
		$wc_query2 = mysql_fetch_assoc($this->mysqlQuery("SELECT webcars_id FROM ccl_".ACCOUNT_SUFFIX."webcars_model WHERE `tcl_id` = ".$this->car_info['car_model']));
		$wc_model = $wc_query2['webcars_id'];
		$dict['authkey'] = 'ea02cc4192c75efd31c0dd54cc5da56b';
		$dict['mark_id'] = $wc_brand;
		$dict['model_id'] = $wc_model;
		$dict['ruletype_id'] = '1';
		$dict['user_id'] = '191';		// Larisa's User ID
		$dict['carrosse_id'] = '3';
		$dict['fuel_id'] = '1';
		$dict['kp_id'] = '1';
		$dict['privod_id'] = '3';
		$dict['price'] = $saleinfo['price'];
		$dict['color'] ='серебро';
		$dict['place_id'] = '1';
		$dict['year'] = $this->car_info['year']=='0' ? '2003' : $this->car_info['year'];
		$dict['vengine'] = $this->car_info['engine']=='0' ? '3300' : $this->car_info['engine'];
		$dict['trip'] = $this->car_info['milage']=='0' ? '0' : $this->car_info['milage'];
		$dict['additional'] = $saleinfo['comment'];
		$dict['options'] = '13;7;17;10;16;15;9';
		$this->getPhotosPaths();
		$dict['photos'] = $this->photolist;

		echo $this->photolist;

//		print '<pre>function<br>';
//		print_r($dict);
//		print 'end function</pre>';
		$this->full_data = $dict;
		return $dict;
	}

	private function getPhotosPaths(){
		$this->files = $this->mysqlQuery(
		"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos`
			WHERE `car` = '".$this->car_info['id']."' ORDER BY `folder` DESC, `id` ASC LIMIT 6");

		//$list = '';
		// Put photos links to array
		$alist = array();
		while($p = mysql_fetch_assoc($this->files)){
			//$list .= 'http://tcl.makmalauto.com/photos/'.$this->car_id.'/'.$p['file'].';';
			$alist[] = 'http://tcl.makmalauto.com/photos/'.$this->car_id.'/'.$p['file'].';';
		}
		// Tiny array resort
		$tmp_link = array_shift($alist);
		array_push($alist,$tmp_link);
		$list = implode($alist);

		$list = rtrim($list, ';');
		$this->photolist = $list;
		$_POST['photos'] = $list;
		return $list;
	}

	private function _delete(){
		$this->carInfo();

		if (!$this->car_id){
			die("Ошибка при удалении");
		}
		
		$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."webcars_sent` WHERE `tcl_id`='".$this->car_id."'");
		header('Location: /?mod=cars&sw=webcars&car_id='.$this->car_id);
		exit;

	}

	private function _send(){
		$postString = '';

		foreach ($this->_post as $key=>$val){
			$postString.=$key.'='.$val.'&';
		}

		$ch = curl_init(self::SEND_URI);
		curl_setopt($ch, CURLOPT_POST , 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS , $postString);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
		curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
		curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
		$this->_result = curl_exec($ch);
//		$this->_result = iconv('utf-8', 'windows-1251',$this->_result);

	}
	private function _result(){
		if (strstr($this->_result, 'Error')){
			echo $this->_result; die;
		}

		if (strstr($this->_result, 'Success')){

			preg_match_all("!.*ID:([0-9]{1,4})!", $this->_result, $res);
			if (isset($res[1][0])){
				$id =  $res[1][0];
				$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."webcars_sent` (`tcl_id`, `webcars_id`, `data`) VALUES('".$this->car_id."', '".$id."', '".serialize($this->_post)."')");
				header('Location: /?mod=cars&sw=webcars&car_id='.$this->car_id);
				exit;
			}
		}
		echo "Неизвестная ошибка";
		die;
	}

	private function carInfo(){
		if(intval($_GET['car_id'])!=0) {
			$this->car_info = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car_id'])."'"));
			$this->car_id = $this->car_info['id'];
		}
		else {header('Location: /?mod=cars');
		exit;
		}
	}
	protected function _makeKey(){
		return md5('tcl.makmal.com'.self::SECTER_WORD);
	}

}
?>