<?php

class Tobuilt extends Proto {

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

   public function view()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->_drawView();
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function add()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();

         if ($_POST)
         {
            $values = self::map('mysql_real_escape_string', $_POST);

            if ($this->_value($values, 'google_map'))
            {
               $values['google_map'] = '<a href="http://maps.google.com/maps?q='.$values['google_map'].'" target="_blank"><img src="./images/GoogleMaps.jpg" border="0" /></a>';
            }

            $values['date_added'] = date('Y-m-d H:i:s');
            $values['added_manually'] = true;
            // 'house_id' 0 by default
            // 'new' true by default

            $sql = "INSERT INTO `ccl_tobuilt_house` (".implode(',', array_keys($values)).") VALUES (".implode(',', self::map('Tobuilt::quote', $values)).")";

            $this->mysqlQuery($sql);

            $id = mysql_insert_id();

            if (! empty($_FILES))
            {
               $dir = $_SERVER['DOCUMENT_ROOT'].'/tbcache/'.$id;
               
               if (@mkdir($dir, 0755, TRUE))
               {
                  @chmod($dir, 0755);

                  $pictures = array();

                  $allowed_types = array('image/jpeg', 'image/gif', 'image/png');

                  $shell_command = 'convert "%s" -resize 150 "%s"';
                  
                  foreach ($_FILES AS $key => $file)
                  {
                     if (in_array($file['type'], $allowed_types))
                     {
                        $filename = $key.'.'.pathinfo($file['name'], PATHINFO_EXTENSION);

                        $pictures[] = $filename;

                        $dest = $dir.'/'.$filename;

                        move_uploaded_file($file['tmp_name'], $dest);

                        shell_exec(sprintf($shell_command, $dest, $dir.'/thumb-'.$filename));
                     }
                  }

                  $this->mysqlQuery("UPDATE `ccl_tobuilt_house` SET `picture` = '{$pictures[0]}' WHERE `id` = $id");
               }
            }

            $this->redirect('/?mod=tobuilt');
         }

