<?
require($_SERVER['DOCUMENT_ROOT'].'/bin/balance.php');

class TransportersList extends Proto {
	
	public function drawContent() {
		$this->page = $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->publish();
		$this->errorsPublisher();
	}
	
	function getContent() {
		
		if(!$_SESSION['fvt']) $update = true;
		else $update = false;
			
		//постраничный переход
		$total_items = mysql_num_rows($this->mysqlQuery("SELECT count(`id`) AS total 
		FROM `ccl_".ACCOUNT_SUFFIX."transporters` WHERE 1"));
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items['total']);
		}
		
		$content = $this->mysqlQuery("
		SELECT id,name,balance 
		FROM `ccl_".ACCOUNT_SUFFIX."transporters` 
		WHERE 1 
		ORDER BY `name` ASC".$pages['qlimit']);
			
		$this->page .= '<div class="location">'.$this->translate->_('Транспортники').' | <a href="'.$this->root_path.'?mod=transporters&sw=form&add">'.$this->translate->_('добавить').'</a></div>';
		$num = mysql_num_rows($content);
		if ($num<1) $this->page.='<div>'.$this->translate->_('Не найдено ни одной записи.').'</div>';
		else {
			$i=1; 
			$class="rowA rowB";
			
			$this->page .= '<table width="930" border="0" cellspacing="0" cellpadding="0" class="list">
				 <tr class="title">
				<td width="200">'.$this->translate->_('Имя / название транспортника').'</td>
				<td>'.$this->translate->_('баланс').'</td>
			  </tr>';
			while ($i<=$num)
			{
				$line = mysql_fetch_array($content);
				if($update) {
					//echo $line['id'].' '; // Вывод id-шек, возможно было сделанно для дебага
					updateTransporterBalance(intval($line['id']));
				}
				$this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" onclick="document.location=\''.$this->root_path.'?mod=transporters&sw=form&sup_id='.$line['id'].'\'">
				<td>'.$line['name'].'</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;<b>'.$line['balance'].'</b></td>
				</tr>';
				$i++;
				if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
			}
			
			$this->page .= '</table>'.$pages['print'];
			$_SESSION['fvt'] = 1;
		}
		
	}
	
}

?>