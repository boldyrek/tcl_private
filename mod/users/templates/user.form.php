<?

//
// форма работы с пользователем
//

function userForm($mode, $content, $clients, $transporters,$expeditors,$user_types) {
	
	global $root_path;
	$translate = Zend_Registry::get('translation');
	
	// делаем список типов пользователей
	require($_SERVER['DOCUMENT_ROOT'].$root_path.'/lib/access.php');
	$out .= '<script language="javascript">
	function switchClients(id)
	{
		if(id==\'2\') document.getElementById(\'clients\').style.display=\'\';
		else document.getElementById(\'clients\').style.display=\'none\';

		if(id==\'8\') document.getElementById(\'transporters\').style.display=\'\';
		else document.getElementById(\'transporters\').style.display=\'none\';
		
		if(id==\'9\') document.getElementById(\'expeditors\').style.display=\'\';
		else document.getElementById(\'expeditors\').style.display=\'none\';
	}
	</script>';

/*
	$num = count($user_types);
	$j=1;
	$types_list = '<select name="userType" tabindex="3" onchange="javascript:switchClients(userType.value)">';
	while($j<=$num)
	{
		$types_list .= '<option value="'.$j.'"';
		if($content['type']==$j) $types_list.=' selected="selected"';
		$types_list .= '>'.$user_types[$j].'</option>
		';
		$j++;
	}	
	$types_list .= '</select>';
*/
$types_list = '<select name="userType" tabindex="3" onchange="javascript:switchClients(userType.value)">';
foreach($user_types as $k=>$v){
	$types_list .= '<option value="'.$k.'"';
	if($content['type']==$k) $types_list.=' selected="selected"';
	$types_list .= '>'.$v.'</option>';
}
$types_list .= '</select>';

//	echo "<pre>";
//	print_r($user_types);
//	echo "</pre>";

	//делаем список клиентов (тип пользователя - 2)
	$cl_list = buildSelect($clients, 'u_id', $content['u_id'], $translate->_('не выбран'),'4');
	//делаем список транспортников (тип пользователя - 8)
	$tr_list = buildSelect($transporters, 't_id', $content['t_id'], $translate->_('не выбран'),'5');
	//делаем список Экспедиторов (тип пользователя - 9)
	$ex_list = buildSelect($expeditors, 'e_id', $content['e_id'], $translate->_('не выбран'),'6');
	//###########################
	
	if($content['type']=='2' or $content['type']=='11') $hide_clients = '';
	else $hide_clients = ' style="display:none;"';
	
	if($content['type']=='8') $hide_transporters = '';
	else $hide_transporters = ' style="display:none;"';

	if($content['type']=='9') $hide_expeditors = '';
	else $hide_expeditors = ' style="display:none;"';

	
	if(isset($_GET['error'])) $report_error = '<div class="warn">'.$translate->_('Логин').' '.$_GET['error'].' '.$translate->_('уже существует').'!</div>';
	else $report_error = '';
	
	$out .= '<form class="myForm" action="'.$root_path.$mode.'" method="post">
<div class="cont_customer">'.$report_error.'
	<h3>'.$translate->_('Пользователь').'</h3>
	<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="113" align="right" class="title">'.$translate->_('логин').'</td>
		<td width="202" class="rowA title"><input type="text" name="login" value="'.$content['log_name'].'" tabindex="1" />		</td>
		<td width="123" align="right" class="title">'.$translate->_('тип').'</td>
		<td width="205" class="rowA title">'.$types_list.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">'.$translate->_('пароль').'</td>
		<td class="rowA rowB title"><input type="text" name="password" tabindex="2"></td>
		<td align="right" class="title rowB">'.$translate->_('имя').'</td>
		<td class="rowA rowB title">
		<div id="clients" name="clients"'.$hide_clients.'>'.$cl_list.'</div>
		<div id="transporters" name="transporters"'.$hide_transporters.'>'.$tr_list.'</div>
		<div id="expeditors" name="expeditors"'.$hide_expeditors.'>'.$ex_list.'</div>
		</td>
	  </tr>
	  <tr>
		<td align="right" class="title">&nbsp;</td>
		<td class="rowA title" style="font-size:10px; color:#777;">'.$translate->_('пароль можно только поменять').'</td>
		<td align="right" class="title">Email</td>
		<td class="rowA title"><input type="text" name="email" value="'.$content['email'].'" tabindex="3" /></td>
	  </tr>
	  <tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	  </tr>
	</table>
  </div>
	<table width="702" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td class="title"><a href="'.$root_path.'?mod=users&sw=delete&id='.intval($_GET['id']).'" class="delete" onclick="return confirm(\''.$translate->_('Вы действительно хотите удалить этого пользователя?').'\')">'.$translate->_('удалить').'</a></td>

		<td width="214" align="right" class="title"><input type="submit" name="Submit" value="'.$translate->_('Сохранить').'" id="save" tabindex="5" /></td>
		<td width="8" align="right" class="title"><br /></td>
	  </tr>
	</table>
	</form>';
	
	return $out;
}

?>