<?

class RecieversList extends Proto {
	
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
		$clList = $this->mysqlQuery("SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."recievers`
		WHERE 1	ORDER BY `name` ASC");
		
		$total_items = mysql_num_rows($clList);
		
		if($total_items>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items);
		}
		
		$request = '
		SELECT id,name,phone,address,passport 
		FROM `ccl_'.ACCOUNT_SUFFIX.'recievers`';
		
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
		$cols[1] = array('name' => 'name', 				'caption' => $this->translate->_('ФИО'), 		'width' => '');
		$cols[2] = array('name' => 'phone', 			'caption' => $this->translate->_('телефон'), 	'width' => '100');
		$cols[3] = array('name' => 'address', 			'caption' => $this->translate->_('адрес'), 		'width' => '300');
		$cols[4] = array('name' => 'passport', 			'caption' => $this->translate->_('паспорт'), 	'width' => '120');
		
		
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
		<tr><td width="130">'.$this->translate->_('Получатели').' | <a href="'.$this->root_path.'?mod=recievers&sw=form&add">'.$this->translate->_('добавить').'</a></td>
		<td align="left" width="600" style="font-size:11px;" height="30" valign="top"><div style="position:absolute; margin-top:0px;">
		';
		foreach(array_unique($letters) as $v) {
			
			if($v=='А') $this->page .= '</div>
			<div style="position:absolute;margin-top:2px;">';
			
			if($_GET['firstLetter']==$v) $sw = 'class="letSelectHover"';
			else $sw = 'class="letSelect" onMouseOver="this.className = \'letSelectHover\'" onMouseOut="this.className= \'letSelect\'"';
			$this->page.='
			<div '.$sw.' onClick="document.location = \''.$this->root_path.'?mod=recievers&firstLetter='.$v.'&filter\'">'.$v.'</div>';
			
		}
		$this->page .= '</div></td>
		<td width="220" align="right">
		<form action="'.$this->root_path.'?mod=recievers&filter" method="post" style="margin:0px;" class="myForm">
		&nbsp;<input type="text" name="textName" style="width:150px;" value="'.$_POST['textName'].'">&nbsp; 
		<input type="submit" value="'.$this->translate->_('найти').' style="width:40px;">
		</form></td></tr></table>
		</div>';
		
			
		$module = 'recievers';
		$item = 'id';
		$list = 'recievers';
		
		$this->page .= $this->buildList($request,$cols,$list,$module,$item,$pages['qlimit']);
		
		$this->page .= '</table>';
		if($filter == '')
		{
			$this->page .= $pages['print'];
		}
		
	}
}

?>