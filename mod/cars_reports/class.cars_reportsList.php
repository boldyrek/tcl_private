<?

class ReportsList extends Proto {
	
	private $totals;
	private $class = "rowA";
	
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
				
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/class.search.php');
		$search = new listSearch();
	
		if(isset($_GET['filter'])) {
			if($_POST['searchNumber']!='') {
					$search->insert('%'.mysql_real_escape_string($_POST['searchNumber']).'%', 'data');
					$search->insert("`frame` LIKE ", 'filter');
				}
				
			if($_POST['searchClient']!='') {
				$search->insert(intval($_POST['searchClient']), 'data');
				$query = "`buyer` = ";
				$search->insert($query, 'filter');
			}
		}
		//сортировка
		$order_list = $this->defineSort('sort_cars_reports', 'ccl_'.ACCOUNT_SUFFIX.'cars.id DESC'); //добавляем сортировку в запрос
		$this->sortDeco('sort_cars_reports'); //выводим указатель того, что сейчас сортируется и направление сортировки
		
		$filter_made = $search->makeFilter();
		if($filter_made!='') $local_filter = "WHERE (ccl_".ACCOUNT_SUFFIX."cars.type = 1 or ccl_".ACCOUNT_SUFFIX."cars.type = 2) AND ".$filter_made; else $local_filter = "WHERE (ccl_".ACCOUNT_SUFFIX."cars.type = 1 or ccl_".ACCOUNT_SUFFIX."cars.type = 2)";
		
		$request = "SELECT ccl_".ACCOUNT_SUFFIX."cars.*, ccl_".ACCOUNT_SUFFIX."customers.name
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		LEFT JOIN ccl_".ACCOUNT_SUFFIX."customers
		ON ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."cars.buyer
		".$local_filter." ORDER BY ".$order_list;
	
		$list = $this->mysqlQuery($request);
		
		$cars = '
		<script type="text/javascript" src="/js/jquery.js"></script>

		<script>
		var cache = new Array();
		function getCarInfo(car) {
			showLayer(car);
			
			if(cache[car]==undefined) {
			$.get("/?mod=cars_reports&sw=info", { car: car },
				  function(data){
				   	fillLayer(car, data);
				   	cache[car] = data;
				  });
			}
			else fillLayer(car, cache[car]);
		}
		
		function showLayer(id) {		
			var layeron = document.getElementById("carinfo"+id);
			layeron.innerHTML = "'.$this->translate->_('загрузка').'";
			layeron.className = "carInfoLayer";	
		}
		
		function fillLayer(car, data) {
			var layerup = document.getElementById("carinfo"+car);
			layerup.innerHTML = data;
		}
				
		function hideLayer(id) {
			if(id!="") {
				var layer = document.getElementById("carinfo"+id);
				layer.className = "hiddenLayer";
			}
		}
		</script>
		<table cellpadding="3" cellspacing="1">
			<tr class="title sortButtons">
			'.$this->sorterTD('cars_reports', 'model', $this->translate->_('автомобиль'), '240').'
			'.$this->sorterTD('cars_reports', 'frame', $this->translate->_('VIN'), '100').'
			'.$this->sorterTD('cars_reports', 'name', $this->translate->_('покупатель'), '200').'
			'.$this->sorterTD('cars_reports', 'total', $this->translate->_('по договору'), '80').'
			<td width="60">'.$this->translate->_('оплачено').'</td>
			<td width="60">'.$this->translate->_('остаток').'</td>
			'.$this->sorterTD('cars_reports', 'invoice', $this->translate->_('инвойс'), '60').'
			<td width="80">'.$this->translate->_('остаток по переводу').'</td>
			<td width="80">'.$this->translate->_('прибыль').'</td>
		  </tr>';
		
