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
            $this->page .= '<div class="location">'.$this->translate->_('Автомобили на продажу').'</div>
		<table width="980" border="0" cellspacing="0" cellpadding="0" class="list vlines">
	 	<tr class="title sortButtons">
		'.$this->sorterTD('sale','date',$this->translate->_('дата'), '70').'
		<td width="160">фото</td>
		'.$this->sorterTD('sale','model',$this->translate->_('модель'),'300').'
		'.$this->sorterTD('sale','year',$this->translate->_('год выпуска'),'60').'
		'.$this->sorterTD('sale','auc_list',$this->translate->_('аукц. лист'),'60').'
		'.$this->sorterTD('sale','price',$this->translate->_('цена'),'50');
		
            if ($this->_isDealer()){
		        $this->page.=$this->sorterTD('sale','dealer_price',$this->translate->_('цена для дилера'),'50');
		
            }
		
	        $this->page.='</tr>';

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
            $is_admin = $_SESSION['user_type'] == '1' ? true : false;
            foreach ($cars as $k => $v)	{
                if($v['auc_list']!='') $auc_list = '<a href="/photos/'.ACCOUNT_SUFFIX.$v['car'].'/'.$v['auc_list'].'"><img src="/img/ccl/auc_list.gif" border="0" vspace="5"></a>';
                else $auc_list = '<br><br>';
                if($photos[$v['car']]!='') $photo = '<a href="/photos/'.ACCOUNT_SUFFIX.$v['car'].'/'.$photos[$v['car']].'" target="_blank"><img src="/photos/'.ACCOUNT_SUFFIX.$v['car'].'/thumb/'.$photos[$v['car']].'" border="0"></a>';
                else $photo = '&nbsp;';

                // Display links to cars for admins only
                if($is_admin) $model_link = '<td><b><a href="/?mod=cars&sw=form&car_id='.$v['car'].'">'.$v['model'].'</a></b>&nbsp;<br>';
                else $model_link = '<td><b>'.$v['model'].'</b>&nbsp;<br>';
                $this->page .= '
		<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" >
			<td>'.$v['date'].'</td>
			<td align="center">'.$photo.'</td>
			'.$model_link
                .nl2br($v['comment']).'</td>
			<td>'.$v['year'].'&nbsp;</td>
			<td align="center">'.$auc_list.'&nbsp;</td>
			<td><b>'.$v['price'].'</b>&nbsp;</td>';
                
                if ($this->_isDealer()){
                    $this->page.='<td><b>'.$v['dealer_price'].'</b>&nbsp;</td>';        
                }
			
		$this->page.='</tr>';
                if ($class=="rowA") $class="rowA rowB"; else $class="rowA";

            }

            $this->page .= '</table>
	'.$pages['print'];

        }
        else $this->page .= '<div style="background-color:#fff; width:930px; text-align:center; color:#22aa22;"><br>
	<b>'.$this->translate->_('нет ни одного автомобиля').'</b>
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

    protected function _isDealer(){
        static $isDealer = null;        
        
        if (!is_null($isDealer)){
            return $isDealer;    
        }
        
        $sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."customers` WHERE `id`='".intval($_SESSION['user_id'])."'";
        $res = $this->mysqlQuery($sql);
        $tmp = mysql_fetch_assoc($res);
        
        $isDealer = $tmp['dealer'];
        
        return $isDealer;
        
    }

}
?>