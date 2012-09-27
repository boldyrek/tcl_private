<?

class CarriagesList extends Proto {

    public function drawContent() {
        $this->page .= $this->templates['header'];
        if($this->checkAuth()) {
            $this->page .= $this->makeTopMenu();
            $this->getContent();
            $this->page .= $this->module_content;
        }
        $this->page .= $this->templates['footer'];

        $this->errorsPublisher();
        $this->publish();
    }

    function getContent() {
        require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'lib/class.search.php');
        $search = new listSearch();

        //выборка
        $filter = '';
        if($_GET['show_arrived']=='1') $_SESSION['show_arrived_conts'] = '1';
        elseif($_GET['show_arrived']=='0') $_SESSION['show_arrived_conts'] = '0';

        if($_SESSION['show_arrived_conts'] == '1')
        {
            $arrived = ' checked="checked"';
            $path = "?mod=carriages&show_arrived=0";
            $show_arrived = "";
        }
        else {
            $arrived = '';
            $path = "?mod=carriages&show_arrived=1";
            $show_arrived = "`arrived` = '0'";
        }

        if(isset($_GET['filter']))
        {
            if(mysql_real_escape_string($_POST['searchNumber'])!='') {
                $search->insert('%'.mysql_real_escape_string($_POST['searchNumber']).'%', 'data');
                $search->insert("`number` LIKE", 'filter');
            }

        }
        $filter_made = $search->makeFilter();
        if($filter_made!='') {
            $filter_made = "WHERE ".$filter_made;
            if($filter!='') $filter_made.=' AND '.$filter;
        }
        else $filter_made = "";
        if($filter_made == '' and $filter!='') $filter_made = "WHERE ".$filter;

        //постраничный переход
        if($filter_made=='')
        {
            $sql = "SELECT COUNT(id) FROM `ccl_".ACCOUNT_SUFFIX."carriage`";
            $res = mysql_query($sql);
            $total = mysql_fetch_row($res);
            $total_items=$total[0];
            if($total_items>$this->per_page)
            {
                $pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items);
            }
        }
        if($filter_made=='' and $show_arrived!='') $filter_made = "WHERE ".$show_arrived;
        elseif($filter_made!='' and $show_arrived!='') $filter_made.=" AND ".$show_arrived;

        //сортировка
        $order_list = $this->defineSort('sort_cont', '`loaddate` DESC'); //добавляем сортировку в запрос
        $this->sortDeco('sort_cont'); //выводим указатель того, что сейчас сортируется и направление сортировки

        $request = "SELECT *
		FROM `ccl_".ACCOUNT_SUFFIX."carriage`
		 ".$filter_made." ORDER BY ".$order_list.$pages['qlimit'];


        $content = $this->mysqlQuery($request);

        $num = mysql_num_rows($content);
        $i=1;
        $class="rowA rowB";

        $this->page .= '
		<script>
			function checkBox(id) {
				var current = id;
				if(document.getElementById(current).checked == true) document.getElementById(current).checked = false;
				else document.getElementById(current).checked = true;
				checkSniffer(current);
			}
		</script>
		<form name="searchCarriages" method="POST" action="'.$this->root_path.'?mod=carriages&filter" class="smallForm">
                   ';

        $this->page .= '
                   <div class="location">
                     <table class="location_tab" width="100%" border="0" cellspacing="0" cellpadding="0">
                     <tr>
                        <td width="480">'.$this->translate->_('Вагоны').' | <a href="'.$this->root_path.'?mod=carriages&sw=form&add">'.$this->translate->_('добавить').'</a></td>
                        <td>&nbsp;</td>
                        <td width="150">'.$this->translate->_('искать по номеру:').'&nbsp;<br>
                           <input type="text" name="searchNumber" value="'.$_POST['searchNumber'].'"></td>
		<td width="210"></td>
		<td width="40"> <input type="submit" value="'.$this->translate->_('найти').'"></td>
		</tr></table></div>

		<div class="location">
		<table width="98%" cellspacing="0" cellpadding="0" style="font-size:12px"><tr>
		<td align="right">
		
		
		<input type="checkbox" name="show_arrived" id="arrived"'.$arrived.' onclick="document.location=\''.$this->root_path.$path.'\'" style="border:0px;"><label for="arrived" style="cursor:hand; cursor:pointer;">'.$this->translate->_('показывать прибывшие').'</label></td>
		</tr></table></div>';
        $this->page .= '</form>';

        $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
		 <tr class="title">
		    '.$this->sorterTD('containers', 'number', $this->translate->_('Номер вагона'), '90').'
		    '.$this->sorterTD('containers', 'loaddate', $this->translate->_('Дата погрузки'), '90').'
		    '.$this->sorterTD('containers', 'arrive_date', $this->translate->_('Ожидается'), '90').'
		    '.$this->sorterTD('containers', 'treking_date', $this->translate->_('Последнее слежение'), '90').'
			'.$this->sorterTD('containers', 'arrived', '&nbsp;', '30').'
		  </tr>';

        while ($line = mysql_fetch_array($content))
        {
            if($line['arrived']=='1') {
                $arrived='<img src="'.$this->root_path.'img/ccl/ok.gif">';
                $class = 'greenTR';
            }
            else $arrived = '~';

            $this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
			'.$this->clistTD($line['id'],$line['number']).
            $this->clistTD($line['id'],$line['loaddate']).
            $this->clistTD($line['id'],$line['arrive_date']).
            $this->clistTD($line['id'],$line['treking_date']).
            '<td align="center">'.$arrived.'</td>
			</tr>';
            $i++;
            if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
        }
        
        $this->page.='</table>'.$pages['print'];


    }

    function clistTD($id, $item) {
        return '
		<td onclick="document.location=\''.$this->root_path.'?mod=carriages&sw=form&cont_id='.$id.'\'">'.$item.'</td>';
    }


}

?>