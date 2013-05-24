<?

class ReportsList extends Proto {
	
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
			if(strlen($_POST['searchNumber'])>5) {
					$car =$this->mysqlQuery("SELECT `container` FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `frame` LIKE '%".intval($_POST['searchNumber'])."%'");
					while($line = mysql_fetch_array($car)){
						if($line['container']!=0) {
							$search->insert($line['container'], 'data');
							$search->insert("`id` = ", 'filter');
						}
					}
			
				}

			if($_POST['searchContainer']!='') {
					$search->insert('%'.trim(mysql_real_escape_string($_POST['searchContainer'])).'%', 'data');
					$search->insert("`number` LIKE ", 'filter');
				}
				
		}
		
		$filter_made = $search->makeFilter();
		if($filter_made!='') $local_filter = "WHERE ".$filter_made; else $local_filter = "WHERE 1";
		
		$containers = $this->mysqlQuery("SELECT *
		FROM `ccl_".ACCOUNT_SUFFIX."containers`
		".$local_filter."
		ORDER BY arrived ASC, sent ASC, number ASC");
		
		$list = '';
		
		while($line = mysql_fetch_array($containers)) {
			$container = '';
			$cars = '<table cellpadding="3" cellspacing="1">
									<tr class="vlines rowB">
										<td width="200">'.$this->translate->_('автомобиль').'</td>
										<td width="80">'.$this->translate->_('VIN').'</td>
										<td width="200">'.$this->translate->_('покупатель').'</td>
										<td width="80">'.$this->translate->_('по договору<br>
										(общая сумма для клиента)').' </td>
										<td width="80">'.$this->translate->_('Фактически оплачено клиентом').'</td>
										<td width="80">'.$this->translate->_('инвойс').'</td>
										<td width="80">'.$this->translate->_('остаток по переводу').'</td>
										<td width="200">'.$this->translate->_('примечание').'</td>
										
									</tr>';
			$car_num = 0;
			for ($i=1; $i<5; $i++) {
				if($line['slot'.$i]!=0) {		
					$cars .= $this->car($this->carData($line['slot'.$i]));
					$car_num++;
				}
			}
			if($car_num>0) $list .= $this->container($line, $cars.'</table>');
		}
		$this->page .= '<div style="padding:20px;background-color:#fff;">'.$this->filterForm().$list.'</div>';
	}
	
	function carData($id) {
		$sql = "SELECT ccl_".ACCOUNT_SUFFIX."cars.*, ccl_".ACCOUNT_SUFFIX."customers.name
		FROM `ccl_".ACCOUNT_SUFFIX."cars`
		LEFT JOIN ccl_".ACCOUNT_SUFFIX."customers
		ON ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."cars.buyer
		WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".$id."'";
		
		
		$data = @mysql_fetch_array($this->mysqlQuery($sql));
		return $data;
	}
	
	function container($info, $cars) {
		if($info['arrived']==1) $green = ' greenTR';
		else $green = '';
		return '
		<table ><tr class="rowA'.$green.'"><td width="140">'.$this->translate->_('контейнер').': <a href="/?mod=containers&sw=form&cont_id='.$info['id'].'"><b>'.$info['number'].'</b></a>
		<table>
			<tr><td>'.$this->translate->_('Отправка').'</td><td>'.$info['sent'].'</td></tr>
			<tr><td>'.$this->translate->_('Порт').'</td><td>'.$info['portdate'].'</td></tr>
			<tr><td>'.$this->translate->_('Ж/Д').'</td><td>'.$info['rail'].'</td></tr>
			<tr><td>'.$this->translate->_('Бишкек').'</td><td>'.$info['bishkek'].'</td></tr>
		</table>
		
		</td>
		<td>'.$cars.'</td>
		
		</tr>
</table>
<br><br>
';
	}
	
	function car($info) {
		
		if($info['id']!=0) {
			$paid = $this->getPayments($info['id']);
			return '
			<tr class="vlines rowA">
				<td>'.(substr($info['model'],0,30)).'</td>
				<td style="font-family:monospace;">'.substr($info['frame'],0,3).'...'.substr($info['frame'],strlen($info['frame'])-8,8).'</td>
				<td>'.(mb_substr($info['name'],0,25)).'...</td>
				<td align="right">'.$info['total'].'</td>
				<td align="right">'.$paid.'</td>
				<td align="right">'.$info['invoice'].'</td>
				<td align="right">'.($paid-$info['invoice']).'</td>
				<td align="right">'.$info['notice'].'</td>
			</tr>';	
		}
	}
	
	function getPayments($car) {               
                $paid = mysql_fetch_array($this->mysqlQuery("
			SELECT SUM(amount) as amount FROM `ccl_".ACCOUNT_SUFFIX."accounting`
			WHERE `car` = '".intval($car)."' AND `type`=1"));                
                /*
		$paid = mysql_fetch_array($this->mysqlQuery("
		SELECT SUM(amount) as amount FROM `ccl_".ACCOUNT_SUFFIX."payments`
		WHERE `car` = '".intval($car)."'"));
                 * 
                 */
		return intval($paid['amount']);
	}
	
	function filterForm() {
		$customers = $this->getCustomersList();
		$customers_list = buildSelect($customers, 'searchClient', $_POST['searchClient'], ' - - - ', '8');
		
		return '<form name="searchFilter" method="post" action="'.$this->root_path.'?mod=con_reports&filter" class="smallForm">
		<table border="0" cellspacing="0" cellpadding="0" class="title noborder" style="border:0px;">
		      <tr>
		        <td width="180">'.$this->translate->_('Поиск по номеру контейнера').':&nbsp;</td>
		        <td width="90"><input type="text" name="searchContainer" value="'.$_POST['searchContainer'].'" style="width:80px"></td>        
		        <td width="80">'.$this->translate->_('по VIN коду').':&nbsp;</td>
		        <td width="90"><input type="text" name="searchNumber" value="'.$_POST['searchNumber'].'" style="width:80px"></td>
		        <td width="150" align="center"><input name="submit" type="submit" style="width:100px;" value="'.$this->translate->_('найти').'"></td>
          </tr>
		    </table>
		</form>';
	}
}

?>