<?

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');
class CarsList extends Proto {
	
	public function drawContent() {
		
		if($this->checkAuth()) {
			$this->getContent();
		}
		$this->debug_mode = false;
		$this->track_queries = false;
		$this->exec_time = false;
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		$sql ="SELECT id, model, frame 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		WHERE `created` > '2008-09-02'
		ORDER BY `id` DESC";	
		
		$cars = $this->mysqlQuery($sql);
		if(isset($_GET['selected'])) $chosen = intval($_GET['selected']);
		else $chosen = 0;
		
		$num = mysql_num_rows($cars);
		
		$i=1;
		if($num>0) {
			$out = '
			<select name="car">
			<option value="0"';
			if($chosen==0) $out .= ' selected="selected"';
			$out .= '>'.$this->translate->_(' - = не выбран = - ').'</option>';
			while($i<=$num) {
				$line = mysql_fetch_array($cars);
				$out .= '
				<option value="'.$line['id'].'"';
				if($chosen==$line['id']) $out .= ' selected="selected"';
				$out .= '>'.stripslashes($line['model']).' - '.$line['frame'].'</option>';
				$i++;
			}
			$out .= '</select>';
		}
		else $out = '';
		
		$this->page = $out;
        //iconv('WINDOWS-1251', 'UTF-8',$out);
		
	}
}

?>