<?

// форма редактирования контейнера

function makeSlotList($content, $n) {

    static $cars = false, $loaded = array();


    if (false === $cars){
        $sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `delivered` = '0' AND `container`!='0'";
        $query = mysql_query($sql);
        while ($tmp=mysql_fetch_assoc($query)){
            $cars[] = $tmp;
        }
    }

    $out = '<option value="0">---</option>';

    foreach ($cars as $num=>$arr){

        $selected = false;

        if (in_array($arr['id'], $loaded)){
            continue;
        }

        if ($arr['id']==$content['slot'.$n]){
            $selected = true;
            $loaded[] = $arr['id'];
        }

        $out.='<option value="'.$arr['id'].'" '.(($selected)?'selected="selected"':'').'>'.substr($arr['frame'],0,3).'..'.substr($arr['frame'],(strlen($arr['frame'])-4),4).' '.$arr['model'].'</option>';
    }

    return $out;
}

//##########################
//
//форма работы с контейнером
//
//##########################
function containerForm($mode, $content) {

    global $root_path;
    $translate = Zend_Registry::get('translation');


    $slot1 = '<select name="slot1" tabindex="6" class="monoSelect">'.makeSlotList($content, 1).'</select>';
    $slot2 = '<select name="slot2" tabindex="7" class="monoSelect">'.makeSlotList($content, 2).'</select>';
    $slot3 = '<select name="slot3" tabindex="8" class="monoSelect">'.makeSlotList($content, 3).'</select>';
    $slot4 = '<select name="slot4" tabindex="9" class="monoSelect">'.makeSlotList($content, 4).'</select>';
    $slot5 = '<select name="slot5" tabindex="10" class="monoSelect">'.makeSlotList($content, 5).'</select>';




    // станция назначения
    $station = listPlaces($content['station']);

    $out = '<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
    <script src="'.$root_path.'js/datepicker.js"></script>

	<form class="myForm" id="myForm" action="'.($_SESSION['user_type'] == 13 ? '' : $root_path.$mode).'" method="post">
	<div class="cont" style="width:704px;"><h3>'.$translate->_('Вагон').'</h3>
	<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
	  <tr>
		<td width="110" align="right" class="rowA title">'.$translate->_('номер').'</td>
		<td width="200" class="rowA title"><input type="text" name="number" value="'.$content['number'].'" tabindex="1" /></td>
		<td class="rowA title" width="5">&nbsp;</td>
		<td width="50" class="rowA title">&nbsp;</td>
		<td align="center" class="rowA title">'.$translate->_('автомобили погруженные').'</td>
	  </tr>
	  <tr>
		<td align="right" class="rowB title"><b>'.$translate->_('Дата погрузки в вагон').'</b></td>
		<td class="rowB title"><input type="text" name="loaddate" id="loaddate" value="'.$content['loaddate'].'" tabindex="2" /></td>
		<td class="rowB title" align="left"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'loaddate\', \'\', myDateFormat);" class="datePicker"></td>
		<td width="120" align="right" class="title rowB">'.captionLink($content['slot1'], '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot1'], '№1').'</td>
		<td width="200" class="rowB title">'.$slot1.'</td>
	  </tr>
	  <tr>
		<td align="right" class="title rowB"><strong>'.$translate->_('ожидаемая дата<br>прибытия').'</strong></td>
		<td class="rowA rowB title"><input type="text" name="arrive_date" value="'.$content['arrive_date'].'" id="arrive_date" tabindex="3"></td>
		<td class="title rowB"><img src="'.$root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'arrive_date\', \'\', myDateFormat);" class="datePicker"></td>
		<td class="title rowB" align="right">'.captionLink($content['slot1'], '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot2'], '№2').'</td>
		<td class="rowA rowB title">'.$slot2.'</td>
	  </tr>
	  
	  <tr>
		<td align="right" class="title rowB"><strong>'.$translate->_('Слежение').'</strong></td>
		<td class="rowA rowB title" rowspan="2"><textarea name="treking" tabindex="4" style="height:50px;">'.$content['treking'].'</textarea></td>
		<td class="title rowB"></td>
		<td class="title rowB" align="right">'.captionLink($content['slot3'], '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot3'], '№3').'</td>
		<td class="rowA rowB title">'.$slot3.'</td>
	  </tr>
	  
      <tr>
		<td align="right" class="title rowB"></td>
		<td class="title rowB"></td>
		<td class="title rowB" align="right">'.captionLink($content['slot4'], '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot4'], '№4').'</td>
		<td class="rowA rowB title">'.$slot4.'</td>
	  </tr>
	  
	  
      <tr>
		<td align="right" class="title rowB">'.$translate->_('Последнее обновление').'</td>
		<td class="rowA rowB title">'.$content['treking_date'].'</td>
		<td class="title rowB"></td>
		<td class="title rowB" align="right">'.captionLink($content['slot5'], '0', $root_path.'?mod=cars&sw=form&car_id='.$content['slot5'], '№5').'</td>
		<td class="rowA rowB title">'.$slot5.'</td>
	  </tr>
	  
	
	  
	 <tr>
		<td align="right" class="title rowB"><input type="checkbox" name="arrived" value="1" style="border:0px;" tabindex="5" id="arrived"'.($content['arrived'] == '1' ? ' checked="checked"' : '').' tabindex="7"></td>
		<td class="rowA rowB title"><label for="arrived" style="cursor:hand; cursor:pointer;">'.$translate->_('доставлен').'</label></td>
		<td class="title rowB"></td>
		<td class="title rowB" align="right">'.$translate->_('станция<br>назначения').'</td>
		<td class="title rowB">'.$station.'</td>
	  </tr>
	  	  
	  ';


    $out .= '<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list"  style="margin-top:3px;">
	  <tr>
		<td class="title"><b>
		';

    if (isset($content['id'])){
        $out.='<a href="'.$root_path.'?mod=carriages&sw=delete&id='.$content['id'].'" class="" onclick="return confirm(\''.$translate->_('Вы действительно хотите удалить этот вагон?').'\')">'.$translate->_('удалить').'</a></b>';
    }
    $out.='
		</td>
		<td width="214" align="right" class="title"><input type="submit" name="Submit" value="'.$translate->_('Сохранить').'" id="save" tabindex="12" /></td>
	  </tr>
	</table>
	</div>
	</form>';

    return $out;
}

function listPlaces($id) {
    $translate = Zend_Registry::get('translation');
    $list = mysql_query("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."places` ORDER BY `name` ASC");
    $out = '<select name="station" tabindex="11">
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

?>