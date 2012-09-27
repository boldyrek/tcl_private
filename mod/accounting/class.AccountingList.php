<?

class AccountingList extends Proto {
	
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
		
/*		if(isset($_GET['list'])) {
			if($_GET['list']=='act') $_SESSION['expenses_list'] = 'active';
			elseif($_GET['list']=='noc') $_SESSION['expenses_list'] = 'not_confirmed';
			elseif($_GET['list']=='all') $_SESSION['expenses_list'] = 'all';
			else $_SESSION['expenses_list'] = 'active';
		}
		
		if(!isset($_SESSION['expenses_list'])) $_SESSION['expenses_list'] = 'active';*/
		
		//постраничный переход
		$total_items = mysql_fetch_array($this->mysqlQuery("
		SELECT COUNT(id) as total 
		FROM `ccl_".ACCOUNT_SUFFIX."accounting` WHERE 1"));
		
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items['total']);
		}
		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."accounting.*, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.frame, ccl_".ACCOUNT_SUFFIX."usrs.log_name
		FROM `ccl_".ACCOUNT_SUFFIX."accounting` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
		ON ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."accounting.car
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."usrs`
		ON ccl_".ACCOUNT_SUFFIX."usrs.id = ccl_".ACCOUNT_SUFFIX."accounting.user_added";
		
		$filter = '';
		if(isset($_GET['filter']))
		{
			if(intval($_POST['searchCar'])>0) {
				$filter .= " ccl_".ACCOUNT_SUFFIX."cars.id = '".intval($_POST['searchCar'])."' AND ccl_".ACCOUNT_SUFFIX."accounting.type = 1";
				//$filter .= " AND ccl_cars.id = '".intval($_POST['searchCar'])."'";
				$request.=" WHERE".$filter;
			}
		}
		
/*		switch ($_SESSION['expenses_list']) {
			case 'active': $st_switch = " status = '1'";
			break;
			case 'not_confirmed': $st_switch = " status = '0'";
			break;
			case 'all': $st_switch = " (status = '1' OR status = '0')";
			break;
		}
		$request.=" WHERE".$st_switch.$filter;
		*/
		
		if($filter!='') $pages['qlimit'] = '';
		
		//задаем параметры таблицы
		$cols[1] = array('name' => 'date', 		'caption' => $this->translate->_('Дата'), 		'width' => '70');
		$cols[2] = array('name' => 'model',		'caption' => $this->translate->_('Автомобиль'),	'width' => '230');
		$cols[3] = array('name' => 'frame', 	'caption' => $this->translate->_('VIN'), 		'width' => '75');
		$cols[4] = array('name' => 'amount', 	'caption' => $this->translate->_('Сумма'), 		'width' => '75');
		$cols[5] = array('name' => 'log_name',	'caption' => $this->translate->_('Добавил(а)'),	'width' => '80');
//		$cols[6] = array('name' => 'purpose', 	'caption' => 'Назначение',	'width' => '80');
		$cols[6] = array('name' => 'comment', 	'caption' => $this->translate->_('Комментарий'), 'width' => '230');
		$cols[7] = array('name' => 'signer', 	'caption' => $this->translate->_('Подтвердил(а)'),'width' => '80');
		$cols[8] = array('name' => 'paid',		'caption' => $this->translate->_('Оплачено'),	'width' => '');
		
/*		$customers = $this->getCustomersList();
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
		else $cars_filter = '';*/
		
		$cars = $this->mysqlQuery("SELECT id, model, frame FROM `ccl_".ACCOUNT_SUFFIX."cars` ORDER BY `id` DESC");
		
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
		<tr><td>'.$this->translate->_('Платежи | Расходы').'&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=accounting&sw=form&add">'.$this->translate->_('добавить').'</a></td>
		<td>&nbsp;</td>
		<td width="350" align="right"><form method="post" action="'.$this->root_path.'?mod=accounting&filter" style="margin:0px;" class="myForm">
		<table>
		<tr><td align="right">
		'.$this->translate->_('автомобиль').': '.$cars_list.'
		</td>
		<td rowspan="2"><input type="submit" value="'.$this->translate->_('найти').'" style="width:40px;">
		</td></tr></table>
		</form></td></tr></table>
		</div>';
		
		$module = 'accounting';
		$item = 'accounting';
		$list = 'accounting';
		
		$this->page .= $this->buildList($request, $cols, $list, $module, $item, $pages['qlimit']);
		
		if($filter == '')
		{
			$this->page .= $pages['print'];
		}
		
	}
	
	function switchPaymentsList() {
/*		$sw = $_SESSION['expenses_list'];
		$out = '
		<div class="tabs'.($sw=='active'?' tsel':'').'" style="width:80px;" onclick="document.location=\''.$this->root_path.'?mod=expenses&list=act\'">активные</div>
		<div style="width:150px;" class="tabs'.($sw=='not_confirmed'?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=expenses&list=noc\'">неутвержденные</div>
		<div style="width:60px;" class="tabs'.($sw=='all'?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=expenses&list=all\'">все</div>';
		return $out;
		*/
	}
}

?>