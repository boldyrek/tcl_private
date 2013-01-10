<?

class PublicPayments extends Proto {
	
	function makePage() {	
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->moduleContent();
		}
		else Proto::redirect('/');
		
		$this->page .= $this->templates['footer'];
		
		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		$this->errorsPublisher();
		$this->publish();
		
	}
	
	private function moduleContent() {
		if(isset($_GET['sw'])) $switch = $_GET['sw'];
		else $switch = '';	
		if($this->exists($switch)) {
			switch($switch) {
				default:
					$this->drawList();
					break;
			}
		}
		else {
			$this->drawList();
		}
	}
	
	private function drawList() {
		$dealership = mysql_fetch_array($this->mysqlQuery("
		SELECT dealer FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE `id` = '".$_SESSION['user_id']."'"));
		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."accounting.*, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.frame 
		FROM `ccl_".ACCOUNT_SUFFIX."accounting` 
		RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
		ON ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."accounting.car
		WHERE (ccl_".ACCOUNT_SUFFIX."cars.buyer='".$_SESSION['user_id']."'";
		
		//выбираем платежи клиентов, если дилер
		if($dealership['dealer']=='1') {
			$clients = $this->mysqlQuery("
			SELECT id 
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			WHERE `mydealer` = '".$_SESSION['user_id']."' 
			AND `id` != '".$_SESSION['user_id']."'");
			if(mysql_num_rows($clients)!=0) {
				$num = mysql_num_rows($clients);
				$i=0;
				while($i<$num) {
					$line = mysql_fetch_array($clients);
					$request .= " OR ccl_".ACCOUNT_SUFFIX."cars.buyer = '".$line['id']."'";
					$i++;
				}
			}
		}
		$request .= ") AND ccl_".ACCOUNT_SUFFIX."accounting.type = 1 ORDER BY date DESC";
		$content = $this->mysqlQuery($request);
		
		$num = mysql_num_rows($content);
		if($num>0) {
			$class="rowA rowB";
			
			$this->page .= '
			<div class="location">'.$this->translate->_('Ваши платежи').'</div>
				<table width="920" border="0" cellspacing="0" cellpadding="0" class="list">
			 	 <tr class="title">
			    <td width="20">&nbsp;</td>
				<td width="100">'.$this->translate->_('дата').'</td>
			    <td width="100">'.$this->translate->_('сумма').'</td>
			    <td>'.$this->translate->_('автомобиль').'</td>
			    <td width="200">'.$this->translate->_('примечание').'</td>
				<td width="30">&nbsp;</td>
			  </tr>';
			  
			$i=1; 
			while ($i<=$num)
			{
				$line = mysql_fetch_array($content);
				$this->page .= '
				<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
					<td>&nbsp;</td>
					<td>'.$line['date'].'</td>
					<td>'.$line['amount'].'</td>
					<td>'.$line['model'].'-'.$line['frame'].'</td>
					<td>'.$line['comment'].'</td>
					<td>&nbsp;</td>
				</tr>';	// line[comment] -> '&nbsp;'.($line['client']!=$_SESSION['user_id']?' -> '.$line['name']:'').
					$i++;
					if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
			}
			
			$this->page .= '</table>';
		}
		else $this->page .= '<div class="notice">'.$this->translate->_('У вас пока нет платежей').'</div>';
		}
}

?>