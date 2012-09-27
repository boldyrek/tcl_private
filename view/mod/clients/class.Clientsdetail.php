<?

class ClientsDetail extends Proto {
	
	var $arrived_filter;
	
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
		//детализированная статистика по клиенту
		if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id']!='0') and ($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4'))
		{
			
		$_SESSION['prev_location'] = $_SESSION['last_location'];
		$_SESSION['last_location'] = 'mod=clients&sw=detail&id='.intval($_GET['id']);
		
		require($include_path.'mod/clients/class.addpayment.php');
			
		$pay = new addPayment();
		if(isset($_GET['addpayment'])) if(!$pay->process()) die('Проблемы с добавлением платежа!');
		
		$payform = $pay->makeForm();
		$client = mysql_fetch_array(mysql_query("SELECT `name` FROM `ccl_".ACCOUNT_SUFFIX."customers` WHERE `id` = '".mysql_real_escape_string(intval($_GET['id']))."'"));
		
		$this->searchFilters();
		
		$date = getdate(mktime()-(60*60*24*30));
		$month_ago = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
		
		if($_GET['dealer'] == 0) {
			$request1 = "
			SELECT id,total,total_som,model,frame,delivered,prepay,prepay_som,buy_date,price_jp 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".intval($_GET['id'])."'".$this->arrived_filter." 
			ORDER BY `buy_date` DESC";
			
			$request2 = "
			SELECT * 
			FROM `ccl_".ACCOUNT_SUFFIX."payments` 
			WHERE `client` = '".intval($_GET['id'])."'
			ORDER BY `date` DESC";
		}
		else {
			$request1 = "
			SELECT id,total,total_som,model,frame,delivered,prepay,prepay_som,buy_date,price_jp 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".intval($_GET['id'])."' OR `dealer` = '".intval($_GET['id'])."'".$this->arrived_filter." 
			ORDER BY `buy_date` DESC";
			
			$request2 = "
			SELECT DISTINCT ccl_".ACCOUNT_SUFFIX."payments.*
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			INNER JOIN `ccl_".ACCOUNT_SUFFIX."payments` 
			ON (ccl_".ACCOUNT_SUFFIX."payments.client = ccl_".ACCOUNT_SUFFIX."customers.id) 
			WHERE ccl_".ACCOUNT_SUFFIX."customers.mydealer = '".intval($_GET['id'])."' 
			AND ccl_".ACCOUNT_SUFFIX."payments.client != '".intval($_GET['id'])."' 
			ORDER BY `date` DESC";	
			
			$mypayments = $this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."payments.* 
			FROM `ccl_".ACCOUNT_SUFFIX."payments` 
			WHERE `client` = '".intval($_GET['id'])."' 
			ORDER BY `date` DESC");
		}
		
		$cars = $this->mysqlQuery($request1);
		
		$payments = $this->mysqlQuery($request2);
	
		
		$cars_total = 0; //общая сумма за доставленные автомобили
		$paid_total = 0; //всего клиент оплатил
		$ontheway = 0; //машин в пути
		$prepay = 0; //предоплата за недоставленные машины
		$delivered = 0; // машин доставлено
		
		//делаем список автомобилей заказанных клиентом
		$j=1;
		$num = mysql_num_rows($cars);
		$class= "rowA rowB";
		$cars_list = '<table class="list" width="100%">
		<tr class="title">
		<td>модель</td>
		<td>кузов</td>
		<td>цена<br>в Японии</td>
		<td width="40">п/о USD</td>
		<td width="40">п/о KGS</td>
		<td width="40">USD</td>
		<td width="40">KGS</td></tr>';
		
		while($j<=$num)
		{
			$line=mysql_fetch_array($cars);
			$cars_price = $cars_price + $line['total'];
			$cars_price_som = $cars_price_som + $line['total_som'];
			if($line['delivered']=='1') {
					$cars_total = $cars_total + $line['total'];
					$cars_total_som = $cars_total_som + $line['total_som'];
					$delivered++;
			}
			else {
				$prepay = $prepay + $line['prepay'];
				$prepay_som = $prepay_som + $line['prepay_som'];
				$ontheway++;
			}
			if(($line['delivered']=='1' and $_SESSION['show_arrived_detail_cars']=='1') or $line['delivered']=='0') {
				if($line['delivered']=='1') $class = "greenTR";
				$cars_list .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$this->root_path.'?mod=cars&sw=form&car_id='.$line['id'].'\'">
				<td valign="top">'.$line['model'].'</td>
				<td style="font-family:monospace;" valign="top">'.substr($line['frame'],0,3).'..'.substr($line['frame'],(strlen($line['frame'])-4),4).'&nbsp;</td>';
				$cars_list .= '<td>'.$line['price_jp'].'</td>';
				
				if($line['delivered']=='1') {
					$cars_list .= '
					<td valign="top"><span style="color:#555;">'.$line['prepay'].'</span></td>
					<td valign="top"><span style="color:#555;">'.$line['prepay_som'].'</span></td>
					<td valign="top"><b>'.$line['total'].'</b>
					<td valign="top"><b>'.$line['total_som'].'</b>';
				}
				else { 
					$cars_list .= '
					<td valign="top"><b>'.$line['prepay'].'</b></td>
					<td valign="top"><b>'.$line['prepay_som'].'</b></td>
					<td valign="top"><span style="color:#555;">'.$line['total'].'</span>
					<td valign="top"><span style="color:#555;">'.$line['total_som'].'</span>';
					
				
				}
				$cars_list .= '</td></tr>
				';
			}
			if($class=="rowA") $class = "rowA rowB";
			else $class = "rowA";
			$j++;
		}
		$cars_list .= '</table>';
		$class= "rowA rowB";
			$detail_payments_list = '
			<table class="list" width="100%">
			<tr class="title">
				<td width="80">дата</td>
				<td>комментарий</td>
				<td width="40">USD</td>
				<td width="40">KGS</td>
			</tr>';
		
		//список платежей клиента
		if(intval($_GET['dealer'])==1) {
			$j=1;
			$num = mysql_num_rows($mypayments);
			if($num>0) { 
				while($j<=$num)	{
					$line=mysql_fetch_array($mypayments);
					
					$cur_date = explode('-',$line['date']);
					$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
					
					if($_SESSION['show_detail_payments']=='0' and $tstamp<$month_ago) $j++;
					else {
						if($line['status']=='0') $class="pinkRow";
						$detail_payments_list .= '
						<tr class="'.$class.'" style="background-color:#cceecc;" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"';
						if($_SESSION['user_type']=='4' and $line['status']=='1') $detail_payments_list .= '>';
						else $detail_payments_list .= ' onclick="document.location=\''.$this->root_path.'?mod=payments&sw=form&payment='.$line['id'].'\'">';
						$detail_payments_list .= '
						<td>'.$line['date'].'</td>
						<td>'.$line['comment'].'&nbsp;</td>
						<td>'.$line['amount'].'</td>
						<td>'.$line['amount_som'].'</td>
						
						</tr>
						';
						
						if($class=="rowA") $class = "rowA rowB";
						else $class = "rowA";
					$j++;
					}
					if($line['status']!='0') {
						$paid_total = $paid_total + $line['amount'];
						$paid_total_som = $paid_total_som + $line['amount_som'];
					}
					
				}
			}
			$legend = '<br>
		<span style="color:#55aa55; font-size:11px;">* зеленым цветом выделены платежи от имени этого дилера.</span>';
		}
		$j=1;
		$num = mysql_num_rows($payments);
		while($j<=$num)
		{
			$line=mysql_fetch_array($payments);
			
			$cur_date = explode('-',$line['date']);
			$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
			
			if($_SESSION['show_detail_payments']=='0' and $tstamp<$month_ago) $j++;
			else {
				if($line['status']=='0') $class="pinkRow";
				$detail_payments_list .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"';
				if($_SESSION['user_type']=='4' and $line['status']=='1') $detail_payments_list .= '>';
				else $detail_payments_list .= 'onclick="document.location=\''.$this->root_path.'?mod=payments&sw=form&payment='.$line['id'].'\'">';
				$detail_payments_list .= '
				<td>'.$line['date'].'</td>
				<td>'.$line['comment'].'&nbsp;</td>
				<td>'.$line['amount'].'</td>
				<td>'.$line['amount_som'].'</td>
				</tr>
				';
			
				if($class=="rowA") $class = "rowA rowB";
				else $class = "rowA";
				$j++;
			}
			if($line['status']!='0') {
				$paid_total = $paid_total + $line['amount'];
				$paid_total_som = $paid_total_som + $line['amount_som'];
			}
		}
		$detail_payments_list .= '</table>';
		
		//вывод всего вышесозданного
		$this->page .= '
		<script language="JavaScript" type="text/javascript" src="/js/drag/core.js"></script>
		<script language="JavaScript" type="text/javascript" src="/js/drag/events.js"></script>
		<script language="JavaScript" type="text/javascript" src="/js/drag/css.js"></script>
		<script language="JavaScript" type="text/javascript" src="/js/drag/coordinates.js"></script>
		<script language="JavaScript" type="text/javascript" src="/js/drag/drag.js"></script>
		<script>
		window.onload = function() {<!--
			var group
			var coordinates = ToolMan.coordinates()
			var drag = ToolMan.drag()
			var boxHandle = document.getElementById("boxHandle")
			group = drag.createSimpleGroup(boxHandle, document.getElementById("handle"))
		}//-->
		</script>
		<div class="cont_customer" style="width:960px">
		<div style="float:right; width:140px;font-size:11px;">
		<a href="'.$this->root_path.'?mod=printbalance&id='.intval($_GET['id']).'" target="_blank">
		<img src="'.$this->root_path.'img/ccl/printer.gif" align="absmiddle" border="0" hspace="3">версия для печати</a></div>
		<div style="float:right; width:140px;font-size:11px;">
		<a href="'.$this->root_path.'?mod=client_cars&client='.intval($_GET['id']).'">автомобили клиента</a></div>
		<h3><span style="color:#6ba6bd;">Детализация баланса по клиенту:</span><br><a href="'.$this->root_path.'?mod=clients&sw=form&customer_id='.intval($_GET['id']).'">'.stripslashes($client['name']).'</a></h3>
		
		<table cellspacing="1" cellpadding="0" width="650" border="0" class="list">
	    <tr class="rowB title">
	      <td width="85">ЗАКАЗАНО<br />
	        на   сумму:</td>
	      <td width="35" align="center">'.($delivered+$ontheway).'</td>
	      <td width="90">ДОСТАВЛЕНО<br />
	      на   сумму:</td>
	      <td width="40" align="center">'.$delivered.'</td>
	      <td width="60">В ПУТИ <br>на сумму:</td>
	      <td width="40" align="center">'.$ontheway.'</td>
	      <td width="72">ОПЛАЧЕНО:</td>
	      <td width="70">К ОПЛАТЕ: </td>
	      <td>БАЛАНС:</td>
	    </tr>
	    <tr>
			<td colspan="9" class="rowA title" align="center" style="background-color:#bedbae;">БАЛАНС В ДОЛЛАРАХ</td>
		</tr>
	    <tr class="title">
	      <td align="center" colspan="2" class="rowB">'.$cars_price.'</td>
	      <td align="center" colspan="2">'.$cars_total.'</td>
	      <td align="center" colspan="2" class="rowB">'.$prepay.'</td>
	      <td align="center">'.$paid_total.'</td>
	      <td class="rowB" align="center"><b>'.($cars_total + $prepay - $paid_total).'</b></td>
		  <td align="center"><b style="color:#'.(($paid_total - $cars_price)<0?'ff5555':'22aa22').'">'.($paid_total - $cars_price).'</b></td>
	    </tr>
	';
		
	// сводка баланса в СОМАХ
	$this->page .= '

		<tr>
			<td colspan="9" class="rowA title" align="center" style="background-color:#f1d1c2;">БАЛАНС В СОМАХ</td>
		</tr>
	    <tr class="title">
	      <td align="center" colspan="2" class="rowB">'.$cars_price_som.'</td>
	      <td align="center" colspan="2">'.$cars_total_som.'</td>
	      <td align="center" colspan="2" class="rowB">'.$prepay_som.'</td>
	      <td align="center">'.$paid_total_som.'</td>
	      <td class="rowB" align="center"><b>'.($cars_total_som + $prepay_som - $paid_total_som).'</b></td>
		  <td align="center"><b style="color:#'.(($paid_total_som - $cars_price_som)<0?'ff5555':'22aa22').'">'.($paid_total_som - $cars_price_som).'</b></td>
	    </tr>
	</table>
	
		<table border="0" cellspacing="5" cellpadding="0" class="list" width="100%">
		<tr class="title">
		<td><b>Автомобили</b> &nbsp;&nbsp;&nbsp;<a href="'.$this->root_path.'?mod=client_cars&client='.intval($_GET['id']).'">на отдельной странице</a>
		&nbsp;&nbsp; <input type="checkbox" name="show_arrived" id="arrived"'.($_SESSION['show_arrived_detail_cars']=='1'?' checked="checked"':'').' onclick="document.location=\''.$this->root_path.'?mod=clients&sw=detail&id='.intval($_GET['id']).(isset($_GET['dealer'])?'&dealer='.intval($_GET['dealer']):'').'&arrived='.$_SESSION['show_arrived_detail_cars'].'\'" style="border:0px;"><label for="arrived" style="cursor:hand; cursor:pointer;">прибывшие</label></td>
		<td>
		<div id="boxHandle" class="box" style="display:none;">
		<div id="handle" class="handle"><img src="/img/ccl/r_ex.gif" align="right" style="cursor:hand; cursor:pointer;float:right;margin-top:5px;" onclick="document.getElementById(\'boxHandle\').style.display=\'none\';"><b>Добавить платеж этому клиенту</b></div>
		'.$payform.'</div>
		<b>Платежи</b> &nbsp;&nbsp;&nbsp;&nbsp; <a onclick="document.getElementById(\'boxHandle\').style.display=\'\';" style="cursor:pointer;">добавить платеж</a>
		&nbsp;&nbsp; <input type="checkbox" name="show_payments" id="payments"'.($_SESSION['show_detail_payments']=='1'?' checked="checked"':'').' onclick="document.location=\''.$this->root_path.'?mod=clients&sw=detail&id='.intval($_GET['id']).(isset($_GET['dealer'])?'&dealer='.intval($_GET['dealer']):'').'&all_payments='.$_SESSION['show_detail_payments'].'\'" style="border:0px;"><label for="payments" style="cursor:hand; cursor:pointer;">все / последний месяц</label>
		</td></tr>
		<tr><td width="50%" valign="top"><div style="overflow:auto;height:350px;">'.$cars_list.'</div></td>
		<td width="50%" valign="top"><div style="overflow:auto;height:350px;">'.$detail_payments_list.'</div>
		'.$legend.'</td></tr>
		</table>
		</div>';
		}
		
		else {
			session_destroy();
			$this->redirect($this->root_path);
		}

	}
	
	public function searchFilters() {
			//фильтр доставленных машин
			if($_SESSION['show_arrived_detail_cars']=='') $_SESSION['show_arrived_detail_cars'] = '0';
			elseif(intval($_GET['arrived'])=='1') $_SESSION['show_arrived_detail_cars'] = '0';
			elseif(isset($_GET['arrived']) and intval($_GET['arrived'])=='0') $_SESSION['show_arrived_detail_cars'] = '1';
			
			if($_SESSION['show_arrived_client_cars']=='0') $this->arrived_filter = " AND `delivered` = '0'";
			else $this->arrived_filter = "";
			//фильтр платежей за последний месяц
			if($_SESSION['show_detail_payments']=='') $_SESSION['show_detail_payments'] = '0';
			elseif(intval($_GET['all_payments'])=='1') $_SESSION['show_detail_payments'] = '0';
			elseif(isset($_GET['all_payments']) and intval($_GET['all_payments'])=='0') $_SESSION['show_detail_payments'] = '1';
	}
	
}
?>