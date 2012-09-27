<?

class ClientsList extends Proto {
	
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
		//постраничный переход
		$clList = $this->getCustomersList();
		$total_items = mysql_num_rows($clList);
		
		if($total_items>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items);
		}
		
		$request = '
		SELECT id,name,balance
		FROM `ccl_'.ACCOUNT_SUFFIX.'customers`';
		
		$filter = '';
		if(isset($_GET['filter'])) {
			if($_GET['firstLetter']!='' and $_POST['textName']=='') {
				$filter = " WHERE name LIKE ('".mysql_real_escape_string($_GET['firstLetter'])."%')";
			}	
			if($_GET['firstLetter']!='' and $_POST['textName']!='') {
				$filter = " WHERE name LIKE('%".mysql_real_escape_string($_POST['textName'])."%') and name LIKE ('".mysql_real_escape_string($_GET['firstLetter'])."%')";
			}
			if($_GET['firstLetter']=='' and $_POST['textName']!='') {
				$filter = " WHERE name LIKE ('%".mysql_real_escape_string($_POST['textName'])."%')";
			}	
		}
			
		$request .= $filter;
		
			
		//задаем параметры таблицы
		$cols[1] = array('name' => 'name', 			'caption' => $this->translate->_('ФИО'), 		'width' => '350');
		
		// скрытые поля для администраторов	
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') {
			$cols[2] = array('name' => 'balance', 		'caption' => $this->translate->_('баланс'), 	'width' => '');
		}
		
		
		$letters = array();
		
		$num = mysql_num_rows($clList);
		if($num>0) {
			$i = 0;
			while($i<$num) {
				$line = mysql_fetch_array($clList);
	
				$char = substr(stripslashes($line['name']), 0, 1);
	
				array_push($letters, $char);
				$i++;
			}
		}
		
		$this->page .= '<div class="location"><table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td width="130">'.$this->translate->_('Клиенты').' | <a href="'.$this->root_path.'?mod=clients&sw=form&add">'.$this->translate->_('добавить').'</a><br>
		<a href="'.$this->root_path.'?mod=recievers">'.$this->translate->_('Получатели').'</a></td>
		<td align="left" width="600" style="font-size:11px;" height="30" valign="top"><div style="position:absolute; margin-top:0px;">
		';
		foreach(array_unique($letters) as $v) {
			
			if($v=='А') $this->page .= '</div>
			<div style="position:absolute;margin-top:17px;">';
			
			if($_GET['firstLetter']==$v) $sw = 'class="letSelectHover"';
			else $sw = 'class="letSelect" onMouseOver="this.className = \'letSelectHover\'" onMouseOut="this.className= \'letSelect\'"';
			$this->page.='
			<div '.$sw.' onClick="document.location = \''.$this->root_path.'?mod=clients&firstLetter='.$v.'&filter\'">'.$v.'</div>';
			
		}
		$this->page .= '</div></td>
		<td width="220" align="right">
		<form action="'.$this->root_path.'?mod=clients&filter" method="post" style="margin:0px;" class="myForm">
		&nbsp;<input type="text" name="textName" style="width:150px;" value="'.$_POST['textName'].'">&nbsp; 
		<input type="submit" value="'.$this->translate->_('найти').'" style="width:40px;">
		</form></td></tr></table>
		</div>';
		
		//всего клиентов, баланс, к оплате
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') {
		
			$trequest = "
			SELECT COUNT(id) 
			AS total_clients 
			FROM `ccl_".ACCOUNT_SUFFIX."customers`";
			$totals = mysql_fetch_array($this->mysqlQuery($trequest));
			
			$totals['totalPayments'] = mysql_fetch_array($this->mysqlQuery("
			SELECT SUM(amount) 
			AS total 
			FROM `ccl_".ACCOUNT_SUFFIX."accounting` WHERE `status` = '1' AND `type` = '1'"));
			
			$totals['totalBalance'] = mysql_fetch_array($this->mysqlQuery("
			SELECT SUM(total) 
			AS total 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE 1"));
		
			$this->page .= '
			<div class="totals">
			<table width="100%"><tr>
			<td width="350">'.$this->translate->_('ВСЕГО').': '.$totals['total_clients'].'</td>
			<td >'.($totals['totalBalance']['total']-$totals['totalPayments']['total']).'</td>
			</tr>
			</table></div>';
		}
		
			
		$module = 'clients';
		$item = 'customer_id';
		$list = 'clients';
		
		$this->page .= $this->buildList($request,$cols,$list,$module,$item,$pages['qlimit']);
		
		$this->page .= '</table>';
		if($filter == '')
		{
			$this->page .= $pages['print'];
		}
		
	}
}

?>