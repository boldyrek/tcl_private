<?
include_once('class.Carscomment.php');
class CarsForm extends Proto {

	var $clients;
	var $transporters;
	var $container;
	var $content;
	var $files;
	var $invoice;
	var $car_id;
	var $ports;
	var $cr;
	var $inspection;
	var $payments;

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
		//require_once ('templates/form.car.php');

		if(!isset($_GET['add'])) {
			$this->car_id = intval($_GET['car_id']);

			$this->content = @mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."cars.*,ccl_".ACCOUNT_SUFFIX."customers.name, ccl_".ACCOUNT_SUFFIX."customers.dealer as isdealer, ccl_".ACCOUNT_SUFFIX."customers.id as buyer_id,
			ccl_".ACCOUNT_SUFFIX."forsale.id as sell_id, ccl_".ACCOUNT_SUFFIX."forsale.price as sell_price, ccl_".ACCOUNT_SUFFIX."forsale.comment as sell_comment, ccl_".ACCOUNT_SUFFIX."forsale.active_through as sell_active_through, ccl_".ACCOUNT_SUFFIX."forsale.sold as sold, ccl_".ACCOUNT_SUFFIX."transporters.name as sup_name
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."cars` 
			ON (ccl_".ACCOUNT_SUFFIX."customers.id=ccl_".ACCOUNT_SUFFIX."cars.buyer) 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."forsale`
			ON (ccl_".ACCOUNT_SUFFIX."forsale.car = ccl_".ACCOUNT_SUFFIX."cars.id)
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."transporters`
			ON (ccl_".ACCOUNT_SUFFIX."transporters.id = ccl_".ACCOUNT_SUFFIX."cars.transporter)
			WHERE ccl_".ACCOUNT_SUFFIX."cars.id ='".$this->car_id."'"));

		}
		else {
			$this->car_id = '0';
			$this->content['id'] = '0';
			$this->content['ready'] = '0';
			$this->content['type'] = 1;
		}

