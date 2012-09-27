<?

class UsersList extends Proto {
	
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
				if($_POST['searchName']!='') {
					$search->insert('%'.mysql_real_escape_string($_POST['searchName']).'%', 'data');
					$search->insert("`name` LIKE ", 'filter');
				}
					
				if($_POST['searchLogin']!='') {
					$search->insert('%'.mysql_real_escape_string($_POST['searchLogin']).'%', 'data');
					$search->insert("`log_name` LIKE ", 'filter');
				}
			}
			
			$search->template = '
			<form action="'.$this->root_path.'?mod=users&filter" method="post" name="srchUsers" class="myForm">
			<table class="location_tab" width="970" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="200">'.$this->translate->_('Пользователи').' | <a href="'.$this->root_path.'?mod=users&sw=form&add">'.$this->translate->_('добавить').'</a></td>
				<td align="right">'.$this->translate->_('искать по имени').'&nbsp;&nbsp; </td>
				<td width="120"><input type="text" name="searchName" value="'.$_POST['searchName'].'"> </td>
				<td width="80" align="right">'.$this->translate->_('по логину:').'&nbsp;&nbsp;</td>
				<td width="100"><input type="text" name="searchLogin" value="'.$_POST['searchLogin'].'"></td>
				<td width="100"><input type="submit" value="'.$this->translate->_('найти').'"></td>
			</tr>
			</table>
			</form';
			
			//постраничный переход
			$total_items = mysql_fetch_array($this->mysqlQuery("SELECT COUNT(`id`) as total FROM `ccl_".ACCOUNT_SUFFIX."usrs` WHERE type!='7'"));
			
			if($total_items['total']>$per_page)
			{
				$pages = $this->pageBrowse(intval($_GET['page']), $_GET['mod'], $total_items['total']);
			}
			
			//сортировка
			$order_list = $this->defineSort('sort_users', 'ccl_'.ACCOUNT_SUFFIX.'usrs.log_name DESC'); //добавляем сортировку в запрос
			$this->sortDeco('sort_users'); //выводим указатель того, что сейчас сортируется и направление сортировки, используется функцией sorterTD
			$sfilter = $search->makeFilter();
			if($sfilter!='') $sfilter.=' AND ';
			
			$request = "SELECT ccl_".ACCOUNT_SUFFIX."usrs.*, ccl_".ACCOUNT_SUFFIX."customers.name as customer, ccl_".ACCOUNT_SUFFIX."transporters.name as transporters, ccl_".ACCOUNT_SUFFIX."expeditors.name as expeditors
			FROM `ccl_".ACCOUNT_SUFFIX."usrs` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` 
			ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."usrs.u_id)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."transporters` 
			ON (ccl_".ACCOUNT_SUFFIX."transporters.id = ccl_".ACCOUNT_SUFFIX."usrs.t_id)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."expeditors` 
			ON (ccl_".ACCOUNT_SUFFIX."expeditors.id = ccl_".ACCOUNT_SUFFIX."usrs.e_id) 
			WHERE ".$sfilter."type!='7' ORDER BY ".$order_list.$pages['qlimit'];
			$users = $this->mysqlQuery($request);
			$num = mysql_num_rows($users);
				
			$this->page.= '<div class="location" style="width:970px">'.$search->parser(); // указатель списка, форма поиска
			
			$this->page .= '</div><table width="970" border="0" cellspacing="0" cellpadding="0" class="list" style="clear:both;">
			 	 <tr class="title">
			    '.$this->sorterTD('users', 'log_name', $this->translate->_('логин'), '120').'
			    '.$this->sorterTD('users', 'type', $this->translate->_('тип'), '100').'
			    '.$this->sorterTD('users', 'customer', $this->translate->_('ФИО клиента'), '300').'
			   	<td>&nbsp;</td>
			  </tr>';
			  
			$class="rowA rowB";  
			$i=1; 
			while ($i<=$num)
			{
				$line = mysql_fetch_array($users);
				$this->page .= '
				<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$this->root_path.'?mod=users&sw=form&id='.$line['id'].'\'">
					<td>'.$line['log_name'].'</td>
					<td>'.$this->user_types[$line['type']].'</td>
					<td>'.$line['customer'].$line['transporters'].$line['expeditors'].'&nbsp;</td>
					<td>&nbsp;</td>
					</tr>';
					$i++;
					if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
			}
			
			$this->page .= '</table>';
			if($sfilter=='') $this->page.=$pages['print'];
	}
	
}

?>