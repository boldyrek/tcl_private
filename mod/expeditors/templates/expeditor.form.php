<?

//
// форма экспедитора
//

function expForm($mode, $content, $containers, $root_path, $ports) {
	$translate = Zend_Registry::get('translation');
	$active_ports = explode(';',$content['ports']);
	$aports = array();
	foreach($active_ports as $k=>$v) {
		if($v!='') $aports[$v] = 1;
	}

	$num = @mysql_num_rows($ports);
	if($num!=0) {
	$i = 1;
	$ports_edit = '<form action="'.$root_path.'?mod=expeditors&sw=save&mini=saveports&id='.$content['id'].'" method="post" style="margin:0px;"><table class="list" width="100%">';
	while($i<=$num) {
		$line = mysql_fetch_array($ports);
		$class=='rowA'?$class='rowB':$class='rowA';
		$ports_edit .= '
		<tr class="'.$class.'"><td><input type="checkbox" name="port['.$line['id'].']" value="'.$line['id'].'" id="p_'.$line['id'].'"';
		if($aports[$line['id']]==1) {
			$ports_edit.=' checked="checked"';
			$ports_list .= $line['name'].'; ';
		}
		$ports_edit .= '></td><td><label for="p_'.$line['id'].'">'.$line['name'].'</label></td></tr>';
		$i++;
	}
	$ports_edit .= '</table>
	';
	}
	
	$out = '<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
    <script src="'.$root_path.'js/datepicker.js"></script>
	<form class="myForm" action="'.$root_path.$mode.'" method="post">
<div class="cont">
	<h3>'.$translate->_('Экспедитор').'</h3>
	<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="113" align="right" class="title">'.$translate->_('имя / название').'</td>
		<td width="202" class="rowA title"><input type="text" name="name" value="'.$content['name'].'"  tabindex="1" />
		</td>
		<td width="123" align="right" class="title">'.$translate->_('адрес').'</td>
		<td width="205" class="rowA title"><input type="text" name="address" value="'.$content['address'].'" tabindex="2" /></td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">'.$translate->_('баланс').'</td>
		<td class="rowA rowB title">'.$content['balance'].'</td>
		<td align="right" class="title rowB">'.$translate->_('телефон').'</td>
		<td class="rowA rowB title"><input type="text" name="phone" value="'.$content['phone'].'" tabindex="3" /></td>
	  </tr>
	  <tr>
		<td align="right" class="title">'.$translate->_('работает в портах').'</td>
		<td class="rowA title">'.$ports_list.'</td>
		<td align="right" class="title">email</td>
		<td class="rowA title"><input type="text" name="email" value="'.$content['email'].'" tabindex="4" /></td>
	  </tr>
	  <tr>
		<td>'.(!isset($_GET['add'])?'<a onClick="document.getElementById(\'portAdd\').style.display=\'\'" style="text-decoration:underline;cursor:hand; cursor:pointer;">'.$translate->_('изменить порты').'</a>':'').'</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	</table>
	</div>
	<table width="702" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td class="title"><a href="'.$root_path.'?mod=expeditors&sw=delete&id='.intval($_GET['exp_id']).'" class="delete" onclick="return confirm(\''.$translate->_('Вы действительно хотите удалить этого экспедитора?').'\')">'.$translate->_('удалить').'</a></td>

		<td width="214" align="right" class="title"><input type="submit" name="Submit" value="'.$translate->_('Сохранить').'" id="save" tabindex="5" /></td>
		<td width="8" align="right" class="title"><br />
			<br /></td>
	  </tr>
	</table>
	</form>
	<div id="portAdd" class="addService" style="position:absolute; font-size:11px; margin-left:130px; top:200px;display:none;background-color:#fe6; width:220px; border:1px solid #fff;padding:5px;">
		<div style="float:right;width:20px;"><img src="img/ccl/r_ex.gif" align="right" style="cursor:hand; cursor:pointer;" onclick="document.getElementById(\'portAdd\').style.display=\'none\';"></div><b>'.$translate->_('Порты этого поставщика').'</b>
		<div style="background-color:#fff;padding:5px;border:1px solid #ccc;">'.$ports_edit.'</div><br>
		<br>
		<div align="center"><input type="submit" value="'.$translate->_('Сохранить').'" id="save"></div>
		
	</form></div>';
	
	//проверяем есть ли у экспедитора контейнеры
	if(@mysql_num_rows($containers) != '0')
	{
		$class = "rowA";
		$num = @mysql_num_rows($containers);
		$i=1; 
		while ($i<=$num)
		{
			$line = mysql_fetch_array($containers);
			$containers_list .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
				<td>'.$translate->_('КОНТЕЙНЕР').'</td>
				<td>'.$line['price'].'</td>
				<td>'.$line['number'].'&nbsp;</td>
				<td width="30" onclick="document.location=\''.$root_path.'?mod=containers&sw=form&cont_id='.$line['id'].'\'"><img src="'.$root_path.'img/ccl/goto.gif"></td>
				</tr>';
				$i++;
				if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
		}
	}
	
	//услуги и платежи экспедитора
	
	if(!isset($_GET['add']))
	{
	$exp_payments = mysql_query("SELECT * from `ccl_".ACCOUNT_SUFFIX."exp_pay` WHERE `to`='".$content['id']."' ORDER BY date DESC");

	$exp_services = mysql_query("SELECT * from `ccl_".ACCOUNT_SUFFIX."exp_serv` WHERE `from`='".$content['id']."' ORDER BY date DESC");

	$out .= '<div style="background-color:#eee; width:692px;padding:4px;">
	<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
		  <tr class="title">
			<td width="50%" style="border-right:1px solid #ccc;"><b>'.$translate->_('Услуги экспедитора').'</b><img src="img/ccl/bl_plus.gif" align="absmiddle" hspace=5 border="0" style="cursor:hand; cursor:pointer;" alt="'.$translate->_('Добавить услугу').'" onclick="document.getElementById(\'addService\').style.display=\'\';">
			<div style="position:absolute; margin-left:110px; margin-top:-16px; background-color:#fe6; width:220px; border:1px solid #fff;padding:5px;display:none;" id="addService"><b>'.$translate->_('Добавление услуги').'</b><img src="img/ccl/r_ex.gif" align="right" style="margin-top:-15px;cursor:hand; cursor:pointer;" onclick="document.getElementById(\'addService\').style.display=\'none\';">
			<form name="add_service" action="'.$root_path.$mode.'&mini=expservice" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
			  <tr>
				<td class="title">'.$translate->_('дата').'</td>
			  </tr>
			  <tr>
				<td class="rowB title"><input type="text" name="date" id="servDate" value="'.date("Y-m-d").'" /><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'servDate\', \'\', myDateFormat);" style="cursor:hand; cursor:pointer;"></td>
			  </tr>
			  <tr>
				<td class="title">'.$translate->_('сумма').'</td>
			  </tr>
			  <tr>
				<td class="rowB title"><input type="text" name="sum" /></td>
			  </tr>
			  <tr>
				<td class="title">'.$translate->_('описание').'</td>
			  </tr>
			  <tr>
				<td class="rowB title"><input type="text" name="comment" /></td>
			  </tr>
  			  <tr>
				<td class="title"><input type="submit" value="'.$translate->_('Сохранить').'" /></td>
			  </tr>
			</table></form>
			</div>
			</td>
			<td width="50%"><b>'.$translate->_('Платежи экспедитору').'</b>
			<img src="img/ccl/bl_plus.gif" align="absmiddle" hspace=5 border="0" style="cursor:hand; cursor:pointer;" alt="'.$translate->_('Добавить услугу').'" onclick="document.getElementById(\'addPayment\').style.display=\'\';">
			<div style="position:absolute; margin-left:125px; margin-top:-16px; background-color:#d2ff66; width:220px; border:1px solid #fff;padding:5px;display:none;" id="addPayment"><b>'.$translate->_('Добавление платежа').'</b><img src="img/ccl/r_ex.gif" align="right" style="margin-top:-15px;cursor:hand; cursor:pointer;" onclick="document.getElementById(\'addPayment\').style.display=\'none\';">
			<form name="add_payment" action="'.$root_path.$mode.'&mini=exppayment" method="post"><table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
			  <tr>
				<td class="title">'.$translate->_('дата').'</td>
			  </tr>
			  <tr>
				<td class="rowB title"><input type="text" name="date" id="payDate" value="'.date("Y-m-d").'" /><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'payDate\', \'\', myDateFormat);" style="cursor:hand; cursor:pointer;"></td>
			  </tr>
			  <tr>
				<td class="title">'.$translate->_('сумма').'</td>
			  </tr>
			  <tr>
				<td class="rowB title"><input type="text" name="sum" /></td>
			  </tr>
			  <tr>
				<td class="title">'.$translate->_('описание').'</td>
			  </tr>
			  <tr>
				<td class="rowB title"><input type="text" name="comment" /></td>
			  </tr>
  			  <tr>
				<td class="title"><input type="submit" value="'.$translate->_('Сохранить').'" /></td>
			  </tr>
			</table></form>
			</div>
			</td>
		  </tr>
		</table>
		<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
		  <tr>
			<td width="50%" style="border-right:1px solid #ccc;" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr class="title">
				<td width="60">'.$translate->_('дата').'</td>
				<td width="60">'.$translate->_('сумма').'</td>
				<td>'.$translate->_('описание').'</td>
				<td width="15">&nbsp;</td>
			  </tr>
			'.$containers_list.makeList($exp_services, 'expservice', intval($_GET['exp_id'])).'
			</table></td>
			<td width="50%" valign="top"><table width="100%" border="0" cellspacing="0" cellpadding="0">
			  <tr class="title">
				<td width="60">'.$translate->_('дата').'</td>
				<td width="60">'.$translate->_('сумма').'</td>
				<td>'.$translate->_('описание').'</td>
				<td width="15">&nbsp;</td>
			  </tr>
			'.makeList($exp_payments, 'exppayment', intval($_GET['exp_id'])).'
			</table></td>
		  </tr>
		</table></div>';
	}
	
	return $out;
	exit;
}

function makeList($list, $who, $type) {
	$translate = Zend_Registry::get('translation');
	$class = "rowA";
	$num = @mysql_num_rows($list);
	$i=1; 
	while ($i<=$num)
	{
		$line = @mysql_fetch_array($list);
		$print .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" >
			<td width="70">'.$line['date'].'</td>
			<td width="40">'.str_replace('.',',',$line['sum']).'</td>
			<td>'.$line['comment'].'&nbsp;</td>
			<td><a href="'.$root_path.'?mod=expeditors&sw=delete&what='.$who.'&id='.$line['id'].'&parent='.$type.'"><img src="img/ccl/del_img.gif" onclick="return confirm(\''.$translate->_('Вы действительно хотите удалить эту запись?').'\')" border="0"></a></td>
			</tr>';
			$i++;
			if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
	}
	return $print;
}

?>