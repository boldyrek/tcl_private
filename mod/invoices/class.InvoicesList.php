<?

class InvoicesList extends Proto {
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
			$this->page .= $this->module_content;
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		$total_items = mysql_fetch_array($this->mysqlQuery("
		SELECT COUNT(id) 
		AS total 
		FROM `ccl_".ACCOUNT_SUFFIX."invoices` 
		WHERE 1"));

		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items['total']);
		}
		//дата, номер, сумма, название авто и вин код
		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."invoices.*, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.frame, ccl_".ACCOUNT_SUFFIX."customers.name as client_name 
		FROM ccl_".ACCOUNT_SUFFIX."invoices
		LEFT JOIN ccl_".ACCOUNT_SUFFIX."cars 
		ON (ccl_".ACCOUNT_SUFFIX."cars.id=ccl_".ACCOUNT_SUFFIX."invoices.carId)
		LEFT JOIN ccl_".ACCOUNT_SUFFIX."customers
		ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."invoices.client)";
		
		//задаем параметры таблицы
		$cols[1] = array('name' => 'date', 			'caption' => $this->translate->_('Дата добавления'),	'width' => '80');
		$cols[2] = array('name' => 'number', 		'caption' => $this->translate->_('Номер'), 		'width' => '70');
		$cols[3] = array('name' => 'itog', 			'caption' => $this->translate->_('Сумма'), 			'width' => '80');
		$cols[4] = array('name' => 'client_name',	'caption' => $this->translate->_('Клиент'), 		'width' => '200');
		$cols[5] = array('name' => 'model', 		'caption' => $this->translate->_('Автомобиль'), 	'width' => '');
		$cols[6] = array('name' => 'frame', 		'caption' => $this->translate->_('Вин код'),  	'width' => '100');
		
		$this->page .= '<div class="location">
		<table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td>'.$this->translate->_('Инвойсы').' | <a href="'.$this->root_path.'?mod=invoices&sw=form&id=0">'.$this->translate->_('Добавить').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=services">'.$this->translate->_('Список услуг').'</a></td>
		<td width="400" align="right"></td></tr></table>
		</div>';
		
		$this->page .= $this->buildList($request, $cols, 'invoices', 'invoices', 'id', $pages['qlimit']);
		
		$this->page .= $pages['print'];
	}
}

?>