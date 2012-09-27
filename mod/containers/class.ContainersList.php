<?

class ContainersList extends Proto {
	
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
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/class.search.php');
		$search = new listSearch();
		
		foreach($suppliersPorts as $k => $v) {
			foreach($v['ports'] as $port => $id) {
			$ids .= $id.',';
			}
			$portSelect .= '
			<option value="'.rtrim($ids, ',').'"';
			if(rtrim($ids, ',')==$_POST['searchSupplier']) $portSelect .= ' selected="selected"';
			$portSelect .= '>'.$v['name'].'</option>';	
			$ids = '';
		};
		//#########################################
		
		//список экспедиторов
		$expList = $this->mysqlQuery("
		SELECT id,name 
		FROM `ccl_".ACCOUNT_SUFFIX."expeditors` 
		WHERE 1 ORDER BY name ASC");
		$num = mysql_num_rows($expList);
		if($num!=0) {
			$i = 0;
			while($i<$num) {
				$line= mysql_fetch_array($expList);
				$expSelect .= '
				<option value="'.$line['id'].'"';
				if($line['id'] == $_POST['expeditorSearch']) $expSelect .= ' selected="selected"';
				$expSelect .= '>'.$line['name'].'</option>';
				$i++;
			}
		}
		
		//выборка
		$filter = '';
		if($_GET['show_arrived']=='1') $_SESSION['show_arrived_conts'] = '1';
		elseif($_GET['show_arrived']=='0') $_SESSION['show_arrived_conts'] = '0';
		
		if($_SESSION['show_arrived_conts'] == '1') 
		{
			$arrived = ' checked="checked"';
			$path = "?mod=containers&show_arrived=0";
			$show_arrived = "";
		}
		else {
			$arrived = '';
			$path = "?mod=containers&show_arrived=1";
			$show_arrived = "`arrived` = '0'";
		}
		
		if($_SESSION['user_type']=='5') {
			$search->insert('4','data');
			$search->insert('ccl_'.ACCOUNT_SUFFIX.'containers.expeditor =', 'filter');
		}
		
		if(isset($_GET['filter']))
		{
			if(mysql_real_escape_string($_POST['searchNumber'])!='') {
				$search->insert('%'.mysql_real_escape_string($_POST['searchNumber']).'%', 'data');
				$search->insert("`number` LIKE", 'filter');
			}
					
			if($_POST['expeditorSearch']!='') {
				$search->insert(mysql_real_escape_string($_POST['expeditorSearch']), 'data');
				$search->insert("`expeditor` = ", 'filter');
			}
		}

                if ($_SESSION['user_type'] == 13)
                {
                   $search->insert($_SESSION['login_id'], 'data');
                   $search->insert('`agent_id` = ', 'filter');
                }
		
		$filter_made = $search->makeFilter();
		if($filter_made!='') { 
			$filter_made = "WHERE ".$filter_made;
			if($filter!='') $filter_made.=' AND '.$filter;
		}
		else $filter_made = "";
		if($filter_made == '' and $filter!='') $filter_made = "WHERE ".$filter;
		
		//постраничный переход
		if($filter_made=='' or $_SESSION['user_type']=='5')
		{
			$total_items = mysql_num_rows($this->mysqlQuery("
			SELECT `id` 
			FROM `ccl_".ACCOUNT_SUFFIX."containers`
			WHERE 1"));
			if($total_items>$this->per_page)
			{
				$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items);
			}
		}
		if($filter_made=='' and $show_arrived!='') $filter_made = "WHERE ".$show_arrived;
		elseif($filter_made!='' and $show_arrived!='') $filter_made.=" AND ".$show_arrived;
		
		//сортировка
		$order_list = $this->defineSort('sort_cont', 'sent DESC'); //добавляем сортировку в запрос
		$this->sortDeco('sort_cont'); //выводим указатель того, что сейчас сортируется и направление сортировки
		
		$request = "SELECT ccl_".ACCOUNT_SUFFIX."containers.*, ccl_".ACCOUNT_SUFFIX."expeditors.name 
		FROM `ccl_".ACCOUNT_SUFFIX."containers`
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."expeditors` 
		ON (ccl_".ACCOUNT_SUFFIX."expeditors.id = ccl_".ACCOUNT_SUFFIX."containers.expeditor)
		 ".$filter_made." ORDER BY ".$order_list.$pages['qlimit'];

		$content = $this->mysqlQuery($request);
			
		$cars = $this->mysqlQuery("
		SELECT id, container 
		FROM `ccl_".ACCOUNT_SUFFIX."cars` 
		WHERE container != '0'");
		
		$j=1;
		while($j<=mysql_num_rows($cars))
		{
			$carsfound[$j] = mysql_fetch_array($cars);
			$j++; 
		}
		
		$num = mysql_num_rows($content);
		$i=1; 
		$class="rowA rowB";
		
		$this->page .= '
		<script>
			function checkBox(id) {
				var current = id;
				if(document.getElementById(current).checked == true) document.getElementById(current).checked = false;
				else document.getElementById(current).checked = true;
				checkSniffer(current);
			}
			function send2print(mode) {
			 	var forma = document.forms.printContainers;
			 	var mode = mode;
			 	if(mode==\'print\') {
			 		forma.action = \'/?mod=containers&sw=print\';
			 		forma.target =\'_blank\';
			 	}
			 	if(mode==\'edit\') {
			 		forma.action = \'/?mod=containers&sw=cprint&step2\';
			 		forma.target =\'_self\';
			 	}
			 	forma.submit();
			}
			function checkSniffer(id) {
				var changed = id;
				$.post("/?mod=containers&sw=checker.php", id=changed);
			}
		</script>
		<form name="searchContainer" method="POST" action="'.$this->root_path.'?mod=containers&filter" class="smallForm">
                   ';

                if ($_SESSION['user_type'] == 13)
                {
                   $this->page .= '<div class="location">'.$this->translate->_('Контейнеры').'</div>';
                }
                else
                {
                $this->page .= '
                   <div class="location">
                     <table class="location_tab" width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                        <td width="480">'.$this->translate->_('Контейнеры').' | <a href="'.$this->root_path.'?mod=containers&sw=form&add">'.$this->translate->_('добавить').'</a>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$this->root_path.'?mod=reload">'.$this->translate->_('Список портов перегруза').'</a> | <a href="'.$this->root_path.'?mod=reload&sw=form">'.$this->translate->_('Добавить порт перегруза').'</a></td>
                        <td>&nbsp;</td>
                        <td width="150">'.$this->translate->_('искать по номеру:').'&nbsp;<br>
                           <input type="text" name="searchNumber" value="'.$_POST['searchNumber'].'"></td>
		<td width="210">'.$this->translate->_('экспедитору:').'<br><select name="expeditorSearch" style="width:200px;">
		<option value=""> - - - </option>'.$expSelect.'
		</select> </td>
		<td width="40"> <input type="submit" value="'.$this->translate->_('найти').'"></td>
		</tr></table></div>

		<div class="location">
		<table width="98%" cellspacing="0" cellpadding="0" style="font-size:12px"><tr>
		<td width="250"><input type="button" value="'.$this->translate->_('распечатать').'" onclick="send2print(\'print\');"> &nbsp;&nbsp;&nbsp; <input type="button" value="'.$this->translate->_('редактировать').'" onclick="send2print(\'edit\');" style="background-color:#fff;"></td>
		<td align="right">
		
		
		<input type="checkbox" name="show_arrived" id="arrived"'.$arrived.' onclick="document.location=\''.$this->root_path.$path.'\'" style="border:0px;"><label for="arrived" style="cursor:hand; cursor:pointer;">'.$this->translate->_('показывать прибывшие').'</label></td>
		</tr></table></div>';
                }
                $this->page .= '
                </form>
		<form action="" method="post" name="printContainers">	
		<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
		 	 <tr class="title">
		 	 <td width="35" align="center">-</td>
		    '.$this->sorterTD('containers', 'number', $this->translate->_('Номер контейнера'), '').'
		    '.$this->sorterTD('containers', 'booking', $this->translate->_('Букинг №'), '90').'
		    '.$this->sorterTD('containers', 'sent', $this->translate->_('Отправка'), '90').'
		    '.$this->sorterTD('containers', 'portdate', $this->translate->_('Порт'), '90').'
		    '.$this->sorterTD('containers', 'rail', $this->translate->_('Ж/Д'), '90').'
		    '.$this->sorterTD('containers', 'bishkek', $this->translate->_('Назначение'), '90').'
		    '.$this->sorterTD('containers', 'arrive_time', $this->translate->_('Ожидается'), '90').'
		    <td width="40">'.$this->translate->_('Машин').'</td>
			'.($_SESSION['user_type']!='5'?$this->sorterTD('containers', 'expeditor', $this->translate->_('Экспедитор'), '80'):'').'
			'.$this->sorterTD('containers', 'arrived', '&nbsp;', '30').'
		  </tr>';
		while ($i<=$num)
		{
			$line = mysql_fetch_array($content);
			
			if($line['arrived']=='1') { 
				$arrived='<img src="'.$this->root_path.'img/ccl/ok.gif">';
				$class = 'greenTR';
			}
			else $arrived = '~';
			
			$this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
			<td onclick="checkBox(\'check_'.$line['id'].'\')" align="center">
			<input type="checkbox" name="check['.$line['id'].']" id="check_'.$line['id'].'" onclick="checkBox(\'check_'.$line['id'].'\')" style="width:15px;cursor:pointer;">
			<input type="hidden" name="hiddenID['.$line['id'].']" value="'.$line['number'].'"></td>
			'.$this->clistTD($line['id'],$line['number']).
			$this->clistTD($line['id'],$line['booking']).
			$this->clistTD($line['id'],$line['sent']).
			$this->clistTD($line['id'],$line['portdate']).
			$this->clistTD($line['id'],$line['rail']).
			$this->clistTD($line['id'],$line['bishkek']).
			$this->clistTD($line['id'],$line['arrive_time']).
			$this->clistTD($line['id'],$this->looter($line['id'], $carsfound)).
			($_SESSION['user_type']!='5'?$this->clistTD($line['id'],$line['name']):'').'
			<td align="center">'.$arrived.'</td>
			</tr>';
			$i++;
			if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
		}
		
		$this->page .= '</table>
		</form>'.$pages['print'];
	}
	
	function clistTD($id, $item) {
		return '
		<td onclick="document.location=\''.$this->root_path.'?mod=containers&sw=form&cont_id='.$id.'\'">'.$item.'</td>';
	}

	function looter($bag, $goods)
		{
			$found=0;
			$i=1;
			$num = count($goods);
			if($num>0)
			{
				while($i<=$num)
				{
					$line = $goods[$i];
					if($line['container']==$bag) $found++;
					$i++;
				}
			}
			return $found;
		}
}

?>