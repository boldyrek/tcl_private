<?

// форма редактирования контейнера

function lootSlot($content,$try,$n) {
	if($content['slot'.$n]==$try) {
		return '1';
		break;
	}
}

function makeSlotList($content, $orphants, $loaded, $n) {
	$out = '';
	if($content['slot'.$n]=='0') $out.='---';

	if($loaded[$n.'id']!='')
	{
		$out.= substr($loaded[$n.'frame'],0,3).'..'.substr($loaded[$n.'frame'],(strlen($loaded[$n.'frame'])-4),4).' '.$loaded[$n.'model'];
	}
	/*$j=1;
	$num=count($orphants);
	
	while($j<=$num)
	{
		if($orphants[$j]['port'] == $content['port']) $out.=substr($orphants[$j]['frame'],0,3).'..'.substr($orphants[$j]['frame'],(strlen($orphants[$j]['frame'])-4),4).' '.$orphants[$j]['model'];
		$j++;
	}*/
	return $out;
}

//##########################
//
//форма работы с контейнером
//
//##########################
function containerForm($mode, $content, $orphants, $loaded, $id, $expeditors, $ports, $myport, $files) {
	
	global $root_path;
		
	if(!isset($_GET['add']))
	{
		$slot1 = makeSlotList($content, $orphants, $loaded, 1);
		$slot2 = makeSlotList($content, $orphants, $loaded, 2);
		$slot3 = makeSlotList($content, $orphants, $loaded, 3);
		$slot4 = makeSlotList($content, $orphants, $loaded, 4);
	}
	
	$attached_files = '
	<div style="background-color:#fff;padding:5px;width:700px;border:1px solid silver;margin-top:3px;"" class="title">
	<table><tr><td class="title" valign="top">'.getFileList($files).'</td></tr></table>';
	
	
	if($_SESSION['user_type'] == '5') {
		$expeditor = mysql_fetch_array(mysql_query("SELECT name FROM `ccl_".ACCOUNT_SUFFIX."expeditors` WHERE `id` ='".$content['expeditor']."'"));
	}
		
	$out = '
	<div class="cont"><h3>Контейнер</h3>
	<table width="694" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="110" align="right" class="title">номер</td>
		<td width="200" class="rowA title">'.$content['number'].'&nbsp;</td>
		<td class="rowA title" width="5">&nbsp;</td>
		<td width="50" class="title">&nbsp;</td>
		<td align="center" class="title">автомобили погруженные</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB"><strong>отправкa</strong></td>
		<td class="rowA rowB title">'.$content['sent'].'</td>
		<td class="title rowB"></td>
		<td width="120" align="right" class="title rowB">&nbsp;</td>
		<td width="200" class="rowA rowB title">&nbsp;
		</td>
	  </tr>
	  <tr>
		<td align="right" class="title"><strong>Дата погрузки</strong></td>
		<td class="rowA title">'.$content['loaddate'].'</td>
		<td class="title" align="left"></td>
		<td align="right" class="title"></td>
		<td class="rowA title">'.$slot1.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB"><strong>Порт</strong></td>
		<td class="rowA rowB title">'.$content['portdate'].'</td>
		<td class="title rowB"></td>
		<td align="right" class="title rowB"></td>
		<td class="rowA rowB title">'.$slot2.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title"><strong>Ж/Д</strong></td>
		<td class="rowA title">'.$content['rail'].'</td>
		<td class="title"></td>
		<td align="right" class="title"></td>
		<td class="rowA title">'.$slot3.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB"><strong>Станция<br>назначения</strong></td>
		<td class="rowA rowB title">'.$content['bishkek'].'</td>
		<td class="title rowB"></td>
		<td class="title rowB" align="right"></td>
		<td class="rowA rowB title">'.$slot4.'</td>
	  </tr>
	  <tr>
		<td align="right" class="rowA title">&nbsp;</td>
		<td class="rowA title"><b>';
		if($content['arrived'] == '1') $out .= 'доставлен ';
		else $out .='не доставлен';
		$out .= '</b></td>
		<td class="rowA title">&nbsp;</td>
		<td align="right" class="rowA title">экспедитор</td>
		<td class="rowA title">
		'.$content['exp_name'].'
		</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB"></td>
		<td class="rowA rowB title"></td>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB">&nbsp;</td>
	  </tr>
	</table></div>
	</form>'.$attached_files;
	return $out;
}

function getFileList($files) {
	$out = '';
	$ico = 'excel.gif';
	require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');
	while($line = mysql_fetch_array($files)) {
		//$ico = fileIco($line['file']);
		if(strstr($line['file'],'.xls'))
		{
			$out .= Proto::wrapFile('<a href="/photos/containers/'.ACCOUNT_SUFFIX.$line['container'].'/'.$line['file'].'" target="_blank"><img src="/img/ico/'.$ico.'" border="0"></a><br>
			<b>'.substr($line['filename'], 0, 20).'</b>');
		}
	}
	return $out;
}

?>