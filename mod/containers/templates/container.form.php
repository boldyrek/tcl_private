<?

// форма редактирования контейнера

function lootSlot($content,$try,$n) {
	if($content['slot'.$n]==$try) {
		return '1';
	}
}

function makeSlotList($content, $orphants, $loaded, $n) {
	$out = '<option value="0"';
	if($content['slot'.$n]=='0') $out.=' selected="selected">---</option>';
	else $out.='>---</option>';

	if($loaded[$n.'id']!='')
	{
		$out.='<option value="'.$loaded[$n.'id'].'" selected="selected">'.substr($loaded[$n.'frame'],0,3).'..'.substr($loaded[$n.'frame'],(strlen($loaded[$n.'frame'])-4),4).' '.$loaded[$n.'model'].'</option>';
		$hid.='<input type=hidden name="hid_slot'.$n.'" value="'.$loaded[$n.'id'].'">'; //автомобиль в этом слоте
	}
	$j=1;
	$num=count($orphants);

	while($j<=$num)
	{
		if($orphants[$j]['port'] == $content['port']) $out.='<option value='.$orphants[$j]['id'].'>'.substr($orphants[$j]['frame'],0,3).'..'.substr($orphants[$j]['frame'],(strlen($orphants[$j]['frame'])-4),4).' '.$orphants[$j]['model'].'</option>';
		$j++;
	}
	return $out.$hid;
}

function getListStuff($stuff)
{
	$buff = '<select id="stuff" style="width:430px;">';
	foreach ($stuff as $key=>$val)
	{
		$buff .= "<option value='{$key}'>{$val}</option>";
	}
	$buff .= '</select>';
	return $buff;
}

function getStuffOnBoard($stuff_onboard)
{
	$translate = Zend_Registry::get('translation');
	if(!isset($stuff_onboard)){return;}
	$buff = ''; $i=1;
	foreach ($stuff_onboard as $val)
	{
		if ($i%2==0) $class='rowA'; else $class="rowB";
		$buff .= "<div class='stuff_in $class'><input type='hidden' name='stuff[]' value='{$val['id']}'>
		".captionLink($val['id'], '0', $root_path.'?mod=stuff&sw=form&stuff_id='.$val['id'], $translate->_('Инфо'))."
		{$val['name']}<a href='#' class='but_del'>[ - ]</a></div>";
		$i++;
	}
	return $buff;
}


