<?

class CarsList extends Proto {

	private $carNameSize = 18;
	private $ownerNameSize = 25;
	private $transporterNameSize = 15;
	
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
		$item_link = $this->root_path.'?mod=cars&sw=form&car_id='; //ссылка на форму редактирования
		$add_link = $this->root_path.'?mod=cars&sw=form&add'; // добавление нового автомобиля

		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/class.search.php');

		$search = new listSearch();
		if(isset($_POST['searchClient']) and $_POST['searchClient']!='0') $_SESSION['client_filter'] = intval($_POST['searchClient']);
		elseif($_POST['searchClient']=='0') $_SESSION['client_filter'] = '';

		if(isset($_POST['place1']) and $_POST['place1']!='0') $_SESSION['place1'] = intval($_POST['place1']);
		elseif($_POST['place1']=='0') $_SESSION['place1'] = '';

		if(isset($_POST['port']) and $_POST['port']!='0') $_SESSION['port'] = intval($_POST['port']);
		elseif($_POST['port']=='0') $_SESSION['port'] = '';

		if(isset($_POST['place3']) and $_POST['place3']!='0') $_SESSION['place3'] = intval($_POST['place3']);
		elseif($_POST['place3']=='0') $_SESSION['place3'] = '';

		if(isset($_GET['viewonly'])) {
			$_SESSION['place1'] = 0;
			$_SESSION['place3'] = 0;
			$_SESSION['port'] = 0;
			$_SESSION['client_filter'] = false;
		}

