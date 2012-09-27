<?
require_once($_SERVER['DOCUMENT_ROOT'].'/public/lib/class.Publicbalance.php');

class PublicPrintBalance extends PublicBalance {
	
	function makePage() {	
		//$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			//$this->page .= $this->makeTopMenu();
			$this->moduleContent();
			//$this->page .= $this->module_content;
		}
		
		//$this->page .= $this->templates['footer'];
		
		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		$this->errorsPublisher();
		$this->publish();
		
	}
	
	private function moduleContent() {
		$this->getContent();
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
	
	private function drawContent()	{
		$print = '';
		$j=1;
		$num = mysql_num_rows($this->cars);
		$style= "border-bottom:1px solid #000;";
		
		$cars_list = '<table class="list" width="100%">
		<tr class="title">
		<td width="90">'.$this->translate->_('дата покупки').'</td><td>'.$this->translate->_('модель').'</td><td width="100">'.$this->translate->_('вин код').'</td><td width="60">'.$this->translate->_('п/о').'</td><td width="60">'.$this->translate->_('сумма').'</td></tr>';
		
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
				$cars_list .= '
				<tr>
				<td valign="top" style="'.$style.'">'.$line['buy_date'].'</td>
				<td valign="top" style="'.$style.'">'.($line['delivered']=='1'?'*':'').$line['model'].'</td>
				<td valign="top" style="'.$style.'">'.$line['frame'].'&nbsp;</td>';
				
				if($line['delivered']=='1') {
				$cars_list .= '<td valign="top" style="'.$style.'"><span style="color:#555;">'.$line['prepay'].'</span></td>
				<td valign="top" style="'.$style.'"><b>'.$line['total'].'</b>';
				}
				else { 
				$cars_list .= '<td valign="top" style="'.$style.'"><b>'.$line['prepay'].'</b></td>
				<td valign="top" style="'.$style.'">'.$line['total'];
				
				}
				$cars_list .= '</td></tr>
				';
				if($style=="border-bottom:1px solid #000;") $style = "border-bottom:1px solid #000;background-color:#eee;";
				else $style = "border-bottom:1px solid #000;";
			}
			$j++;
		}
		$cars_list .= '</table>';
		$style= "border-bottom:1px solid #000;";
			$payments_list = '
			<table class="list" width="100%">
			<tr class="title">
			<td width="90">'.$this->translate->_('дата').'</td><td>'.$this->translate->_('комментарий').'</td><td width="60">'.$this->translate->_('сумма').'</td></tr>';
		
		//список платежей клиента
		if($_GET['dealer']==1 or $client['dealer']==1) {
			$j=1;
			$num = mysql_num_rows($this->mypayments);
			if($num>0) { 
				while($j<=$num)	{
					$line=mysql_fetch_array($this->mypayments);
					
					$cur_date = explode('-',$line['date']);
					$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
					
					if($_SESSION['show_detail_payments']=='0' and $tstamp<$this->month_ago) $j++;
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
			
			$cur_date = explode('-',$line['date']);
			$tstamp = mktime(0,0,0,$cur_date[1],$cur_date[2],$cur_date[0]);
					
			if($_SESSION['show_detail_payments']=='0' and $tstamp<$this->month_ago) $j++;
			else {
			$payments_list .= '
			<tr>
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
		
		$balance_style = ' style="border-bottom:1px solid #000"';
		
		//вывод всего вышесозданного
		$this->page .= '
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
		<table width="690" cellpadding="0" cellspacing="10" border="0">
	  <tr>
	    <td width="50%" valign="top">
		<table cellspacing="1" cellpadding="2" border="0" width="90%">
	    <tr class="rowB title">
	      <td>&nbsp;</td>
	      <td width="80" align="right">'.$this->translate->_('сумма').'</td>
	      <td width="60" align="right">'.$this->translate->_('штук').'</td>
	      </tr>
	    <tr>
	      <td'.$balance_style.'>'.$this->translate->_('ЗАКАЗАНО:').'<br>
		  &nbsp;</td>
	      <td align="right"'.$balance_style.'><span class="rowB">'.$cars_price.'</span></td>
	      <td'.$balance_style.' align="right">'.($delivered+$ontheway).'</td>
	      </tr>
	    <tr class="rowB title">
	      <td'.$balance_style.'>'.$this->translate->_('ОПЛАЧЕНО:').'</td>
	      <td align="right"'.$balance_style.'>'.$paid_total.'</td>
	      <td'.$balance_style.'>&nbsp;</td>
	      </tr>
		      <tr class="rowB title">
	      <td'.$balance_style.'>'.$this->translate->_('БАЛАНС:').'</td>
	      <td align="right"'.$balance_style.'><b style="color:#'.($paid_total - $cars_price).'">'.($paid_total - $cars_price).'</b></td>
	      <td'.$balance_style.'>&nbsp;</td>
	      </tr>
		  </table></td>
	    <td width="50%">
		<table cellspacing="1" cellpadding="2" border="0" width="90%">
		    <tr class="rowB title">
	      <td>&nbsp;</td>
	      <td width="80" align="right">'.$this->translate->_('сумма').'</td>
	      <td width="60" align="right">'.$this->translate->_('штук').'</td>
	      </tr>
		<tr class="rowB title">
	      <td'.$balance_style.'>'.$this->translate->_('В ПУТИ:').'<br>
		  '.$this->translate->_('(предоплата)').'</td>
	      <td align="right"'.$balance_style.'><span class="rowB">'.$prepay.'</span></td>
	      <td'.$balance_style.' align="right">'.$ontheway.'</td>
	      </tr>
		  <tr class="rowB title">
	      <td'.$balance_style.'>'.$this->translate->_('ДОСТАВЛЕНО:').'</td>
	      <td align="right"'.$balance_style.'>'.$cars_total.'</td>
	      <td'.$balance_style.' align="right">'.$delivered.'</td>
	      </tr>
	    <tr class="rowB title">
	      <td'.$balance_style.'>'.$this->translate->_('ОПЛАЧЕНО:').'</td>
	      <td align="right"'.$balance_style.'>'.$paid_total.'</td>
	      <td'.$balance_style.'>&nbsp;</td>
	      </tr>
	    <tr class="rowB title">
	      <td'.$balance_style.'>'.$this->translate->_('К ОПЛАТЕ:').' </td>
	      <td align="right"'.$balance_style.'><b>'.($cars_total + $prepay - $paid_total).'</b></td>
	      <td'.$balance_style.'>&nbsp;</td>
	      </tr>
	
	
		  </table></td>
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
	}
}
?>