//##########################
//
//форма работы с контейнером
//
//##########################
function containerForm($mode, $content, $orphants, $loaded, $id, $expeditors, $ports, $myport, $files, $stuff, $stuff_onboard) {

	global $root_path;
	$translate = Zend_Registry::get('translation');

	//выбор порта отправки
	//если порт выбран, а также погружены машины, нельзя поменять порт, пока не будут убраны все машины.
	if(isset($_GET['add'])) {
		$content['slot1']='0';
		$content['slot2']='0';
		$content['slot3']='0';
		$content['slot4']='0';
                $content['slot5']='0';
		$attached_files = '';
	}
	else $attached_files = '
	<div style="background-color:#fff;padding:5px;width:700px;border:1px solid silver;margin-top:3px;"" class="title">
	<table><tr><td class="title" valign="top">
	<b>'.$translate->_('Загрузить документ').':</b><br><br>
				<form action="'.$root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
				'.$translate->_('название файла').':
				<input type="text" name="title" style="width:80%;"><br><br>
				<input type="file" name="file"><br>
				<input type="hidden" name="container" value="'.$content['id'].'">
				<br>
				<input type="submit" name="Submit" value="'.$translate->_('Загрузить').'" id="save" /></form>
				</td><td>
				'.getFileList($files).'</td></tr></table>';

	if($content['slot1']!='0' or $content['slot2']!='0' or $content['slot3']!='0' or $content['slot4']!='0' or $content['slot5']!='0')
	{
		$ports_list .= '<input type="hidden" name="port" value="'.$content['port'].'">'.$myport['name'];
	}
	else {
		$num = @mysql_num_rows($ports);
		if($num!=0 and $num!=1) {
			$num = mysql_num_rows($ports);
			$i=1;
			if($num>0) {
				$out = '<select name="port" tabindex="10" id="List'.$name.'">
				<option value="0"';
				if($content['port']==0) $out .= ' selected="selected"';
				$out .= '>'.$translate->_(' - не выбран - ').'</option>';
				while($i<=$num) {
					$line = mysql_fetch_array($ports);
					$out .= '
					<option value="'.$line['id'].'"';
					if($content['port']==$line['id']) $out .= ' selected="selected"';
					$out .= '>'.stripslashes($line['name']).'</option>';
					$i++;
				}
				$out .= '</select>';
				mysql_data_seek($ports, 0);
			}
			$ports_list = $out;
		}
		elseif($num == 1) {
			$line = mysql_fetch_array($ports);
			$ports_list = $line['name'].'
			<input type="hidden" name="port" value="'.$line['id'].'">\n';
		}
		else $ports_list = $translate->_('нет портов');
	}


	if($content['port'] == '0') $slot1 = $translate->_('не выбран порт отправки!');
	elseif(!isset($_GET['add']))
	{
		$slot1 = '<select name="slot1" tabindex="10" class="monoSelect">'.makeSlotList($content, $orphants, $loaded, 1).'</select>';
		$slot2 = '<select name="slot2" tabindex="11" class="monoSelect">'.makeSlotList($content, $orphants, $loaded, 2).'</select>';
		$slot3 = '<select name="slot3" tabindex="12" class="monoSelect">'.makeSlotList($content, $orphants, $loaded, 3).'</select>';
		$slot4 = '<select name="slot4" tabindex="13" class="monoSelect">'.makeSlotList($content, $orphants, $loaded, 4).'</select>';
                $slot5 = '<select name="slot5" tabindex="14" class="monoSelect">'.makeSlotList($content, $orphants, $loaded, 5).'</select>';
	}


	if($_SESSION['user_type'] == '5') {
		$expeditor = mysql_fetch_array(mysql_query("SELECT name FROM `ccl_".ACCOUNT_SUFFIX."expeditors` WHERE `id` ='".$content['expeditor']."'"));
	}

	// станция назначения
	$station = listPlaces($content['station']);

	// время прибытия
	$arrive_time = '<input type="text" name="arrive_time" value="'.$content['arrive_time'].'" id="arrive_time">';

	// порт перегруза на Ж/Д
	$torail = toRail($content['torail']);

	// список экспедиторов
	if($content['port']!=0 and $content['port']!='') $expeditors_list = buildSelect($expeditors, 'expeditor', $content['expeditor'], $translate->_('не выбран'),'14');
	else $expeditors_list = $translate->_('не выбран порт');

        $agents = array(0 => $translate->_('не выбран'));

        $query = mysql_query("SELECT `id`, `log_name` FROM `ccl_usrs` WHERE `type` = 13");

        while ($row = mysql_fetch_object($query))
        {
           $agents[$row->id] = $row->log_name;
        }

        unset($query);

	$out = '<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
    <script src="'.$root_path.'js/datepicker.js"></script>

	<form class="myForm" id="myForm" action="'.($_SESSION['user_type'] == 13 ? '' : $root_path.$mode).'" method="post">
	<div class="cont" style="width:704px;"><h3>'.$translate->_('Контейнер').'</h3>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="110" align="right" class="rowA title">'.$translate->_('номер').'</td>
		<td width="200" class="rowA title"><input type="text" name="number" value="'.$content['number'].'" tabindex="1" /></td>
		<td class="rowA title" width="5">&nbsp;</td>
		<td width="50" class="rowA title">&nbsp;</td>
		<td align="center" class="rowA title">'.$translate->_('автомобили погруженные').'</td>
	  </tr>
	  <tr>
		<td align="right" class="rowB title"><b>'.$translate->_('Дата погрузки в контейнер').'</b></td>
		<td class="rowB title"><input type="text" name="loaddate" id="loaddate" value="'.$content['loaddate'].'" tabindex="3" /></td>
		<td class="rowB title" align="left"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'loaddate\', \'\', myDateFormat);" class="datePicker"></td>
		<td width="120" align="right" class="title rowB">'.$translate->_('порт').'</td>
		<td width="200" class="rowB title">'.$ports_list.'
		</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowA"><strong>'.$translate->_('Отправкa из США').'</strong></td>
		<td class="rowA title"><input type="text" name="sent" id="sentDate" value="'.$content['sent'].'" tabindex="2" /></td>
		<td class="title rowA"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'sentDate\', \'\', myDateFormat);" class="datePicker"></td>
		<td align="right" class="title">'.captionLink(($_SESSION['user_type'] == 13 ? 0 : $content['slot1']), '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot1'], '№1').'</td>
		<td class="rowA title">'.$slot1.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB"><strong>'.$translate->_('Приход в порт перегруза').'</strong></td>
		<td class="rowA rowB title"><input type="text" name="portdate" id="portDate" value="'.$content['portdate'].'" tabindex="4" /></td>
		<td class="title rowB"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'portDate\', \'\', myDateFormat);" class="datePicker"></td>
		<td align="right" class="title rowB">'.captionLink(($_SESSION['user_type'] == 13 ? 0 : $content['slot2']), '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot2'], '№2').'</td>
		<td class="rowA rowB title">'.$slot2.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title"><strong>'.$translate->_('Погрузка на Ж/Д платформу').'</strong></td>
		<td class="rowA title"><input type="text" name="rail" id="railDate" value="'.$content['rail'].'" tabindex="5" /></td>
		<td class="title"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'railDate\', \'\', myDateFormat);" class="datePicker"></td>
		<td align="right" class="title">'.captionLink(($_SESSION['user_type'] == 13 ? 0 : $content['slot3']), '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot3'], '№3').'</td>
		<td class="rowA title">'.$slot3.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB"><strong>'.$translate->_('Станция <br>назначения - Бишкек').'</strong></td>
		<td class="rowA rowB title"><input type="text" name="bishkek" id="bishkekDate" value="'.$content['bishkek'].'" tabindex="6" /></td>
		<td class="title rowB"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'bishkekDate\', \'\', myDateFormat);" class="datePicker"></td>
		<td class="title rowB" align="right">'.captionLink(($_SESSION['user_type'] == 13 ? 0 : $content['slot4']), '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot4'], '№4').'</td>
		<td class="rowA rowB title">'.$slot4.'</td>
	  </tr>
          <tr>
		<td align="right" class="title rowB"></td>
		<td class="rowA rowB title"></td>
		<td class="title rowB"></td>
		<td class="title rowB" align="right">'.captionLink(($_SESSION['user_type'] == 13 ? 0 : $content['slot5']), '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot5'], '№5').'</td>
		<td class="rowA rowB title">'.$slot5.'</td>
	  </tr>
	  ';

        if ($_SESSION['user_type'] == 13)
        {
           $out .= '<tr>
		<td class="title rowB" align="right">'.$translate->_('Морская Линия').'</td>
		<td class="title rowB"><input type="text" name="sealine" value="'.$content['sealine'].'" /></td>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB" align="right">'.$translate->_('Номер букинга').'</td>
		<td class="title rowB"><input type="text" name="booking" value="'.$content['booking'].'" /></td>
	  </tr>';
        }
        else
        {
        $out .= '
           <tr>
		<td align="right" class="rowA title">&nbsp;</td>
		<td class="rowA title">
                <table><tr><td width="18" style="border:0px;"><input type="checkbox" name="delivered" style="border:0px;" id="del"'.($content['arrived'] == '1' ? ' checked="checked"' : '').' tabindex="7"></td><td class="title" style="border:0px;"><label for="del" style="cursor:hand; cursor:pointer;">'.$translate->_('доставлен').'</label></td></tr></table>
                </td>
		<td class="rowA title">&nbsp;</td>
		<td align="right" class="rowA title">'.$translate->_('экспедитор').'</td>
		<td class="rowA title">
		'.($_SESSION['user_type']!='5'?$expeditors_list:'<input type="hidden" name="expeditor" value="'.$content['expeditor'].'">'.$expeditor['name']).'
		</td>
	  </tr>
          <tr>
		<td align="right" class="rowA title">&nbsp;</td>
		<td class="rowA title">&nbsp;</td>
		<td class="rowA title">&nbsp;</td>
		<td align="right" class="rowA title">'.$translate->_('Forwarding agent').'</td>
		<td class="rowA title">
                   <select name="agent_id">
          ';
                  foreach ($agents AS $agent_id => $agent_name)
                  {
                     $out .= '<option value="'.$agent_id.'"'.($content['agent_id'] == $agent_id ? ' selected="selected"' : '').'>'.$agent_name.'</option>';
                  }
        $out .= '
                   </select>
                </td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">'.$translate->_('цена  доставки').'</td>
		<td class="rowA rowB title"><input type="text" name="price" value="'.$content['price'].'" tabindex="8" /></td>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB" align="right">'.$translate->_('Номер букинга').'</td>
		<td class="title rowB"><input type="text" name="booking" value="'.$content['booking'].'" /></td>
	  </tr>
	  <tr>
		<td align="right" class="title rowA">'.$translate->_('порт перегруза<br>на Ж/Д').'</td>
		<td class="rowA rowA title">'.$torail.'</td>
		<td class="title rowA">&nbsp;</td>
		<td class="title rowA" align="right">'.$translate->_('Морская Линия').'</td>
		<td class="title rowA"><input type="text" name="sealine" value="'.$content['sealine'].'" /></td>
	  </tr>
	 <tr>
		<td align="right" class="title rowB">'.$translate->_('ожидаемая дата<br>прибытия').'</td>
		<td class="rowA rowB title">'.$arrive_time.'</td>
		<td class="title rowB"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'arrive_time\', \'\', myDateFormat);" class="datePicker"></td>
		<td class="title rowB" align="right">'.$translate->_('станция<br>назначения').'</td>
		<td class="title rowB">'.$station.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowA">'.$translate->_('Номер ж/д платформы').'</td>
		<td class="rowA rowA title"><input type="text" name="platform" value="'.$content['platform'].'"></td>
		<td class="title rowA">&nbsp;</td>
		<td class="title rowA" align="right">'.$translate->_('Стоимость контейнера<br>в Бишкеке').'</td>
		<td class="title rowA"><input type="text" name="sellprice" value="'.$content['sell_price'].'" /></td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB">'.$translate->_('Собственный контейнер').'</td>
		<td class="rowA rowB title" align="left"><input type="checkbox" id="own-control" name="own" '.($content['own'] == 1 ? 'checked="checked"' : '').' value="'.$content['own'].'" /></td>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB" align="right">'.$translate->_('Стоимость разгрузки<br>контейнера').'</td>
		<td class="title rowB"><input type="text" name="unloadprice" value="'.$content['unload_price'].'" /></td>
	  </tr>
          <tr>
		<td align="right" class="title rowA">'.$translate->_('Документы готовы').'</td>
		<td class="rowA title" align="left"><input type="checkbox" id="docs_ready" name="docs_ready" '.($content['docs_ready'] ? 'checked="checked"' : '').' value="'.$content['docs_ready'].'" /></td>
		<td class="title rowA" colspan="3">&nbsp;</td>
	  </tr>
          <tr id="own-row" style="display:'.($content['own'] == 1 ? 'table-row' : 'none').'">
		<td align="right" class="title rowB">'.$translate->_('Имя получателя').'</td>
		<td class="rowB title"><input type="text" name="reciever_name" value="'.$content['reciever_name'].'" /></td>
		<td class="title rowB">&nbsp;</td>
		<td class="title rowB" align="right">'.$translate->_('Адрес получателя').'</td>
		<td class="title rowB"><input type="text" name="reciever_address" value="'.$content['reciever_address'].'" /></td>
	  </tr>';
        }

        $out .= '</table>
	
	<script src="js/jquery.js"></script>
	<script>
	
	  $(document).ready(function(){
          $("#own-control").click(function()
          {
             $("#own-row").toggle();
             $(this).val(this.checked ? 1 : 0);
          });
          $("#docs_ready").click(function()
          {
             $(this).val(this.checked ? 1 : 0);
          });
          ';
        
        if ($_SESSION['user_type'] == 13)
        {
           $out .= '$("#myForm input, #myForm select").attr("disabled", "disabled"); $("img.datePicker").remove();';
        }
    
    $out .= '$("#stuff_but").click(function () {
          var txt = $("#stuff option:selected").text();
          var val = $("#stuff").val();
          $("#stuff_field").html($("#stuff_field").html()+\'<div class="stuff_in st_new"><input type="hidden" name="stuff[]" value="\'+val+\'"> \'+txt+\' <a href="#" class="but_del">[ - ]</a></div>\');  
	            $(".but_del").bind("click", function(){
				  $(this).parent().remove();
				});
          });
          
          $(".but_del").bind("click", function(){
				  $(this).parent().remove();
				});
          
        
  });

	
	</script>';

    if ($_SESSION['user_type'] != 13)
    {
	$out .= '<table width="100%" border="0" cellpadding="3" cellspacing="0" class="list" style="margin-top:3px;">
		<tr>
			<td class="title rowB" colspan=2><b>'.$translate->_('Товар в контейнере').'<b></td>
		</tr>
		<tr>
		<tr>
			<td width="500" class="title">'.$translate->_('Добавить').': '.getListStuff($stuff).'
			</td>
			<td>
			<input type="button" id="stuff_but" value="[ + ]" style="width:70px;">
			</td>			
		</tr>
		<tr>
			<td style="padding-top:10px;"  class="title" colspan=2>
			<div id="stuff_field">
			'.getStuffOnBoard($stuff_onboard).'
			</div>
			</td>
		</tr>
         </table>';
    }
	$out .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list"  style="margin-top:3px;">
	  <tr>
		<td class="title">'.($_SESSION['user_type'] == 13 ? '' : '<b><a href="'.$root_path.'?mod=containers&sw=delete&id='.$id.'" class="" onclick="return confirm(\''.$translate->_('Вы действительно хотите удалить этот контейнер?').'\')">'.$translate->_('удалить').'</a></b>').'
                '.($content['docs_ready'] ? '<span style="margin-left:10px"><a href="'.$root_path.'?mod=containers&sw=download&doc=invoice1&id='.$id.'" class="delete">'.$translate->_('invoice for cars').'</a></span>
                '.($content['own'] ? '<span style="margin-left:10px"><a href="'.$root_path.'?mod=containers&sw=download&doc=invoice2&id='.$id.'" class="delete">'.$translate->_('invoice for container').'</a></span>' : '').'
                <span style="margin-left:10px"><a href="'.$root_path.'?mod=containers&sw=download&doc=customer&id='.$id.'" class="delete">'.$translate->_('Limco Form').'</a></span>
                <span style="margin-left:10px"><a href="'.$root_path.'?mod=containers&sw=download&doc=shipping&id='.$id.'" class="delete">'.$translate->_('Westcoast Shipping').'</a></span>' : '').'
                </td>
		<td width="214" align="right" class="title">'.($_SESSION['user_type'] == 13 ? '' : '<input type="submit" name="Submit" value="'.$translate->_('Сохранить').'" id="save" tabindex="15" />').'</td>
	  </tr>
	</table>
	</div>
	</form>
	'.($_SESSION['user_type'] == 13 ? '' : $attached_files);
	return $out;
}

