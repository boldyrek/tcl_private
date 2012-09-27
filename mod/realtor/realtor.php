<?php

class Realtor extends Proto {

   public function grid()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->_drawGrid();
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   protected function _drawGrid()
   {
      $this->page .= '<style>
         .group {font-weight:bold; border-bottom:1px dotted #000; color:#000}
         .rows td {padding-left:10px}
      </style>';
      $this->page .= '<div class="location">Realtor</div>';

      $this->page .= '<div id="list">';
      $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
      $this->page .= '<tr class="title sortButtons">';
      $this->page .= $this->sorterTD('realtor', 'address', 'Адрес');
      $this->page .= $this->sorterTD('realtor', 'price', 'Цена');
      $this->page .= $this->sorterTD('realtor', 'square', 'Общая площадь');
      $this->page .= $this->sorterTD('realtor', 'per_sm', 'Цена за кв. м.');
      $this->page .= $this->sorterTD('realtor', 'year_completed', 'Год постройки здания');
      $this->page .= '<td>Ссылка на здание</td>';
      $this->page .= $this->sorterTD('realtor', 'date_added', 'Добавлен');
      $this->page .= '</tr>';

      $sql = 'SELECT COUNT(DISTINCT(address)) FROM `ccl_tobuilt_apartment` GROUP BY `address`';
      
      $total = mysql_num_rows(mysql_query($sql));
      
      $pages = array();

      if ($total > $this->per_page)
      {
         $qs = http_build_query(array
         (
            'action' => 'grid',
            'sortby' => $_GET['sortby'],
            'order' => $_GET['order'],
         ));

         $pages = $this->pageBrowse(mysql_real_escape_string($_GET['page']), 'realtor', $total, '&'.$qs);
      }

      $sql = 'SELECT a.id, a.address, COUNT(*) AS `total`, CEILING(price/square) AS `per_sm`, h.year_completed, h.id AS `house_id`
         FROM `ccl_tobuilt_apartment` AS a
         LEFT JOIN `ccl_tobuilt_house` AS h ON (a.house_id = h.id)
         GROUP BY `address`
         ORDER BY `'.(empty($_GET['sortby']) ? 'per_sm' : $_GET['sortby']).'` '.(empty($_GET['order']) ? 'ASC' : $_GET['order']).' '.$pages['qlimit'];

      $query = mysql_query($sql);

      $class = 'rowA rowB';

      while ($row = mysql_fetch_object($query))
      {
         $this->page .= '
            <tr id="row-'.$row->id.'" c="'.$class.'" class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
               <td class="sm"><span class="group">'.$row->address.'</span> ('.$row->total.')</td>
               <td class="sm" align="center" colspan="3">&nbsp;</td>
               <td class="sm" align="center">'.($row->year_completed ? $row->year_completed : '-').'</td>
               <td class="sm" align="center">'.($row->house_id ? '<a href="/?mod=tobuilt&action=view&id='.$row->house_id.'" target="_blank"><img src="/img/link.png" alt="" /></a>' : '-').'</td>
               <td class="sm" align="center">&nbsp;</td>
            </tr>
         ';

         $class = ($class == 'rowA') ? 'rowA rowB' : 'rowA';
      }

      $this->page .= '</table><br />';

      $this->page .= ( isset($pages['print'])) ? $pages['print'] : '<br />';

      $this->page .= '<script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>';

      $this->page .= '<script type="text/javascript">
         $("span.group").toggle(function(){
            var rowId = $(this).parents("tr").attr("id");
            $.get("/", {mod:"realtor", action:"rows", address:$(this).text(), c:$(this).parents("tr").attr("c")+" "+rowId}, function(response){
               $("tr#"+rowId).after(response);
            });
         }, function(){
            $("tr."+$(this).parents("tr").attr("id")).remove();
         });
      </script>';
   }

   public function sorterTD($module, $item, $caption)
   {
      $order = ($_GET['order'] == '' OR $_GET['order'] == 'ASC') ? 'DESC' : 'ASC';

      $qs = http_build_query(array
      (
         'mod' => $module,
         'action' => 'grid',
         'sortby' => $item,
         'order' => $order,
         'page' => (empty($_GET['page']) ? 1 : (int) $_GET['page'])
      ));

      return '<td onMouseOver="this.className=\'sortButtonsHover\'" onMouseOut="this.className=\'\'" onclick="document.location=\'/?'.$qs.'\'">'.$caption.($_GET['sortby'] == $item ? $this->sortImg(strtolower($order)) : '').'</td>';
   }

   protected function sortImg($order)
   {
      return '&nbsp;<img src="'.$this->root_path.'img/ccl/'.$order.'.gif" style="margin-top:2px; margin-left:2px;">';
   }

   public function rows()
   {
      $sql = 'SELECT *, CEILING(price/square) AS `per_sm`
         FROM `ccl_tobuilt_apartment`
         WHERE `address` = "'.mysql_real_escape_string($_GET['address']).'"
         ORDER BY `per_sm` ASC';

      $query = mysql_query($sql);
      
      $rows = '';

      while ($row = mysql_fetch_object($query))
      {
         $rows .= '
            <tr class="'.$_GET['c'].' rows">
               <td class="sm"><a target="_blank" href="http://www.realtor.ca/propertyDetails.aspx?propertyId='.$row->property_id.'&PidKey='.$row->pid_key.'">'.$row->full_address.'</a></td>
               <td class="sm" align="center">'.$row->price.'</td>
               <td class="sm" align="center">'.$row->square.'</td>
               <td class="sm" align="center">'.$row->per_sm.'</td>
               <td class="sm" align="center" colspan="2">&nbsp;</td>
               <td class="sm">'.date('d-m-Y H:i', strtotime($row->date_added)).'</td>
            </tr>';
      }

      echo $rows;

      exit;
   }

}