		if($this->content['id']=='') {
			$this->page .= '<div class="warn" style="width:900px;">Ошибка! Автомобиль с такими параметрами в базе не обнаружен</div>';
		}
		else {
			if (isset($_GET['success'])) $this->page .='<h4 class="report">Изменения сохранены</h4>';
			$this->getAdditional();
			$this->page .= $this->car_edit();
			if($this->permissions['cars']==1)
			{
				$comment = new CarsComment();
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
		if(!isset($_GET['add'])) {
			$this->files = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos`
			WHERE `car` = '".$this->car_id."' ORDER BY `folder` DESC, `id` DESC");
	
			$this->expeditorphoto = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."expeditors_photo`
			WHERE `car` = '".$this->car_id."' ORDER BY `id` DESC");
	
			$this->cr = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_cert`
			WHERE `car` = '".$this->car_id."' ORDER BY `id` DESC");
	
			$this->invoice = mysql_fetch_array($this->mysqlQuery("
			SELECT id
			FROM `ccl_".ACCOUNT_SUFFIX."invoices` 
			WHERE `carid` 
			= '".$this->car_id.";' LIMIT 1"));
	
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
			"SELECT * FROM `ccl_".ACCOUNT_SUFFIX."inspections`
			WHERE ccl_".ACCOUNT_SUFFIX."inspections.car = '".$this->car_id."'
			ORDER BY `id` DESC");
			
			$this->adddoc = $this->mysqlQuery(
			"SELECT * FROM `ccl_".ACCOUNT_SUFFIX."adddoc`
			WHERE ccl_".ACCOUNT_SUFFIX."adddoc.car = '".$this->car_id."'
			ORDER BY `id` DESC");
			
			$this->payments = mysql_fetch_array($this->mysqlQuery("
			SELECT SUM(amount) as amount FROM `ccl_".ACCOUNT_SUFFIX."payments`
			WHERE `car` = '".$this->car_id."'"));
		}
	}


	function car_edit() {

		//адрес формы
		if(!isset($_GET['add'])) $form_link = '&sw=save&id='.$this->car_id;
		else $form_link = '&sw=add';


		//проверка, может быть форма открывается после попытки добавить автомобиль с уже имеющимся в базе вин кодом
		if(isset($_GET['exists'])) {
			$this->page .= '<div class="warn" style="width:660px;">Автомобиль с таким вин кодом ('.$_SESSION['car_exists']['frame'].')
			<br>уже есть в базе: '.$_SESSION['car_exists']['model'].', владелец '.$_SESSION['car_exists']['buyer'].'!</div>';
			$content = $_SESSION['carForm'];
		}


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
		$recievers_list = $this->getRecieversList();
		if($this->content['buyer']=='0' or isset($_GET['add'])) {
			$car_owner = '<select name="buyer" tabindex="1" id="customerSelect">
		    <option value="0" selected="selected"> не выбран </option>
			'.$clients_list.'
		      </select>';
		}
		else $car_owner = '<span id="customerSelect"></span>'.
		$ownerInfo;
		//#######################################


		//фотографии автомобиля
		$photos = array();
		$num = @mysql_num_rows($this->files);
		if($num>0)
		{
			$j = 1;
			while($j<=$num)
			{
				$line = mysql_fetch_array($this->files);
				
				if($line['id']==$this->content['top_photo']) $top_bg = '#d2f4d9';
				else $top_bg = '';
				
				$photos[$line['folder']] .= $this->wrapFile('
				<input type="radio" name="top_photo" value="'.$this->car_id.'" onClick="saveTopPhoto(\''.$line['id'].'\')" '.($line['id']==$this->content['top_photo']?' checked="checked"':'').'>
				<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'photos/'.$this->car_id.'/thumb/'.$line['file'].'" border="0"></a><br>
				<a href="'.$this->root_path.'?mod=cars&sw=delete&what=photo&name='.$line['file'].'&chk='.$this->car_id.'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить это фото?\')">удалить</a>', $top_bg);
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
		else $photo_list = '';



		//фотографии автомобиля от экспедитора
		if(mysql_num_rows($this->expeditorphoto)) {
			$expeditorphoto = '<table width="100%" class="list">
			<tr class="rowB"><td class="title"><b>Фотографии экспедитора</b><tr><td>';
			while($line = mysql_fetch_array($this->expeditorphoto)) {
				$expeditorphoto .= $this->wrapFile('<a href="'.$this->root_path.'upload/expeditors_photo/'.$line['file'].'.jpg" target="_blank" alt="'.$line['descr'].'"><img src="'.$this->root_path.'upload/expeditors_photo/'.$line['file'].'.thumb.jpg" border=0></a><br><a href="'.$this->root_path.'?mod=cars&sw=delete&what=expeditorsphoto&name='.$line['file'].'&car='.$this->car_id.'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить это фото?\')">удалить</a>');
			}
			$expeditorphoto .= '</table>';
		} else $expeditorphoto = '';


		//#######################################

		//Отчет о состоянии автомобиля или CR
		$num = @mysql_num_rows($this->cr);
		$cr_list = '';
		if($num>0)
		{
			while($line = mysql_fetch_array($this->cr)) {
				$ico = fileIco($line['file']);
				$cr_list .= $this->wrapFile('<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank">
				<img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"><br>
				<a href="'.$this->root_path.'?mod=cars&sw=delete&what=certificate&name='.$line['file'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить Отчет о состоянии автомобиля или CR?\')">удалить</a>');
			}
		}
		//#######################################

		//форма выбора поставщиков
		$sup_list = buildSelect($this->transporters, 'transporter', $this->content['transporter'], 'не выбран', '15');
		$place_list1 = buildSelectArray($this->places, 'place1', $this->content['place_id1'], 'пусто поле', '17');
		$place_list2 = buildSelectArray($this->places, 'place2', $this->content['place_id2'], 'пусто поле', '18');
		$place_list3 = buildSelect($this->places3, 'place3', $this->content['place_id3'], 'не выбрано', '19');

		//#######################################

		//вывод портов для выбора в зависимости от поставщика
		
		$num = @mysql_num_rows($this->ports);
		if($num>0) {
			$ports_list = buildSelect($this->ports, 'port', $this->content['port'], 'не выбран', '16');
		}
		else $ports_list = ' нет портов! ';
	
	
		//#######################################

		//готовим ссылку на инвойс этого автомобиля

		if($this->content['invoice_file']!='') {
			$invoiceLink .= '
				<a href="'.$this->root_path.'?mod=invoices&sw=file&inv_id='.$this->content['invoice_file'].'" target="_blank"><img src="'.$this->root_path.'img/ccl/doc_ico.gif" align="absmiddle" hspace="3" border="0">инвойс</a><br>';
		}
		else $invoiceLink = 'нет инвойса';


		//#############################

		//дней со дня покупки
		if(isset($_GET['add'])) $this->content['buy_date'] = date("Y-m-d");
		else {
			if($this->content['buy_date']!='' and $this->content['buy_date']!='0000-00-00') {
				if($this->container['bishkek']!='0000-00-00' and $this->container['arrived']=='1') {
					$carArriveDate = explode('-',$this->container['bishkek']);
				}
				else $carArriveDate = explode('-',date("Y-m-d"));
				$buyDate = explode("-",$this->content['buy_date']);
				$daysPassed = round((mktime(0,0,0,$carArriveDate[1], $carArriveDate[2], $carArriveDate[0]) - mktime(0,0,0,$buyDate[1], $buyDate[2], $buyDate[0]))/(60*60*24));

				//считаем сколько месяцев прошло
				$month = 0;
				while($daysPassed>30) {
					if($daysPassed>30) {
						$months++;
						$daysPassed = $daysPassed-30;
					}
				}

				$monthLang = array(
				'1' => 'месяц',
				'2' => 'месяца',
				'3' => 'месяца',
				'4' => 'месяца',
				'5' => 'месяцев',
				'6' => 'месяцев',
				'7' => 'месяцев',
				'8' => 'месяцев',
				'9' => 'месяцев',
				'10' => 'месяцев',
				'11' => 'месяцев',
				'12' => 'месяцев');

				$daysLang = array(
				'0' => 'дней',
				'1' => 'день',
				'2' => 'дня',
				'3' => 'дня',
				'4' => 'дня',
				'5' => 'дней',
				'6' => 'дней',
				'7' => 'дней',
				'8' => 'дней',
				'9' => 'дней',
				);

				if(strlen($daysPassed)==1) $daysText = $daysLang[$daysPassed];
				elseif(strlen($daysPassed)>1) {
					if(substr($daysPassed, 0, 1)==1) $daysText = 'дней';
					else $daysText = $daysLang[substr($daysPassed, 1, 1)];
				}
				$daysPassed = 'с покупки прошло: '.($months!=0?'<b>'.$months.'</b> '.$monthLang[$months].' и ':'').'<b>'.$daysPassed.'</b> '.$daysText;
			}
		}
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


		// заметка об автомобиле
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') {
			$admin_note = '<tr>
			<td class="title rowB rowA" align="right"><input type="checkbox" style="width:15px;" name="ready" '.($this->content['ready']=='1'?' checked="checked"':'').' id="ready"></td>
			<td class="rowA rowB title"><label for="ready" onclick="document.getElementById(\'date_ready\').style.display=\'\';setReadyDate();">'.($this->content['ready']=='1'?'<b>':'').'готова к отправке</label><br>
			<div id="date_ready" '.($this->content['ready']=='0'?'style="display:none;"':'').'>дата готовности:<br>
			<input type="text" name="date_ready" id="readyDate" value="'.$this->content['date_ready'].'" style="width:90%;">
			<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'readyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></div>
			'.$daysFromReadyPassed.'</td>
			<td class="title rowA rowB" colspan="2"><textarea name="notice" rows="1">'.$this->content['notice'].'</textarea></td>
		  </tr>';
		}
		else $admin_note = '';
		//#######################

		//считаем сумму расходов на автомобиль
		$carExpences = 0;

		$carExpences =
		$this->content['price_jp']
		+$this->content['aucfee']
		+$this->content['dealer_comission']
		+$this->content['inspection']
		+$this->content['cost_to_port']
		+$this->content['cost_to_destination']
		+$this->content['unload']
		+$this->content['insurance']
		+$this->content['other'];
		//#######################

		// список аукицонов
		$aucList = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."auctions` WHERE 1 ORDER BY name ASC");
		$aucSelect = $this->buildSelect($aucList, 'auction', $this->content['auction'], ' - не выбран -', '15');
		//##########################

		// файл после-продажной инспекции
		if(mysql_num_rows($this->inspection)>0) {

			while($line = mysql_fetch_array($this->inspection)) {

				$ico = fileIco($line['file']);

				$inspection_file .= $this->wrapFile('
			<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a><br>
			<a href="'.$this->root_path.'?mod=cars&sw=delete&what=inspection_file&name='.$line['file'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить После Продажную инспекцию?\')">удалить</a>');

			}
		}

		// файл Сопроводительный документ
		if(mysql_num_rows($this->adddoc)>0) {

			while($line = mysql_fetch_array($this->adddoc)) {

				$ico = fileIco($line['file']);

				$adddoc_file .= $this->wrapFile('
			<a href="'.$this->root_path.'photos/'.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a><br>
			<a href="'.$this->root_path.'?mod=cars&sw=delete&what=adddoc_file&name='.$line['file'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить Сопроводительный документ?\')">удалить</a>');

			}
		}

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
		<form action="?mod=payments&sw=add&return='.$this->car_id.'" class="myForm" method="post">
		<table class="list" width="230">
		<tr class="rowA"><td colspan="2"><img src="/img/ccl/r_ex.gif" align="right" style="cursor:hand; cursor:pointer;float:right;" onclick="document.getElementById(\'addPayment\').style.display=\'none\';showSelects();">
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
		<input type="hidden" name="car" value="'.$this->car_id.'">
		<input type="hidden" name="client" value="'.$this->content['buyer'].'">
		</form>
		</div>
		';
		//###############################################
			
		$smallColWidth = '150';
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
		function countCarExpenses() {
			cexp = parseFloat(document.getElementById(\'aucprice\').value)
			+parseFloat(document.getElementById(\'aucfee\').value)
			+parseFloat(document.getElementById(\'dealer_comission\').value)
			+parseFloat(document.getElementById(\'inspection\').value)
			+parseFloat(document.getElementById(\'cost_to_port\').value)
			+parseFloat(document.getElementById(\'cost_to_destination\').value)
			+parseFloat(document.getElementById(\'unload\').value)
			+parseFloat(document.getElementById(\'insurance\').value)
			+parseFloat(document.getElementById(\'other\').value);
			document.getElementById(\'carExpenses\').innerHTML = cexp;
		}
		function setReadyDate() {
			document.getElementById(\'readyDate\').value = \''.date('Y-m-d').'\';
		}
		var id;
		function saveTopPhoto(id) {
					
			$.get("/?mod=cars&sw=settopphoto", { top_photo: id, car: \''.$this->car_id.'\' },
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
		
		function hideSelects() {
			document.getElementById(\'Listtransporter\').style.display=\'none\';
			document.getElementById(\'Listport\').style.display=\'none\';
		}
		
		function showSelects() {
			document.getElementById(\'Listtransporter\').style.display=\'\';
			document.getElementById(\'Listport\').style.display=\'\';
		}
		function jump2payments() {
			document.forms.showPayments.submit();
		}
		</script>
		'.$add_payment.'
				<form class="myForm" action="'.$this->root_path.'?mod=cars'.$form_link.'" method="post">
		'.$inv_report.'
		<div class="cont_car" style="width:960px;">
		<h3>Автомобиль</h3>
		<div style="position:absolute; margin-left: 692px; margin-top:3px; width:265px; background-color:#fff;">
		<table width="260" cellspacing="0" cellpadding="3" class="list" style="border:0px;">
		<tr class="rowB title"><td colspan="2"><b>Расходы на автомобиль</b></td></tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">цена в Америке на аукционе</td>
			<td><input type="text" name="price_jp" value="'.$this->content['price_jp'].'" tabindex="10" id="aucprice" onchange="javascript:countCarExpenses();"></td>
		</tr>		
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">Аукционный сбор</td>
			<td><input type="text" name="aucfee" value="'.$this->content['aucfee'].'" id="aucfee" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Комиссия дилера</td>
			<td><input type="text" name="dealer_comission" value="'.$this->content['dealer_comission'].'" id="dealer_comission" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">ПП инспекция</td>
			<td><input type="text" name="inspection" value="'.$this->content['inspection'].'" id="inspection" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr><td colspan="2" class="title">&nbsp;</td></tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Доставка до порта</td>
			<td><input type="text" name="cost_to_port" value="'.$this->content['cost_to_port'].'" id="cost_to_port" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">Доставка до места назначения</td>
			<td><input type="text" name="cost_to_destination" value="'.$this->content['cost_to_destination'].'" id="cost_to_destination" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr><td colspan="2" class="title">&nbsp;</td></tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Разгрузка</td>
			<td><input type="text" name="unload" value="'.$this->content['unload'].'" id="unload" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">Страховка</td>
			<td><input type="text" name="insurance" value="'.$this->content['insurance'].'" id="insurance" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr><td colspan="2" class="title">&nbsp;</td></tr>
		<tr class="rowA title">
			<td width="'.$smallColWidth.'">Прочее</td>
			<td><input type="text" name="other" value="'.$this->content['other'].'" id="other" onchange="javascript:countCarExpenses();"></td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr class="rowB title">
			<td width="'.$smallColWidth.'">ВСЕГО:</td>
			<td id="carExpenses">'.$carExpences.'</td></tr>
		</table>
		</div>
		
		<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
		    <td align="right" class="title" width="120">
		    <div style="width:90px; float:right;">'.captionLink($this->content['buyer'], '0', ($_SESSION['user_type']==1?$this->root_path.'?mod=clients&sw=detail&id='.$this->content['buyer']:$this->root_path.'?mod=clients&sw=form&customer_id='.$this->content['buyer']), '<strong>владелец</strong>').'</div>
		    <div style="float:left;"><img src="'.$this->root_path.'img/ccl/add.gif" border="0" style="cursor:pointer;" alt="Добавить клиента" title="Добавить клиента" onclick="javascript:showAddCustomer();"></div></td>
		    <td class="title" style="border:1px solid #ACE3AC;" width="210">
			'.$car_owner.'
			'.$hiddenFields.'
			  '.$carsDealer.'
			  </td>
			<td align="right" class="title">дата покупки</td>
			<td width="225" class="rowA title"><input type="text" name="buy_date" value="'.$this->content['buy_date'].'" tabindex="9" id="buyDate" style="width:90%;" />
			<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'buyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;">
			<br>'.$daysPassed.'</td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">
		    <div style="width:90px; float:right;">
		    '.captionLink($this->content['reciever'], '0', $this->root_path.'?mod=recievers&sw=form&id='.$this->content['reciever'], 'получатель').'
		    </div>
		     <div style="float:left;"><a href="'.$this->root_path.'?mod=recievers&sw=form&add"><img src="'.$this->root_path.'img/ccl/add.gif" border="0" style="cursor:pointer;" alt="Добавить получателя" title="Добавить получателя"></a></div></td>
		    <td class="rowB title">
		    '.$this->getRecieversList().'
			</td>
			<td align="right" class="rowB title">всего<br> за автомобиль</td>
			<td class="rowB title"><input type="text" name="total" value="'.$this->content['total'].'" tabindex="11">
			<input type="hidden" name="lastPrice" value="'.$this->content['total'] .'"></td>
		  </tr>
		  <tr>
		    <td align="right" class="title">название</td>
		    <td class="rowA title"><input type="text" name="model" value="'.$this->content['model'].'" tabindex="2" /></td>
			<td align="right" class="title">оплачено</td>
			<td class="rowA title"><div style="float:right;"><img src="img/ccl/bl_plus.gif" align="absmiddle" hspace=5 border="0" style="cursor:hand; cursor:pointer;" alt="Добавить услугу" onclick="document.getElementById(\'addPayment\').style.display=\'\';"> <a onclick="document.getElementById(\'addPayment\').style.display=\'\';hideSelects();" style="cursor:hand;cursor:pointer;">добавить платеж</a></div>
			<a onclick="jump2payments();" href="#">'.$this->payments['amount'].'</a>
		</td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">вин код</td>
		    <td class="rowB title"><input type="text" name="frame" value="'.$this->content['frame'].'" tabindex="4" /></td>
			<td align="right" class="title rowB">баланс</td>
			<td class="rowB title">'.($this->content['total']-$this->content['paid']-$this->payments['amount']).'</td>
		  </tr>
		  <tr>
		    <td align="right" class="title">дата выпуска</td>
		    <td class="rowA title"><input type="text" name="year" value="'.$this->content['year'].'" tabindex="5" /></td>
			<td align="right" class="title">&nbsp;</td>
			<td class="rowA title">&nbsp;</td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">объем двигателя</td>
		    <td class="rowB title"><input type="text" name="engine" value="'.$this->content['engine'].'" maxlength="4" tabindex="6"/></td>
			<td align="right" class="title rowB">цена в инвойсе</td>
			<td class="rowA rowB title"><input type="text" name="invoice" value="'.$this->content['invoice'].'" tabindex="13" /></td>
		  </tr>
		  <tr>
		    <td align="right" class="title">вес</td>
		    <td class="rowA title"><input type="text" name="weight" value="'.$this->content['weight'].'" tabindex="7" style="width:50px;">кг &nbsp;
		    &nbsp;&nbsp;&nbsp;объем: <input type="text" name="volume" value="'.$this->content['volume'].'" tabindex="8" style="width:50px;">м<sup>3</sup></td>
			<td align="right" class="title">'.captionLink($this->content['container'], '0', '?mod=containers&sw=form&cont_id='.$this->container['id'], 'контейнер').'</td>
			<td class="rowA title">'.$this->container['number'].'</td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">пробег</td>
		    <td class="rowB title"><input type="text" name="milage" value="'.$this->content['milage'].'"></td>
			<td class="title rowB" align="right">'.captionLink($this->content['transporter'], '0', $this->root_path.'?mod=transporters&sw=form&sup_id='.$this->content['transporter'], 'транспортник').'</td>
			<td class="rowB title">'.($_SESSION['user_type']=='5'?'<input type="hidden" value="'.$this->content['transporter'].'" name="transporter">'.$this->content['sup_name']:$sup_list).'</td>
		  </tr>
		  	  <tr>
			<td class="title"></td>
			<td class="rowA title">'.$invoiceLink.'</td>
			<td class="rowA title" align="right">порт</td>
			<td class="title">'.$ports_list.'</td>
		  </tr>
		  	<tr>
			<td align="right" class="title rowB">дата реальной доставки</td>
			<td class="rowA rowB title">
				<input type="text" name="date_realydeliver" id="realyDate" value="'.($this->content['date_realydeliver']!="0000-00-00"?$this->content['date_realydeliver']:"").'" style="width:90%;">
				<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'realyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;">
			
			</td>
			<td align="right" class="title rowB">статус транспортировки</td>
			<td class="rowA rowB title">'.($this->content['pickedup']=='1'&&$this->content['transstatus']=='0'?'<b>забрал</b>':'').($this->content['transstatus']=='1'?'<b>доставлена</b>':'').'</td>
		  </tr>

		  <tr>
			<td class="title rowB">&nbsp;</td>
			<td class="rowA rowB title">&nbsp;</td>
			<td class="title rowB">&nbsp;</td>
			<td class="rowA rowB title">'.($this->content['delivered']=='1'?'доставлена':'').'</td>
		  </tr>
		  
		  <tr>
			<td align="right" class="title">Местонахождение автомобиля</td>
			<td class="rowA title">'.$place_list1.'</td>
			<td align="right" class="title">Тайтл</td>
			<td class="rowA title">'.$place_list2.'</td>
		  </tr>
		  
		  
		  <tr>
			<td align="right" class="title rowB">Аукцион</td>
			<td class="rowA title rowB">'.$aucSelect.'</td>
			<td align="right" class="title rowB">место назначения</td>
			<td class="rowA title rowB">'.$place_list3.'</td>
		  </tr>
		 '.$admin_note.'		 
		</table>
		</div>
		'.(isset($_GET['add'])?'
		<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:968px">
		  <tr>
		  <td>&nbsp;</td>
		  	<td class="title" width="230">
			<input type="checkbox" name="invite_user" id="invite" style="width:20px;"><label for="invite">&nbsp;пригласить клиента</label>
			</td></tr>
			</table>
		':'').'
		<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:968px">
		  <tr>
			<td class="title"><a href="'.$this->root_path.'?mod=cars&sw=delete&id='.intval($this->car_id).'&container='.$this->content['container'].'" class="delete" onClick="return confirm(\'Вы действительно хотите удалить этот автомобиль?\')">удалить</a></td>
			<td class="title" align="center">'.$this->typeSwitch().'</td>
			<td><input type="button" onclick="document.getElementById(\'sellCar\').style.display=\'\'" value="Продать" id="save" style="width:100px;margin-right:30px;color:#999;"></td>
			<td width="214" align="right" class="title">
			<input type="hidden" name="allow_inspection" value="'.$this->content['allow_inspection'].'" id="insp_allow_trigger">
			<input type="hidden" name="allow_codocs" value="'.$this->content['allow_codocs'].'" id="codocs_allow_trigger">
			<input type="hidden" name="last_owner" value="'.$this->content['buyer_id'].'">
			<input type="submit" name="Submit" value="Сохранить" id="save" tabindex="17" /></td>
			<td width="8" align="right" class="title"><br />
				<br /></td>
		  </tr>
		  	  
		</table>
		</form>
		';

		if($this->car_id!=0) {

			//выставить автомобиль на продажу
			$this->page .= '<form style="margin:0px;" action="'.$this->root_path.'?mod=cars&sw=forsale&car_id='.$this->car_id.'" method="post">
			<table width="702" border="0" cellpadding="0" cellspacing="0" class="list">
			<tr class="rowB title">
				<td><input type="checkbox" name="sell" id="sell" onClick="document.getElementById(\'sellOptions\').style.display=\'\'"'.($this->content['sell_id']==''?'':' checked="checked"').'> <label for="sell">выставить на продажу</label> &nbsp; &nbsp;</td></tr>
				<tr class="rowB title"><td align="right">
					<div id="sellOptions"'.($this->content['sell_id']==''?' style="display:none"':'').'>
					<table width="100%">
					<tr class="rowB">
						<td width="103">
							цена: <input type="text" name="sellPrice" size="5" style="border:1px solid #bbb;" value="'.$this->content['sell_price'].'">
							
						</td>
						<td colspan="2">
							комментарий: <input type="text" name="sellComment" style="border:1px solid #bbb;width:80%;" value="'.$this->content['sell_comment'].'">
							
						</td>
						
					</tr>
					<tr>
						<td>
							<input type="checkbox" name="sold" id="sold_check"'.($this->content['sold']==1?' checked="checked"':'').'><label for="sold_check">продан</label>
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
				<td align="right" nowrap><a href="/?mod=cars&sw=allphotos&car_id='.$this->car_id.'" target="_blank" class="delete">[+] все фотографии</a></td>
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
				<input type="hidden" name="foto_number" value="1">
				<input type="hidden" name="owner" value="'.$this->car_id.'">
				<input type="submit" name="Submit" value="Загрузить" id="save" /></form>
				</td>
			</tr>
			<tr><td class="title">
			Отчет (CR):<br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="certImage" value="'.$this->car_id.'">
			<br><input type="submit" value="Загрузить" id="save"></form></td></tr>
			<tr><td class="title">
			ПП инспекция:<br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="uploadInspection" value="'.$this->car_id.'">
			<input type="submit" name="Submit" value="Загрузить" id="save" /></form></td>
			</tr>
			
			<tr><td class="title">
			сопроводительные документы:<br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="uploadAdddoc" value="'.$this->car_id.'">
			<input type="submit" name="Submit" value="Загрузить" id="save" /></form></td>
			</tr>

			
			</table>
			</td></tr></table>
			</div>';

			$this->page .= ($expeditorphoto?'<div class="cont_customer" style="width:694px;">'.$expeditorphoto.'</div>':'');

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

		//скрытая форма продажи автомобиля другому клиенту
		$this->page .= '<div style="display:none;width:240px;position:absolute; left:705px; top:400px; background-color:#fff; border:1px solid #bbb; padding:5px; background-color:#c33" id="sellCar">
		<form style="margin:0px;" action="'.$this->root_path.'?mod=cars&sw=sell&car_id='.$this->content['id'].'" method="post" class="myForm" name="sellCarForm" id="sellCarForm">
		<table border="0" cellspacing="1" cellpadding="2" width="99%" class="list">
		<tr class="title"><td>
		<b>Продажа автомобиля</b><img src="'.$this->root_path.'img/ccl/r_ex.gif" style="position:absolute; right:15px; top:9px;cursor:pointer;" alt="Закрыть окно" title="Закрыть окно" onclick="document.getElementById(\'sellCar\').style.display=\'none\';"></td></tr>
		<tr class="title"><td><span style="color:#008800"><b>Продавец:<b></span><br> '.$ownerInfo.'<br></td></tr>
		<tr class="title"><td><span style="color:#990000"><b>Покупатель:</b></span> <select name="newOwner">'.$clients_list.'	</select><br></td></tr>
		<tr class="title"><td>Цена: <input type="text" name="carNewPrice" style="width:150px;"></td></tr>
		<tr><td><br><input type="button" name="sendForm" value="Отправить" id="save" onclick="if(confirm(\'Вы действительно хотите сменить владельца автомобиля?\')){checkSellCarForm();}">
		<br></td></tr>
		</table>
		'.$hiddenFields.'
		<input type="hidden" name="oldPrice" value="'.$this->content['total'].'">
		<input type="hidden" name="carFrame" value="'.$this->content['frame'].'">
		<input type="hidden" name="sellDate" value="'.date('Y-m-d').'">
		</form>
		</div>
		<script>
		function checkSellCarForm() {
			if(sellCarForm.owner.value == sellCarForm.newOwner.value) {
				alert(\'Покупатель и продавец одно лицо!\');
				exit;
			}
			else { 
				if(sellCarForm.carNewPrice.value==\'\') {
					alert(\'Не заполнено поле ЦЕНА!\');
					exit;
				}
				else sellCarForm.submit();
			
			}
		}
		</script>
		<form class="myForm" name="showPayments" action="?mod=payments&filter" method="post"><input type="hidden" name="searchCar" value="'.$this->car_id.'"><input type="hidden" name="searchClient" value="'.$this->content['buyer'].'"></form>';
	}

	function getRecieversList() {
		$list = $this->mysqlQuery("SELECT id,name FROM `ccl_".ACCOUNT_SUFFIX."recievers` WHERE 1 ORDER BY name ASC");
		$out = '<select name="reciever">
		<option value="0"';
		if($this->content['reciever']==0) $out .= ' selected="selected"';
		$out .= '> - не выбран - </option>';

		while($line=mysql_fetch_array($list)) {

			$out .= '<option value="'.$line['id'].'"';
			if($this->content['reciever']==$line['id']) {
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
}

?>