         $this->_form();
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   protected function _form($values = array())
   {
      $defaults = array('active' => 1);

      if (empty($values))
      {
         $values += $defaults;
      }

      $this->page .= '
         <style type="text/css">
           ul.files, ul.files li, ul.files li ul, ul.container, dl, dd {margin:0;padding:0}
           ul.files li {list-style:none}
           ul.files > li {margin-bottom:3px}
           ul.container li {display:inline;float:left;margin-right:5px}
           ul.container li img {cursor:pointer}
           .error {color:red}
           .clear {clear:both;font-size:0;line-height:0;height:0}
         </style>

         <script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>
         <script type="text/javascript" src="/js/jquery.validate.min.js"></script>

         <script type="text/javascript">
            $(function()
            {
               $("#my-form").validate({
                  rules:{
                     floors:{minlength:1,maxlength:3,digits:true},
                  }
               });
               
               $("#add-file").live("click", function()
               {
                  var lastId = $("ul#files li.item:last").attr("id").split("-")[1];
                  var nextId = parseInt(lastId)+1;
                  var content = "";

                  content += \'<li class="item" id="item-\'+nextId+\'">\';
                  content += \'<ul class="container">\';
                  content += \'<li><input type="file" name="picture-\'+nextId+\'" /></li>\';
                  content += \'<li><img src="/img/delete.png" alt="" class="delete-file" /></li>\';
                  content += \'</ul>\';
                  content += \'<div class="clear">\';
                  content += \'</li>\';

                  $(content).appendTo("ul#files");
               });

               $(".delete-file").live("click", function()
               {
                  var item = $(this).parents().get(2);
                  if ($(item).attr("id") != "item-0")
                  {
                     $(item).remove();
                  }
               });
            });
         </script>

         <div class="location"><a href="/?mod=tobuilt">Tobuilt</a> / Добавление/редактирование</div>
         <div class="cont_car" style="width:960px">
            <form class="myForm" id="my-form" method="post" action="" enctype="multipart/form-data">
               <table width="960" cellspacing="0" cellpadding="0" border="0" class="list">
                  <tr>
                     <td class="title rowA" align="right" width="30%">
                        <label for="name">Name</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="name" id="name" value="'.$this->_value($values, 'name').'" class="required" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="address">Address</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="address" id="address" value="'.$this->_value($values, 'address').'" class="required" />
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="location">Location</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="location" id="location" value="'.$this->_value($values, 'location').'" class="required" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="identification">Other Identification</label>:
                     </td>
                     <td class="title rowB">
                        <textarea name="identification" id="identification">'.$this->_value($values, 'location').'</textarea>
                     </td>
                     <td class="title rowB"></td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="notes">Notes</label>:
                     </td>
                     <td class="title rowA">
                        <textarea name="identification" id="identification">'.$this->_value($values, 'notes').'</textarea>
                     </td>
                     <td class="title rowA"></td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="quote">Quote</label>:
                     </td>
                     <td class="title rowB">
                        <textarea name="quote" id="quote">'.$this->_value($values, 'quote').'</textarea>
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="status">Status</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="status" id="status" value="'.$this->_value($values, 'status').'" class="required" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="year_completed">Year Completed</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="year_completed" id="year_completed" value="'.$this->_value($values, 'year_completed').'" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="companies">Companies</label>:
                     </td>
                     <td class="title rowB">
                        <textarea name="companies" id="companies">'.$this->_value($values, 'companies').'</textarea>
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="google_map">Query for Google Map</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="google_map" id="google_map" value="'.$this->_value($values, 'google_map').'" />
                     </td>
                     <td class="title rowA">
                        <small>for example: 250 12th Street+Toronto,ON, Canada</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="building_data">Height and Building Data</label>:
                     </td>
                     <td class="title rowB">
                        <textarea name="building_data" id="building_data">'.$this->_value($values, 'building_data').'</textarea>
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="floors">Floors</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="floors" id="floors" value="'.$this->_value($values, 'floors').'" class="required" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="height">Height (m)</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="height" id="height" value="'.$this->_value($values, 'height').'" />
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="type">Building type</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="type" id="type" value="'.$this->_value($values, 'type').'" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="general_use">General Use</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="general_use" id="general_use" value="'.$this->_value($values, 'general_use').'" />
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="specific_use">Specific Use</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="specific_use" id="specific_use" value="'.$this->_value($values, 'specific_uses').'" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="former_use">Former Use</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="former_use" id="former_use" value="'.$this->_value($values, 'former_use').'" />
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="heritage">Heritage</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="heritage" id="heritage" value="'.$this->_value($values, 'heritage').'" />
                     </td>
                     <td class="title rowA">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="main_style">Main Style</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="main_style" id="main_style" value="'.$this->_value($values, 'main_style').'" />
                     </td>
                     <td class="title rowB">
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="">Pictures</label>:
                     </td>
                     <td class="title rowA">
                        <ul class="files" id="files">
                           <li class="item" id="item-0">
                              <ul class="container">
                                 <li><input type="file" name="picture-0" /></li>
                                 <li><img src="/img/delete.png" alt="" class="delete-file" /></li>
                                 <li><img src="/img/add.png" alt="" id="add-file" /></li>
                              </ul>
                              <div class="clear">
                           </li>
                        </ul>
                     </td>
                     <td class="title rowA">
                        <small>Allowed images: JPG, JPEG, GIF, PNG</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB"></td>
                     <td class="title rowB">
                        <input type="submit" value="Сохранить" style="width:100px;font-weight:bold" />
                     </td>
                     <td class="title rowB"></td>
                  </tr>
               </table>
            </form>
         </div>
      ';
   }

