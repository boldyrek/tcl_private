<?
/*
 * Saving arrays to Database.
 * -= name =-          -= description =-
 * "purposes"        - expenses purposes array
 * "car_location"    - car location
 * "title_location"  - title location
 */
class ProfileSave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
			$error='Save completed, but nothing to save!';
			$_SESSION['error']=$error;
		}
	}

	protected function str2arr($str){
		//$s=$this->filterstr($str);
		$s=$str;
		$r=array();
		if(!empty($s)){
			preg_match_all('/^(\d+):(.+)$/m', $s, $r);
			foreach($r[2] as $id=>$val) $r[2][$id] = $this->filterstr($val);
			return array_combine($r[1],$r[2]);
		}
		else return array();
	}

	protected function filterstr($str){
		$s=stripslashes($str);
		$carriage= array("\r\n", "\r", "\n");
		$s = str_replace($carriage, "", $s);
		$s = str_replace("'", "", $s);
		$s = str_replace("\"", "", $s);

		$s = htmlspecialchars($s);
		$s = str_ireplace("script", "blocked", $s);
		$s = mysql_real_escape_string($s);
		return $s;
	}

	function Process()
	{
		$place = $this->str2arr($_POST['carlocation']);
		$tplace = $this->str2arr($_POST['titlelocation']);
		$purposes = $this->str2arr($_POST['purposes']);
		/*if(ACCOUNT_ID != '0')*/{
			//$this->mysqlQuery('INSERT INTO `ccl_'.ACCOUNT_SUFFIX.'settings` (name,data) VALUES (\'car_location\',\''.serialize($place).'\')');
			//$this->mysqlQuery('INSERT INTO `ccl_'.ACCOUNT_SUFFIX.'settings` (name,data) VALUES (\'title_location\',\''.serialize($tplace).'\')');
			//$this->mysqlQuery('INSERT INTO `ccl_'.ACCOUNT_SUFFIX.'settings` (name,data) VALUES (\'purposes\',\''.serialize($purposes).'\')');

			$this->mysqlQuery('UPDATE `ccl_'.ACCOUNT_SUFFIX.'settings` SET data=\''.serialize($place).'\' WHERE name=\'car_location\' LIMIT 1');
			$this->mysqlQuery('UPDATE `ccl_'.ACCOUNT_SUFFIX.'settings` SET data=\''.serialize($tplace).'\' WHERE name=\'title_location\' LIMIT 1');
			$this->mysqlQuery('UPDATE `ccl_'.ACCOUNT_SUFFIX.'settings` SET data=\''.serialize($purposes).'\' WHERE name=\'purposes\' LIMIT 1');
			if(mysql_errno()) echo mysql_error();

			$this->place = $place;
			$this->tplace = $tplace;
			$this->purposes = $purposes;
		}
		$this->redirect($this->root_path.'?mod=profile'); exit;
	}
}
?>