<?

class ClientsList extends Proto {
	
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
		if(isset($_GET['mod'])) $mod = $_GET['mod'];
		else $mod = '';
		
		$clList = $this->getCustomersList();
		$total_items = mysql_num_rows($clList);
		//постраничный переход
		if($total_items>$this->per_page)
		{
			if(isset($_GET['page'])) $page = $_GET['page'];
			else $page = '';
			$pages = $this->pageBrowse($page, $mod, $total_items);
		}
		
		$request = '
		SELECT id,name,balance,real_balance
		FROM `ccl_'.ACCOUNT_SUFFIX.'customers`';
		
		$filter = '';
		if(isset($_GET['firstLetter'])) $f_letter = $_GET['firstLetter'];
		else $f_letter = '';
		
		if(isset($_GET['filter'])) {
		
			if($f_letter!='' and $_POST['textName']=='') {
				$filter = " WHERE name LIKE ('".mysql_real_escape_string($_GET['firstLetter'])."%')";
			}	
			if($f_letter!='' and $_POST['textName']!='') {
				$filter = " WHERE name LIKE('%".mysql_real_escape_string($_POST['textName'])."%') and name LIKE ('".mysql_real_escape_string($_GET['firstLetter'])."%')";
			}
			if($f_letter=='' and $_POST['textName']!='') {
				$filter = " WHERE name LIKE ('%".mysql_real_escape_string($_POST['textName'])."%')";
			}	
		}
			
		$request .= $filter;
		
			
		//задаем параметры таблицы
		$cols[1] = array('name' => 'name', 			'caption' => 'ФИО', 		'width' => '300');		
		
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
		<tr><td width="130">Клиенты </td>
		<td align="left" width="600" style="font-size:11px;" height="30"><div style="position:absolute; margin-top:-15px;">
		';
		foreach(array_unique($letters) as $v) {
			
			if($v=='А') $this->page .= '</div>
			<div style="position:absolute;margin-top:2px;">';
			
			if($f_letter==$v) $sw = 'class="letSelectHover"';
			else $sw = 'class="letSelect" onMouseOver="this.className = \'letSelectHover\'" onMouseOut="this.className= \'letSelect\'"';
			$this->page.='
			<div '.$sw.' onClick="document.location = \''.$this->root_path.'view/?mod=clients&firstLetter='.$v.'&filter\'">'.$v.'</div>';
			
		}
		
		if(isset($_POST['textName'])) $text_name = $_POST['textName'];
		else $text_name = '';
		
		$this->page .= '</div></td>
		<td width="150">
		<form action="'.$this->root_path.'view/?mod=clients&filter" method="post" style="margin:0px;" class="myForm">
		&nbsp;<input type="text" name="textName" style="width:100px;" value="'.$text_name.'">&nbsp; 
		<input type="submit" value="найти" style="width:40px;">
		</form></td></tr></table>
		</div>';	
			
		$module = 'clients';
		$item = 'customer_id';
		$list = 'clients';
		if($filter=='') $limit_for_pages = $pages['qlimit'];
		else $limit_for_pages = '';
		$this->page .= $this->buildList($request,$cols,$list,$module,$item,$limit_for_pages);
		
		$this->page .= '</table>';
		if($filter == '')
		{
			$this->page .= $pages['print'];
		}
		
	}
}

?>