   protected function _drawGrid()
   {
      $this->page .= '<div class="location">Tobuilt / <a href="/?mod=tobuilt&amp;action=add">добавить</a></div>';

      $this->page .= '<div id="list">';
      $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
      $this->page .= '<tr class="title sortButtons">';
      $this->page .= '<td>Picture</td>';
      $this->page .= $this->sorterTD('tobuilt', 'location', 'Building Name');
      $this->page .= $this->sorterTD('tobuilt', 'year_completed', 'Year');
      $this->page .= $this->sorterTD('tobuilt', 'status', 'Status');
      $this->page .= $this->sorterTD('tobuilt', 'floors', 'Floors');
      $this->page .= $this->sorterTD('tobuilt', 'height', 'Height');
      $this->page .= $this->sorterTD('tobuilt', 'type', 'Building Type');
      $this->page .= $this->sorterTD('tobuilt', 'general_use', 'General Use');
      $this->page .= $this->sorterTD('tobuilt', 'date_added', 'Date added');
      $this->page .= '</tr>';

      $query = mysql_query('SELECT COUNT(*) AS `total` FROM `ccl_tobuilt_house`');
      $row = mysql_fetch_array($query, MYSQL_ASSOC);
      $total = (int) $row['total'];
      $pages = array();

      if ($total > $this->per_page)
      {
         $qs = http_build_query(array
         (
            'action' => 'grid',
            'sortby' => $_GET['sortby'],
            'order' => $_GET['order'],
         ));

         $pages = $this->pageBrowse(mysql_real_escape_string((int) $_GET['page']), 'tobuilt', $total, '&'.$qs);
      }

      $sql = 'SELECT * FROM `ccl_tobuilt_house` ORDER BY `'.(empty($_GET['sortby']) ? 'id' : $_GET['sortby']).'` '.(empty($_GET['order']) ? 'DESC' : $_GET['order']).' '.$pages['qlimit'];

      $query = mysql_query($sql) OR die('Неверно указаны параметры запроса');

      $class = 'rowA rowB';

      while ($row = mysql_fetch_object($query))
      {
         $this->page .= '
            <tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
               <td class="sm" align="center">
                  <div style="height:150px; overflow:hidden; margin:5px">
                     <a href="/?mod=tobuilt&amp;action=view&amp;id='.$row->id.'">
                        <img src="/tbcache/'.$row->id.'/thumb-'.$row->picture.'" alt="" />
                     </a>
                  </div>
               </td>
               <td class="sm"><h2><a href="/?mod=tobuilt&amp;action=view&amp;id='.$row->id.'">'.$row->name.'</a></h2>'.$row->address.'<br />'.$row->location.($row->new ? '<img src="/img/new.png" alt="" style="margin-left:3px" />' : '').'</td>
               <td class="sm" align="center">'.$row->year_completed.'</td>
               <td class="sm" align="center">'.$row->status.'</td>
               <td class="sm" align="center">'.$row->floors.'</td>
               <td class="sm" align="center">'.$row->height.'</td>
               <td class="sm">'.$row->type.'</td>
               <td class="sm">'.$row->general_use.'</td>
               <td class="sm">'.date('d-m-Y H:i', strtotime($row->date_added)).'</td>
            </tr>
         ';

         $class = ($class == 'rowA') ? 'rowA rowB' : 'rowA';
      }

      $this->page .= '</table><br />';

      $this->page .= ( isset($pages['print'])) ? $pages['print'] : '<br />';
   }