		while($line = mysql_fetch_array($list)) {
		
			$cars .= $this->car($line);

		}
		$this->page .= '<div style="padding:20px;background-color:#fff;">'.$this->filterForm().$cars.$this->makeTotalsRow().'</table></div>';
	}
	
	
	function car($info) {
		
		if($info['id']!=0) {
			$paid = $this->getPayments($info['id']);
			$left = $info['invoice']-$paid;
			if($left<0) $left = 0;
			$profit = $paid - $info['invoice'];
			if($profit<0 or $info['invoice']==0) $profit = 0;
			//if($left>0 or $info['invoice']==0) {
				$this->totals['cars']++;
				$this->totals['total'] = $this->totals['total'] + $info['total'];
				$this->totals['paid'] = $this->totals['paid'] + $paid;
				$this->totals['total-paid'] = $this->totals['total-paid'] + ($info['total']-$paid);
				$this->totals['invoice'] = $this->totals['invoice'] + $info['invoice'];
				$this->totals['left'] = $this->totals['left'] + $left;
				$this->totals['profit'] = $this->totals['profit'] + $profit;
				$left_in_contract = $info['total']-$paid;
				if($left_in_contract<0) $left_in_contract = 0;
				return '
				<tr class="vlines '.$this->class.'" id="car_row_'.$info['id'].'">
					<td onmouseover="getCarInfo(\''.$info['id'].'\'); document.getElementById(\'car_row_'.$info['id'].'\').className=\'vlines rowA hovered\';"  onmouseout="hideLayer(\''.$info['id'].'\');  document.getElementById(\'car_row_'.$info['id'].'\').className=\'vlines '.$this->class.'\';"><span id="carinfo'.$info['id'].'" class="hiddenLayer">'.$info['id'].'</span> &nbsp; <a href="/?mod=cars&sw=form&car_id='.$info['id'].'">'.(substr($info['model'],0,30)).'</a></td>
					<td style="font-family:monospace;">'.substr($info['frame'],0,3).'...'.substr($info['frame'],strlen($info['frame'])-8,8).'</td>
					<td>'.(substr($info['name'],0,25)).'...</td>
					<td align="right">'.$info['total'].'</td>
					<td align="right">'.$paid.'</td>
					<td align="right" style="color:#f00">'.$left_in_contract.'</td>
					<td align="right">'.$info['invoice'].'</td>
					<td align="right" style="color:#2a2">'.$left.'</td>
					<td align="right" style="color:#2a2">'.$profit.'</td>
				</tr>';	
				if ($this->class =="rowA") $this->class="rowA rowB"; else $this->class="rowA";
			//}
		}
	}
	
	function getPayments($car) {
		$paid = mysql_fetch_array($this->mysqlQuery("
		SELECT SUM(amount) as amount FROM `ccl_".ACCOUNT_SUFFIX."payments`
		WHERE `car` = '".intval($car)."'"));
		return $paid['amount'];
	}
	
	function makeTotalsRow() {
		return '<tr class="vlines rowB">
					<td>ВСЕГО</td>
					<td>'.$this->totals['cars'].' '.$this->translate->_('автомобилей').'</td>
					<td>&nbsp;</td>
					<td align="right">'.$this->totals['total'].'</td>
					<td align="right">'.$this->totals['paid'].'</td>
					<td align="right" style="color:#f00">'.$this->totals['total-paid'].'</td>
					<td align="right">'.$this->totals['invoice'].'</td>
					<td align="right" style="color:#2a2">'.$this->totals['left'].'</td>
					<td align="right" style="color:#2a2">'.$this->totals['profit'].'</td>
				</tr>';	
	}
	
	function filterForm() {
		$customers = $this->getCustomersList();
		$customers_list = buildSelect($customers, 'searchClient', $_POST['searchClient'], ' - - - ', '8');
		
		return '<form name="searchFilter" method="post" action="'.$this->root_path.'?mod=cars_reports&filter" class="smallForm">
		<table border="0" cellspacing="0" cellpadding="0" class="title noborder" style="border:0px;">
		      <tr>
		        <td width="130">'.$this->translate->_('Поиск по VIN коду').':&nbsp;</td>
		        <td width="90"><input type="text" name="searchNumber" value="'.$_POST['searchNumber'].'" style="width:80px"></td>
		        <td width="100" nowrap>'.$this->translate->_('по владельцу').'</td>
		        <td width="200" align="left">'.$customers_list.'</td>
		        <td width="120" align="center"><input name="submit" type="submit" style="width:100px;" value="'.$this->translate->_('найти').'"></td>
          </tr>
		    </table>
		</form>';
	}
}

?>