function destinationCountry($id) {
	$translate = Zend_Registry::get('translation');
	
	$list = array(
	'1'=>'Кыргызстан',
	'2'=>'Казахстан'
	);
	$out = '<select name="country">
	<option value="0"';
	if($id == '0') $out.= ' selected="selected"';
	$out.='> '.$translate->_('- не выбрана -').' </option>';
	foreach($list as $k=>$v)
	{
		$out .= '
		<option value="'.$k.'"';
		if($id==$k) $out .= ' selected="selected"';
		$out .= '>'.$v.'</option>';
	}
	$out .= '</select>';
	return $out;
}

function listPlaces($id) {
	$translate = Zend_Registry::get('translation');
	$list = mysql_query("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."places` ORDER BY `name` ASC");
	$out = '<select name="station">
	<option value="0"';
	if($id == '0') $out.= ' selected="selected"';
	$out.='> '.$translate->_('- не выбрана -').' </option>';
	while($line = mysql_fetch_array($list))
	{
		$out .= '
		<option value="'.$line['id'].'"';
		if($id==$line['id']) $out .= ' selected="selected"';
		$out .= '>'.$line['name'].'</option>';
	}
	$out .= '</select>';
	return $out;
}

function toRail($id) {
	$translate = Zend_Registry::get('translation');
	$list = mysql_query("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."reload_ports` ORDER BY `name` ASC");
	$out = '<select name="torail">
	<option value="0"';
	if($id == '0') $out.= ' selected="selected"';
	$out.='> '.$translate->_('- не выбран -').' </option>';
	while($line=mysql_fetch_array($list))
	{
		$out .= '
		<option value="'.$line['id'].'"';
		if($id==$line['id']) $out .= ' selected="selected"';
		$out .= '>'.$line['name'].'</option>';
	}
	$out .= '</select>';
	return $out;
}

function getFileList($files) {
	$translate = Zend_Registry::get('translation');
	$out = '';
	require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');
	while($line = mysql_fetch_array($files)) {

		$ico = fileIco($line['file']);

		$out .= Proto::wrapFile('<a href="/photos/containers/'.ACCOUNT_SUFFIX.$line['container'].'/'.$line['file'].'" target="_blank"><img src="/img/ico/'.$ico.'" border="0"></a><br>
		<b>'.substr($line['filename'], 0, 40).'</b><br>
		<a href="/?mod=containers&sw=delete&what=file&name='.$line['file'].'" class="delete" onclick="return confirm(\''.$translate->_('Вы действительно хотите удалить этот файл?').'\')">'.$translate->_('удалить').'</a>');
	}
	return $out;
}


?>