		//обработка выборки
		if(isset($_GET['filter'])) {
			if($_POST['searchNumber']!='') {
				$search->insert('%'.mysql_real_escape_string($_POST['searchNumber']).'%', 'data');
				$search->insert("`frame` LIKE ", 'filter');
			}

			if($_POST['searchCar']!='') {
				$search->insert('%'.mysql_real_escape_string($_POST['searchCar']).'%', 'data');
				$search->insert("`model` LIKE ", 'filter');
			}

			if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['dateFrom']) and $_POST['dateFrom']!='') {
				$search->insert(mysql_real_escape_string($_POST['dateFrom']), 'data');
				$search->insert("`buy_date`>=", 'filter');
			}

			if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/', $_POST['dateTo']) and $_POST['dateTo']!='') {
				$search->insert(mysql_real_escape_string($_POST['dateTo']), 'data');
				$search->insert("`buy_date`<=", 'filter');
			}
		}

		if($_SESSION['client_filter']!='') {
			$search->insert(intval($_SESSION['client_filter']), 'data');
			$query = "`buyer` = ";
			$search->insert($query, 'filter');
		}

		if($_SESSION['place1']!='') {
			$search->insert(intval($_SESSION['place1']), 'data');
			$search->insert('`place_id1` =', 'filter');
		}
		if($_SESSION['port']!='') {
			$search->insert(intval($_SESSION['port']), 'data');
			$search->insert('ccl_'.ACCOUNT_SUFFIX.'cars.container=0 and ccl_'.ACCOUNT_SUFFIX.'cars.port =', 'filter');
		}
		if($_SESSION['place3']!='') {
			$search->insert(intval($_SESSION['place3']), 'data');
			$search->insert('`place_id3` =', 'filter');
		}

		/** 20080321 BW **/
		if(isset($_GET['viewonly']) and intval($_GET['viewonly'])>0 and intval($_GET['viewonly'])<5) $_SESSION['viewonly'] = intval($_GET['viewonly']);
		elseif(!isset($_SESSION['viewonly']))
		{
			$_GET['viewonly'] = 1;
			$_SESSION['viewonly']=1;
		}
		//		elseif($_POST['viewonly']=='0') $_SESSION['viewonly'] = '0';
		/** /20080321 **/

		if($search->makeFilter() != '') {
			$_GET['viewonly'] = 0;
			$_SESSION['viewonly']=0;
		}

		/** 20080321 BW **/
		if($_SESSION['viewonly']==1) {
			
			$search->insert("1", 'data');
			$search->insert('`type` != \'3\' and `number` IS NULL and', 'filter');
		}elseif($_SESSION['viewonly']==2) {
			$search->insert("1", 'data');
			$search->insert('`type` != \'3\' and `number` IS NOT NULL and', 'filter');
			$search->insert("0", 'data');
			$search->insert('`delivered` =', 'filter');
		}elseif($_SESSION['viewonly']==3){
			$search->insert("1", 'data');
			$search->insert('`type` = \'3\' or `delivered` =', 'filter');
		}elseif($_SESSION['viewonly']==4){
			// Paid for car
			$search->insert("0", 'data');
			$search->insert('`paid_total` = ', 'filter');
			// Container with car - arrived
			$search->insert("1", 'data');
//			$search->insert('`arrived` = ', 'filter');
			$search->insert('`delivered` = ', 'filter');
			// Date limit
			$search->insert("2009-09-01", 'data');
			$search->insert('`created` > ', 'filter');
		}

		/** /20080321 **/


		//список клиентов для поиска
		$customers = $this->getCustomersList();
		$customers_list = buildSelect($customers, 'searchClient', $_SESSION['client_filter'], ' - - - ', '8');

		//список мест для поиска
		$places_list = buildSelectArray($this->place, 'place1', $_SESSION['place1'], ' - - - ', '5');
		$port_list = buildSelect($this->getPortList(), 'port', $_SESSION['port'], ' - - - ', '7');
		$places_list3 = buildSelect($this->getPlacesList(), 'place3', $_SESSION['place3'], ' - - - ', '6');

		//поисковая форма
		$search->template = '<form name="searchFilter" method="post" action="'.$this->root_path.'?mod=cars&filter" class="smallForm">
		<table class="location_tab" width="960" border=0 cellspacing="0" cellpadding="0">
		<tr>
			<td width="290" nowrap>'.$this->translate->_('Автомобили');
                if ($_SESSION['user_type'] != 12)
                {
                   $search->template .= ' | <a href="'.$add_link.'">'.$this->translate->_('добавить').'</a>';
                }
                $search->template .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=places">'.$this->translate->_('Список мест').'</a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=ports">'.$this->translate->_('Список портов').'</a></td>
			<td align="right"><table border="0" cellspacing="0" cellpadding="0" class="title noborder" style="border:0px;">
		      <tr>
		        <td>'.$this->translate->_('по названию').'</td>
		        <td width="90"><input type="text" name="searchCar" value="'.$_POST['searchCar'].'" style="width:80px"></td>
		        <td width="80">'.$this->translate->_('по Вин коду').':&nbsp;</td>
		        <td width="90"><input type="text" name="searchNumber" value="'.$_POST['searchNumber'].'" style="width:80px"></td>
		        <td width="140" nowrap>'.$this->translate->_('с').':
		          <input type="text" name="dateFrom" value="'.$_POST['dateFrom'].'" style="width:60px" id="dateFrom">
		            <img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onClick="show_calendar(\'dateFrom\', \'\', myDateFormat);" style="margin:0px;margin-bottom:-3px;cursor:pointer;">
		            <input name="button" type="button" style="border:1px solid #bbb; background-color:#fff; width:30px;" onClick="javascript:equalize();" value="="></td>
		        <td width="100" align="left">'.$this->translate->_('по').':
		          <input type="text" name="dateTo" value="'.$_POST['dateTo'].'" style="width:60px" id="dateTo">
		          <img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onClick="show_calendar(\'dateTo\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
		      </tr>
		    </table></td>
		  </tr>
		<tr>
		<td height="25" valign=top><a href="'.$this->root_path.'?mod=marka">'.$this->translate->_('Список марок').'</a> &nbsp;&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=model">'.$this->translate->_('Список моделей').'</a></td>
		<td align="right"><table cellspacing="0" cellpadding="0" class="title noborder" >
		  <tr>
		    <td nowrap>'.$this->translate->_('место нахождения авто').'</td>
		    <td width="245">'.$places_list.'</td>
		    <td width="80" nowrap>'.$this->translate->_('место назначения').'</td>
		    <td width="325">'.$places_list3.'</td>
		  </tr>
		  <tr>
		    <td nowrap align="right">'.$this->translate->_('в порту').'</td>
		    <td width="245">'.$port_list.'</td>
		    <td width="80" nowrap>'.$this->translate->_('владелец').'</td>
		    <td width="325">'.$customers_list.'</td>
		    <!--<td width="60"><input name="submit" type="submit" style="width:90%;" value="'.$this->translate->_('найти').'"></td>-->
		  </tr>
		</table></td></tr>
		</table>
		<table cellspacing="0" cellpadding="0" style="width:100%"><tr>
		<td>'.$this->switchCarsviewList().'<!--
			<table class="location_tab" style="background:#DFF1FF;"><tr>
			<td style="width:150px;text-align:center;"><input type="radio" name="viewonly" id="viewonly0" value="0"'.($_SESSION['viewonly']=='0'?' checked':'').' style="border:0px;"><br><label for="viewonly0" style="cursor:hand; cursor:pointer;">показывать все</label>
			<td style="width:150px;text-align:center;"><input type="radio" name="viewonly" id="viewonly1" value="1"'.($_SESSION['viewonly']=='1'?' checked':'').' style="border:0px;"><br><label for="viewonly1" style="cursor:hand; cursor:pointer;">показывать новые</label>
			<td style="width:150px;text-align:center;"><input type="radio" name="viewonly" id="viewonly2" value="2"'.($_SESSION['viewonly']=='2'?' checked':'').' style="border:0px;"><br><label for="viewonly2" style="cursor:hand; cursor:pointer;">показывать в дороге</label>
			<td style="width:150px;text-align:center;"><input type="radio" name="viewonly" id="viewonly3" value="3"'.($_SESSION['viewonly']=='3'?' checked':'').' style="border:0px;"><br><label for="viewonly3" style="cursor:hand; cursor:pointer;">архив</label>
			</table>-->
		<td style="text-align:right;">
			<input name="submit" type="submit" style="width:90%;" value="'.$this->translate->_('найти').'">
		</table>
		</form>';

		//постраничный переход
		if($_SESSION['viewonly']!=0) {
			switch($_SESSION['viewonly']) {
				case 1: $condition = " WHERE `container` = '0'";break;
				case 2: $condition = " WHERE `container` != '0' and `delivered` = '0'";break;
				case 3: $condition = " WHERE `delivered` = '1'";break;
				case 4: $condition = " WHERE `total` = 0 AND delivered = 1 AND created > '2009-09-01' ";break;	//`delivered` = '1' AND
			}
		}
		else $condition = " WHERE 1";
		$total_items = mysql_fetch_array($this->mysqlQuery("SELECT COUNT(`id`) as total FROM `ccl_".ACCOUNT_SUFFIX."cars`".$condition));
		$this->per_page = 25; // меняем количество записей на страницу
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse(mysql_real_escape_string($_GET['page']), mysql_real_escape_string($_GET['mod']), $total_items['total'], intval($_GET['viewonly'])!=0?'&viewonly='.intval($_GET['viewonly']):'');
		}

		//сортировка
		$order_list = $this->defineSort('sort_cars', 'ccl_'.ACCOUNT_SUFFIX.'cars.buy_date DESC'); //добавляем сортировку в запрос
		$this->sortDeco('sort_cars'); //выводим указатель того, что сейчас сортируется и направление сортировки

		$filter_made = $search->makeFilter();

		if($filter_made!='') $local_filter = "WHERE".$filter_made; else $local_filter = "";

		/*
		if($_SESSION['show_arrived_cars']=='1') {
		if($filter_made!='') $local_filter = "WHERE".$filter_made;
		else $local_filter = "";
		}
		elseif($_SESSION['show_arrived_cars']=='0') {

		if((!isset($_POST['searchNumber']) or $_POST['searchNumber']=='') and $filter_made=='') {
		$local_filter = "WHERE `delivered` = '0'";
		}
		elseif($filter_made!='') {
		$local_filter = "WHERE ".$filter_made." AND `delivered` = '0'";
		}
		else $local_filter = '';
		}
		*/
		/** **/
		/*
		$request = "
		SELECT `ccl_car_comment`.`car_id`,`ccl_usrs`.`type` FROM `ccl_car_comment`, `ccl_usrs` 
		WHERE `ccl_car_comment`.`dat` IN (select max(`dat`) FROM `ccl_car_comment` group by `car_id`) 
		and `ccl_car_comment`.`user_id`=`ccl_usrs`.`id` and `ccl_usrs`.`type`=2
		";
		$content = $this->mysqlQuery($request);
		while($line = mysql_fetch_array($content)) $Cids[$line[0]]=$line[1];
		*/
		/** **/

		//основной запрос в базу
		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."containers.number AS number, ccl_".ACCOUNT_SUFFIX."cars.*, 
		ccl_".ACCOUNT_SUFFIX."customers.name as cust_name, ccl_".ACCOUNT_SUFFIX."transporters.name as sup_name,
		ccl_".ACCOUNT_SUFFIX."ports.name as port_name, auctions.name as auction_name
		FROM `ccl_".ACCOUNT_SUFFIX."containers` 
		RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."cars` 
		ON (ccl_".ACCOUNT_SUFFIX."cars.container = ccl_".ACCOUNT_SUFFIX."containers.id) 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` 
		ON(ccl_".ACCOUNT_SUFFIX."cars.buyer = ccl_".ACCOUNT_SUFFIX."customers.id)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."transporters`
		ON(ccl_".ACCOUNT_SUFFIX."cars.transporter = ccl_".ACCOUNT_SUFFIX."transporters.id)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."auctions` as auctions
		ON(auctions.id = ccl_".ACCOUNT_SUFFIX."cars.auction)		
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."ports`
		ON(ccl_".ACCOUNT_SUFFIX."cars.port = ccl_".ACCOUNT_SUFFIX."ports.id) ".$local_filter." ORDER BY ".$order_list;


		if($search->makeFilter()=='' or $_SESSION['viewonly']!=0) $request .= $pages['qlimit'];

		$content = $this->mysqlQuery($request);
		$num = @mysql_num_rows($content);

		$this->page .= '<div class="location" style="width:970px">'.$search->parser().'
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
			'.$this->sorterTD('cars', 'buy_date', $this->translate->_('дата покупки'), '105').'
			'.$this->sorterTD('cars', 'frame', $this->translate->_('вин код'), '70').'
			'.$this->sorterTD('cars', 'cust_name', $this->translate->_('владелец'), '250').'
			'.$this->sorterTD('cars', 'model', $this->translate->_('модель'), '175').'
			'.$this->sorterTD('cars', 'number', $this->translate->_('контейнер'), '80').'
			'.$this->sorterTD('cars', 'sup_name', $this->translate->_('транспортник'), '90').'
			'.$this->sorterTD('cars', 'port_name', $this->translate->_('порт'), '').'
			'.$this->sorterTD('cars', 'place_id1', $this->translate->_('местонахождение авто'), '120').'
			'.$this->sorterTD('cars', 'place_id2', $this->translate->_('местонахождение тайтла'), '90').'
			'.$this->sorterTD('cars', 'auction_name', $this->translate->_('Аукцион'), '').'
