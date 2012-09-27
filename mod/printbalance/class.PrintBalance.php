<?

class PrintBalance extends Proto {

	var $cars;
	var $payments;
	var $client;

	public function drawContent() {
		if($this->checkAuth()) {
			$this->getContent();
			$this->page .= $this->drawResult();
		}
				
		$this->publish();
	}
	
	function getContent() {
	//детализированная статистика по клиенту
	if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id']!='0') and ($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4'))
	{
		
		$this->client = mysql_fetch_array(
		$this->mysqlQuery("
		SELECT `name` 
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE `id` = '".intval($_GET['id'])."'"));
		
		$date = getdate(mktime()-(60*60*24*30));
		$month_ago = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
		
		if(intval($_GET['dealer']) == 0) {
			$request1 = "
			SELECT id,total,model,frame,delivered,buy_date,price_jp 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".intval($_GET['id'])."'
			ORDER BY `buy_date` DESC";
			
			$request2 = "
			SELECT * 
			FROM `ccl_".ACCOUNT_SUFFIX."payments` 
			WHERE `client` = '".intval($_GET['id'])."' AND `status` = '1' 
			ORDER BY `date` DESC";
		}
		else {
			$request1 = "
			SELECT id,total,model,frame,delivered,prepay,buy_date,price_jp 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".intval($_GET['id'])."' OR `dealer` = '".intval($_GET['id'])."' ORDER BY `buy_date` DESC";
			
			$request2 = "
			SELECT DISTINCT ccl_".ACCOUNT_SUFFIX."payments.id, ccl_".ACCOUNT_SUFFIX."payments.car, ccl_".ACCOUNT_SUFFIX."payments.date, ccl_".ACCOUNT_SUFFIX."payments.amount, ccl_".ACCOUNT_SUFFIX."payments.comment 
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			INNER JOIN `ccl_".ACCOUNT_SUFFIX."payments` 
			ON (ccl_".ACCOUNT_SUFFIX."payments.client = ccl_".ACCOUNT_SUFFIX."customers.id) 
			WHERE ccl_".ACCOUNT_SUFFIX."customers.mydealer = '".intval($_GET['id'])."' 
			AND ccl_".ACCOUNT_SUFFIX."payments.client != '".intval($_GET['id'])."' 
			AND ccl_".ACCOUNT_SUFFIX."payments.status = '1' 
			ORDER BY `date` DESC";	
			
			$mypayments = $this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."payments.id, ccl_".ACCOUNT_SUFFIX."payments.car, ccl_".ACCOUNT_SUFFIX."payments.date, ccl_".ACCOUNT_SUFFIX."payments.amount, ccl_".ACCOUNT_SUFFIX."payments.comment 
			FROM `ccl_".ACCOUNT_SUFFIX."payments` 
			WHERE `client` = '".intval($_GET['id'])."' 
			AND `status` = '1' 
			ORDER BY `date` DESC");
		}
			
		$this->cars = $this->mysqlQuery($request1);
		
		$this->payments = $this->mysqlQuery($request2);
	}
	}
	
	function drawResult() {
		$j=1;
		$num = mysql_num_rows($this->cars);
		$style= "border-bottom:1px solid #000;";
		
		$date = getdate(mktime()-(60*60*24*30));
		$month_ago = mktime(0,0,0,$date['mon'],$date['mday'],$date['year']);
	
		$style= "border-bottom:1px solid #000;";
			$payments_list = '<table class="list" width="100%">
			<tr class="title">
			<td width="90">'.$this->translate->_('дата').'</td><td>'.$this->translate->_('комментарий').'</td><td width="60">'.$this->translate->_('сумма').'</td></tr>';
		
		$carPayments = array();
		//список платежей клиента
		if($_GET['dealer']==1 or $this->client['dealer']==1) {
			$j=1;
			$num = mysql_num_rows($mypayments);
			if($num>0) { 
				while($j<=$num)	{
					$line=mysql_fetch_array($mypayments);
					$carPayments[$line['car']] = $carPayments[$line['car']]+$line['amount'];
					$cur_date = explode('-',$line['date']);
					$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
					
					if($_SESSION['show_detail_payments']=='0' and $tstamp<$month_ago) $j++;
					else {
						$payments_list .= '<tr>
						<td style="'.$style.'">'.$line['date'].'</td>
						<td style="'.$style.'">'.$line['comment'].'&nbsp;</td>
						<td style="'.$style.'">'.$line['amount'].'</td></tr>
						';
						
						if($style=="border-bottom:1px solid #000;") $style = "border-bottom:1px solid #000;background-color:#eee;";
						else $style = "border-bottom:1px solid #000;";
						$j++;
					}
					$paid_total = $paid_total + $line['amount'];
				}
			}
			
		}
		$j=1;
		$style= "border-bottom:1px solid #000;";
		$num = mysql_num_rows($this->payments);
		while($j<=$num)
		{
			$line=mysql_fetch_array($this->payments);
			$carPayments[$line['car']] = $carPayments[$line['car']]+$line['amount'];
			$cur_date = explode('-',$line['date']);
			$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
					
			if($_SESSION['show_detail_payments']=='0' and $tstamp<$month_ago) $j++;
			else {
			$payments_list .= '<tr>
			<td style="'.$style.'">'.$line['date'].'</td>
			<td style="'.$style.'">'.$line['comment'].'&nbsp;</td>
			<td style="'.$style.'">'.$line['amount'].'</td></tr>
			';
			
			if($style=="border-bottom:1px solid #000;") $style = "border-bottom:1px solid #000;background-color:#eee;";
			else $style = "border-bottom:1px solid #000;";
			$j++;
			}
			$paid_total = $paid_total + $line['amount'];
		}
		$payments_list .= '</table>';
		
			
		$cars_list = '<table class="list" width="100%">
		<tr class="title">
		<td width="90">'.$this->translate->_('дата покупки').'</td><td>'.$this->translate->_('модель').'</td><td width="100">'.$this->translate->_('вин код').'</td><td width="60">'.$this->translate->_('всего').'</td><td width="60">'.$this->translate->_('оплачено').'</td><td width="60">'.$this->translate->_('баланс').'</td></tr>';
		
		while($line=mysql_fetch_array($this->cars)) {
			$cars_price = $cars_price + $line['total'];
			if($line['delivered']=='1') {
					$delivered++;
			}
			else {
				$prepay = $prepay + $line['prepay'];
				$ontheway++;
			}
			$cars_total = $cars_total + $line['total'];
			//$paid = $paid + $line['paid'];
			if(($line['delivered']=='1' and $_SESSION['show_arrived_detail_cars']=='1') or $line['delivered']=='0') {
				$cars_list .= '
				<tr onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
				<td valign="top" style="'.$style.'">'.$line['buy_date'].'&nbsp;</td>
				<td valign="top" style="'.$style.'">'.($line['delivered']=='1'?'*':'').$line['model'].'&nbsp;</td>
				<td valign="top" style="'.$style.'">'.$line['frame'].'&nbsp;</td>			
				<td valign="top" style="'.$style.'"><b>'.$line['total'].'</b>&nbsp;</td>
				<td valign="top" style="'.$style.'">'.$carPayments[$line['id']].'&nbsp;</td>
				<td valign="top" style="'.$style.'">'.($line['total']-$carPayments[$line['id']]).'&nbsp;</td>
				</tr>
				';
				if($style=="border-bottom:1px solid #000;") $style = "border-bottom:1px solid #000;background-color:#eee;";
				else $style = "border-bottom:1px solid #000;";
		}

		}
		$cars_list .= '</table>';
		
		$balance_style = ' style="border-bottom:1px solid #000"';
		
		//вывод всего вышесозданного
		$out .= '
			<html>
			<head>
			<title>'.$this->translate->_('Детализация баланса:').' '.$this->client['name'].' '.$this->translate->_('распечатка от:').' '.date('d-m-Y').'</title>
			<style>
			body,td,p,div,span {
				font-size:14px;
			}
			body {
				font-family: Times New Roman;
				
			}
			</style>
			<script>
			PKOLWebBrowser(Control).Navigate(\'about:blank\');
			</script>
			</head>
			<body>
			<div class="cont_customer">
			<h3>'.$this->translate->_('Детализация баланса:').' '.stripslashes($this->client['name']).'</h3>
			<table cellspacing="1" cellpadding="2" border="0" width="300">
		    <tr class="rowB title">
		      <td>&nbsp;</td>
		      <td width="80" align="right">'.$this->translate->_('сумма').'</td>
		      <td width="60" align="right">'.$this->translate->_('штук').'</td>
		      </tr>
		    <tr>
		      <td'.$balance_style.'>'.$this->translate->_('ЗАКАЗАНО:').'<br>
			  &nbsp;</td>
		      <td align="right"'.$balance_style.'><span class="rowB">'.$cars_total.'</span></td>
		      <td'.$balance_style.' align="right">'.($delivered+$ontheway).'</td>
		      </tr>
		    <tr class="rowB title">
		      <td'.$balance_style.'>'.$this->translate->_('ОПЛАЧЕНО:').'</td>
		      <td align="right"'.$balance_style.'>'.$paid_total.'</td>
		      <td'.$balance_style.'>&nbsp;</td>
		      </tr>
			      <tr class="rowB title">
		      <td'.$balance_style.'>'.$this->translate->_('БАЛАНС:').'</td>
		      <td align="right"'.$balance_style.'>'.($paid_total - $cars_total).'</b></td>
		      <td'.$balance_style.'>&nbsp;</td>
		      </tr>
			  </table>
			
		<br>
		<i>'.$this->translate->_('Подробнее...').'</i><br>
			<table border="0" cellspacing="5" cellpadding="0" class="list" width="650">
			<tr class="title">
			<td style="border-bottom:2px solid #000;"><b>'.$this->translate->_('Автомобили').'</b> '.($_SESSION['show_arrived_detail_cars']=='0'?$this->translate->_('(НЕ доставленные)'):$this->translate->_('(все)')).'</td>
			</tr>
			<tr><td>'.$cars_list.'</td>
			</tr>
			<tr><td style="border-bottom:2px solid #000;"><b>'.$this->translate->_('Платежи').'</b> '.($_SESSION['show_detail_payments']=='0'?$this->translate->_('(за последние 30 дней)'):$this->translate->_('(все)')).'</td></tr>
			<tr>
			<td>'.$payments_list.'</td></tr>
			</table>
			</div>
			</body>
			</html>
			<div style="display:none;">';
			
			echo $out;
	}
}


?>