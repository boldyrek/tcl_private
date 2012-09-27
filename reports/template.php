<?
	$translate = Zend_Registry::get('translation');
	$query_form = '
	<script>var myDateFormat = new Array("dd-mm-yyyy");</script>
	<script src="'.$root_path.'js/datepicker.js"></script>
	<div style="width:200px;position:absolute; margin-left: 706px; background-color:#caf0ff; border:1px solid #aaa;">
	<form action="" method="post" style="margin:0px;">
		<table border="0" cellspacing="0" cellpadding="5" width="180" style="margin:10px;background-color:#fff;"><tr>
		<td colspan="2" class="title">
		'.$translate->_('Поставщик').':
		'.buildSelect($suppliers, 'supplier', $_POST['supplier'], ' - все - ', '').'
		</td></tr>
		<tr>
		<td class="title" align="right">с</td>
		<td width="170"><input type="text" name="from_date" id="from_date" value="'.$from_date.'" size="9"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'from_date\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
		</tr>
		<tr>
		<td class="title" align="right">по</td>
		<td><input type="text" name="to_date" value="'.$to_date.'" id="to_date" size="9"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'to_date\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
		</tr>
		<tr><td>
		<input type="radio" name="arrived" value="0" id="arr1"'.$arr_checked1.'></td>
		<td class="title"><label for="arr1">'.$translate->_('все').'</label></td></tr>
		<tr><td><input type="radio" name="arrived" value="1" id="arr2"'.$arr_checked2.'>
		</td><td class="title"><label for="arr2">'.$translate->_('доставленные').'</label></td></tr>
		<tr><td><input type="radio" name="arrived" value="2" id="arr3"'.$arr_checked3.'>
		</td><td class="title"><label for="arr3">'.$translate->_('в пути').'</label></td></tr>
		</td>
		</tr>
		<tr><td colspan="3"><input type="submit" value="'.$translate->_('СОЗДАТЬ ОТЧЕТ').'"></td>
		</tr></table></form></div>
	';

?>