<!-- 			<td></td> -->
		  </tr>';

		$class="rowA rowB";
		$i=1;

		while ($i<=$num)
		{
			$color='';
			$line = mysql_fetch_array($content);
			$j=1;
			if($line['name']=='') $line['name'] = $this->translate->_('- = не выбран = -');
			if($line['delivered']=='1') $class=$this->rowDecoSwitch('delivered');
			if($line['type']==2) $class=$this->rowDecoSwitch('sale');
			if($line['type']==3) $class=$this->rowDecoSwitch('cancel');

			if ($line['buy_date']=='0000-00-00') {
				$buy_date=$line['created'];
				$buy_date= substr($buy_date,0,strpos($buy_date,' '));
			}
			else {
				$buy_date=$line['buy_date'];
			}

			$date=explode("-", $buy_date);

			$startPeriod = mktime(0,0,0,$date[1], $date[2], $date[0]);
			$pastTime = calc_period($startPeriod,time());

			if ($line['delivered']!=1 and $line['type']!=3)
			{
				if (!$this->checkTitleCar($line['place_id2'],$buy_date)) {$color='style="background-color:#FCFFA1;"';}
				if (!$this->checkLocationCar($line['place_id1'],$buy_date)) {$color='style="background-color:#FF7777;"';}
				if (in_array($line['place_id1'],$this->place_in_amerika) and $line['container']==0 and $pastTime[4]>=1) {$color='style="background-color:#FF7777;"';}
				if ($this->checkBishkek($pastTime, $line['place_id1'])) {$color='style="background-color:#FF7777;"';}
			}


			$this->page .= '<tr class="'.$class.'" '.$color.' onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$item_link.$line['id'].'\'"/>
				<td class="sm">'.$line['buy_date'].'</td>
				<td class="sm mono">'.substr($line['frame'],0,2).'.'.substr($line['frame'],(strlen($line['frame'])-7),7).'&nbsp;</td>
				<td class="sm">'.(cleanContent($line['cust_name'])==''?'&nbsp;':trim(mb_substr(cleanContent($line['cust_name']),0,$this->ownerNameSize)).(mb_strlen($line['cust_name'])>$this->ownerNameSize?"..":"")).'</td>
				<td class="sm" style="font-size:11px">'.($line['model']==''?'&nbsp;':trim(substr($line['model'],0,$this->carNameSize)).(strlen($line['model'])>$this->carNameSize?"..":"")).'</td>
				<td class="sm">'.($line['number']==''?'&nbsp;':$line['number']).'</td>	
				'.($_SESSION['user_type']!='5'?'<td class="sm">'.($line['sup_name']==''?'&nbsp;':trim(substr($line['sup_name'],0,$this->transporterNameSize)).(strlen($line['sup_name'])>$this->transporterNameSize?'..':'')):'').'	
				<td class="sm">'.$line['port_name'].'&nbsp;</td>
				<td class="sm">'.$this->place[$line['place_id1']].'&nbsp;</td>
				<td class="sm">'.$this->tplace[$line['place_id2']].'&nbsp;</td>
				<td class="sm">'.$line['auction_name'].'&nbsp;</td>
				</tr>'.'
				';
//				<td class="sm" style="padding-right:0px;">'.(isset($Cids[$line['id']])?"<img src='{$this->root_path}img/chat.png'>":"").'
			$i++;
			if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
		}

		$this->page .= '</table>';
		//показываем постраничный переход, если не было выборки
		if($search->makeFilter() == '' or $_SESSION['viewonly']!=0)
		{
			$this->page .= $pages['print'];
		}
		$this->page .= '<br><br>';
		// пустой список
		if(@mysql_num_rows($content) == '0') $this->page .= '<div class="green">'.$this->translate->_('по вашему запросу ничего не найдено').'</div>';
		$_SESSION['viewonly']=1;

	}

	function makeDate($date, $weekCount)
	{
		list ($year, $month, $day)=explode('-',$date);
		$startTime=mktime(0,0,0, $month, $day, $year);
		$millcount=$weekCount*7*86400;
		if ((time()-$startTime)>$millcount) {return false;}

		return true;
	}

	function checkLocationCar($place, $date)
	{
		if ($place==0 or $place==1 or $place==4)
		{
			if (!$this->makeDate($date,2)) {return  false;}
		}
		return  true;
	}

	function checkTitleCar($place, $date)
	{
		if ($place==0 or $place==1 or $place==4)
		{
			if (!$this->makeDate($date,2)) {return  false;}
		}
		return  true;
	}
	function checkBishkek($pastTime, $place_id1)
	{
		if ($pastTime[4]>=2 && $place_id1!=7)
		{
			if ($pastTime[4]>=3){return true;}
			if ($pastTime[4]==2 and $pastTime[3]>=21){return true;}
		}
		return false;
	}

	function switchCarsviewList() {
		$sw = $_SESSION['viewonly'];
		$out = '
		<div style="width:100px;" class="tabs'.($sw==1?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=cars&viewonly=1\'">'.$this->translate->_('новые').'</div>
		<div style="width:100px;" class="tabs'.($sw==2?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=cars&viewonly=2\'">'.$this->translate->_('в пути').'</div>
		<div style="width:100px;" class="tabs'.($sw==4?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=cars&viewonly=4\'">'.$this->translate->_('не проданые').'</div>
		<div style="width:100px;" class="tabs'.($sw==3?' tsel':'').'" onclick="document.location=\''.$this->root_path.'?mod=cars&viewonly=3\'">'.$this->translate->_('архив').'</div>';
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