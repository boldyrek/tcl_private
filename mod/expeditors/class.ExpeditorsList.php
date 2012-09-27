<?

class ExpeditorsList extends Proto {
	
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
		// на случай, если пользователь поставщик, представитель EastPoint11
		if($_SESSION['user_type']=='5') $this->redirect($this->root_path.'?mod=expeditors&sw=form&exp_id=4');
		
		//постраничный переход
		$total_items = mysql_num_rows($this->mysqlQuery("SELECT `id` FROM `ccl_".ACCOUNT_SUFFIX."expeditors` WHERE 1"));
		if($total_items>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['ref'], $total_items);
		}
		
		$request = 'SELECT id,name,balance 
		FROM `ccl_'.ACCOUNT_SUFFIX.'expeditors` 
		WHERE 1 ORDER BY `name` ASC'.$pages['qlimit'];
		$content = $this->mysqlQuery($request);
		
		$this->page .= '<div class="location">'.$this->translate->_('Экспедиторы').' | <a href="'.$this->root_path.'?mod=expeditors&sw=form&add">'.$this->translate->_('добавить').'</a></div>';
		$num = mysql_num_rows($content);
		if ($num<1) $this->page.='<div>'.$this->translate->_('Не найдено ни одной записи.').'</div>';
		else {
			$i=1; 
			$class="rowA rowB";
			
			$this->page .= '<table width="930" border="0" cellspacing="0" cellpadding="0" class="list">
				 <tr class="title">
				<td width="200">'.$this->translate->_('Имя / название экспедитора').'</td>
				<td>'.$this->translate->_('баланс').'</td>
			  </tr>';
			while ($i<=$num)
			{
				$line = mysql_fetch_array($content);
			$this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" onclick="document.location=\''.$this->root_path.'?mod=expeditors&sw=form&exp_id='.$line['id'].'\'">
				<td>'.$line['name'].'</td>
				<td>'.$line['balance'].'</td>
				</tr>';
				$i++;
				if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
			}
			
			$this->page .= '</table>
			'.$pages['print'];
			}
	}
	
}

?>