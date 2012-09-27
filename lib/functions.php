<?
//построение списка данных
function buildList($request,$cols,$list,$module,$item,$limit)
{
	//сортировка
	if($cols[1]['name']=='date' or $cols[1]['name']=='save_date') $sort_dir = 'DESC';
	else $sort_dir = 'ASC';
	$order_list = defineSort('sort_'.$list, '`'.$cols[1]['name'].'` '.$sort_dir); //добавляем сортировку в запрос
	$sortNow = sortDeco('/', 'sort_'.$list); //выводим указатель того, что сейчас сортируется и направление сортировки
	$request = $request." ORDER BY ".$order_list.$limit;

	$content = mysql_query($request);

	$num = mysql_num_rows($content);
	$i=1;
	$class="rowA rowB";
	$out.='<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
		<tr class="title sortButtons">';
	while($i<=count($cols))
	{
		$out.='
		<td width="'.$cols[$i]['width'].'" onclick="document.location=\''.$root_path.'?mod='.$list.'&sort='.$cols[$i]['name'].'&sdir='.$sortNow['resort'][$cols[$i]['name']].'\'" onMouseOver="this.className=\'sortButtonsHover\'" onMouseOut="this.className=\'\'">'.$cols[$i]['caption'].' '.$sortNow['image'][$cols[$i]['name']].'</td>';

		$i++;
	}
	$out.='</tr>';
	$i=1;
	while ($i<=$num)
	{
		$line = mysql_fetch_array($content);
		if((!isset($_GET['mod']) or $_GET['mod']=='clients') and ($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7')) {
			$onclick_location = 'document.location=\''.$root_path.'?mod='.$list.'&sw=detail&id='.$line['id'].'\'';
		}
		else $onclick_location = 'document.location=\''.$root_path.'?mod='.$list.'&sw=form&'.$item.'='.$line['id'].'\'';

		$out .= '
	<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" onclick="'.$onclick_location.'">';
		$j=1;
		while($j<=count($cols))
		{
			$out.='
			<td>'.cleanContent($line[$cols[$j]['name']]).'&nbsp;</td>';
			$j++;
		}
		$out .= '
		</tr>';
		$i++;
		if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
	}
	$out .= '</table>';

	return $out;
}

//постраничный переход
function pageBrowse($page, $module, $total) {
	$per_page = Proto::$per_page;
	if($page>1 and $page!=0)
	{
		$start = ($page*$per_page)-$per_page;
		$out['qlimit'] = " LIMIT ".$start.", ".$per_page;
		$out['next_page'] = $page+1;
		$out['prev_page'] = $page-1;
	}

	else {
		$out['qlimit'] = " LIMIT 0, ".$per_page;
		$out['next_page'] = 2;
		$page = '1';
	}
	$out['print'] = '<div class="pages">';

	if($module!='') $location = 'mod='.$module.'&';

	if($out['next_page'] > 2) $out['print'] .= '<a href="?'.$location.'page='.$out['prev_page'].'">&laquo;&laquo;</a> ';
	$out['print'] .= '[ '.$page.' ]';

	if($page*$per_page < $total) $out['print'] .= ' <a href="?'.$location.'page='.$out['next_page'].'">&raquo;&raquo;</a>';
	$out['print'] .= '</div>';
	return $out;
}

//построение выпадающего списка
function buildSelect($table, $name, $condition, $empty, $tabindex) {
	$num = mysql_num_rows($table);
	$i=1;
	if($num>0) {
		$out = '<select name="'.$name.'" tabindex="'.$tabindex.'" id="List'.$name.'">
		<option value="0"';
		if($condition==0) $out .= ' selected="selected"';
		$out .= '>'.$empty.'</option>';
		while($i<=$num) {
			$line = mysql_fetch_array($table);
			$out .= '
			<option value="'.$line['id'].'"';
			if($condition==$line['id']) $out .= ' selected="selected"';
			$out .= '>'.stripslashes($line['name']).'</option>';
			$i++;
		}
		$out .= '</select>';
		mysql_data_seek($table, 0);
	}
	else $out = '';
	return $out;
}

//построение выпадающего списка из массива
function buildSelectArray($array, $name, $condition, $empty, $tabindex) {
	// 	$num = sizeof($array);
	//  	$i=1;
	// 	if($num>0) {
	$out = '<select name="'.$name.'" tabindex="'.$tabindex.'" id="List'.$name.'">
		<option value="0"';
	if($condition==0) $out .= ' selected="selected"';
	$out .= '>'.$empty.'</option>';
	// 		while($i<=$num) {
	foreach($array as $k=>$v) {
		// 			$line = mysql_fetch_array($table);
		$out .= '
			<option value="'.$k.'"';
		if($condition==$k) $out .= ' selected="selected"';
		$out .= '>'.$v.'</option>';
		// 			$i++;
	}
	$out .= '</select>';
	// 		mysql_data_seek($table, 0);
	// 	} else $out = '';
	return $out;
}

//сортировка списков
function defineSort($list, $default) {

	if(isset($_GET['sort']) and isset($_GET['sdir'])) {
		if($_GET['sdir']=='up') $sdir = 'ASC';
		elseif($_GET['sdir']=='down' or $_GET['sdir']=='') $sdir = 'DESC';
		else break;
		$_SESSION[$list] = mysql_real_escape_string($_GET['sort']);
		$_SESSION[$list.'_dir'] = $sdir;
	}

	if(isset($_SESSION[$list])) $order = $_SESSION[$list].' '.$_SESSION[$list.'_dir'];
	else $order = $default;

	return $order;
}

function sortDeco($root_path, $list) {

	if($_SESSION[$list.'_dir'] == 'ASC') {
		$sdir = 'asc';
		$out['resort'][$_SESSION[$list]] = 'down';
	}
	elseif($_SESSION[$list.'_dir'] == 'DESC') {
		$sdir = 'desc';
		$out['resort'][$_SESSION[$list]] = 'up';
	}
	$out['image'][$_SESSION[$list]] = '<img src="'.$root_path.'img/ccl/'.$sdir.'.gif" style="position:absolute;margin-top:2px; margin-left:2px;">';

	return $out;
}

function captionLink($item, $value, $link, $caption) {
	if($item!=$value) return '<a href="'.$link.'"><img src="img/ccl/more_info.gif" border="0" align="absmiddle" hspace="3">'.$caption.'</a>';
	else return $caption;
}

function cleanContent($subject) {
	return str_replace('"','&quot;',stripslashes($subject));
}

function sorterTD($module, $item, $caption, $width) {
	global $root_path;
	global $sortNow;
	if(isset($_GET['ref'])) $type = 'ref';
	elseif(isset($_GET['mod'])) $type = 'mod';
	return '<td width="'.$width.'" onclick="document.location=\''.$root_path.'?'.$type.'='.$module.'&sort='.$item.'&sdir='.$sortNow['resort'][$item].'\'" onMouseOver="this.className=\'sortButtonsHover\'" onMouseOut="this.className=\'\'" style="cursor:pointer;">'.$caption.' '.$sortNow['image'][$item].'</td>';

}

function calc_period($date_start, $date_finish) {
                $st = explode('-', date('d-m-Y-H-i-s', $date_start));
                $fin = explode('-', date('d-m-Y-H-i-s', $date_finish));

                if (($seconds = $fin[5] - $st[5]) < 0) {
                        $fin[4]--;
                        $seconds += 60;
                }

                if (($minutes = $fin[4] - $st[4]) < 0) {
                        $fin[3]--;
                        $minutes += 60;
                }

                if (($hours = $fin[3] - $st[3]) < 0) {
                        $fin[0]--;
                        $hours += 24;
                }

                if (($days = $fin[0] - $st[0]) < 0) {
                        $fin[1]--;
                        $days = date('t', mktime(1, 0, 0, $st[1], $st[0], $st[2])) - $st[0] + $fin[0];
                }

                if (($months = $fin[1] - $st[1]) < 0) {
                        $fin[2]--;
                        $months += 12;
                }

                $years = $fin[2] - $st[2];
                
                return array($seconds, $minutes, $hours, $days, $months, $years);
}

function Start_Timer() {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	return $mtime;
}

function End_Timer($start_time) {
	$mtime = microtime();
	$mtime = explode(" ",$mtime);
	$mtime = $mtime[1] + $mtime[0];
	$totaltime = $mtime - $start_time;
	return $totaltime;
}

function cleanFilename($str)
{
	$str = preg_replace("/[^а-яa-z0-9-\.]/", "_", strtolower($str));
	return $str;
//	$replace='';
//	$pattern="/([[:alnum:]_\.- ]*)/i";
//	return str_replace(str_split(preg_replace($pattern,$replace,$str)),$replace,$str);
}

function clean_txt($txt)
{
	return addslashes(htmlspecialchars($txt));
}
function clean_area($txt)
{
	return nl2br(addslashes(htmlspecialchars($txt)));
}
function br2nl($txt)
{
	$txt=str_replace("<br />", '', $txt);
	$txt=str_replace("<br>", '', $txt);
	$txt=str_replace("<BR>", '', $txt);
	$txt=str_replace("<BR />", '', $txt);
	return $txt;
}

function fileIco($name) {
	$length = strlen($name);
	if($length>4) {
		$typo = substr($name, $length-3, 3);
		switch($typo) {
			case 'doc': $ico = 'word.gif'; break;
			case 'xls': $ico = 'excel.gif'; break;
			case 'pdf': $ico = 'pdf.gif'; break;
			default: $ico = 'any.gif'; break;
		}
	}
	else $ico = '';
	return $ico;

}

// перевод русского текста в транслит
function ruslat ($string)
{
	$string = ereg_replace("ж","zh",$string);
	$string = ereg_replace("ё","yo",$string);
	$string = ereg_replace("й","i",$string);
	$string = ereg_replace("ю","yu",$string);
	$string = ereg_replace("ь","",$string);
	$string = ereg_replace("ч","ch",$string);
	$string = ereg_replace("щ","sh",$string);
	$string = ereg_replace("ц","c",$string);
	$string = ereg_replace("у","u",$string);
	$string = ereg_replace("к","k",$string);
	$string = ereg_replace("е","e",$string);
	$string = ereg_replace("н","n",$string);
	$string = ereg_replace("г","g",$string);
	$string = ereg_replace("ш","sh",$string);
	$string = ereg_replace("з","z",$string);
	$string = ereg_replace("х","h",$string);
	$string = ereg_replace("ъ","",$string);
	$string = ereg_replace("ф","f",$string);
	$string = ereg_replace("ы","y",$string);
	$string = ereg_replace("в","v",$string);
	$string = ereg_replace("а","a",$string);
	$string = ereg_replace("п","p",$string);
	$string = ereg_replace("р","r",$string);
	$string = ereg_replace("о","o",$string);
	$string = ereg_replace("л","l",$string);
	$string = ereg_replace("д","d",$string);
	$string = ereg_replace("э","yе",$string);
	$string = ereg_replace("я","jа",$string);
	$string = ereg_replace("с","s",$string);
	$string = ereg_replace("м","m",$string);
	$string = ereg_replace("и","i",$string);
	$string = ereg_replace("т","t",$string);
	$string = ereg_replace("б","b",$string);
	return $string;
}
?>