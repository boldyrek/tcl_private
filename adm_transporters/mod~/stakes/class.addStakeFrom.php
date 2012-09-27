<?


//
//  форма добавления ставка
//

class addStakeForm {
	var $auc_list;
	var $template;
		
	function makeForm() {
		$auclist = $this->getAucList();
		$this->template = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/public/mod/stakes/templates/add_form.php');
		$out = str_replace('[%auctions%]', $auclist, $this->template);
		$out = str_replace('[%month%]', $this->makeMonthList(), $out);
		$out = str_replace('[%model_value%]', $_POST['model'], $out);
		$out = str_replace('[%day_value%]', $_POST['lot_date']['d'], $out);
		$out = str_replace('[%lot_value%]', $_POST['lot'], $out);
		$out = str_replace('[%sum_value%]', $_POST['sum'], $out);
		return $out;
	}
	
	function getAucList() {
		
		$num = mysql_num_rows($this->auc_list);
		$i = 0;
		$aucs = '<select name="auction">';
		while($i<$num) {
			$line = mysql_fetch_array($this->auc_list);
			$aucs .= '
			<option value="'.$line['id'].'">'.$line['name'].'</option>';
			$i++;
		}
		$aucs .= '</select>';
		return $aucs;
	}
	function makeMonthList() {
		$out = '<select name="lot_date[m]">';
          
		$monthes = array(
		'1' => 'январь',
		'2'	=> 'февраль',
		'3'	=> 'март',
		'4'	=> 'апрель',
		'5'	=> 'май',
		'6'	=> 'июнь',
		'7'	=> 'июль',
		'8'	=> 'август',
		'9'	=> 'сентябрь',
		'10'=> 'октябрь',
		'11'=> 'ноябрь',
		'12'=> 'декабрь');
		
		$out .= '<option value="'.date('m').'">'.$monthes[ltrim(date('m'),'0')].'</option>';
		if(ltrim(date('d'),'0')>20) {
			$next_month = ltrim(date('m'),'0')+1;
			if($next_month>12) $next_month = $next_month - 12;
			$out .= '<option value="'.$next_month.'">'.$monthes[$next_month].'</option>';
		}
			
        $out .= '</select>';
        return $out;
	}
}

?>