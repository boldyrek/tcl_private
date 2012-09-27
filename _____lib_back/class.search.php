<?

class listSearch {
	
	var $filter = array(); // корректор запроса, критерий поиска
	var $data = array(); // POST или GET запрос от пользователя, содержащий параметры поиска
	var $output = ''; // выходные данные класса, скорее все форма поиска, с указанными, когда возможно, параметрами
	var $template = ''; // шаблон формы поиска
	
	function makeFilter() {
		if($this->data) {
			$out = '';
			foreach ($this->data as $k=>$v) {
				
				if($v!='') {
					
					if($out!='') $out .= ' AND ';
					$out .= "(".$this->filter[$k]." '".$v."')";
				}
			}
			if($out!='') return ' '.$out;
			else return '';
		}
	}
	

	function parser() {
		
		if($this->template) {
			$this->output = $this->template;
		}

		return $this->output;
	}
	
	
	function insert($input, $type) {
		switch($type) {
			case 'data': array_push($this->data, $input);
			break;
			
			case 'filter': array_push($this->filter, $input);
			break;
		}
	}
}

?>