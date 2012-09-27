<?

class info extends Proto {
	
	private $totals;
	private $class = "rowA";
	
	public function drawContent() {
		
		if($this->checkAuth()) {
			$this->getContent();
		}

		$this->publish();
	}
	
	function getContent() {
		if(intval($_GET['car'])!=0) {
			$car = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car'])."'"));
			$out = '<table width="220" cellspacing="0" cellpadding="3" class="list" style="border:0px;">
		<tr class="rowB title"><td colspan="2">'.$car['model'].'</td></tr>
		<tr class="rowB title"><td colspan="2"><b>'.$this->translate->_('Расходы на автомобиль').'</b></td></tr>
		<tr class="rowA title">

			<td width="150">'.$this->translate->_('цена в Америке на аукционе').'</td>
			<td align="right">'.$car['price_jp'].'</td>
		</tr>		
		<tr class="rowB title">
			<td width="150">'.$this->translate->_('Аукционный сбор').'</td>
			<td align="right">'.$car['aucfee'].'</td>
		</tr>
		<tr class="rowA title">

			<td width="150">'.$this->translate->_('Комиссия дилера').'</td>
			<td align="right">'.$car['dealer_commission'].'</td>
		</tr>
		<tr class="rowB title">
			<td width="150">'.$this->translate->_('ПП инспекция').'</td>
			<td align="right">'.$car['inspection'].'</td>
		</tr>

		<tr class="rowA title">
			<td width="150">'.$this->translate->_('Доставка до порта').'</td>
			<td align="right">'.$car['cost_to_port'].'</td>
		</tr>
		<tr class="rowB title">
			<td width="150">'.$this->translate->_('Доставка до места назначения').'</td>
			<td align="right">'.$car['cost_to_destination'].'</td>
		</tr>
		<tr class="rowA title">
			<td width="150">'.$this->translate->_('Разгрузка').'</td>
			<td align="right">'.$car['unload'].'</td>
		</tr>
		<tr class="rowB title">
			<td width="150">'.$this->translate->_('Страховка').'</td>
			<td align="right">'.$car['insurance'].'</td>

		</tr>
		<tr class="rowA title">
			<td width="150">'.$this->translate->_('Прочее').'</td>
			<td align="right">'.$car['other'].'</td>
		</tr>
		<tr><td colspan="2">&nbsp;</td></tr>
		<tr class="rowB title">

			<td width="150">'.$this->translate->_('ВСЕГО').':</td>
			<td id="carExpenses" align="right">'.($car['price_jp']+$car['aucfee']+$car['dealer_commission']+$car['inspection']+$car['cost_to_port']+$car['cost_to_destination']+$car['unload']+$car['insurance']+$car['other']).'</td></tr>
		</table>';
		}
		else $out = '';
		echo iconv('cp1251','utf8',$out);
	}
}
	
?>