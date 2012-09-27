<?
//##############################
//
//форма платежа
//
//##############################
function paymentForm ($mode, $id, $content, $clients, $cars) {
	global $root_path;

	$translate = Zend_Registry::get('translation');

	if(isset($_GET['add'])) $cars_list = $cars;	//iconv('UTF-8', 'WINDOWS-1251', $cars);
	else $cars_list = '';

	if($content['client']==0) $cars_list = '';
	else $cars_list = $cars;	//iconv('UTF-8', 'WINDOWS-1251', $cars);
	
	if(isset($_GET['add'])) $content['date'] = date("Y-m-d");
	if($content['last_edited']!="0000-00-00 00:00:00") $last_edited = $translate->_('последнее изменение').': '.$content['last_edited'].' '.$translate->_('пользователь').': '.$content['log_name'];
	else $last_edited = $translate->_('нет данных о последних изменениях');
	
	if($content['status']!='') {
		if($content['status']=='1') $status_info = $translate->_('активный');
		elseif($content['status']=='0') $status_info = '<span style="color:#f33">'.$translate->_('не подтвержденный').'</span>';
	}
	
	if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $activate = '<input type="button" value="'.($content['status']=='1'?'ДЕ':'').$translate->_('активировать').'" onclick="activate(\''.($content['status']=='1'?'off':'on').'\');" style="width:95px;">';
	
	if(isset($_GET['success'])) $out.='<div class="notice">'.$translate->_('изменения сохранены').'</div>';
	
	$out.='<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
    <script src="'.$root_path.'js/datepicker.js"></script>
    <script src=../js/jquery.js></script>
    <script>
    var mode;
    function activate(mode) {
    	if(mode==\'on\') document.getElementById(\'payStatus\').checked=\'checked\';
    	if(mode==\'off\') document.getElementById(\'payStatus\').checked=\'\';
    	document.forms.savePayment.submit();
    }
    function switchCars(id) {
	    $.get("/?mod=payments&sw=cars", { owner: id },
			 	function(data) {
			 		document.getElementById("carsContainer").innerHTML = data;
			 		
			 	} );
	}
    </script>
	<form class="myForm" action="'.$root_path.$mode.'" name="savePayment" method="post">
	<div class="cont_customer">
	<h3>'.$translate->_('Платеж').'</h3>
	<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="80" align="right" class="title">'.($content['client']!=''?'<a href="'.$root_path.'?mod=clients&sw=detail&id='.$content['client'].'">'.$translate->_('Плательщик').'</a>':$translate->_('Плательщик')).'</td>
		<td width="253" class="rowA title">
		'.Proto::buildSelect($clients, 'client', $content['client'], $translate->_('не выбран'), '1', 'switchCars(this.value);').'
		</td>
		<td width="70" align="right" class="title">'.$translate->_('Сумма').'</td>
		<td width="140" class="rowA title" colspan="2"><input type="text" name="amount" value="'.$content['amount'].'" tabindex="2" /></td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">'.$translate->_('Автомобиль').'</td>
		<td class="rowA rowB title" id="carsContainer">'.$cars_list.'</td>
		<td align="right" class="title rowB">&nbsp;</td>
		<td class="rowA rowB title">&nbsp;</td>
		<td class="rowB"></td>
	  </tr>
	  <tr>
		<td align="right" class="title">'.$translate->_('статус платежа').':</td>
		<td class="rowA title"><nobr>'.$status_info.'&nbsp;'.$activate.'</nobr></td>
		<td align="right" class="title">'.$translate->_('дата').'</td>
		<td class="rowA title"><input type="text" name="date" id="payDate" value="'.$content['date'].'" tabindex="3" style="width:90%;" /></td>
		<td><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'payDate\', \'\', myDateFormat);" class="datePicker" style="margin-left:-22px;"></td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">'.$translate->_('комментарий').'</td>
		<td colspan="3" class="rowA rowB title"><span class="rowA title">
		  <input type="text" name="comment" value="'.$content['comment'].'" tabindex="4" />
		</span></td>
		<td class="rowB"></td>
		</tr>
	  <tr>
		<td colspan="5" class="rowA" align="right" style="color:#777;">'.$last_edited.'&nbsp;</td>
	  </tr>
	</table><input type="checkbox" name="payment_status" id="payStatus" style="display:none;" '.($content['status']=='1'?'checked="checked"':'').'></div>
	<table width="702" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td class="title"><a href="'.$root_path.'?mod=payments&sw=delete&id='.$id.'&client='.$content['client'].'&dealer='.$content['dealer'].'" class="delete" onclick="return confirm(\''.$translate->_('Вы действительно хотите удалить этот платеж?').'\')">'.$translate->_('удалить').'</a></td>
		<td width="214" align="right" class="title"><input type="submit" name="Submit" value="'.$translate->_('Сохранить').'" id="save" tabindex="5" /></td>
		<td width="8" align="right" class="title"><br />
		  <br /></td>
	  </tr>
	</table>
	</form>';
	return $out;
}


?>