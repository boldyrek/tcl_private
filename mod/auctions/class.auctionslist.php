<?

class auctionslist extends Proto {
	
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
			//настройки списка
			$item_link = $this->root_path.'?mod=auctions&sw=form&id='; //ссылка на форму редактирования
			$add_link = $this->root_path.'?mod=auctions&sw=form&add'; // добавление нового автомобиля
			
			
			$total_items = mysql_fetch_array($this->mysqlQuery("SELECT COUNT(id) AS total FROM ccl_".ACCOUNT_SUFFIX."auctions"));
		
			if($total_items['total']>$this->per_page)
			{
				$pages = $this->pageBrowse(mysql_real_escape_string($_GET['page']), mysql_real_escape_string($_GET['mod']), $total_items['total']);
			}
			
			//сортировка
			$order_list = $this->defineSort('sort_auctions', 'ccl_'.ACCOUNT_SUFFIX.'auctions.name ASC'); //добавляем сортировку в запрос
			$this->sortDeco('sort_auctions'); //выводим указатель того, что сейчас сортируется и направление сортировки
			
			//основной запрос в базу
			$request = "
			SELECT * FROM ccl_".ACCOUNT_SUFFIX."auctions".$local_filter." ORDER BY ".$order_list.$pages['qlimit'];
			
			$content = $this->mysqlQuery($request);
			$num = @mysql_num_rows($content);
			
			$this->page .= '
			<div class="location" style="width:970px">'.$this->translate->_('Аукционы').' &nbsp;|&nbsp; <a href="'.$this->root_path.'?mod=auctions&sw=form&add">'.$this->translate->_('Добавить').'</a>&nbsp;</div>
						
				<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
			 	<tr class="title sortButtons">
					'.$this->sorterTD('auctions', 'name', ''.$this->translate->_('Название').'','150').'
					'.$this->sorterTD('auctions', 'address', ''.$this->translate->_('адрес').'', '200').'
					'.$this->sorterTD('auctions', 'phones', ''.$this->translate->_('телефоны').'', '200').'
					'.$this->sorterTD('auctions', 'comment', ''.$this->translate->_('примечание').'', '').'
				</tr>';
			
			$class="rowA rowB"; 
			$i=1; 
			while ($i<=$num)
			{
				$line = mysql_fetch_array($content);
				$j=1;
				$this->page .= '
				<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$item_link.$line['id'].'\'">
						<td class="sm">'.$line['name'].'&nbsp;</td>
						<td class="sm">'.$line['address'].'&nbsp;</td>
						<td class="sm">'.$line['phones'].'&nbsp;</td>
						<td class="sm">'.$line['comment'].'&nbsp;</td>
					</tr>';
					$i++;
					if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
			}
			
			$this->page .= '</table>
			'.$pages['print'];
						
			// пустой список
			if($num == '0') $this->page .= '<div class="green">по вашему запросу ничего не найдено</div>';
		}
}

?>