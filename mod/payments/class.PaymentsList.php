<?

class PaymentsList extends Proto {
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		// список платежей
		
		$_SESSION['prev_location'] = $_SESSION['last_location'];
		$_SESSION['last_location'] = $_SERVER['QUERY_STRING'];
		
		if(isset($_GET['list'])) {
			if($_GET['list']=='act') $_SESSION['payments_list'] = 'active';
			elseif($_GET['list']=='noc') $_SESSION['payments_list'] = 'not_confirmed';
			elseif($_GET['list']=='all') $_SESSION['payments_list'] = 'all';
			else $_SESSION['payments_list'] = 'active';
		}
		
		if(!isset($_SESSION['payments_list'])) $_SESSION['payments_list'] = 'active';
		
		//постраничный переход
		$total_items = mysql_fetch_array($this->mysqlQuery("
		SELECT COUNT(id) as total 
		FROM `ccl_".ACCOUNT_SUFFIX."payments` WHERE 1"));
		
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items['total']);
		}
		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."payments.*, ccl_".ACCOUNT_SUFFIX."customers.name, ccl_".ACCOUNT_SUFFIX."cars.model
		FROM `ccl_".ACCOUNT_SUFFIX."payments` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` 
		ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."payments.client)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
		ON ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."payments.car";
		
		$filter = '';
		if(isset($_GET['filter']))
		{
			if($_POST['searchClient']!='')
			{
					$filter = "AND client = '".mysql_real_escape_string($_POST['searchClient'])."'";
			}
			if(intval($_POST['searchCar'])>0) {
				$filter .= " AND ccl_".ACCOUNT_SUFFIX."cars.id = '".intval($_POST['searchCar'])."'";
			}
		}
		
		switch ($_SESSION['payments_list']) {
			case 'active': $st_switch = " status = '1'";
			break;
			case 'not_confirmed': $st_switch = " status = '0'";
			break;
			case 'all': $st_switch = " (status = '1' OR status = '0')";
			break;
		}
		
		$request.=" WHERE".$st_switch.$filter;
		
		if($filter!='') $pages['qlimit'] = '';
		
		//задаем параметры таблицы
		$cols[1] = array('name' => 'date', 		'caption' => 'Дата', 		'width' => '80');
		$cols[2] = array('name' => 'name', 		'caption' => 'Клиент', 		'width' => '200');
		$cols[3] = array('name' => 'model',		'caption' => 'Автомобиль',	'width' => '200');
		$cols[4] = array('name' => 'amount', 	'caption' => 'Сумма', 		'width' => '100');
		$cols[5] = array('name' => 'comment', 	'caption' => 'Комментарий', 'width' => '');
		
		$customers = $this->getCustomersList();
		$num = mysql_num_rows($customers);
		$j=1;
		if(!isset($_POST['searchClient'])) $customers_list = '<option value="" selected="selected"> - - - </option';
		else $customers_list = '<option value=""> - - - </option>';
		while ($j<=$num)
		{
			$line = mysql_fetch_array($customers);
			$customers_list .= '
			<option value="'.$line['id'].'"';
			if($_POST['searchClient']==$line['id']) $customers_list .= ' selected="selected"';
			$customers_list .= '>'.$line['name'].'</option>';
			$j++;
		}
		
		if(intval($_POST['searchClient'])!=0) {
			$cars_filter = " WHERE `buyer` = '".intval($_POST['searchClient'])."' ";
		}
		else $cars_filter = '';
		
		$cars = $this->mysqlQuery("SELECT id, model, frame
		FROM `ccl_".ACCOUNT_SUFFIX."cars`".$cars_filter."
		ORDER BY `model` DESC");
		
		$cars_list = '<select name="searchCar" style="width:240px;">
		<option value="0"> - - - </option>
		';
		while($line=mysql_fetch_array($cars)) {
			$cars_list .= '
			<option value="'.$line['id'].'"';
			if(intval($_POST['searchCar'])==$line['id']) $cars_list .= ' selected="selected"';
			$cars_list .= '>'.$line['model'].' - '.$line['frame'].'</option>';
		}
		$cars_list .= '</select>';
		
		
		$this->page.='<div class="location"><table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td>Платежи | <a href="'.$this->root_path.'?mod=payments&sw=form&add">добавить</a></td>
		<td>'.$this->switchPaymentsList().'</td>
		<td width="350" align="right"><form method="post" action="'.$this->root_path.'?mod=payments&filter" style="margin:0px;" class="myForm">
		<table>
		<tr><td align="right">клиент: <select name="searchClient" style="width:240px;">
		'.$customers_list.'
		</select></td></tr>
		<tr><td align="right">
		авто: '.$cars_list.'
		</td>
		<td rowspan="2"><input type="submit" value="найти" style="width:40px;">
		</td></tr></table>
		</form></td></tr></table>
		</div>';
		
		$module = 'payments';
		$item = 'payment';
		$list = 'payments';
		
		$this->page .= $this->buildList($request, $cols, $list, $module, $item, $pages['qlimit']);
		
		if($filter == '')
		{
			$this->page .= $pages['print'];
		}
		
	}
	
	function switchPaymentsList() {
		$sw = $_SESSION['payments_list'];
		$out = '
		<div class="tabs'.($sw=='active'?' tsel':'').'" style="width:80px;" onclick="document.location=\''.$this->root_path.'?mod=payments&list=act\'">активные</div>
		<div style="width:150px;" class="tabs'.($sw=='not_confirmed'?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=payments&list=noc\'">неутвержденные</div>
		<div style="width:60px;" class="tabs'.($sw=='all'?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=payments&list=all\'">все</div>';
		return $out;
		
	}
}

?>