   protected function _drawView()
   {
      $this->page .= '
         <style type="text/css">
            ul,li {list-style:none;margin:0;padding:0}
            #pictures li {display:inline; float:left; margin:5px; height:150px; overflow:hidden; border:1px solid #CCC; padding:5px}
            .clear {clear:both; height:0; line-height:0; font-size:0;}
         </style>
      ';

      $this->page .= '<div class="location"><a href="/?mod=tobuilt">Tobuilt</a></div>';

      $query = mysql_query('SELECT * FROM `ccl_tobuilt_house` WHERE `id` = '.mysql_real_escape_string($_GET['id']));

      $item = mysql_fetch_object($query);

      $this->page .= '<div style="width:950px; padding:10px; background-color:#ACE3AC">';
      $this->page .= '<h3>Description</h3>';
      $this->page .= '<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">';
      $this->page .= '
         <tr>
            <td class="rowA title" width="200">Name &amp; Location</td>
            <td class="rowA title"><h2>'.$item->name.'</h2>'.$item->address.'<br />'.$item->location.'</td>
         </tr>
         <tr>
            <td class="rowB title">Other Identification</td>
            <td class="rowB title">'.nl2br($item->identification).'</td>
         </tr>
         <tr>
            <td class="rowA title">Notes</td>
            <td class="rowA title">'.nl2br($item->notes).'</td>
         </tr>
         <tr>
            <td class="rowB title">Quote</td>
            <td class="rowB title">'.nl2br($item->quote).'</td>
         </tr>
         <tr>
            <td class="rowA title">Status</td>
            <td class="rowA title">'.$item->status.'</td>
         </tr>
         <tr>
            <td class="rowB title">Year Completed</td>
            <td class="rowB title">'.(int) $item->year_completed.'</td>
         </tr>
         <tr>
            <td class="rowA title">Companies</td>
            <td class="rowA title">'.nl2br($item->companies).'</td>
         </tr>
         <tr>
            <td class="rowB title">Click for Google Map</td>
            <td class="rowB title">'.str_replace('./images/', '/img/', $item->google_map).'</td>
         </tr>
         <tr>
            <td class="rowA title">Height and Building Data</td>
            <td class="rowA title">'.$item->building_data.'</td>
         </tr>
         <tr>
            <td class="rowB title">Floors</td>
            <td class="rowB title">'.(int) $item->floors.'</td>
         </tr>
         <tr>
            <td class="rowA title">Height (m)</td>
            <td class="rowA title">'.(float) $item->height.'</td>
         </tr>
         <tr>
            <td class="rowB title">Building type</td>
            <td class="rowB title">'.$item->type.'</td>
         </tr>
         <tr>
            <td class="rowA title">General Use</td>
            <td class="rowA title">'.$item->general_use.'</td>
         </tr>
         <tr>
            <td class="rowB title">Specific Use</td>
            <td class="rowB title">'.$item->specific_use.'</td>
         </tr>
         <tr>
            <td class="rowA title">Former Use</td>
            <td class="rowA title">'.$item->former_use.'</td>
         </tr>
         <tr>
            <td class="rowB title">Heritage</td>
            <td class="rowB title">'.$item->heritage.'</td>
         </tr>
         <tr>
            <td class="rowB title">Main Style</td>
            <td class="rowB title">'.$item->main_style.'</td>
         </tr>';

      if ($item->added_manually == 0)
      {
         $this->page .= '
         <tr>
            <td class="rowA title">Link to original page</td>
            <td class="rowA title"><a href="'.$item->url.'" target="_blank">Go to building page</a></td>
         </tr>';
      }
      
      $this->page .= '</table><br />';

      $files = scandir($_SERVER['DOCUMENT_ROOT'].'/tbcache/'.$item->id);

      $pictures = '';

      foreach ($files AS $file)
      {
         if (strpos($file, 'thumb-') !== false)
         {
            $image = str_replace('thumb-', '', $file);
            $pictures .= '<li><a rel="fancybox" href="/tbcache/'.$item->id.'/'.$image.'"><img src="/tbcache/'.$item->id.'/'.$file.'" alt="" /></a></li>';
         }
      }

      $this->page .= '
         <script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>
         <script type="text/javascript" src="/js/fancybox/jquery.mousewheel-3.0.4.pack.js"></script>
	 <script type="text/javascript" src="/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
	 <link rel="stylesheet" type="text/css" href="/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
      ';

      $this->page .= '<h3>Pictures</h3>';
      $this->page .= '
         <table width="100%" border="0" cellspacing="0" cellpadding="0" class="list">
            <tr>
               <td class="rowB title">
                  <ul id="pictures">'.$pictures.'</ul>
                  <div class="clear"></div>
               </td>
            </tr>
         </table>
         <script type="text/javascript">$("a[rel=fancybox]").fancybox();</script>';
      $this->page .= '</div>';
   }

   protected function _value($array, $key, $default = null)
   {
      return (isset($array[$key]) ? $array[$key] : $default);
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

   public static function map($callback, $array, $keys = NULL)
   {
      foreach ($array as $key => $val)
      {
         if (is_array($val))
         {
            $array[$key] = self::map($callback, $array[$key]);
         }
         elseif (!is_array($keys) or in_array($key, $keys))
         {
            if (is_array($callback))
            {
               foreach ($callback as $cb)
               {
                  $array[$key] = call_user_func($cb, $array[$key]);
               }
            }
            else
            {
               $array[$key] = call_user_func($callback, $array[$key]);
            }
         }
      }

      return $array;
   }

   public static function quote($value)
   {
      return "'$value'";
   }

}