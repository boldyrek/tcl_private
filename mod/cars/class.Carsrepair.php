<?

class Repair extends Proto {

	const SECRET_WORD = 'dsjk434343d934LIsi23983';
	private $car_info,$car_id, $sent_info;
	private $num_row_photo = 6;

	private $view = false;

	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->carInfo();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];

		$this->errorsPublisher();
		$this->publish();
	}

	private function getContent() {
		$this->carInfo();
		$this->getMark();
		$this->getModel();
		
		if (true===$this->getSentInfo()){
			$this->_renderSentForm();
			return ;
		}
		
		
		$this->getPhotos();
		$this->_renderForm();
	}

	private function _renderForm(){
		$this->view = (Object)$this->car_info;
		$this->view->authkey = self::SECRET_WORD;

		ob_start();
		require_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/templates/repairForm.php');
		$res = ob_get_contents();
		ob_clean();

		$this->page.=$res;
	}
	
	private function _renderSentForm(){
		$this->view = (Object)$this->car_info;
		$this->view->authkey = self::SECRET_WORD;

		ob_start();
		require_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/templates/repairsentForm.php');
		$res = ob_get_contents();
		ob_clean();

		$this->page.=$res;
	}
	
	private function getSentInfo(){
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."repair_sent` WHERE `tcl_id`='".$this->car_id."' LIMIT 1";
		$res = mysql_fetch_assoc($this->mysqlQuery($sql));
		if (!empty($res)){
			$this->car_info['sent_id'] = $res['repair_id'];
			$this->car_info['sold'] = $res['sold'];
			return true;
		}
		return false;
	}


	private function getMark(){
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."marka` WHERE `id`='".$this->car_info['car_marka']."'";
		$res = mysql_fetch_assoc($this->mysqlQuery($sql));
		if (empty($res)){
			die("Не найдена марка в таблице ccl_".ACCOUNT_SUFFIX."marka");
			return ;
		}
		$this->car_info['mark'] = (Object)$res;

	}

	private function getModel(){
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."model` WHERE `id`='".$this->car_info['car_model']."'";
		$res = mysql_fetch_assoc($this->mysqlQuery($sql));
		if (empty($res)){
			die("Не найдена модель в таблице ccl_".ACCOUNT_SUFFIX."model");
			return ;
		}
		$this->car_info['model'] = (Object)$res;

	}
	
	private function carInfo() {
		if(intval($_GET['car_id'])!=0) {
			$this->car_info = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car_id'])."'"));
			$this->car_id = $this->car_info['id'];
		}
		else header('Location: /?mod=cars');
	}
	private function getPhotos(){
		$this->files = $this->mysqlQuery(
		"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos`
			WHERE `car` = '".$this->car_info['id']."' ORDER BY `folder` DESC, `id` ASC LIMIT 6");

		//фотографии автомобиля
		$photos = array();
		$num = @mysql_num_rows($this->files);
		if($num>0)
		{
			$j = 1;
			while($j<=$num)
			{
				$line = mysql_fetch_array($this->files);
				if($line['file']!='')$photos[] = $line['file'];
				$j++;
			}
			$photos = array_chunk($photos, $this->num_row_photo);

			$photo_list = '<table cellspacing="2" cellpadding="0" style="border-bottom:1px solid #ccc;">';
			$r_=0;
			foreach ($photos as $k=>$v) {
				$chk_list_row ='<tr class="row'.($r_%2? 'B':'A').'">';

				$photo_list_row = '<tr class="row'.($r_%2? 'B':'A').'">';
				for($i=0;$i<$this->num_row_photo;$i++)
				{
					$photo_td = '<td align="center" style="padding:0 10px">';
					if(isset($v[$i]))
					{
						$photo_td .= '<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$v[$i].'" target="_blank"><img src="'.$this->root_path.'photos/'.$this->car_id.'/thumb/'.$v[$i].'" border="0"></a>';
					}else{
						$photo_td .= '&nbsp;';
					}
					$photo_td .= '</td>';
					$photo_list_row .= $photo_td;

					$chk_td = '<td align="center" style="padding: 15px 10px 0 10px;border:none">';
					if(isset($v[$i]))
					{
						$chk_td .= '<input type="checkbox" '.(is_array($form['photos']) && in_array($v[$i], $form['photos'])? "checked='checked'":"").' name="photos[]" value="'.$v[$i].'">';
					}else{
						$chk_td .= '&nbsp;';
					}
					$chk_td .= '</td>';
					$chk_list_row .= $chk_td;
				}
				$photo_list_row .= '</tr>';
				$chk_list_row .='</tr>';
				$photo_list .= $chk_list_row.$photo_list_row;
				$r_++;
			}
			$photo_list .= '</table>';
		}
		else $photo_list = '';
		
		$this->car_info['photos'] = $photo_list;
	}

	


}
function arr_echo($arr)
{
	echo "<pre>";
	print_r($arr);
	echo "</pre>";
}
?>