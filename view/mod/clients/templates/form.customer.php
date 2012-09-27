<?

function customerForm($mode, $content, $dealers, $id, $total, $ontheway, $paid, $prepay) {
	if($content['dealer']=='1') $dealer = ' checked="checked"';
	else $dealer ='';
	
	if($content['balance']<0) $advance=" (аванс)";
	if(isset($_GET['add'])) $content = array();
	//скан паспорта
	if($content['scan']!='')
	{
		$thumb = '<img src="'.$root_path.'img/ccl/attached.gif" vspace="5" border="0">';
		$scan = '<div style="padding-left:50px;"><a href="'.$root_path.'photos/scan/'.ACCOUNT_SUFFIX.$id.'/'.$content['scan'].'" target="_blank">'.$thumb.'</a><br>
		<a href="'.$root_path.'?mod=clients&sw=delete&what=scan&name='.$content['scan'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить прикрепленный файл?\')">удалить</a></div>';
	}
	else {
	$scan = '<form action="'.$root_path.'?ref=upload" method="post" class="myForm" enctype="multipart/form-data">
	<input type="file" name="file">
	<input type="hidden" name="scanImage" value="'.$id.'">
	<br><input type="submit" value="Загрузить" id="save"></form>';
	}
	
	//список всех дилеров
	$myDealers = buildSelect($dealers, 'mydealer', $content['mydealer'], 'не выбран', '10');
	
	$out = '<form action="'.$root_path.$mode.(isset($_GET['hidemenu'])?'&hidemenu':'').'" method="post" enctype="multipart/form-data" class="myForm">
	<div class="cont_customer">
	<div style="float:right;font-size:11px;"><a href="'.$root_path.'?mod=client_cars&client='.$content['id'].'">автомобили клиента</a></div>
	<h3>Клиент</h3>
	<table width="792" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="113" align="right" class="title">ФИО</td>
		<td width="253" class="rowA title"><input type="text" name="name" value="'.cleanContent($content['name']).'" tabindex="1" /></td>
		';
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $out .= '<td align="right" class="title" width="150">доставлено на сумму:</td>
		<td width="204" class="rowA title">'.$content['cars_delivered'].' ('.($total-$ontheway).')</td>';
		else $out .= '<td class="title"></td>
		<td width="204" class="rowA title"></td>';
		$out .= '
		</tr>
	  <tr>
		<td align="right" class="title rowB">адрес</td>
		<td class="rowA rowB title"><input type="text" name="address" value="'.$content['address'].'" tabindex="2"/></td>
		';
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $out .= '<td align="right" class="title rowB">предоплата<br>за машины в пути:</td>
		<td class="rowA rowB title">'.$prepay.' ('.$ontheway.')</td>';
		else $out .= '<td class="title rowB"></td>
		<td class="rowA rowB title"></td>';
	  $out .= '
	  </tr>
	  <tr>
		<td align="right" class="title">Name (English)</td>
		<td class="rowA title"><input type="text" name="name_en" value="'.$content['name_en'].'" tabindex="3"/></td>
		';
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $out .= '<td align="right" class="title">платежи</td>
		<td class="rowA title">'.$paid.'</td>';
		else $out .= '<td class="title"></td>
		<td class="rowA title"></td>';
		$out .= '
	  </tr>
	  <tr>
		<td align="right" class="title rowB">Address (English)</td>
		<td class="rowA rowB title"><input type="text" name="address_en" value="'.$content['address_en'].'" tabindex="4"/></td>';
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $out .= '<td align="right" class="title rowB">к оплате</td>
		<td class="rowA rowB title"><nobr>'.(isset($_GET['hidemenu'])?'':'<a href="'.$root_path.'?mod=clients&sw=detail&id='.$id.'&dealer='.$content['dealer'].'">').'<b>'.$content['balance'].'</b>'.(isset($_GET['hidemenu'])?'':'</a>').' '.$advance.' &nbsp; &nbsp;баланс: '.$content['real_balance'].'</nobr></td>';
		else $out .= '<td class="title rowB"></td>
		<td class="rowA rowB title"></td>';
		$out .= '
	  </tr>
	  <tr>
		<td align="right" class="title">контакты</td>
		<td class="rowA title"><input type="text" name="contacts" value="'.$content['contacts'].'" tabindex="5" /></td>';
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $out .= '<td align="right" class="title">машины в пути</td>
		<td class="rowA title">'.$ontheway.'</td>';
		else $out .= '<td class="title"></td>
		<td class="rowA title"></td>';
	  $out .= '
	  </tr>
	  <tr>
		<td align="right" class="title rowB">паспорт</td>
		<td class="rowA rowB title"><input type="text" name="passport" value="'.$content['passport'].'" tabindex="6" /></td>';
		if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $out .= '<td align="right" class="title rowB">всего машин заказано</td>
		<td class="rowA rowB title">'.$total.'</td>';
		else $out .= '<td class="title rowB"></td>
		<td class="rowA rowB title"></td>';
		$out .= '
	  </tr>
	  <tr>
		<td align="right" class="title">email</td>
		<td class="rowA title"><input type="text" name="email" value="'.$content['email'].'" tabindex="7" /></td>
		<td class="title">&nbsp;</td>
		<td class="rowA title"><table border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="25" style="border:0px;"><input type="checkbox" name="dealer" id="dealer" style="border:0px;"'.$dealer.' tabindex="8" onClick="javascript:switchList(this.checked);"></td>
    <td style="font-size:11px;border:0px;"><label for="dealer" style="cursor:hand; cursor:pointer;">дилер</label></td>
  </tr>
</table></td>
	  </tr>
	  <tr>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB" align="right">дилер этого клиента:</td>
		<td class="rowA rowB title">'.$myDealers.'&nbsp;</td>
	  </tr>
	  <tr>
	    <td class="title" align="right">логин:</td>
	    <td class="rowA title">';
		if($id!='') $out .= '<input type="text" value="'.ClientID($id).'" name="login" tabindex="11">';
		if(isset($_GET['error'])) $out .= '<br><span style="font-size:11px; color:#f00;">логин <b>'.$_GET['error'].'</b> уже существует!</span>';
		$out .='</td>
	    <td class="title">&nbsp;</td>
	    <td class="rowA title">&nbsp;</td>
      </tr>
	  <tr>
	    <td class="title rowB" align="right" valign="top">пароль:</td>
	    <td class="rowA rowB title">';
		if($id!='') $out .= '<input type="text" name="password" tabindex="12">
		<br><span style="font-size:10px; color:#777;">пароль можно только сменить!</span>';
		$out.='</td>
	    <td class="title rowB" align="right">контракт</td>
	    <td class="rowA rowB title">'.(isset($_GET['hidemenu'])?'&nbsp;':(isset($_GET['add'])?'&nbsp;':contractsList($content['contract_info']))).'</td>
      </tr>
	</table>
	</div>
	<table width="802" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td class="title">'.(isset($_GET['add'])?'':'<a href="'.$root_path.'?mod=clients&sw=delete&id='.$id.(isset($_GET['hidemenu'])?'&hidemenu':'').'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить этого клиента?\')">удалить</a>').'</td>
		<td width="214" align="right" class="title"><input type="submit" name="Submit" value="Сохранить" id="save" tabindex="13" /></td>
		<td width="8" align="right" class="title"><br />
		  <br /></td>
	  </tr>
	</table>
	</form>';
	if(!isset($_GET['add'])) $out.='
	<table width="230" class="list"><tr>
	    <tr><td class="title rowB">копия паспорта:</td></tr>
	    <tr><td class="rowA rowB title">'.$scan.'</td></tr>
	</table>';
	
	$out .= '
	<script>

		function switchList(handler) {
		document.getElementById("Listmydealer").disabled = handler;
		}
		
		switchList(document.getElementById(\'dealer\').checked);
	</script>';
	
	if(isset($_GET['hidemenu'])) $out .= '
	<script>
	function toParent() {
		if(parent.check == \'1\') {
		parent.showForm();
		}
	}
	window.onload = toParent();
	</script>
	';
	return $out;
}

function contractsList($data) {
	$num = @mysql_num_rows($data);
	if($num>0) {
		$i=0;
		while($i<$num) {
			$line = mysql_fetch_array($data);
			$out .= ', <a href="'.$root_path.'?mod=contracts&sw=form&contract='.$line['id'].'">'.$line['number'].'</a>';
			$i++;
		}
	}
	
	else {
		$out = 'нет договоров, <a href="'.$root_path.'?mod=contracts&sw=add&client='.intval($_GET['customer_id']).'">создать</a>';
	}
	return ltrim($out,', ');
}

function ClientID($id) {
	$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."usrs` WHERE `type` = '2' and `u_id` = '".$id."'";
	$result = @mysql_fetch_array(mysql_query($sql));

	return $result['log_name'];
}


?>