<?

class PublicBalance extends Proto {
	
	private $arrived_filter;
	protected $client;
	protected $date;
	protected $month_ago;
	protected $cars;
	protected $payments;
	protected $mypayments;
	
	function makePage() {	
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->moduleContent();
			$this->page .= $this->module_content;
		}
		else Proto::redirect('/');
		
		$this->page .= $this->templates['footer'];
		
		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		$this->errorsPublisher();
		$this->publish();
		
	}
	
	private function moduleContent() {
			if($this->exists($_GET['sw'])) {
			switch($_GET['sw']) {
				default:
					$this->drawContent();
					break;
			}
		}
		else {
			$this->drawContent();
		}
	}
	
	private function drawContent() {		
		$this->getContent();
		
		$cars_total = 0; //общая сумма за доставленные автомобили
		$paid_total = 0; //всего клиент оплатил
		$ontheway = 0; //машин в пути
		$prepay = 0; //предоплата за недоставленные машины
		$delivered = 0; //доставленные автомобили
		
		if(isset($_GET['print'])) {
			require($_SERVER['DOCUMENT_ROOT'].'/mod/clients/print_balance.php');
		}
		else {
		
		//список платежей клиента
		$payments_list = '<table class="list" width="100%">
		<tr class="title">
		<td width="80">'.$this->translate->_('дата').'</td>
		<td>'.$this->translate->_('комментарий').'</td>
		<td width="40">'.$this->translate->_('сумма').'</td></tr>';
		$class = "rowA rowB";
		if($this->client['dealer']==1) {
			$j=1;
			$num = mysql_num_rows($this->mypayments);
			if($num>0) { 
				while($j<=$num)	{
					$line=mysql_fetch_array($this->mypayments);
					
					$carPayments[$line['car']] = $carPayments[$line['car']]+$line['amount'];
					$paid = $paid + $line['amount'];
					$cur_date = explode('-',$line['date']);
					$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
					
					if($_SESSION['show_detail_payments']=='0' and $tstamp<$this->month_ago) $j++;
					else {
					$payments_list .= '
					<tr class="'.$class.'" style="background-color:#cceecc;" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
					<td>'.$line['date'].'</td>
					<td>'.$line['comment'].'&nbsp;</td>
					<td>'.$line['amount'].'</td></tr>
					';
					if($class=="rowA") $class = "rowA rowB";
					else $class = "rowA";
					$j++;
					}
					$paid_total = $paid_total + $line['amount'];
				}
			}
			$legend = '<br>
		<span style="color:#55aa55; font-size:11px;">'.$this->translate->_('* зеленым цветом выделены платежи от вашего имени.').'</span>';
		}
		$j=1;
		$num = mysql_num_rows($this->payments);
		while($j<=$num)
		{
			$line=mysql_fetch_array($this->payments);
		
			$carPayments[$line['car']] = $carPayments[$line['car']]+$line['amount'];
			$paid = $paid + $line['amount'];
			$cur_date = explode('-',$line['date']);
			$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
					
			if($_SESSION['show_detail_payments']=='0' and $tstamp<$this->month_ago) $j++;
			else {
				$payments_list .= '
				<tr  class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
				<td>'.$line['date'].'</td>
				<td>'.$line['comment'].'&nbsp;</td>
				<td>'.$line['amount'].'</td></tr>
				';
				if($class=="rowA") $class = "rowA rowB";
				else $class = "rowA";
				$j++;
			}
			$paid_total = $paid_total + $line['amount'];
		
		}
		$payments_list .= '</table>';
		
		//делаем список автомобилей заказанных клиентом
		$j=1;
		$num = mysql_num_rows($this->cars);
		$class= "rowA rowB";
		$cars_list = '
		<table class="list" width="100%">
		<tr class="title">
		<td>'.$this->translate->_('модель').'</td>
		<td>'.$this->translate->_('вин код').'</td>
		<td width="40">'.$this->translate->_('всего').'</td>
		<td width="40">'.$this->translate->_('оплачено').'</td>
		<td width="40">'.$this->translate->_('баланс').'</td>
		</tr>';
		
		while($j<=$num)
		{
			$line=mysql_fetch_array($this->cars);
			$cars_price = $cars_price + $line['total'];
			if($line['delivered']=='1') {
					$cars_total = $cars_total + $line['total'];
					$delivered++;
			}
			else {
				$prepay = $prepay + $line['prepay'];
				$ontheway++;
			}
						
			if(($line['delivered']=='1' and $_SESSION['show_arrived_detail_cars']=='1') or $line['delivered']=='0') {
				if($line['delivered']=='1') $class = "greenTR";
				$cars_list .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$this->root_path.'public/?mod=cars&sw=form&car_id='.$line['id'].'\'">
				<td valign="top">'.$line['model'].'</td>
				<td style="font-family:monospace;" valign="top">'.substr($line['frame'],0,2).'..'.substr($line['frame'],(strlen($line['frame'])-6),6).'&nbsp;</td>';
				
				$cars_list .= '<td valign="top"><b>'.$line['total'].'</b></td>
				<td valign="top">'.$carPayments[$line['id']].'</td>
				<td valign="top">'.($line['total']-$carPayments[$line['id']]).'</td></tr>
				';
			}
			if($class=="rowA") $class = "rowA rowB";
			else $class = "rowA";
			$j++;
		}
		$carPayments = array();
		$cars_list .= '</table>';

		
		
		
		//вывод всего вышесозданного
		
		$this->page .= '<div class="cont_customer" style="width:800px">
		<div style="float:right; width:130px;font-size:11px;">
		<a href="'.$this->root_path.$this->user_folder.'/?mod=printbalance" target="_blank">
		<img src="'.$this->root_path.'img/ccl/printer.gif" align="absmiddle" border="0" hspace="3">'.$this->translate->_('версия для печати').'</a></div>
		<h3><span style="color:#6ba6bd;">'.$this->translate->_('Детализация баланса для:').'</span> <a href="'.$this->root_path.'public?mod=private">'.$this->client['name'].'</a></h3>
		
		<table cellspacing="1" cellpadding="0" width="350" border="0" class="list">
		
		<tr class="rowB title">
		  <td width="85">'.$this->translate->_('ЗАКАЗАНО').'<br />
		    '.$this->translate->_('на   сумму:').'</td>
		  <td width="35" align="center">'.($delivered+$ontheway).'</td>
		  <td width="72">'.$this->translate->_('ОПЛАЧЕНО:').'</td>
		  <td>'.$this->translate->_('МОЙ БАЛАНС:').'</td>
		</tr>
		<tr class="title">
		  <td align="center" colspan="2" class="rowB">'.$cars_price.'</td>
		  <td align="center">'.$paid.'</td>
		  <td align="center"><b style="color:#'.(($paid - $cars_price)<0?'ff5555':'22aa22').'">'.($paid - $cars_price).'</b></td>
		</tr>
		</table>
		<br>
		
		<table border="0" cellspacing="5" cellpadding="0" class="list" width="100%">
		<tr class="title">
		<td width="210"><b>'.$this->translate->_('Автомобили').'</b>&nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.$this->user_folder.'/?mod=cars">'.$this->translate->_('на отдельной странице').'</a>
		&nbsp;&nbsp; <input type="checkbox" name="show_arrived" id="arrived"'.($_SESSION['show_arrived_detail_cars']=='1'?' checked="checked"':'').' onclick="document.location=\''.$this->root_path.'public/?mod=balance&arrived='.$_SESSION['show_arrived_detail_cars'].'\'" style="border:0px;"><label for="arrived" style="cursor:hand; cursor:pointer;">прибывшие</label></td>
		<td width="210"><b>'.$this->translate->_('Платежи').'</b>
		&nbsp;&nbsp; <input type="checkbox" name="show_payments" id="payments"'.($_SESSION['show_detail_payments']=='1'?' checked="checked"':'').' onclick="document.location=\''.$this->root_path.'public/?mod=balance&all_payments='.$_SESSION['show_detail_payments'].'\'" style="border:0px;"><label for="payments" style="cursor:hand; cursor:pointer;">'.$this->translate->_('все / последний месяц').'</label></td></tr>
		<tr><td width="50%" valign="top"><div style="overflow:auto;height:330px;">'.$cars_list.'</div></td>
		<td width="50%" valign="top"><div style="overflow:auto;height:330px;">'.$payments_list.'</div>
		'.$legend.'</td></tr>
		</table>
		</div>';
		}
	}
	
	private function filterArrived() {
		if($_SESSION['show_arrived_detail_cars']=='') $_SESSION['show_arrived_detail_cars'] = '0';
		elseif(intval($_GET['arrived'])=='1') $_SESSION['show_arrived_detail_cars'] = '0';
		elseif(isset($_GET['arrived']) and intval($_GET['arrived'])=='0') $_SESSION['show_arrived_detail_cars'] = '1';
		
		if($_SESSION['show_arrived_client_cars']=='0') $this->arrived_filter = " AND `delivered` = '0'";
		else $this->arrived_filter = "";
	}
	
	private function filterPayments() {
		if($_SESSION['show_detail_payments']=='') $_SESSION['show_detail_payments'] = '0';
		elseif(intval($_GET['all_payments'])=='1') $_SESSION['show_detail_payments'] = '0';
		elseif(isset($_GET['all_payments']) and intval($_GET['all_payments'])=='0') $_SESSION['show_detail_payments'] = '1';
	}
	
	protected function getContent() {
		$this->client = mysql_fetch_array($this->mysqlQuery("
		SELECT name, dealer 
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE `id` = '".$_SESSION['user_id']."'"));
	
		//фильтр доставленных машин
		$this->filterArrived();
		
		//фильтр платежей за последний месяц
		$this->filterPayments();
		
		$this->date = getdate(mktime()-(60*60*24*30));
		$this->month_ago = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);	
		
		
		if($this->client['dealer'] == 0) {
			$request1 = "SELECT id,total,model,frame,delivered,buy_date,price_jp 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".$_SESSION['user_id']."'".$this->arrived_filter." 
			ORDER BY `buy_date` DESC, `delivered` ASC";
			
			$request2 = "SELECT ccl_".ACCOUNT_SUFFIX."accounting.*
			FROM `ccl_".ACCOUNT_SUFFIX."accounting`, ccl_".ACCOUNT_SUFFIX."cars
			WHERE ccl_".ACCOUNT_SUFFIX."accounting.car = ccl_".ACCOUNT_SUFFIX."cars.id
			AND ccl_".ACCOUNT_SUFFIX."cars.buyer = '".$_SESSION['user_id']."'
			AND ccl_".ACCOUNT_SUFFIX."accounting.type = 1
			ORDER BY `date` DESC";
		}
		else {
			$request1 = "SELECT id,total,model,frame,delivered,buy_date,price_jp 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".$_SESSION['user_id']."' 
			OR `dealer` = '".$_SESSION['user_id']."'".$this->arrived_filter." 
			ORDER BY `delivered` ASC";
			
			$request2 = "SELECT DISTINCT ccl_".ACCOUNT_SUFFIX."accounting.id, ccl_".ACCOUNT_SUFFIX."accounting.date, ccl_".ACCOUNT_SUFFIX."accounting.amount, ccl_".ACCOUNT_SUFFIX."accounting.comment 
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			INNER JOIN `ccl_".ACCOUNT_SUFFIX."accounting`
			ON (ccl_".ACCOUNT_SUFFIX."accounting.client = ccl_".ACCOUNT_SUFFIX."customers.id) 
			WHERE `mydealer` = '".$_SESSION['user_id']."' 
			AND `client` != '".$_SESSION['user_id']."' 
			AND ccl_".ACCOUNT_SUFFIX."accounting.type = 1
			ORDER BY `date` DESC";	
			
			$this->mypayments = $this->mysqlQuery("SELECT `ccl_".ACCOUNT_SUFFIX."accounting`.*
			FROM `ccl_".ACCOUNT_SUFFIX."accounting`, `ccl_".ACCOUNT_SUFFIX."cars`
			WHERE ccl_".ACCOUNT_SUFFIX."accounting.car = ccl_".ACCOUNT_SUFFIX."cars.id
			AND ccl_".ACCOUNT_SUFFIX."cars.buyer = '".$_SESSION['user_id']."
			AND ccl_".ACCOUNT_SUFFIX."accounting.type = 1' 
			ORDER BY `date` DESC");
		}
		
		$this->cars = $this->mysqlQuery($request1);
		
		$this->payments = $this->mysqlQuery($request2);
	}
}
?>