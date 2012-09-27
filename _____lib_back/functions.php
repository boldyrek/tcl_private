<?

//���������� ������ ������
function buildList($request,$cols,$list,$module,$item,$limit)
{
	//����������
	if($cols[1]['name']=='date' or $cols[1]['name']=='save_date') $sort_dir = 'DESC';
	else $sort_dir = 'ASC';
	$order_list = defineSort('sort_'.$list, '`'.$cols[1]['name'].'` '.$sort_dir); //��������� ���������� � ������
	$sortNow = sortDeco('/', 'sort_'.$list); //������� ��������� ����, ��� ������ ����������� � ����������� ����������
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

//������������ �������
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

//���������� ����������� ������
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

//���������� ����������� ������ �� �������
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

//���������� �������
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

// ������� �������� ������ � ��������
function ruslat ($string) 
{
	$string = ereg_replace("�","zh",$string);
	$string = ereg_replace("�","yo",$string);
	$string = ereg_replace("�","i",$string);
	$string = ereg_replace("�","yu",$string);
	$string = ereg_replace("�","",$string);
	$string = ereg_replace("�","ch",$string);
	$string = ereg_replace("�","sh",$string);
	$string = ereg_replace("�","c",$string);
	$string = ereg_replace("�","u",$string);
	$string = ereg_replace("�","k",$string);
	$string = ereg_replace("�","e",$string);
	$string = ereg_replace("�","n",$string);
	$string = ereg_replace("�","g",$string);
	$string = ereg_replace("�","sh",$string);
	$string = ereg_replace("�","z",$string);
	$string = ereg_replace("�","h",$string);
	$string = ereg_replace("�","",$string);
	$string = ereg_replace("�","f",$string);
	$string = ereg_replace("�","y",$string);
	$string = ereg_replace("�","v",$string);
	$string = ereg_replace("�","a",$string);
	$string = ereg_replace("�","p",$string);
	$string = ereg_replace("�","r",$string);
	$string = ereg_replace("�","o",$string);
	$string = ereg_replace("�","l",$string);
	$string = ereg_replace("�","d",$string);
	$string = ereg_replace("�","y�",$string);
	$string = ereg_replace("�","j�",$string);
	$string = ereg_replace("�","s",$string);
	$string = ereg_replace("�","m",$string);
	$string = ereg_replace("�","i",$string);
	$string = ereg_replace("�","t",$string);
	$string = ereg_replace("�","b",$string);
	return $string;
}
?>