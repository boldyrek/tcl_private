<?

class PublicPrivate extends Proto {
	
	function makePage() {	
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->moduleContent();
			$this->page .= $this->module_content;
		}
		else Proto::redirect('/');
		
		$this->page .= $this->templates['footer'];
		
		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		$this->errorsPublisher();
		$this->publish();
		
	}
	
	private function moduleContent() {
			if($this->exists($_GET['sw'])) {
			switch($_GET['sw']) {
				default:
					$this->drawContent();
					break;
			}
		}
		else {
			$this->drawContent();
		}
	}
	
	private function drawContent() {
		$request = "SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE `id` = '".$_SESSION['user_id']."'";
		$content = $this->mysqlQuery($request);
		$line = mysql_fetch_array($content);
		
			$this->page .= '
			<div class="cont_car">
			<h3>'.$this->translate->_('Личные данные').'</h3>
			<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
			  <tr>
				<td width="83" align="right" class="title">'.$this->translate->_('имя').'</td>
				<td width="262" class="rowA title">'.$line['name'].'</td>
				<td width="80" align="right" class="title">'.$this->translate->_('контактные данные').'</td>
				<td class="rowA title">'.$line['contacts'].'</td>
			  </tr>
			  <tr>
				<td align="right" class="title rowB">'.$this->translate->_('адрес').'</td>
				<td class="rowA rowB title">'.$line['address'].'</td>
				<td align="right" class="title rowB"><a href="'.$this->root_path.$this->user_folder.'/?mod=balance">'.$this->translate->_('баланс').'</a></td>
				<td class="rowA rowB title"><b><span style="color:#'.($line['balance']<0 ? 'f54' : '4a4').'">'.$line['balance'].'</span></b></td>
			  </tr>
			  <tr>
				<td align="right" class="title">'.$this->translate->_('паспорт').'</td>
				<td class="rowA title">'.$line['passport'].'</td>
				<td align="right" class="title">'.$this->translate->_('к оплате').'</td>
				<td class="rowA title"><b><span style="color:#22e">'.$line['balance'].'</span></b></td>
			  </tr>
			  <tr>
				<td align="right" class="title rowB" colspan="4">&nbsp;</td>
			  </tr>
			</table>
		';
	}	// <span style="color:#aaa">'.$this->translate->_('[временно не доступно]').'</span>

}
?>