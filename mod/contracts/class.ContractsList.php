<?

class ContractsList extends Proto {
	
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
		require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/class.list.php');
		
		$list = new dataList;
		
		//постраничный переход
		$list->total_items = mysql_fetch_array($this->mysqlQuery("
		SELECT COUNT(id) AS total 
		FROM `ccl_".ACCOUNT_SUFFIX."contracts` WHERE 1"));
		$list->page = $_GET['page'];
		$list->request = "
		SELECT ccl_".ACCOUNT_SUFFIX."contracts.*, ccl_".ACCOUNT_SUFFIX."customers.name 
		FROM `ccl_".ACCOUNT_SUFFIX."contracts` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` 
		ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."contracts.client) 
		WHERE 1";
		$list->def_sort = 'date';
		$list->def_sort_dir = 'DESC';
		$list->per_page = $this->per_page;
		$list->root_path = $this->root_path;
		
		//задаем параметры таблицы
		$list->cols[1] = array('name' => 'date', 		'caption' => 'Дата', 		'width' => '100');
		$list->cols[2] = array('name' => 'number', 		'caption' => 'Номер', 		'width' => '60');
		$list->cols[3] = array('name' => 'name', 		'caption' => 'Клиент', 		'width' => '');
		$list->cols[4] = array('name' => 'sum', 		'caption' => 'Сумма', 		'width' => '80');
		$list->cols[5] = array('name' => 'paid', 		'caption' => 'Оплачено', 	'width' => '80');
		$list->cols[6] = array('name' => 'dolg', 		'caption' => 'Долг', 		'width' => '80');
		$list->cols[7] = array('name' => 'agent', 		'caption' => 'Агенту', 		'width' => '80');
		
		$this->page.='<div class="location">Контракты | <a href="'.$this->root_path.'?mod=contracts&sw=form">добавить</a></div>';

		$list->module = 'contracts';
		$list->item = 'contract';
		$list->list_name = 'contracts';
		$this->page .= $list->buildList();
	}
}

?>