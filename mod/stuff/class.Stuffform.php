<?
include_once('class.Stuffcomment.php');
class StuffForm extends Proto {

	var $clients;
	var $transporters;
	var $container;
	var $content;
	var $files;
	var $invoice;
	var $stuff_id;
	var $ports;
	var $cr;
	var $inspection;
	var $payments;
	var $nal;
	var $total;
	var $balance;

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

	public function getContent() {

		if(!isset($_GET['add'])) {
			$this->stuff_id = intval($_GET['stuff_id']);

			$this->content = @mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."stuff.*, ccl_".ACCOUNT_SUFFIX."customers.name as buyer, 
			ccl_".ACCOUNT_SUFFIX."customers.id as buyer_id, ccl_".ACCOUNT_SUFFIX."transporters.name as sup_name,
			ccl_".ACCOUNT_SUFFIX."stuff_forsale.id as sell_id, ccl_".ACCOUNT_SUFFIX."stuff_forsale.price as sell_price,
			ccl_".ACCOUNT_SUFFIX."stuff_forsale.comment as sell_comment, ccl_".ACCOUNT_SUFFIX."stuff_forsale.active_through as sell_active_through,
			ccl_".ACCOUNT_SUFFIX."stuff_forsale.sold as for_sold
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."stuff` 
			ON (ccl_".ACCOUNT_SUFFIX."customers.id=ccl_".ACCOUNT_SUFFIX."stuff.buyer) 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."transporters`
			ON (ccl_".ACCOUNT_SUFFIX."transporters.id = ccl_".ACCOUNT_SUFFIX."stuff.transporter_id)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."containers`
			ON (ccl_".ACCOUNT_SUFFIX."containers.id = ccl_".ACCOUNT_SUFFIX."stuff.container)			
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."stuff_forsale`
			ON (ccl_".ACCOUNT_SUFFIX."stuff_forsale.stuff = ccl_".ACCOUNT_SUFFIX."stuff.id)			
			WHERE ccl_".ACCOUNT_SUFFIX."stuff.id ='".$this->stuff_id."'"));
		}
		else {
			$this->stuff_id = '0';
			$this->content['id'] = '0';
			$this->content['count']='0';
			$this->content['buyer'] = '0';
			$this->content['transporter_id']='0';
			$this->content['container']='0';
			$this->content['price']='0';
			$this->content['sold']='0';
			$this->content['delivery']='0';
			$this->content['other']='0';
			$this->content['total']='0';
			$this->content['paid']='0';
			$this->content['balance']='0';
			$this->content['invoice']='0';
			$this->content['uid'] = $this->makeAndCheckUid();
		}

		if($this->content['id']=='') {
			$this->page .= '<div class="warn" style="width:900px;">Ошибка! Товар с такими параметрами в базе не обнаружен</div>';
		}
		else {
			if (isset($_GET['success'])) $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
			$this->getAdditional();
			$this->page .= $this->car_edit();
			if($this->permissions['cars']==1)
			{
				$comment = new StuffComment();
				$comment->setCarOwnerId($this->content['buyer'], $this->content['reciever'], $this->content['dealer']);
				$this->page .= $comment->getContent();
			}
		}
	}

