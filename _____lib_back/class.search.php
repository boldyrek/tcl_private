<?

class listSearch {
	
	var $filter = array(); // ��������� �������, �������� ������
	var $data = array(); // POST ��� GET ������ �� ������������, ���������� ��������� ������
	var $output = ''; // �������� ������ ������, ������ ��� ����� ������, � ����������, ����� ��������, �����������
	var $template = ''; // ������ ����� ������
	
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