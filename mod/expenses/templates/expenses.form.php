<?
//##############################
//
//форма платежа
//
//##############################
function expenseForm ($mode, $id, $content, $cars) {
	global $root_path;
	$this->translate = Zend_Registry::get('translation');
	/*if(isset($_GET['add'])) */$cars_list = iconv('UTF-8', 'WINDOWS-1251', $cars);
//	else $cars_list = '';

/*	if($content['client']==0) $cars_list = '';
	else $cars_list = iconv('UTF-8', 'WINDOWS-1251', $cars);*/

	// Generating Pay purposes list.

	
//	$content['signer'];

	require($_SERVER['DOCUMENT_ROOT'].'/inc/ExpensesPurposes.php');
	
	$purpose='<select name="purpose" id="purpose">'; // onchange="puttext(\'purpose\',\'comment\');"
	foreach($purposes as $a=>$b)
	{
		$purpose.='<option value="'.$a.'" '.($content['purpose']==$a?'selected="selected"':'').'>'.$b.'</option>'."\n";
	}
	$purpose.='</select>';
	
	if(isset($_GET['add'])) $content['date'] = date("Y-m-d");
	if($content['last_edited']!="0000-00-00 00:00:00") $last_edited = 'последнее изменение: <span style="background-color:#777;color:#ddd"> &nbsp;&nbsp;&nbsp;<b>'.$content['last_edited'].'</b> &nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; создал: <span style="background-color:#777;color:#ddd"> &nbsp;&nbsp;&nbsp;<b>'.$content['log_name'].'</b> &nbsp;&nbsp;&nbsp;</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; подтвердил: <span style="background-color:#777;color:#ddd"> &nbsp;&nbsp;&nbsp;<b>'.$content['signer'].'</b> &nbsp;&nbsp;&nbsp;</span>';
	else $last_edited = 'нет данных о последних изменениях';
	
	if($content['status']!='') {
		if($content['status']=='1') $status_info = 'активный';
		elseif($content['status']=='0') $status_info = '<span style="color:#f33">не подтвержденный</span>';
	}
	
	if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $activate = '<input type="button" value="'.($content['status']=='1'?'ДЕ':'').'активировать" onclick="activate(\''.($content['status']=='1'?'off':'on').'\');" style="width:95px;">';
	
	if(isset($_GET['success'])) $out.='<div class="notice">'.$this->translate->_('изменения сохранены').'</div>';
	
	$out.='<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
    <script src="'.$root_path.'js/datepicker.js"></script>
    <script src=../js/jquery.js></script>
    <script>
    var mode;
    function activate(mode) {
    	if(mode==\'on\') document.getElementById(\'payStatus\').checked=\'checked\';
    	if(mode==\'off\') document.getElementById(\'payStatus\').checked=\'\';
    	document.forms.saveExpense.submit();
    }
	function puttext(div1,div2){
		var a = document.getElementById(div1);
		var b = document.getElementById(div2);
		b.value=a.options[a.value].innerHTML + b.value;
	}
    function switchCars(id) {
	    $.get("/?mod=expenses&sw=cars", { owner: id },
			 	function(data) {
			 		document.getElementById("carsContainer").innerHTML = data;
			 	} );
	}
    </script>
	<form class="myForm" action="'.$root_path.$mode.'" name="saveExpense" method="post">
	<div class="cont_customer">
	<h3>Расход</h3>
	<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="80" align="right" class="title">&nbsp;</td>
		<td width="253" class="rowA title" align=right>Сумма</td>
		<td width="70" align="right" class="title"><input type="text" name="amount" value="'.$content['amount'].'" tabindex="2" style="font-weight:bold; font-size:14;" /></td>
		<td width="140" class="rowA title" colspan="2"></td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">Автомобиль</td>
		<td class="rowA rowB title" id="carsContainer">'.$cars_list.'</td>
		<td align="right" class="title rowB">дата</td>
		<td class="rowA rowB title"><input type="text" name="date" id="payDate" value="'.$content['date'].'" tabindex="3" style="width:90%;" /></td>
		<td class="rowB"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'payDate\', \'\', myDateFormat);" class="datePicker" style="margin-left:-22px;"></td>
	  </tr>
	  <tr>
		<td align="right" class="title">Назначение</td>
		<td class="rowA title">'.$purpose.'</td>
		<td align="right" class="title"><input type="checkbox" name="confirm" id="confirm" value="1" '.($content['paid']=='1'?'checked':'').'></td>
		<td class="rowA title" style="text-align:left;"><label for="confirm">Оплачено?</label></td>
		<td>&nbsp;</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">комментарий</td>
		<td colspan="3" class="rowA rowB title"><span class="rowA title">
		  <input type="text" name="comment" id="comment" value="'.$content['comment'].'" tabindex="4" />
		</span></td>
		<td class="rowB"></td>
		</tr>
	  <tr>
		<td colspan="5" class="rowA" align="right" style="color:#777;">'.$last_edited.'&nbsp;</td>
	  </tr>
	</table><input type="checkbox" name="expense_status" id="payStatus" style="display:none;" '.($content['status']=='1'?'checked="checked"':'').'></div>
	<table width="702" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td class="title"><a href="'.$root_path.'?mod=expenses&sw=delete&id='.$id.'&client='.$content['client'].'&dealer='.$content['dealer'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить этот платеж?\')">удалить</a></td>
		<td width="214" align="right" class="title"><input type="submit" name="Submit" value="Сохранить" id="save" tabindex="5" /></td>
		<td width="8" align="right" class="title"><br />
		  <br /></td>
	  </tr>
	</table>
	</form>';
	return $out;
}


?>