<?
require_once ($include_path.'lib/functions.php');
class dataList {

	var $per_page;
	var $root_path;
	var $total_items = ''; //общее число записей в списке
	var $request = ''; //запрос в базу
	var $cols = array(); //столбцы списка
	var $module = ''; // название списка, открывается форма редактирования
	var $item = ''; // элемент списка, для обозначения единицы
	var $list_name = ''; // название списка
	var $limit = ''; // ограничитель выборки
	var $page = '';
	var $def_sort = '';// поле для сортировки по умолчанию
	var $def_sort_dir = ''; //направление сортировки по умолчанию
	var $sort_dir = ''; // текущее направление сортировки выбранного поля

	function buildList()
	{
		//сортировка
		if($this->def_sort!='') $this->sort_dir = $this->def_sort_dir;
		else $this->sort_dir = 'ASC';
		
		$page_navigation = $this->pageBrowse();
		
		$order_list = $this->defineSort(); //добавляем сортировку в запрос
		$sortNow = $this->sortDeco(); //выводим указатель того, что сейчас сортируется и направление сортировки

		$this->request = $this->request." ORDER BY ".$order_list.$this->limit;
		
		$content = mysql_query($this->request);
			
		$num = mysql_num_rows($content);
		$i=1; 
		$class="rowA rowB";
		$out.='<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
			<tr class="title sortButtons">';
		
		while($i<=count($this->cols))
		{
			$out.='<td width="'.$this->cols[$i]['width'].'" onclick="document.location=\''.$root_path.'?mod='.$this->list_name.'&sort='.$this->cols[$i]['name'].'&sdir='.$sortNow['resort'][$this->cols[$i]['name']].'\'" onMouseOver="this.className=\'sortButtonsHover\'" onMouseOut="this.className=\'\'">'.$this->cols[$i]['caption'].' '.$sortNow['image'][$this->cols[$i]['name']].'</td>';
			 
			$i++;
		}
		$out.='</tr>';
		$i=1; 
		while ($i<=$num)
		{
			$line = mysql_fetch_array($content);
		$out .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" onclick="document.location=\''.$root_path.'?mod='.$this->module.'&sw=form&'.$this->item.'='.$line['id'].'\'">';
			$j=1;
			while($j<=count($this->cols))
			{
				$out.='<td>'.cleanContent($line[$this->cols[$j]['name']]).'&nbsp;</td>';
				$j++;
			}
			$out .= '</tr>';
			$i++;
			if ($class=="rowA") $class="rowA rowB"; 
			else $class="rowA";
		}
		$out .= '</table>';
		
		if($this->total_items['total']>$this->per_page)
		{
			$out .= $page_navigation;
		}
		
		return $out;
		
	}

	//постраничный переход
	function pageBrowse() {
		
		$pp = $this->per_page;
		$pg = $this->page;

		if($pg>1 and $pg!=0)
		{
			$start = ($pg*$pp)-$pp;
			$this->limit = " LIMIT ".$start.", ".$pp;
			$next_page = $pg+1;
			$prev_page = $pg-1;
		}

		else {
			$this->limit = " LIMIT 0, ".$pp;
			$next_page = 2;
			$pg = '1';
		}
		$out = '<div class="pages">';
		
		if($this->module!='') $location = 'mod='.$this->module.'&';
		
		if($next_page > 2) $out .= '<a href="?'.$location.'page='.$prev_page.'">&laquo;&laquo;</a> ';
		$out .= '[ '.$pg.' ]';
		;
		if(($pg*$pp) < $this->total_items['total']) $out .= ' <a href="?'.$location.'page='.$next_page.'">&raquo;&raquo;</a>';
		$out .= '</div>';
		return $out;
	}

	//сортировка списков
	function defineSort() {
	
		if(isset($_GET['sort']) and isset($_GET['sdir'])) {
			if($_GET['sdir']=='up') $sdir = 'ASC';
			elseif($_GET['sdir']=='down' or $_GET['sdir']=='') $sdir = 'DESC';
			else break;
			$_SESSION['sort_'.$this->list_name] = mysql_real_escape_string($_GET['sort']);
			$_SESSION['sort_'.$this->list_name.'_dir'] = $sdir;
		}
		
		if(isset($_SESSION['sort_'.$this->list_name])) $order = $_SESSION['sort_'.$this->list_name].' '.$_SESSION['sort_'.$this->list_name.'_dir'];
		else $order = '`'.$this->def_sort.'` '.$this->sort_dir;
		
		return $order;
	}

	// указатели направления сортировки
	function sortDeco() {
		
		$sname = $_SESSION['sort_'.$this->list_name];
		$sname_dir = $_SESSION['sort_'.$this->list_name.'_dir'];
	
		if($sname_dir == 'ASC') { 
			$sdir = 'asc';
			$out['resort'][$sname] = 'down';
		}
		elseif($sname_dir == 'DESC') {
			$sdir = 'desc';
			$out['resort'][$sname] = 'up';
		}
		if($sname_dir!='') $out['image'][$sname] = '<img src="'.$this->root_path.'img/ccl/'.$sdir.'.gif" style="position:absolute;margin-top:2px; margin-left:2px;">';
		else $out['image'][$sname] = '';
		
		return $out;
	}

}

?>