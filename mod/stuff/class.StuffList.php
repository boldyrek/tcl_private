<?
class StuffList extends Proto {

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
		$item_link = $this->root_path.'?mod=stuff&sw=form&stuff_id='; //ссылка на форму редактирования
		$add_link = $this->root_path.'?mod=stuff&sw=form&add'; // добавление нового товара

		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/class.search.php');

		$search = new listSearch();

		if(isset($_POST['buyer']) and $_POST['buyer']!='0') $_SESSION['buyer'] = intval($_POST['buyer']);
		elseif($_POST['buyer']=='0') $_SESSION['buyer'] = '';

		if(isset($_POST['current_place']) and $_POST['current_place']!='0') $_SESSION['current_place'] = intval($_POST['current_place']);
		elseif($_POST['current_place']=='0') $_SESSION['current_place'] = '';

		if(isset($_POST['place_in']) and $_POST['place_in']!='0') $_SESSION['place_in'] = intval($_POST['place_in']);
		elseif($_POST['place_in']=='0') $_SESSION['place_in'] = '';


		//обработка выборки
		if(isset($_GET['filter'])) {
			if($_POST['uid']!='') {
				$search->insert('%'.mysql_real_escape_string($_POST['uid']).'%', 'data');
				$search->insert("`uid` LIKE ", 'filter');
			}

			if($_POST['name']!='') {
				$search->insert('%'.mysql_real_escape_string($_POST['name']).'%', 'data');
				$search->insert("ccl_".ACCOUNT_SUFFIX."stuff.name LIKE ", 'filter');
			}

			if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['dateFrom']) and $_POST['dateFrom']!='') {
				$search->insert(mysql_real_escape_string($_POST['dateFrom']), 'data');
				$search->insert("`date_buy`>=", 'filter');
			}

			if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['dateTo']) and $_POST['dateTo']!='') {
				$search->insert(mysql_real_escape_string($_POST['dateTo']), 'data');
				$search->insert("`date_buy`<=", 'filter');
			}
		}
		if($_SESSION['buyer']!='') {
			$search->insert(intval($_SESSION['buyer']), 'data');
			$query = "`buyer` = ";
			$search->insert($query, 'filter');
		}

		if($_SESSION['current_place']!='') {
			$search->insert(intval($_SESSION['current_place']), 'data');
			$search->insert('`current_place` =', 'filter');
		}
		if($_SESSION['place_in']!='') {
			$search->insert(intval($_SESSION['place_in']), 'data');
			$search->insert('`place_in` =', 'filter');
		}

		/** 20080321 BW **/

		if(isset($_GET['viewonly']) and intval($_GET['viewonly'])==2) $_SESSION['stuff_viewonly'] = intval($_GET['viewonly']);
		elseif(!isset($_SESSION['stuff_viewonly']))
		{
			$_GET['viewonly'] = 0;
			$_SESSION['stuff_viewonly']=0;
		}



		//список клиентов для поиска
		$customers = $this->getCustomersList();
		$customers_list = buildSelect($customers, 'buyer', $_SESSION['buyer'], ' - - - ', '8');

		//список мест для поиска
		$places_list = buildSelectArray($this->tplace, 'current_place', $_SESSION['current_place'], ' - - - ', '6');
		$places_list3 = buildSelect($this->getPlacesList(), 'place_in', $_SESSION['place_in'], ' - - - ', '7');

		//поисковая форма
		$search->template = '<form name="searchFilter" method="post" action="'.$this->root_path.'?mod=stuff&filter&viewonly='.$_SESSION['stuff_viewonly'].'" class="smallForm">
		<table class="location_tab" width="100%" border=0 cellspacing="0" cellpadding="0">
		<tr>
			<td width="290" nowrap>Товары | <a href="'.$add_link.'">добавить</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=post">Почтовые службы</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=places">Список мест</a></td>
			<td align="right"><table border="0" cellspacing="0" cellpadding="0" class="title noborder" style="border:0px;">
		      <tr>
		        <td>По названию</td>
		        <td width="90"><input type="text" name="name" value="'.$_POST['name'].'" style="width:80px" tabindex=1></td>
		        <td width="50">по UID:&nbsp;</td>
		        <td width="90"><input type="text" name="uid" value="'.$_POST['uid'].'" style="width:80px" tabindex=2></td>
		        <td width="140" nowrap>с:
		          <input type="text" name="dateFrom" value="'.$_POST['dateFrom'].'" style="width:60px" id="dateFrom"  tabindex=3>
		            <img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onClick="show_calendar(\'dateFrom\', \'\', myDateFormat);" style="margin:0px;margin-bottom:-3px;cursor:pointer;">
		            <input name="button" type="button" style="border:1px solid #bbb; background-color:#fff; width:30px;" onClick="javascript:equalize();" value="=" tabindex=4></td>
		        <td width="100" align="left">по:
		          <input type="text" name="dateTo" value="'.$_POST['dateTo'].'" style="width:60px" id="dateTo"  tabindex=5>
		          <img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onClick="show_calendar(\'dateTo\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
		      </tr>
		    </table></td>
		  </tr>
		<tr>
		<td height="25" valign=top><a href="'.$this->root_path.'?mod=marka">Список марок</a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=model">Список моделей</a></td>
		<td align="right"><table cellspacing="0" cellpadding="0" class="title noborder" >
		  <tr>
		    <td nowrap>Местонахождение товара</td>
		    <td width="245">'.$places_list.'</td>
		    <td width="80" nowrap>Место назначение</td>
		    <td width="325">'.$places_list3.'</td>
		  </tr>
		  <tr>
		    <td nowrap align="right">&nbsp;</td>
		    <td width="245">&nbsp;</td>
		    <td width="80" nowrap align=right>Владелец</td>
		    <td width="325">'.$customers_list.'</td>
		    <!--<td width="60"><input name="submit" type="submit" style="width:90%;" value="найти"></td>-->
		  </tr>
		</table></td></tr>
		</table>
		<table cellspacing="0" cellpadding="0" style="width:100%" ><tr>
		<td>'.$this->switchStuffsviewList().'
		<td style="text-align:right;">
			<input name="submit" type="submit" style="width:90%;" value="найти" tabindex=9>
		</table>
		</form>';

		//постраничный переход

		if ($_SESSION['stuff_viewonly']==0) {
			$condition="WHERE deliveried='0'";
		} else {
			$condition="WHERE deliveried='1'";
		}

		$total_items = mysql_fetch_array($this->mysqlQuery("SELECT COUNT(`id`) as total FROM `ccl_".ACCOUNT_SUFFIX."stuff`".$condition));

		$this->per_page = 25; // меняем количество записей на страницу
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse(mysql_real_escape_string($_GET['page']), mysql_real_escape_string($_GET['mod']), $total_items['total'], '&viewonly='.intval($_GET['viewonly']));
		}

		//сортировка
		$order_list = $this->defineSort('sort_stuff', 'ccl_'.ACCOUNT_SUFFIX.'stuff.date_buy DESC'); //добавляем сортировку в запрос
		$this->sortDeco('sort_stuff'); //выводим указатель того, что сейчас сортируется и направление сортировки

		$filter_made = $search->makeFilter();

		if($filter_made!='') $local_filter = 'AND '.$filter_made; else $local_filter = "";


		//основной запрос в базу

		if ($_SESSION['stuff_viewonly']==0) {
			$local_filter="deliveried='0' ".$local_filter;
		} else {
			$local_filter="deliveried='1' ".$local_filter;
		}

		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."stuff.*, 
		ccl_".ACCOUNT_SUFFIX."transporters.name as sup_name
		FROM `ccl_".ACCOUNT_SUFFIX."transporters`
		RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."stuff`
		ON(ccl_".ACCOUNT_SUFFIX."stuff.transporter_id = ccl_".ACCOUNT_SUFFIX."transporters.id) 
		WHERE ".$local_filter." ORDER BY ".$order_list;

		//var_dump($request);
		if($search->makeFilter()=='' or $_SESSION['stuff_viewonly']!=0) $request .= $pages['qlimit'];

		$content = $this->mysqlQuery($request);
		echo mysql_error();
		$num = @mysql_num_rows($content);

		$this->page .= '<div class="location" style="width:960px">'.$search->parser().'
		</div>
			<script>
			var myDateFormat = new Array("yyyy-mm-dd");
			function equalize() {
				document.getElementById("dateTo").value = document.getElementById("dateFrom").value;
				document.forms.searchFilter.submit();
			}
			</script>
			<script src="'.$this->root_path.'js/datepicker.js"></script>
			
			<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
		 	<tr class="title sortButtons">
			'.$this->sorterTD('stuff', 'date_buy', 'дата покупки', '150').'
			'.$this->sorterTD('stuff', 'name', 'Наименование', '450').'
			'.$this->sorterTD('stuff', 'uid', 'UID', '75').'
			'.$this->sorterTD('stuff', 'count', 'Наличие', '75').'
			'.$this->sorterTD('stuff', 'sup_name', 'Транспортник', '150').'
			'.$this->sorterTD('stuff', 'current_place', 'Местонахождение', '140').'
		  </tr>';

		$class="rowA rowB";

		while ($line = mysql_fetch_array($content))
		{
			$nal=$line['count']-$line['mount'];
			$this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$item_link.$line['id'].'\'"/>
				<td class="sm">'.$line['date_buy'].'</td>
				<td class="sm" >'.($line['name']==''?'&nbsp;':trim(substr($line['name'],0,30)).(strlen($line['name'])>30?"...":"")).'</td>
				<td class="sm">'.$line['uid'].'</td>
				<td class="sm">'.($line['count']-$line['sold']).'&nbsp;</td>
				'.($_SESSION['user_type']!='5'?'<td class="sm">'.($line['sup_name']==''?'&nbsp;':$line['sup_name']).'</td>':'').'	
				<td class="sm">'.$this->tplace[$line['current_place']].'&nbsp;</td>
				</tr>';
			if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
		}

		$this->page .= '</table>';

		//показываем постраничный переход, если не было выборки
		if($search->makeFilter() == '')
		{
			$this->page .= $pages['print'];
		}
		$this->page .= '<br><br>';
		// пустой список
		if(@mysql_num_rows($content) == '0') $this->page .= '<div class="green">по вашему запросу ничего не найдено</div>';
		$_SESSION['stuff_viewonly']=0;

	}


	function switchStuffsviewList() {
		$sw = $_SESSION['stuff_viewonly'];
		$out = '
		<div style="width:100px;" class="tabs'.($sw==0?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=stuff&viewonly=0\'">новые</div>
		<div style="width:100px;" class="tabs'.($sw==2?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=stuff&viewonly=2\'">архив</div>';
		return $out;
	}

	function rowDecoSwitch($type) {
		switch($type) {
			case 'delivered':
				return 'greenTR';
				break;
			case 'sale':
				return 'blueTR';
				break;
			case 'cancel':
				return 'redTR';
				break;
		}
	}

}

?>