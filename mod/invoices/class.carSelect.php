<?
//
// используется для вывода информации об автомобилях в форме инвойса
//

class carSelect {
	
	var $carSQLlist;
	
	var $haveCars;
	
	function getCarInfo($id, $formID) {
		
		$car = mysql_fetch_array(mysql_query("SELECT ccl_".ACCOUNT_SUFFIX."cars.*, ccl_".ACCOUNT_SUFFIX."containers.number FROM `ccl_".ACCOUNT_SUFFIX."cars` LEFT JOIN `ccl_".ACCOUNT_SUFFIX."containers`
	ON (ccl_".ACCOUNT_SUFFIX."containers.id = ccl_".ACCOUNT_SUFFIX."cars.container) WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".intval($id)."'"));
		if($car['id']!='') $out = '
		<table class="list" border="0" width="96%" cellspacing="0" style="margin-bottom:5px;margin-top:-5px;">
		<tr>
		<td align="right" class="rowB title" width="150">Объем двигателя</td>
		<td class="rowB title" width="150">'.$car['engine'].'</td>
		<td align="right" class="rowB title">Вес</td>
		<td class="rowB title" style="text-transform: uppercase;" width="150">'.$car['weight'].'</td>
		</tr>
		<tr>
		<td align="right" class="title">Вин код</td>
		<td class="title" style="text-transform: uppercase;">'.$car['frame'].'</td>
		<td align="right" class="title">Цена</td>
		<td class="title" style="text-transform: uppercase;">'.$car['invoice'].'</td>  			    
		</tr>
		<tr>
		<td align="right" class="rowB title">Контейнер</td>
		<td class="rowB title">'.$car['number'].'</td>
		<td align="right" class="rowB title">Тип топлива</td>
		<td class="rowB title"><input type="text" name="car['.$formID.'][fuel_type]" value="'.($car['fuel_type']==''?'G':$car['fuel_type']).'" maxlength="1" style="width:20px;"></td>
		</tr>		    
		<td align="right" class="title">Год выпуска</td>
		<td class="title">'.$car['year'].'</td>
		<td class="title" align="right">Посадочных мест</td>
		<td class="title"><input type="text" name="car['.$formID.'][seats]" value="'.($car['seats']=='0'?'5':$car['seats']).'" maxlength="2" style="width:20px;"></td>
		</tr>
		</table>';
	
		else $out = '<div class="rowA title" style="background-color:#fff; border:1px solid #ccc; padding:3px;text-align:center;width:96%;">не выбран автомобиль!</div>';
		return $out;
	}
	
	function makeCarSelect($table) {
		$num = count($table);
		$i=0;
		if($num>0) {
			$cars_list = '<select name="car['.$listID.'][id]" id="car_'.$listID.'" tabindex="1" style="font-family:monospace; width:250px;" onChange="getCarInfo('.$listID.', '.$current.');"><option value="0"';
			if($current==0) $cars_list .= ' selected="selected"';
			$cars_list .= '> не выбран </option>';
			while($i<$num) {
				$line = $table[$i];
				$cars_list .= '<option value="'.$line['id'].'"';
				if($current==$line['id']) $cars_list .= ' selected="selected"';
				$cars_list .= '>'.substr($line['frame'],0,3).'..'.substr($line['frame'],(strlen($line['frame'])-4),4).' - '.$line['model'].'</option>';
				$i++;
			}
			$cars_list .= '</select>';
		}
		return $cars_list;
	}

	function carListItem($id, $i) {
		
		$carInfo = $this->carIdentity($id);
	
		foreach ($this->haveCars as $k => $v) {
		if($v!='') {
			if($v == $id) { 
				$checked = ' checked="checked"';
				$highlight = 'rowHighlighted';
				break;
			}
			else {
				$checked = '';
				$highlight = 'rowPlain';
			}
		}
			
		}
		
		return '<div class="'.$highlight.'" id="tr'.$id.'" style="width:750px;">
				<table class="list" border="0" width="750" cellspacing="0" style="border-top:5px; background-color:transparent;">
					<tr>
						<td align="right" width="10" class="title">&nbsp;</td>
						<td align="right" width="20" class="title">
							<input type="checkbox" name="use['.$id.']"'.$checked.' onClick="switchHighlight(\'tr'.$id.'\');listenChanges();" id="chk'.$id.'" style="border:0px;"></td>
						<td style="text-transform: uppercase;" class="title">
							<label for="chk'.$id.'">'.$carInfo['model'].' - '.$carInfo['frame'].'</label>
							<input type="hidden" name="car['.$i.'][id]" value="'.$id.'"></td>
						<td width="50" class="title">
							<a href="/?mod=cars&sw=form&car_id='.$id.'"><img src="img/ccl/more_info.gif" border="0" align="absmiddle" hspace="3" alt="Открыть форму автомобиля" title="Открыть форму автомобиля"></a></td>
						<td width="200" class="title">
							(year:
							<input type="text" name="car['.$i.'][year]" value="'.$carInfo['year'].'" style="width:40px" onChange="checkField(\'carInfoCheck'.$i.'\')" maxlength="4"> /
							<input type="text" name="car['.$i.'][seats]" value="'.($carInfo['seats']=='0'?'5':$carInfo['seats']).'" maxlength="2" style="width:20px;" onChange="checkField(\'carInfoCheck'.$i.'\')"> P/ <input type="text" style="width:40px;" value="'.$carInfo['engine'].'" name="car['.$i.'][engine]" onChange="checkField(\'carInfoCheck'.$i.'\')"> /
							<input type="text" name="car['.$i.'][fuel_type]" value="'.($carInfo['fuel_type']==''?'G':$carInfo['fuel_type']).'" maxlength="1" style="width:20px;" onChange="checkField(\'carInfoCheck'.$i.'\')">
							)</td>
						<td width="70" class="title">
							<input type="text" name="car['.$i.'][weight]" value="'.$carInfo['weight'].'" style="width:40px" onChange="checkField(\'carInfoCheck'.$i.'\')"> KGS</td>
						<td width="60" class="title">
							<input type="text" name="car['.$i.'][volume]" value="'.$carInfo['volume'].'" style="width:40px" onChange="checkField(\'carInfoCheck'.$i.'\')"> M<sup>3</sup></td>
						<td class="title" style="text-transform: uppercase;" width="65">US$ <input type="text" name="car['.$i.'][invoice]" value="'.$carInfo['invoice'].'" style="width:40px;" onChange="checkField(\'carInfoCheck'.$i.'\')"></td>
					</tr>
					
					
				</table>
				<input type="checkbox" name="carInfoCheck['.$i.']" style="display:none;" id="carInfoCheck'.$i.'"></div>
				';
		
	}
	
	function carIdentity($id) {
		$car = mysql_fetch_array(mysql_query("SELECT ccl_".ACCOUNT_SUFFIX."cars.*, ccl_".ACCOUNT_SUFFIX."containers.number FROM `ccl_".ACCOUNT_SUFFIX."cars` LEFT JOIN `ccl_".ACCOUNT_SUFFIX."containers`
	ON (ccl_".ACCOUNT_SUFFIX."containers.id = ccl_".ACCOUNT_SUFFIX."cars.container) WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".mysql_real_escape_string($id)."'"));
		return $car;
	}
	
	function getContainerList($invoiceID, $current) {
		$list = mysql_query("SELECT id, number FROM `ccl_".ACCOUNT_SUFFIX."containers` WHERE `invoice`='0' or `invoice`='".$invoiceID."' ORDER BY number ASC");
		$num = mysql_num_rows($list);
		if($num>0) {
			$i = 0;
			$out = '<select name="container" onChange="listenChanges();">
			<option value="0"> - не выбран - </option>';
			while($i<$num) {
				$line = mysql_fetch_array($list);
				if($line['number']!='') {
				
					$out .= '
					<option value="'.$line['id'].'"';
					if($line['id']==$current) $out .= ' selected="selected"';
					$out .= '>'.$line['number'].'</option>';
				}
				$i++;
			}
			$out .= '</select>';
		}
		else $out = 'Нет ни одного контейнера';
		return $out;
	}
}
?>