<?

class carsForSale extends Proto {

var $table;
var $pages;
var $total_items = array();
var $order_list;
var $content;
var $request;
var $default_sort;
var $sort_link;

public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}

function getContent() {

	$this->order_list = $this->defineSort($this->table, $this->default_sort); //добавляем сортировку в запрос
	
	$this->sortDeco($this->table); //выводим указатель того, что сейчас сортируется и направление сортировки
	
	$total = mysql_fetch_array($this->mysqlQuery("SELECT COUNT(`id`) as total FROM `".$this->table."`"));
	if($total['total']>$this->per_page)
	{
		$pages = $this->pageBrowse(mysql_real_escape_string($_GET['page']), mysql_real_escape_string($_GET['mod']), $total['total']);
	}
	
	$this->content = $this->mysqlQuery($this->request.$this->order_list.$pages['qlimit']);;
	
	$num = @mysql_num_rows($this->content);
	
	if($num>0) {
	$this->page .= '<div class="location">Автомобили на продажу</div>
		<table width="930" border="0" cellspacing="0" cellpadding="0" class="list vlines">
	 	<tr class="title sortButtons">
		'.$this->sorterTD('sale','date','дата', '70').'
		<td width="160">фото</td>
		'.$this->sorterTD('sale','model','модель','350').'
		'.$this->sorterTD('sale','year','год выпуска','60').'
		'.$this->sorterTD('sale','auc_list','аукц. лист','60').'
		'.$this->sorterTD('sale','price','цена','50').'
	  </tr>';
	  
	$i=0; 
	$class="rowA";
	while ($i<$num)
	{
		$line = mysql_fetch_array($this->content);
		$cars[$i] = $line;
		$ids[$i] = $line['car'];
		$i++;	
	}
	$photos = $this->getPhotos($ids);
	foreach ($cars as $k => $v)	{
		if($v['auc_list']!='') $auc_list = '<a href="/photos/'.ACCOUNT_SUFFIX.$v['car'].'/'.$v['auc_list'].'"><img src="/img/ccl/auc_list.gif" border="0" vspace="5"></a>';
		else $auc_list = '<br><br>';
		if($photos[$v['car']]!='') $photo = '<a href="/photos/'.ACCOUNT_SUFFIX.$v['car'].'/'.$photos[$v['car']].'" target="_blank"><img src="/photos/'.ACCOUNT_SUFFIX.$v['car'].'/thumb/'.$photos[$v['car']].'" border="0"></a>';
		else $photo = '&nbsp;';
		$this->page .= '
		<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" >
			<td>'.$v['date'].'</td>
			<td align="center">'.$photo.'</td>
			<td><b>'.$v['model'].'</b>&nbsp;<br>
			'.nl2br($v['comment']).'</td>
			<td>'.$v['year'].'&nbsp;</td>
			<td align="center">'.$auc_list.'&nbsp;</td>
			<td><b>'.$v['price'].'</b>&nbsp;</td>
		</tr>';
		if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
				
	}
	
	$this->page .= '</table>
	'.$pages['print'];
	
	}
	else $this->page .= '<div style="background-color:#fff; width:930px; text-align:center; color:#22aa22;"><br>
	<b>нет ни одного автомобиля</b>
	<br><br></div>';
}

function getPhotos($target) {
	
	foreach ($target as $k => $v) {
		$where .= " or `car` = '".intval($v)."'";
	}
	
	$photos = $this->mysqlQuery("
	SELECT car,file 
	FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` 
	WHERE ".ltrim($where, ' or'));
	$num = mysql_num_rows($photos);
	if($num>0) {
		$i = 0;
		while($i<$num) {
			$line = mysql_fetch_array($photos);
			if($out[$line['car']]=='') $out[$line['car']] = $line['file'];
			$i++;
		}
	}
	return $out;
}

}
?>