	private function getAdditional() {
		$this->clients = $this->getCustomersList();

		$this->transporters = $this->mysqlQuery(
		"SELECT id,name
		FROM `ccl_".ACCOUNT_SUFFIX."transporters` 
		WHERE 1 ORDER BY `name` ASC");

		if($this->exists($this->content['container']) and $this->content['container']!='0') {
			$this->container = @mysql_fetch_array($this->mysqlQuery(
			"SELECT id,number,bishkek,arrived
			FROM `ccl_".ACCOUNT_SUFFIX."containers`
			WHERE `id` = ".$this->content['container']));
		}

		$this->post = $this->mysqlQuery(
		"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."post` 
			WHERE 1
			ORDER BY `name` ASC");


		if(!isset($_GET['add'])) {
			$this->files = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_photos`
			WHERE `stuff` = '".$this->stuff_id."' ORDER BY `folder` DESC, `id` DESC");

			$this->cr = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_cert`
			WHERE `stuff` = '".$this->stuff_id."' ORDER BY `id` DESC");

			$this->invoice = mysql_fetch_array($this->mysqlQuery("
			SELECT id
			FROM `ccl_".ACCOUNT_SUFFIX."invoices` 
			WHERE `carid` 
			= '".$this->stuff_id.";' LIMIT 1"));

			$this->content['invoice_file'] = $this->invoice['id'];
			$this->ports = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."ports` 
			WHERE 1
			ORDER BY `name` ASC");
			$this->places = $this->place;

			$this->places3 = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."places`");

			$this->inspection = $this->mysqlQuery(
			"SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff_inspections`
			WHERE stuff = '".$this->stuff_id."'
			ORDER BY `id` DESC");

			$this->adddoc = $this->mysqlQuery(
			"SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff_adddoc`
			WHERE stuff = '".$this->stuff_id."'
			ORDER BY `id` DESC");

			$this->sell = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_sell`
			WHERE `stuff_id` = '".$this->stuff_id."' ORDER BY `sell_dat` DESC");

			$this->payments = mysql_fetch_array($this->mysqlQuery("
			SELECT SUM(amount) as amount FROM `ccl_".ACCOUNT_SUFFIX."payments`
			WHERE `stuff` = '".$this->stuff_id."'"));
		}
	}


	function car_edit() {

		//адрес формы
		if(!isset($_GET['add'])) $form_link = '&sw=save&id='.$this->stuff_id;
		else $form_link = '&sw=add';


		//проверка, может быть форма открывается после попытки добавить автомобиль с уже имеющимся в базе вин кодом
		if(isset($_GET['exists'])) {
			$this->page .= '<div class="warn" style="width:660px;">Товар с таким uid ('.$_SESSION['carForm']['uid'].') уже есть в базе</div>';
			$content = $_SESSION['stuffForm'];
		}

		$this->nal=$this->content['count']-$this->content['sold'];
		$this->total=$this->content['price']*$this->content['count']+$this->content['delivery']+$this->content['other'];
		$this->balance=$this->payments['amount']-$this->total;

		//формирование списка покупателей и дилеров
		$num = @mysql_num_rows($this->clients);
		$i=1;
		while($i<=$num)
		{
			$line = mysql_fetch_array($this->clients);
			//все клиенты
			$clients_list.='<option value="'.$line['id'].'"';
			if($this->content['buyer_id'] == $line['id']) {
				$clients_list.=' selected="selected"';
				$ownerInfo = '<input type="hidden" value="'.$line['id'].'" name="owner">
				<input type="hidden" value="'.$line['id'].'" name="buyer">
				'.stripslashes($line['name']);
				if($this->content['isdealer']==1) $carsDealer = '<input type="hidden" name="dealer" value="'.$this->content['buyer_id'].'">
				';
				else $carsDealer = '<input type="hidden" name="dealer" value="'.$line['mydealer'].'">
				';
			}

			$clients_list.='>'.stripslashes($line['name']).'</option>
			';

			$hiddenFields .= '
			<input type="hidden" name="isDealer'.$line['id'].'" value="'.$line['dealer'].'"><input type="hidden" name="myDealer'.$line['id'].'" value="'.$line['mydealer'].'">';

			$i++;
		}
		//if($this->content['buyer']=='0' or isset($_GET['add'])) {
			$car_owner = '<select name="buyer" tabindex="1" id="customerSelect">
		    <option value="0" selected="selected"> не выбран </option>
			'.$clients_list.'
		      </select>';
		//}
		//else $car_owner = '<span id="customerSelect"></span>'.
		//$ownerInfo;
		//#######################################


		//фотографии автомобиля
		$photos = array();
		$num = @mysql_num_rows($this->files);
		$photo_list='';
		if($num>0)
		{
			$j = 1;
			while($j<=$num)
			{
				$line = mysql_fetch_array($this->files);
				if($line['id']==$this->content['top_photo']) $top_bg = '#d2f4d9';
				else $top_bg = '';
				$photos[$line['folder']] .= $this->wrapFile('
				<input type="radio" name="top_photo" value="'.$this->stuff_id.'" onClick="saveTopPhoto(\''.$line['id'].'\')" '.($line['id']==$this->content['top_photo']?' checked="checked"':'').'>
				<a href="'.$this->root_path.'photos/stuff/'.ACCOUNT_SUFFIX.$this->stuff_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'photos/stuff/'.ACCOUNT_SUFFIX.$this->stuff_id.'/thumb/'.$line['file'].'" border="0"></a>
				<!--'.($line['filename']!=''?"<div style='font-size:9px;'>{$line['filename']}</div>":'').'-->
				<a href="'.$this->root_path.'?mod=stuff&sw=delete&what=photo&name='.$line['file'].'&chk='.$this->stuff_id.'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить это фото?\')">удалить</a>', $top_bg);
				$j++;
			}
			foreach ($this->photo_folders as $k=>$v) {
				if($photos[$k]!='') {
					$photo_list .= '<table width="100%" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #ccc; margin-bottom:10px;"><tr><td bgcolor="#eeeeee" align="center" class="rowB">'.$v.'</td>
				</tr>
				<tr><td>'.$photos[$k].'</td></tr></table>';
				}
			}
		}
		//#######################################

		//Отчет о состоянии автомобиля или CR
		$num = @mysql_num_rows($this->cr);
		$cr_list = '';
		if($num>0)
		{

			while($line = mysql_fetch_array($this->cr)) {
				$ico = fileIco($line['file']);
				$cr_list .= $this->wrapFile('<a href="'.$this->root_path.'photos/stuff/'.ACCOUNT_SUFFIX.$this->stuff_id.'/'.$line['file'].'" target="_blank">
				<img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a>
				'.($line['filename']!=''?"<div style='font-size:9px;'>{$line['filename']}</div>":'').'
				<a href="'.$this->root_path.'?mod=stuff&sw=delete&what=certificate&name='.$line['file'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить Отчет о состоянии автомобиля или CR?\')">удалить</a>');
			}
		}

		// файл после-продажной инспекции
		if(mysql_num_rows($this->inspection)>0) {

			while($line = mysql_fetch_array($this->inspection)) {

				$ico = fileIco($line['file']);

				$inspection_file .= $this->wrapFile('
			<a href="'.$this->root_path.'photos/stuff/'.ACCOUNT_SUFFIX.$this->stuff_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a><br>
			'.($line['filename']!=''?"<div style='font-size:9px;'>{$line['filename']}</div>":'').'
			<a href="'.$this->root_path.'?mod=stuff&sw=delete&what=inspection_file&name='.$line['file'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить После Продажную инспекцию?\')">удалить</a>');

			}
		}

		// файл Сопроводительный документ
		if(mysql_num_rows($this->adddoc)>0) {

			while($line = mysql_fetch_array($this->adddoc)) {

				$ico = fileIco($line['file']);

				$adddoc_file .= $this->wrapFile('
			<a href="'.$this->root_path.'photos/stuff/'.ACCOUNT_SUFFIX.$this->stuff_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a><br>
			'.($line['filename']!=''?"<div style='font-size:9px;'>{$line['filename']}</div>":'').'
			<a href="'.$this->root_path.'?mod=stuff&sw=delete&what=adddoc_file&name='.$line['file'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить Сопроводительный документ?\')">удалить</a>');

			}
		}

		//#######################################

		$sup_list = buildSelect($this->transporters, 'transporter_id', $this->content['transporter_id'], 'не выбран', '4');
		$place_list = buildSelectArray($this->tplace, 'current_place', $this->content['current_place'], 'не выбрано', '18');
		$place_list3 = buildSelect($this->places3, 'place_in', $this->content['place_in'], 'не выбрано', '8');

		//#######################################

		//вывод портов для выбора в зависимости от поставщика

		$num = @mysql_num_rows($this->ports);
		if($num>0) {
			$ports_list = buildSelect($this->ports, 'port_id', $this->content['port_id'], 'не выбран', '5');
		}
		else $ports_list = ' нет портов ';


		//готовим ссылку на инвойс этого автомобиля

		if($this->content['invoice_file']!='') {
			$invoiceLink .= '
				<a href="'.$this->root_path.'?mod=invoices&sw=file&inv_id='.$this->content['invoice_file'].'" target="_blank"><img src="'.$this->root_path.'img/ccl/doc_ico.gif" align="absmiddle" hspace="3" border="0">инвойс</a><br>';
		}
		else $invoiceLink = 'нет инвойса';


		//#############################

		//дней со дня покупки

		//####################

		// дней со дня готовности до дня, когда машину забрал транспортник
		if($this->content['ready']=='1') {
			if($this->content['pickedup']=='0') {

				$readyDate = explode("-",$this->content['date_ready']);

				$daysFromReadyPassed = round((mktime(0,0,0,date('m'), date('d'), date('Y')) - mktime(0,0,0,$readyDate[1], $readyDate[2], $readyDate[0]))/(60*60*24));

				//считаем сколько месяцев прошло
				$months = 0;
				while($daysFromReadyPassed>30) {
					if($daysFromReadyPassed>30) {
						$months++;
						$daysFromReadyPassed = $daysFromReadyPassed-30;
					}
				}

				if(strlen($daysFromReadyPassed)==1) $daysFromReadyText = $daysLang[$daysFromReadyPassed];
				elseif(strlen($daysFromReadyPassed)>1) {
					if(substr($daysFromReadyPassed, 0, 1)==1) $daysFromReadyText = 'дней';
					else $daysText = $daysLang[substr($daysFromReadyPassed, 1, 1)];
				}
				$daysFromReadyPassed = 'прошло: '.($months!=0?'<b>'.$months.'</b> '.$monthLang[$months].' и ':'').'<b>'.$daysFromReadyPassed.'</b> '.$daysFromReadyText;
			}
		}
		//####################################


		//#####################
		if(isset($_GET['inv'])) $inv = $_GET['inv'];
		else $inv = '';
		switch($inv) {
			case 'sent':
				$inv_report = '<h4 class="report">Пользователь создан.<br>
				Приглашение клиенту успешно отправлено.</h4>';
				break;
			case 'notsent':
				$inv_report = '<div class="notice">Пользователь создан.<br>Приглашение клиенту отправить НЕ удалось!</div>';
				break;
			case 'error':
				$inv_report = '<div class="notice">Пользователь не создан! <br>Приглашение клиенту НЕ было отправлено! <br>Возможно у него не указаны email или имя.</div>';
				break;
			case 'exists':
				$inv_report = '<h4 class="report">У этого клиента уже есть логин. Приглашение не требуется.</h4>';
				break;
			case 'no':
				$inv_report = '';
				break;
			default:
				$inv_report = '';
				break;
		}

		// форма добавления платежа за этот автомобиль
		$add_payment = '<div id="addPayment" style="display:none;width:230px;position:absolute;border:2px solid #f55;margin-top:110px;margin-left:455px;">
		<form action="?mod=payments&sw=add&return='.$this->stuff_id.'" class="myForm" method="post">
		<table class="list" width="230">
		<tr class="rowA"><td colspan="2"><img src="/img/ccl/r_ex.gif" align="right" style="cursor:hand; cursor:pointer;float:right;" onclick="document.getElementById(\'addPayment\').style.display=\'none\';">
		<b>Платеж от имени владельца</b></td></tr>
		<tr class="rowA">
			<td>Сумма:</td>
			<td>Дата:</td>
		</tr>
		<tr class="rowB">
			<td><input type="input" name="amount"></td>
			<td><img src="img/ccl/cal.gif" border=0 onclick="show_calendar(\'payDate\', \'\', myDateFormat);" style="margin-left:-22px;float:right;cursor:hand;cursor:pointer;"><input type="text" name="date" id="payDate" value="'.date('Y-m-d').'" tabindex="3" style="width:78%;" />
			</td>
		</tr>
		<tr class="rowA">
			<td colspan="2">Комментарий:</td>
		</tr>
		<tr class="rowB">
			<td colspan="2"><input type="text" name="comment" value=""></td>
		</tr>
		
		<tr class="rowA"><td colspan="2" align="center"><input type="submit" value="Добавить" id="save"></td></tr>
		</table>	
		<input type="hidden" name="stuff" value="'.$this->stuff_id.'">
		<input type="hidden" name="client" value="'.$this->content['buyer_id'].'">
		</form>
		</div>
		';
		//###############################################

		$smallColWidth = '130';
		$this->page .= '
		<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
		<script src="'.$this->root_path.'js/datepicker.js"></script>
		<script src="'.$this->root_path.'js/jquery.js"></script>
		<script>
		function showAddCustomer() {
			document.getElementById(\'customerSelect\').style.display=\'none\';
			document.getElementById(\'fog\').style.display=\'\';
			document.getElementById(\'addCustomerContainer\').style.display=\'\';
			document.getElementById(\'addCustomer\').src=\''.$this->root_path.'?mod=clients&sw=form&add&hidemenu\';
		}
		function finishAddCustomer() {
			document.getElementById(\'fog\').style.display=\'none\';
			document.getElementById(\'addCustomerContainer\').style.display=\'none\';
			document.getElementById(\'addCustomer\').style.display=\'none\';
			document.location=document.location;
		}
		var check = \'1\';
		
		function showForm() {
			document.getElementById(\'addCustomer\').style.display=\'\';
		}
		
		var cexp;
		function countTotal() {
			cexp = parseFloat(document.getElementById(\'price\').value)
			*parseFloat(document.getElementById(\'count\').value)
			+parseFloat(document.getElementById(\'delivery\').value)
			+parseFloat(document.getElementById(\'other\').value);
			document.getElementById(\'total\').value = cexp;
			countBalance();
		}
		
		function setNal() {
			document.getElementById(\'nal\').value = parseFloat(document.getElementById(\'count\').value);
			countTotal();
		}		

		function countBalance() {
			total = parseFloat(document.getElementById(\'price\').value)
			*parseFloat(document.getElementById(\'count\').value)
			+parseFloat(document.getElementById(\'delivery\').value)
			+parseFloat(document.getElementById(\'other\').value);
			cexp = parseFloat(document.getElementById(\'paid\').value)-
			total;
			document.getElementById(\'balance\').value = cexp;
		}		
		
		function setReadyDate() {
			document.getElementById(\'readyDate\').value = \''.date('Y-m-d').'\';
		}
		
		var id;
		function saveTopPhoto(id) {
					
		$.get("/?mod=stuff&sw=settopphoto", { top_photo: id, stuff: \''.$this->stuff_id.'\' },
			  function(data){
			  });
			 
		}
		
		var file_id = 1;
		var next_file;
		var holder;
		function addUploadFile() {
			next_file = file_id + 1;
			holder = \'up_\'+file_id;

			document.getElementById(holder).innerHTML = \'<input type="file" name="file_\'+next_file+\'"><span id="up_\'+next_file+\'">&nbsp;</span>\';
			file_id++;
		}
		
		function switchHidden(name) {
			var trig;
			if(document.getElementById(name).checked)	trig = 1;
			else trig = 0;
			document.getElementById(name+\'_trigger\').value=trig;
			
		}
		
		function jump2payments() {
			document.forms.showPayments.submit();
		}
		
		function doDieselRePost(sale, parent) {
			document.getElementById(\'repostInfo\').innerHTML = "<img src=img/loading.gif>";
			$.get("/?mod=repost&type=stuff", { sale: sale, parent: parent },
			  function(data){
			  	document.getElementById(\'repostInfo\').innerHTML = data;
			  });
			 
		}
		</script>
		'.$add_payment.'
		<form class="myForm" action="'.$this->root_path.'?mod=stuff'.$form_link.'" method="post">
		'.$inv_report.'
		<div class="cont_car" style="width:960px;">
		<h3>Товар</h3>
		<div style="position:absolute; margin-left: 692px; margin-top:3px; width:265px; background-color:#fff;">
		<table width="260" cellspacing="0" cellpadding="3" class="list" style="border:1px;">
		<tr class="rowB title"><td colspan="2"><b>РАСХОДЫ НА ТОВАР</b></td></tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Цена в Америке</td>
			<td><input type="text" name="price" value="'.$this->content['price'].'" tabindex="11" id="price" onchange="javascript:countTotal();"></td>
		</tr>		
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">Количество осталось</td>
			<td><input type="text" name="nal" value="'.$this->nal.'" id="nal" onchange="javascript:countTotal();" tabindex="12" readonly="readonly" style="border:0;font-weight:bold;background:#EFEFEF;"></td>
		</tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Доставка до порта</td>
			<td><input type="text" name="delivery" value="'.$this->content['delivery'].'" id="delivery" onchange="javascript:countTotal();" tabindex="13"></td>
		</tr>
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">Прочее</td>
			<td><input type="text" name="other" value="'.$this->content['other'].'" id="other" onchange="javascript:countTotal();" tabindex="14"></td>
		</tr>
		<tr><td colspan="2" class="title" height=12></td></tr>		
		<tr class="rowB title"><td colspan="2"><b>БАЛАНС</b></td></tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Всего за товар</td>
			<td><input type="text" name="total" value="'.$this->total.'" id="total" readonly="readonly" style="border:0;font-weight:bold;"></td>
		</tr>
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">Оплачено</td>
			<td class="rowA title">'.(!isset($_GET['add'])?'<div style="float:right;"><img src="img/ccl/bl_plus.gif" align="absmiddle" hspace=5 border="0" style="cursor:hand; cursor:pointer;" onclick="document.getElementById(\'addPayment\').style.display=\'\';"> <a onclick="document.getElementById(\'addPayment\').style.display=\'\';" style="cursor:hand;cursor:pointer;">платеж</a></div>':'').'
			<b><input type="text" name="paid" value="'.($this->payments['amount']?$this->payments['amount']:0).'" id="paid" readonly="readonly" style="border:0;font-weight:bold;width:40px;background:#EFEFEF;"></b>
		</td>
		</tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Баланс</td>
			<td><input type="text" name="balance" value="'.$this->balance.'" id="balance"  readonly="readonly" style="border:0;font-weight:bold;"></td>
		</tr>
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">Цена в инвойсе</td>
			<td><input type="text" name="invoice" value="'.$this->content['invoice'].'" id="invoice" tabindex="17"></td>
		</tr>
		</table>
		</div>
		<table width="691" border="0" cellpadding="0" cellspacing="0" class="list">
		  	<tr>
			  	<td valign=top width=57%>
				  	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
					  <tr>
					    <td align="right" class="title" width="120">
					    <div style="width:90px; float:right;">'.captionLink($this->content['buyer'], '0', ($_SESSION['user_type']==1?$this->root_path.'?mod=clients&sw=detail&id='.$this->content['buyer']:$this->root_path.'?mod=clients&sw=form&customer_id='.$this->content['buyer_id']), 'владелец').'</div>
					    <div style="float:left;"><img src="'.$this->root_path.'img/ccl/add.gif" border="0" style="cursor:pointer;" alt="Добавить клиента" title="Добавить клиента" onclick="javascript:showAddCustomer();"></div></td>
					    <td class="title" width="210">
						'.$car_owner.'
						'.$hiddenFields.'
						  '.$carsDealer.'
						  </td>
					  </tr>
					  <tr>
					    <td align="right" class="rowB title">Наименование</td>
					    <td class="rowB title"><input type="text" name="name" value="'.$this->content['name'].'" tabindex="2" /></td>
					  </tr>
					  <tr>
					    <td align="right" class="rowA title">Количество куплено</td>
					    <td class="rowA title"><input type="text" name="count" id="count" value="'.$this->content['count'].'" tabindex="3" '.(isset($_GET['add'])?'onchange="javascript:setNal();"':'onchange="javascript:countTotal();"').'/></td>
					  </tr>
					  <tr>
						<td class="title rowB" align="right">'.captionLink($this->content['transporter_id'], '0', $this->root_path.'?mod=transporters&sw=form&sup_id='.$this->content['transporter_id'], 'транспортник').'</td>
						<td class="rowB title">'.($_SESSION['user_type']=='5'?'<input type="hidden" value="'.$this->content['transporter_id'].'" tabindex="4" name="transporter_id">'.$this->content['sup_name']:$sup_list).'</td>
					  </tr>
					  <tr>
						<td class="rowA title" align="right">Порт</td>
						<td class="title">'.$ports_list.'</td>
					  </tr>
					  <tr>
						<td align="right" class="rowB title">Дата покупки</td>
						<td width="225" class="rowB title"><input type="text" name="date_buy" value="'.$this->content['date_buy'].'" tabindex="6" id="buyDate" style="width:100px;" />
						<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'buyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;">
						</td>
					  </tr>
					  <tr>
						<td class="rowA title" align="right">Тип транспортировки</td>
						<td class="title">'.$this->getPostList().'</td>
					  </tr>					  
			  		  <tr>
						<td align="right" class="title">'.captionLink($this->content['container'], '0', '?mod=containers&sw=form&cont_id='.$this->container['id'], 'Контейнер').'</td>
						<td class="rowA title">'.$this->container['number'].'</td>
					  </tr>
					  <tr>
						<td align="right" class="rowB title">Место назначения</td>
						<td class="rowB title">'.$place_list3.'</td>
					  </tr>
					  <tr>
					    <td align="right" class="rowA title">UID</td>
					    <td class="rowB title"><input type="text" name="uid" value="'.$this->content['uid'].'"  tabindex=9 readonly="readonly"/></td>
					  </tr>
				  	</table>
			  	</td>
			  	<td valign=top width=43% class="rowB title">
			  	Серийные номера<br>
			  	<textarea style="height:235px;" tabindex=10 name="serials">'.$this->content['serials'].'</textarea>
			  	</td>
		  	<tr>
		</table>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
		  	<tr>
			  	<td valign=top width=100% class="rowB title" colspan=5><b>МЕСТОНАХОЖДЕНИЕ</b></td>
		  	<tr>
			<tr>
				<td class="rowA title" align="right" width="120" height=30>Местонахождение</td>
				<td class="title" width="200">'.$place_list.'</td>
				<td class="rowA title" align="right" width="170">Доставлено в место назначения</td>
				<td class="title" width="140"><input type="checkbox" name="deliveried" style="width:20px;" tabindex=19 '.($this->content['deliveried']==1?'checked=checked':'').'></td>
				<td class="title" width="200">&nbsp;</td>				
			</tr>		  	
		</table>				
		</div>
		<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:968px">
		  <tr>
			<td class="title">'.(isset($_GET['add'])?'':'<a href="'.$this->root_path.'?mod=stuff&sw=delete&id='.intval($this->stuff_id).'&container='.$this->content['container'].'" class="delete" onClick="return confirm(\'Вы действительно хотите удалить этот товар?\')">удалить</a>').'</td>
			<td>'.((isset($_GET['add']) or ($this->nal<=0))?'':'<input type="button" onclick="document.getElementById(\'sellCar\').style.display=\'\'" value="Продать" id="save" style="width:100px;margin-right:30px;">').'</td>
			<td width="214" align="right" class="title">
			<input type="hidden" name="allow_inspection" value="'.$this->content['allow_inspection'].'" id="insp_allow_trigger">
			<input type="hidden" name="allow_codocs" value="'.$this->content['allow_codocs'].'" id="codocs_allow_trigger">
			<input type="submit" name="Submit" value="Сохранить" id="save" tabindex="20" /></td>
			<td width="8" align="right" class="title"><br />
				<br /></td>
		  </tr>
		  	  
		</table>
		</form>
		';

		if ($this->sell && mysql_num_rows($this->sell)>0)
		{
			$sell_list='';
			$i=0;
			while($line=mysql_fetch_array($this->sell))
			{
				$sell_list.="<tr class='row".($i%2==0?"A":"B")." title'>
					<td>{$line['sell_dat']}</td>
					<td>{$line['sell_count']}</td>
					<td>{$line['sell_price']}</td>
					<td><a href='{$this->root_path}?mod=stuff&sw=delete&what=sell&name={$line['id']}&stuff_id={$this->stuff_id}'><img src=/img/ccl/del_img.gif style='border:1px solid #BBBBBB;' title='удалить'></a></td>
				</tr>";
				$i++;
			}

			$this->page .='
		<div class="cont title" style="width:960px;background:#FFC7D5;">
		<b>ПРОДАЖИ ТОВАРА</b>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
		<tr class="rowB title">
			<tr  class="rowB title">
				<td><b>Дата продажи</b></td>
				<td><b>Количество товара</b></td>
				<td><b>Цена за единицу</b></td>
				<td>&nbsp;</td>
				</tr>
			'.$sell_list.'
		</table>
		</div>
		';

		}


		if($this->stuff_id!=0) {
			$list = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."tpl` WHERE type='t' ORDER BY name");
			$tpl_list = buildSelect($list, 'tpl', 0, 'Не указан', '17');
			if ($list && mysql_num_rows($list)>0)
			{
				$this->page .= '<script language="JavaScript">
				
					$(document).ready(function()
					{
						$(\'#Listtpl\').change( function()
						 	{
							var id=$("#Listtpl option:selected").val();
							if (id>0){
								$.get("/ajax/gettpl.php?id="+id, function(data){
									$("#sellComment").val(data);
									});
								} else 	$("#sellComment").val("");
						 	
						 	});
					});
			
					</script>';	


			}
			//выставить автомобиль на продажу
			$this->page .= '<form style="margin:0px;" action="'.$this->root_path.'?mod=stuff&sw=forsale&stuff_id='.$this->stuff_id.'" method="post">
			<table width="968" border="0" cellpadding="0" cellspacing="0" class="list">
			<tr class="rowB title">
				<td><input type="checkbox" name="sell" id="sell" onClick="document.getElementById(\'sellOptions\').style.display=\'\'"'.($this->content['sell_id']==''?'':' checked="checked"').'> <label for="sell">выставить на продажу</label> &nbsp; &nbsp;
				<a href="'.$this->root_path.'?mod=tpl&type=t">Редактировать шаблоны</a>
				</td></tr>
				<tr class="rowB title"><td align="right">
					<div id="sellOptions"'.($this->content['sell_id']==''?' style="display:none"':'').'>
					<table width="100%" >
					<tr class="rowB">
						<td colspan="2" valign=top>
							шаблон: '.$tpl_list.'
							<br>
							<a href="#" onclick="doDieselRePost('.$this->content['sell_id'].', '.$this->stuff_id.');">Выложить на Diesel еще раз</a>
							<br><span id="repostInfo"></span>
						</td>
						<td colspan="3">
							комментарий: 
							<textarea name="sellComment" id="sellComment" style="border:1px solid #bbb;width:100%;height:100px;" >'.$this->content['sell_comment'].'</textarea>
						
						</td>
						
					</tr>
					<tr>
						<td width="103">
							цена: <input type="text" name="sellPrice" size="5" style="border:1px solid #bbb;" value="'.$this->content['sell_price'].'">
						</td>
						<td>
							<input type="checkbox" name="sold" id="sold_check"'.($this->content['for_sold']==1?' checked="checked"':'').'><label for="sold_check">продан</label>
						</td>
						<td>
							выставить до: &nbsp;<input type="text" name="post_till" value="'.$this->content['sell_active_through'].'" id="posttillDate" style="border:1px solid #bbb;"><img src="img/ccl/cal.gif" border=0 onclick="show_calendar(\'posttillDate\', \'\', myDateFormat);" style="cursor:hand;cursor:pointer;">
						</td>
						<td align="right">&nbsp;<input type="hidden" name="sell_id" value="'.($this->content['sell_id']==''?0:$this->content['sell_id']).'">
							<input type="submit" value="сохранить">&nbsp;</td>
					</tr>
					</table>
				</div></td>
			</tr>
			
			</table></form> 
			';
			//################################

			$this->page .= '<div class="cont" style="width:960px;">
			<table border="0" width="955" class="list">
			<tr>
			<td width="725" valign="top">
				<table width="100%" class="list">
			<tr class="rowB">
				<td class="title"><b>Фотографии</b>
				</td>
				<td align="right" nowrap><a href="/?mod=stuff&sw=allphotos&stuff_id='.$this->stuff_id.'" target="_blank" class="delete">[+] все фотографии</a></td>
			</tr>
			<tr>
				<td colspan="2">
				'.$photo_list.'				
				</td>
			</tr>
			<tr class="rowB">
				<td class="title" colspan="2"><b>Отчет о состоянии автомобиля или CR</b></td>
			</tr>
			<tr>
				<td class="rowA title" valign="top" colspan="2">
				
				'.$cr_list.'
				
				</td>
			</tr>

			<tr class="rowB">
				<td class="title" valign="bottom"><b>ПП инспекция</b></td>
				<td><input type="checkbox" '.($this->content['allow_inspection']==1?' checked="checked"':'').' id="insp_allow" onchange="switchHidden(\'insp_allow\')"> <label for="insp_allow">показывать клиенту</label></td>
			</tr>
			<tr>
			<td valign="top" colspan="2">'.$inspection_file.'</td></tr>	
			
			<tr class="rowB">
				<td class="title" width="200"><b>Сопроводительные документы</b></td>
				<td><input type="checkbox" '.($this->content['allow_codocs']==1?' checked="checked"':'').' id="codocs_allow" onchange="switchHidden(\'codocs_allow\')"> <label for="codocs_allow">показывать клиенту</label></td></tr>
			<tr><td class="rowA title" valign="top" colspan="2">'.$adddoc_file.'</td></tr>

				</table>
			</td>
			<td valign="top">
			<table width="100%" class="list">
			<tr>
			<tr><td class="title"><b>Загрузить...</b></td></tr>
				<td class="title">
				Фотографии:<br>
				<form action="'.$this->root_path.'?mod=multiupload" method="post" class="myForm" enctype="multipart/form-data">
				'.$this->folderSelect().'<br>
				<input type="file" name="file_1"><br>
				<span id="up_1">&nbsp;</span>
				<input type="button" onclick="addUploadFile();" name="add" value="+" style="margin-top:5px;margin-bottom:5px;">
				<input type="hidden" name="owner" value="'.$this->stuff_id.'">
				<input type="hidden" name="type" value="stuff">
				<input type="submit" name="Submit" value="Загрузить" id="save" /></form>
				</td>
			</tr>
			<tr><td class="title">
			Отчет (CR):<br>
			<form action="'.$this->root_path.'?mod=stuff_upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="certImage" value="'.$this->stuff_id.'">
			<br><input type="submit" value="Загрузить" id="save"></form></td></tr>
			<tr><td class="title">
			ПП инспекция:<br>
			<form action="'.$this->root_path.'?mod=stuff_upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="uploadInspection" value="'.$this->stuff_id.'">
			<input type="submit" name="Submit" value="Загрузить" id="save" /></form></td>
			</tr>
			
			<tr><td class="title">
			сопроводительные документы:<br>
			<form action="'.$this->root_path.'?mod=stuff_upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="uploadAdddoc" value="'.$this->stuff_id.'">
			<input type="submit" name="Submit" value="Загрузить" id="save" /></form></td>
			</tr>

			
			</table>
			</td></tr></table>
			</div>';

		}

		//скрытая форма добавления клиента
		$this->page .= '
		<div style="position:absolute;left:0px;top:0px;display:none;z-index:74;width:100%;height:100%;cursor:arrow;background-color:#fff;filter:alpha(opacity=50);-moz-opacity: 0.5;-khtml-opacity: 0.5;opacity: 0.5;" id="fog">
		</div>
		
		<div style="display:none; position:absolute; top:110px; left:120px; width:835px; height:490px; background-color:#fff; z-index:76; border:1px solid #0056ca; font:menu;	background-image:url(\'img/ccl/loading.gif\');background-position:center;	background-repeat:no-repeat;" id="addCustomerContainer">
		<div style="position:absolute; right:60px;margin-top:4px;"><img src="'.$this->root_path.'img/ccl/new_client_button.gif" alt="Добавить еще одного клиента" title="Добавить еще одного клиента" onclick="document.getElementById(\'addCustomer\').src=\''.$this->root_path.'?mod=clients&sw=form&add&hidemenu\';document.getElementById(\'addCustomer\').style.display=\'none\'; this.src=\''.$this->root_path.'img/ccl/new_client_button_on.gif\'" style="cursor:pointer;" onMouseOut="this.src=\''.$this->root_path.'img/ccl/new_client_button.gif\'"></div>
		<img src="'.$this->root_path.'img/ccl/r_ex.gif" style="position:absolute; right:5px; top:5px;cursor:pointer;" alt="Закрыть окно" title="Закрыть окно" onclick="javascript:finishAddCustomer();">
		<iframe style="display:none;  width:830px; height:450px; margin:5px; margin-top:25px; z-index:77;" scrolling="no" frameborder="0" marginheight="0" marginwidth="0" id="addCustomer" src=""></iframe>
		</div>';
		//###################################

		//скрытая форма продажи товара
		$this->page .= '<div style="display:none;width:350px;position:absolute; left:400px; top:400px; background-color:#FFEEBA; border:1px solid #bbb; padding:5px; " id="sellCar">
		<form style="margin:0px;" action="'.$this->root_path.'?mod=stuff&sw=sell&stuff_id='.$this->content['id'].'" method="post" class="myForm" name="sellStuffForm" id="sellStuffForm">
		<table border="0" cellspacing="1" cellpadding="2" width="99%">
		<tr><td>
		<b>Продажа товара</b><img src="'.$this->root_path.'img/ccl/r_ex.gif" style="position:absolute; right:15px; top:9px;cursor:pointer;" alt="Закрыть окно" title="Закрыть окно" onclick="document.getElementById(\'sellCar\').style.display=\'none\';"></td></tr>
		</table>
		<table border="0" cellspacing="1" cellpadding="2" width="99%" class="list">
			<tr class="title"><td width=155>Название товара:</td><td><b>'.$this->content['name'].'</b></td></tr>
			<tr class="title"><td>Количество:</td><td><input type="text" name="sell_count" style="width:150px;" value='.$this->nal.'></td></tr>
			<tr class="title"><td>Цена за единицу:</td><td><input type="text" name="sell_price" style="width:150px;" ></td></tr>
			<tr class="title"><td>Дата продажи:</td><td><input type="text" name="sell_dat" id="sell_dat" value="'.date('Y-m-d').'" style="width:150px;">
			<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'sell_dat\', \'\', myDateFormat);" class="datePicker" style="margin:0px;">
			</td></tr>
			<tr><td colspan=2 align=right><br><input type="button" name="sendForm" value="Продать" id="save" onclick="if(confirm(\'Вы действительно желаете продать товар?\')){checkSellStuffForm();}" style="width:100px;margin-right:23px;"></tr>
		</table>
		'.$hiddenFields.'
		</form>
		</div>
		<script>
		function checkSellStuffForm() {
			if(document.sellStuffForm.sell_count.value == "") {
				alert(\'Количество товара не указано!\');
				document.sellStuffForm.sell_count.focus();
				return;
			}
			if(isNaN(document.sellStuffForm.sell_count.value)) {
				alert(\'Количество товара не является числом!\');
				document.sellStuffForm.sell_count.focus();
				return;
			}
			if(parseInt(document.sellStuffForm.sell_count.value)==0) {
				alert(\'Количество товара должно быть больше нуля!\');
				document.sellStuffForm.sell_count.focus();
				return;
			}
			if(document.sellStuffForm.sell_price.value==\'\') {
					alert(\'Не заполнено поле Цена за единицу!\');
					document.sellStuffForm.sell_price.focus();
					return;
				}
			if(document.sellStuffForm.sell_dat.value==\'\') {
					alert(\'Не указана дата продажи!\');
					document.sellStuffForm.sell_dat.focus();
					return;
				}
			document.sellStuffForm.submit();
		}
		</script>
		<form class="myForm" name="showPayments" action="?mod=payments&filter" method="post"><input type="hidden" name="searchCar" value="'.$this->stuff_id.'"><input type="hidden" name="searchClient" value="'.$this->content['buyer'].'"></form>';
	}

	function getPostList() {
		$list = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."post` WHERE 1 ORDER BY `name` ASC");
		$out = '<select name="post_id" tabindex=7>
		<option value=""> - не выбран - </option>
		<option value="-1" '.($this->content['post_id']==-1?'selected="selected"':'').'>Контейнер</option>
		';
		while($line=mysql_fetch_array($list)) {
			$out .= '<option value="'.$line['id'].'"';
			if($this->content['post_id']==$line['id'])
			{
				$out.=' selected="selected"';
			}
			$out.='>'.stripslashes($line['name']).'</option>
			';
		}
		$out .= '</select>';
		return $out;
	}

	function wrapFile($in, $bg='') {
		if($bg!='') $wrap_bg = 'background-color:'.$bg;
		return '<div style="float:left; width:128px; padding:2px; border:1px solid #ddd;text-align:center; margin-right:4px; margin-bottom:5px;'.$wrap_bg.'">
		'.$in.'</div>';
	}

	function typeSwitch() {
		$out = array();
		switch($this->content['type']) {
			case 1:
				$out['order'] = ' checked="checked"';
				break;
			case 2:
				$out['sale'] = ' checked="checked"';
				break;
			case 3:
				$out['cancel'] = ' checked="checked"';
				break;

		}

		return '<span style="color:#555;">
		<input type="radio" name="type" value="1" id="type_order"'.$out['order'].' style="width:20px;"><label for="type_order">заказ</label>
		<input type="radio" name="type" value="2" id="type_sale"'.$out['sale'].' style="width:20px;"><label for="type_sale">на продажу</label>
		<input type="radio" name="type" value="3" id="type_cancel"'.$out['cancel'].' style="width:20px;"><label for="type_cancel">отказ</label>
		</span>';
	}

	function folderSelect() {
		$out = '
		
		<select name="folder" style="width:120px;float:right;">';
		foreach ($this->photo_folders as $k=>$v) {
			$out .= '
			<option value="'.$k.'">'.$v.'</option>';
		}
		$out .= '</select>раздел:<br>';
		return $out;
	}

	function makeUid()
	{
		$newUid='';
		for ($i=0;$i<8;$i++)
		{
			$newUid.=rand(0,9);
		}
		return $newUid;
	}

	function checkUid($uid)
	{
		$sql=mysql_query("SELECT id FROM `ccl_".ACCOUNT_SUFFIX."stuff` WHERE uid='$uid'");
		return ($sql && mysql_num_rows($sql)>0);
	}

	function makeAndCheckUid()
	{
		$uid=$this->makeUid();
		if (!$this->checkUid($uid)) return $uid;  else $this->makeAndCheckUid();
	}


}


?>