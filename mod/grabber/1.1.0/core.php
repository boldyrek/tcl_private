<?php

class Core extends Proto {

   protected $_config;

   protected $_colors = array();

   public function __construct()
   {
      $this->_config = Zend_Registry::get('config');

      ini_set('display_errors', 'on');

      set_exception_handler(array($this, 'exception_handler'));

      parent::__construct();

      $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_colors_ref`");

      while ($row = mysql_fetch_object($query))
      {
         $this->_colors[$row->code] = $row->desc;
      }
   }

   public function exception_handler(Exception $exception)
   {
      echo 'Uncaught exception: '.$exception->getMessage();
      echo '<pre>';
      print_r($exception->getTraceAsString());
      // print_r($exception->getTrace());
      echo '</pre>';
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

            $ext = $int = $hl = array();

            if (isset($values['exterior']))
            {
               $ext = $values['exterior'];
               $int = $values['interior'];
               $hl = $values['hl'];

               unset($values['exterior'], $values['interior'], $values['hl']);
            }
            
            $opts = $values['options'];
            
            unset($values['options']);

            $sql = "
               INSERT INTO `ccl_grabber_admin_cars` (".implode(',', array_keys($values)).")
               VALUES (".implode(',', self::map('Core::quote', $values)).")
            ";

            $this->mysqlQuery($sql);

            $car_id = mysql_insert_id();

            if (! empty($ext))
            {
               foreach ($ext AS $key => $e)
               {
                  if (! empty($e))
                  {
                     foreach (explode(',', $int[$key]) AS $i)
                     {
                        $this->mysqlQuery("INSERT INTO `ccl_grabber_admin_colors` VALUES('$car_id', '$e', '$i')");
                     }
                  }
               }
            }

            if (! empty($hl))
            {
               foreach (explode(',', $hl) AS $color)
               {
                  $this->mysqlQuery("INSERT INTO `ccl_grabber_admin_colors_hl` VALUES('$car_id', '$color')");
               }
            }
            
            $this->mysqlQuery("INSERT INTO `ccl_grabber_admin_options` VALUES('$car_id', '{$opts['condition']}', '{$opts['exception']}')");

            $this->redirect('/?mod=grabber');
         }

         $this->_form();
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function edit()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();

         $id = (int) $_GET['id'];

         if ($_POST)
         {
            $values = self::map('mysql_real_escape_string', $_POST);
            
            $ext = $int = $hl = array();

            if (isset($values['exterior']))
            {
               $ext = $values['exterior'];
               $int = $values['interior'];
               $hl = $values['hl'];

               unset($values['exterior'], $values['interior'], $values['hl']);
            }
            
            $opts = $values['options'];
            
            unset($values['options']);

            $sql = "UPDATE `ccl_grabber_admin_cars` SET ";
            
            $lines = array();

            foreach ($values AS $key => $value)
            {
               $lines[] = "`$key` = '$value'";
            }

            $sql .= implode(',', $lines);

            $sql .= " WHERE `id` = $id";

            $this->mysqlQuery($sql);

            $this->mysqlQuery("DELETE FROM `ccl_grabber_admin_colors` WHERE `car_id` = $id");

            if (! empty($ext))
            {
               foreach ($ext AS $key => $e)
               {
                  if (! empty($e))
                  {
                     foreach (explode(',', $int[$key]) AS $i)
                     {
                        $this->mysqlQuery("INSERT INTO `ccl_grabber_admin_colors` VALUES('$id', '$e', '$i')");
                     }
                  }
               }
            }

            $this->mysqlQuery("DELETE FROM `ccl_grabber_admin_colors_hl` WHERE `car_id` = $id");

            if (! empty($hl))
            {
               foreach (explode(',', $hl) AS $color)
               {
                  $this->mysqlQuery("INSERT INTO `ccl_grabber_admin_colors_hl` VALUES('$id', '$color')");
               }
            }
            
            $q = mysql_query("SELECT * FROM `ccl_grabber_admin_options` WHERE `car_id` = $id");
            
            if (! mysql_num_rows($q))
            {
               $this->mysqlQuery("INSERT INTO `ccl_grabber_admin_options` VALUES ('$id', '{$opts['condition']}', '{$opts['exception']}', '{$opts['hl']}')");
            }
            else
            {
               $this->mysqlQuery("UPDATE `ccl_grabber_admin_options` SET `condition` = '{$opts['condition']}', `exception` = '{$opts['exception']}', `hl` = '{$opts['hl']}' WHERE `car_id` = $id");
            }
            
            $this->redirect('/?mod=grabber');
         }
         
         $colors = $hl = $options = array();

         $query = $this->mysqlQuery("SELECT `exterior` FROM `ccl_grabber_admin_colors` WHERE `car_id` = {$id} GROUP BY `exterior`");
         while ($row = mysql_fetch_object($query))
         {
            $request = $this->mysqlQuery("SELECT `interior` FROM `ccl_grabber_admin_colors` WHERE `car_id` = {$id} AND `exterior` = '{$row->exterior}'");

            while ($col = mysql_fetch_object($request))
            {
               $colors[$row->exterior][] = $col->interior;
            }
         }

         $query = $this->mysqlQuery("SELECT `color` FROM `ccl_grabber_admin_colors_hl` WHERE `car_id` = {$id}");
         while ($row = mysql_fetch_object($query))
         {
            $hl[] = $row->color;
         }
         
         $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_options` WHERE `car_id` = {$id} LIMIT 1");
         $options = mysql_fetch_array($query, MYSQL_ASSOC);

         $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_cars` WHERE `id` = {$id}");
         $values = mysql_fetch_array($query, MYSQL_ASSOC);

         $values['colors'] = $colors;
         $values['hl'] = implode(',',$hl);
         $values['options'] = $options;

         $this->_form($values);
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function start()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();

         $id = (int) $_GET['id'];

         $this->mysqlQuery("UPDATE `ccl_grabber_admin_cars` SET `active` = 1 WHERE `id` = $id");

         $this->redirect('/?mod=grabber');
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function stop()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();

         $id = (int) $_GET['id'];

         $this->mysqlQuery("UPDATE `ccl_grabber_admin_cars` SET `active` = 0 WHERE `id` = $id");

         $this->redirect('/?mod=grabber');
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function delete()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();

         $id = (int) $_GET['id'];

         $this->mysqlQuery("DELETE FROM `ccl_grabber_admin_cars` WHERE `id` = $id");

         $this->mysqlQuery("DELETE FROM `ccl_grabber_admin_colors` WHERE `car_id` = $id");

         $this->mysqlQuery("DELETE FROM `ccl_grabber_admin_colors_hl` WHERE `car_id` = $id");

         $this->redirect('/?mod=grabber');
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
           .error {color:red}
           ul.colors, ul.colors li, ul.colors li ul, ul.container, dl, dd {margin:0;padding:0}
           ul.colors li {list-style:none}
           ul.colors > li {margin-bottom:3px}
           ul.container li {display:inline;float:left;margin-right:5px}
           ul.container li input.ext {width:50px}
           ul.container li input.int {width:170px}
           ul.container li img {cursor:pointer}
           .clear {clear:both;font-size:0;line-height:0;height:0}
           .reference {width:400px; border:1px dotted #ccc; background-color:#fff; margin:0; display:none; padding:5px}
           .toggle-reference {border-bottom:1px dotted #000; cursor:pointer}
           .top {margin-top:14px}
           .color-item {margin:4px 0}
           .delete-color-item, .delete-option-item {margin-right:3px; float:left}
         </style>

         <script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>
         <script type="text/javascript" src="/js/jquery.validate.min.js"></script>

         <script type="text/javascript">
            $(function()
            {
               $("#my-form").validate({
                  rules:{
                     year_from:{minlength:4,maxlength:4,digits:true},
                     year_to:{minlength:4,maxlength:4,digits:true},
                     mileage:{minlength:4,maxlength:7,digits:true},
                  }
               });

               $("#add-color").live("click", function()
               {
                  var lastId = $("ul#colors li.item:last").attr("id").split("-")[1];
                  var nextId = parseInt(lastId)+1;
                  var content = "";
                  
                  content += \'<li class="item" id="item-\'+nextId+\'">\';
                  content += \'<ul class="container">\';
                  content += \'<li><input type="text" name="exterior[\'+nextId+\']" value="" maxlength="5" class="ext colorbox" /></li>\';
                  content += \'<li><input type="text" name="interior[\'+nextId+\']" value="" class="int colorbox" /></li>\';
                  content += \'<li><img src="/img/delete.png" alt="" class="delete-color" /></li>\';
                  content += \'</ul>\';
                  content += \'<div class="clear">\';
                  content += \'</li>\';

                  $(content).appendTo("ul#colors");
               });

               $(".delete-color").live("click", function()
               {
                  var item = $(this).parents().get(2);
                  if ($(item).attr("id") != "item-0")
                  {
                     $(item).remove();
                  }
               });

               $("#toggle-colors-reference").click(function()
               {
                  $("#colors-reference").toggle();
               });
               
               $("#add-color-item").live("click", function()
               {
                  var code = $("input#code");
                  var desc = $("input#desc");

                  var params = {
                     mod:"colors",
                     action:"add",
                     code:code.val(),
                     desc:desc.val(),
                     is_ext:$("select#is_ext").val()
                  };
                  
                  $.getJSON("/", params, function(response)
                  {
                     alert(response.result ? "Цвет успешно добавлен" : "Ошибка при добавлении. Возможно такой цвет уже существует");

                     code.val("");
                     desc.val("");

                     var content = "<dd class=\"color-item\">";
                     content += "<img id=\"color-item-"+response.id+"\" class=\"delete-color-item\" alt=\"\" src=\"/img/delete.png\">";
                     content += "<span>"+response.params.code+" - "+response.params.desc+"</span>";
                     content += "<div class=\"clear\"></div></dd>";
                     
                     $(content).appendTo(response.params.is_ext == 1 ? "#ext-list" : "#int-list");
                  });
               });
               
               $(".delete-color-item").live("click", function()
               {
                  if (confirm("Вы уверены?"))
                  {
                     $(this).parent("dd").remove();

                     var params = {
                        mod:"colors",
                        action:"delete",
                        id:this.id.split("-")[2]
                     };

                     $.getJSON("/", params, function(response)
                     {
                     });
                  }
               });
               
               $("#toggle-options-reference").click(function()
               {
                  $("#options-reference").toggle();
               });
               
               $("#add-option-item").live("click", function()
               {
                  var code = $("input#option-code");
                  var desc = $("input#option-desc");

                  var params = {
                     mod:"options",
                     action:"add",
                     code:code.val(),
                     desc:desc.val()
                  };
                  
                  $.getJSON("/", params, function(response)
                  {
                     alert(response.result ? "Опция успешно добавлен" : "Ошибка при добавлении. Возможно такая опция уже существует");

                     code.val("");
                     desc.val("");

                     var content = "<dd class=\"color-item\">";
                     content += "<img id=\"option-item-"+response.id+"\" class=\"delete-option-item\" alt=\"\" src=\"/img/delete.png\">";
                     content += "<span>"+response.params.code+" - "+response.params.desc+"</span>";
                     content += "<div class=\"clear\"></div></dd>";
                     
                     $(content).appendTo("#options-reference-items");
                  });
               });
               
               $(".delete-option-item").live("click", function()
               {
                  if (confirm("Вы уверены?"))
                  {
                     $(this).parent("dd").remove();

                     var params = {
                        mod:"options",
                        action:"delete",
                        id:this.id.split("-")[2]
                     };

                     $.getJSON("/", params);
                  }
               });
            });
         </script>

         <div class="location"><a href="/?mod=grabber">Grabber</a> / Добавление/редактирование</div>
         <div class="cont_car" style="width:960px">
            <form class="myForm" id="my-form" method="post" action="">
               <table width="960" cellspacing="0" cellpadding="0" border="0" class="list">
                  <tr>
                     <td class="title rowA" align="right" width="30%">
                        <label for="name">Название</label>:
                     </td>
                     <td class="title rowA" width="30%">
                        <input type="text" name="name" id="name" value="'.$this->_value($values, 'name').'" class="required" />
                     </td>
                     <td class="title rowA">
                        <small>например: &laquo;Lexus RX 300&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="year_from">Год от</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="year_from" id="year_from" value="'.$this->_value($values, 'year_from').'" maxlength="4" class="required" />
                     </td>
                     <td class="title rowB"></td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="year_to">Год до</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="year_to" id="year_to" value="'.$this->_value($values, 'year_to').'" maxlength="4" class="required" />
                     </td>
                     <td class="title rowA"></td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="mileage">Пробег (более)</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="mileage" id="mileage" value="'.$this->_value($values, 'mileage').'" class="required" />
                     </td>
                     <td class="title rowB">
                        <small>например: &laquo;120000&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="vincode">Условие для вин-кода</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="vincode" id="vincode" value="'.$this->_value($values, 'vincode').'" />
                     </td>
                     <td class="title rowA">
                        <small>например: &laquo;начинаются с JTEBU17R&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right" valign="top">
                        <div class="top">Цвета:</div>
                     </td>
                     <td class="title rowB">
                        <ul class="colors" id="colors">';

                        if (empty($values['colors']))
                        {
                           $this->page .= '
                           <li class="item" id="item-0">
                              <ul class="container">
                                 <li><input type="text" name="exterior[0]" value="" maxlength="5" class="ext colorbox" /></li>
                                 <li><input type="text" name="interior[0]" value="" class="int colorbox" /></li>
                                 <li><img src="/img/delete.png" alt="" class="delete-color" /></li>
                                 <li><img src="/img/add.png" alt="" id="add-color" /></li>
                              </ul>
                              <div class="clear">
                           </li>
                           ';
                        }
                        else
                        {
                           $i = 0;

                           foreach ($values['colors'] AS $ext => $int)
                           {
                              $this->page .= '
                              <li class="item" id="item-'.$i.'">
                                 <ul class="container">
                                    <li><input type="text" name="exterior['.$i.']" value="'.$ext.'" maxlength="5" class="ext colorbox" /></li>
                                    <li><input type="text" name="interior['.$i.']" value="'.implode(',', $int).'" class="int colorbox" /></li>
                                    <li><img src="/img/delete.png" alt="" class="delete-color" /></li>
                                    '.($i == 0 ? '<li><img src="/img/add.png" alt="" id="add-color" /></li>' : '').'
                                 </ul>
                                 <div class="clear">
                              </li>
                              ';

                              $i++;
                           }
                        }
                        
                        $this->page .= '
                        </ul>
                     </td>
                     <td class="title rowB" valign="top">
                        <small>1 цвет кузова<br />если цвет салона более 1 - через запятую, например: &laquo;LB22,LB00,WZ22&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right" valign="top">
                        <span id="toggle-colors-reference" class="toggle-reference top">Справочник цветов</span>
                     </td>
                     <td class="title rowB" colspan="2">
                        <div id="colors-reference" class="reference">
                           <ul class="container">
                              <li style="margin-right:30px" id="ext-list">
                                 <b>Цвета кузова</b>
                                 <dl>
                        ';

                        $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_colors_ref` WHERE `is_ext` = 1");

                        while ($row = mysql_fetch_object($query))
                        {
                           $this->page .= '
                              <dd class="color-item">
                                 <img id="color-item-'.$row->id.'" src="/img/delete.png" alt="" class="delete-color-item" />
                                 <span>'.$row->code.' - '.$row->desc.'</span>
                                 <div class="clear"></div>
                              </dd>
                           ';
                        }

                        $this->page .= '
                                 </dl>
                              </li>
                              <li id="int-list">
                                 <b>Цвета салона</b>
                                 <dl>
                        ';

                        $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_colors_ref` WHERE `is_ext` = 0");

                        while ($row = mysql_fetch_object($query))
                        {
                           $this->page .= '
                              <dd class="color-item">
                                 <img id="color-item-'.$row->id.'" src="/img/delete.png" alt="" class="delete-color-item" />
                                 <span>'.$row->code.' - '.$row->desc.'</span>
                                 <div class="clear"></div>
                              </dd>
                           ';
                        }

                        $this->page .= '
                                 </dl>
                              </li>
                           </ul>
                           <div class="clear"></div>
                           <br />
                           <b>Добавить новый цвет</b>
                           <ul class="colors">
                              <li>
                                 <ul class="container">
                                    <li style="width:62px">Тип:</li>
                                    <li style="width:52px">Код:</li>
                                    <li>Описание:</li>
                                    <li>&nbsp;</li>
                                 </ul>
                                 <div class="clear"></div>
                              </li>
                              <li>
                                 <ul class="container">
                                    <li>
                                       <select id="is_ext">
                                          <option value="1">кузов</option>
                                          <option value="0">салон</option>
                                       </select>
                                    </li>
                                    <li><input type="text" maxlength="5" value="" id="code" class="ext required" /></li>
                                    <li><input type="text" value="" id="desc" class="required" style="width:200px" /></li>
                                    <li><img id="add-color-item" alt="" src="/img/add.png" /></li>
                                 </ul>
                                 <div class="clear"></div>
                              </li>
                           </ul>
                        </div>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="hl">Выделить цвета салона</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="hl" id="hl" value="'.$this->_value($values, 'hl').'" />
                     </td>
                     <td class="title rowA">
                        <small>если более 1 - через запятую, например: &laquo;LK10,WZ22&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="hl">Включить сл. опции</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="options[condition]" value="'.$this->_value($values['options'], 'condition').'" />
                     </td>
                     <td class="title rowB">
                        <small>если более 1 - через запятую, например: &laquo;86843,86842,86841&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" align="right">
                        <label for="hl">Исключить сл. опции</label>:
                     </td>
                     <td class="title rowA">
                        <input type="text" name="options[exception]" value="'.$this->_value($values['options'], 'exception').'" />
                     </td>
                     <td class="title rowA">
                        <small>если более 1 - через запятую, например: &laquo;86843,86842,86841&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowB" align="right">
                        <label for="hl">Подсвечивать сл. опции</label>:
                     </td>
                     <td class="title rowB">
                        <input type="text" name="options[hl]" value="'.$this->_value($values['options'], 'hl').'" />
                     </td>
                     <td class="title rowB">
                        <small>если более 1 - через запятую, например: &laquo;86843,86842,86841&raquo;</small>
                     </td>
                  </tr>
                  <tr>
                     <td class="title rowA" valign="top" align="right">
                        <span id="toggle-options-reference" class="toggle-reference top">Справочник опций</span>
                     </td>
                     <td class="title rowA" colspan="2">
                        <div id="options-reference" class="reference" style="background-color:#EFEFEF">
                           <ul class="container">
                              <li id="options-reference-items">
                                 <dl>';
                        
                        $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_options_ref` ORDER BY `id` ASC");

                        while ($row = mysql_fetch_object($query))
                        {
                           $this->page .= '
                              <dd class="color-item">
                                 <img id="option-item-'.$row->id.'" src="/img/delete.png" alt="" class="delete-option-item" />
                                 <span>'.$row->code.' - '.$row->desc.'</span>
                                 <div class="clear"></div>
                              </dd>
                           ';
                        }
                        
                        $this->page .= '
                                 </dl>
                              </li>
                           </ul>
                           <div class="clear"></div>
                           <br />
                           <b>Добавить новую опцию</b>
                           <ul class="colors">
                              <li>
                                 <ul class="container">
                                    <li style="width:72px">Код:</li>
                                    <li>Описание:</li>
                                    <li>&nbsp;</li>
                                 </ul>
                                 <div class="clear"></div>
                              </li>
                              <li>
                                 <ul class="container">
                                    <li><input type="text" style="width:70px" class="required" id="option-code" value="" maxlength="7"></li>
                                    <li><input type="text" style="width:200px" class="required" id="option-desc" value=""></li>
                                    <li><img src="/img/add.png" alt="" id="add-option-item"></li>
                                 </ul>
                                 <div class="clear"></div>
                              </li>
                           </ul>
                        </div>
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

   protected function _value($array, $key, $default = null)
   {
      return (isset($array[$key]) ? $array[$key] : $default);
   }

   protected function _stat($id)
   {
      $i = 0;

      foreach ($this->_config['search'] AS $item)
      {
         if ($item['parent'] == $id)
         {
            $i++;
         }
      }

      return $i;
   }

   public function email()
   {
      if ($_POST AND $_POST['address'])
      {
         $message = '
         <p>Здравствуйте.<br />
         Мы рекомендуем Вам следующий автомобиль:<br />
         '.$_POST['item']['name'].' / '.preg_replace('/(\d{4})(?:\.)?(\d{2})/', '$2.$1', $_POST['item']['date_made']).' /'.$_POST['item']['vincode'].' / $'.$_POST['item']['price'].' / '.$_POST['item']['options'].'<br />
         Для детального просмотра перейдите по <a href="'.$_POST['item']['url'].'">ссылке</a></p>
         <p>
         ---------<br />
         С уважением, Makmalauto
         </p>
         ';

         $address = $_POST['address'];

         if ($this->sMail(array_values($address), $message, $_POST['item']['name']))
         {
            $sql = 'INSERT INTO `ccl_grabber_mail` (`user_id`, `car_id`) VALUES ';

            foreach ($address AS $key => $value)
            {
               $sql_values[] = '('.$key.', '.$_POST['item']['id'].')';
            }

            $sql .= implode(', ', $sql_values);

            mysql_query($sql) or die(mysql_error());
         }

         $this->redirect('/?mod=grabber');
      }
      else
      {
         throw new Exception('Недостаточно данных');
      }
   }

   public function drawUsers()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->getUsers();
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function getUsers()
   {
      $this->page .= $this->getStyles();

      $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_cars` WHERE `id` = ".(int) mysql_real_escape_string($_GET['id']));

      $result = mysql_fetch_array($query, MYSQL_ASSOC);

      //var_dump($result); exit;

      if (! $result)
      {
         $this->page .= '<div class="error" style="margin-top:10px">'.$this->translate->_('Ничего не найдено').'</div>';
      }
      else
      {
         $this->page .= '<form action="/?mod='.MODULE.'&action=email" method="post">';

         foreach ($result AS $key => $value)
         {
            $this->page .= '<input type="hidden" name="item['.$key.']" value="'.$value.'" />';
         }

         $this->page .= '
         <div class="header">
            <h2>'.$result['name'].'</h2>
         </div>';

         $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
         $this->page .= '<tr class="title">';
         $this->page .= '<td width="25">&nbsp;</td>';
         $this->page .= '<td width="50%">Ф.И.О</td>';
         $this->page .= '<td>E-mail</td>';
         $this->page .= '</tr>';
         $this->page .= '<tbody>';

         $query = mysql_query("
            SELECT c.id AS `id`, c.name AS `name`, c.email AS `email`, (SELECT COUNT(car_id) FROM `ccl_grabber_mail` AS m WHERE c.id = m.user_id) AS `quantity`
            FROM `ccl_customers` AS c
            WHERE `email` IS NOT NULL AND `email` != ''
            ORDER BY `quantity` DESC, `name` ASC
         ");

         $class = "rowA rowB";

         while ($user = mysql_fetch_object($query))
         {
            $this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">';
            $this->page .= '
               <td class="sm">
                  <input type="checkbox" value="'.$user->email.'" name="address['.$user->id.']" id="user-'.$user->id.'" />
               </td>
               <td class="sm"><label for="user-'.$user->id.'">'.$user->name.'</label> <small>('.(int) $user->quantity.')</small></td>
               <td class="sm">'.$user->email.'</td>
            ';
            $this->page .= '</tr>';

            $class = ($class == 'rowA') ? 'rowA rowB' : 'rowA';
         }

         $this->page .= '</tbody>';
         $this->page .= '</table><br />';
         $this->page .= '<input type="submit" value="Отправить" />';
         $this->page .= '</form><br />';
      }
   }

   public function drawList()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->getList();
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function getList()
   {
      $this->page .= $this->getStyles();

      $this->page .= '<script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>';
      $this->page .= '<div class="location">'.ucfirst(MODULE).' / Типы поиска <a style="margin-left:20px" href="/?mod=grabber&amp;action=add">добавить</a></div>';

      $this->page .= '<div id="list">';
      $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
      $this->page .= '<tr class="title sortButtons">';
      $this->page .= '<td width="30">'.$this->translate->_('ID').'</td>';
      $this->page .= '<td>'.$this->translate->_('Название').'</td>';
      $this->page .= '<td>'.$this->translate->_('Условия поиска').'</td>';
      $this->page .= '<td>'.$this->translate->_('Активность').'</td>';
      $this->page .= '</tr>';

      $class = "rowA rowB";

      $query = mysql_query("SELECT * FROM `ccl_grabber_admin_cars` ORDER BY `id` ASC");

      while ($row = mysql_fetch_object($query))
      {
         $request = mysql_query("SELECT COUNT(id) AS `total` FROM `ccl_grabber_cars` WHERE `target_id` = '{$row->id}'") or die(mysql_error());
         $cars = mysql_fetch_object($request);
         $total_cars = (int) $cars->total;

         // '.(! intval($row->active) ? 'style="background-color:red;opacity:0.2;color:white"' : '').'
         $this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" '.(! $row->active ? 'style="background-color:#FFCCCC;"' : '').'>';
         $this->page .= '<td class="sm" align="center">'.$row->id.'</td>';
         $this->page .= '<td class="sm"><h3>'.($total_cars > 0 ? '<a href="/?mod='.MODULE.'&amp;action=grid&amp;target='.$row->id.'">'.$row->name.'</a>' : $row->name).'</h3>';
         //$this->page .= 'присвоено поисков: '.$this->_stat($row->id);
         $this->page .= '<p><a href="/?mod=grabber&amp;action=edit&amp;id='.$row->id.'" title="Редактировать"><img src="/img/edit.png" alt="" /></a>';
         $this->page .= '<a style="margin-left:5px" href="/?mod=grabber&amp;action='.($row->active ? 'stop' : 'start').'&amp;id='.$row->id.'" title="'.($row->active ? 'Stop' : 'Start').'"><img src="/img/'.($row->active ? 'stop' : 'start').'.png" alt="" /></a>';
         $this->page .= '</p></td>';

         $request = mysql_query("SELECT `exterior` FROM `ccl_grabber_admin_colors` WHERE `car_id` = {$row->id} GROUP BY `exterior`");

         $colors = array();

         while ($e = mysql_fetch_object($request))
         {
            $r = mysql_query("SELECT `interior` FROM `ccl_grabber_admin_colors` WHERE `car_id` = {$row->id} AND `exterior` = '{$e->exterior}'");

            while ($i = mysql_fetch_object($r))
            {
               $colors[$e->exterior][] = $i->interior;
            }
         }

         $this->page .= '
         <td class="sm">
            <ul class="conditions">
               <li><b>годы выпуска:</b> '.$row->year_from.'-'.$row->year_to.'</li>
               <li><b>пробег:</b> более '.$row->mileage.' миль</li>'
               .(! empty($row->vincode) ? '<li><b>VIN:</b> '.$row->vincode.'</li>' : '')
               /*.(! empty($row->interior_color) ? '<li><b>Цвета салона:</b> '.$row->interior_color.'</li>' : '')
               .(! empty($row->exterior_color) ? '<li><b>Цвета кузова:</b> '.$row->exterior_color.'</li>' : '')*/
            .'</ul>';

         if (! empty($colors))
         {
            $this->page .= '
            <span class="toggle-colors">Показать цвета</span>
            <ul class="colors conditions">';

            foreach ($colors AS $e => $arr)
            {
               $this->page .= '<li><b>кузов:</b> '.$e.' <b>салон:</b> '.implode(',', $arr).'</li>';
            }

            $this->page .= '</ul>';
         }

         $this->page .= '</td>';

         $vin = null;

         if ($total_cars > 0)
         {
            $request = mysql_query("SELECT v.vincode, v.date_added FROM `ccl_grabber_vins` v, `ccl_grabber_cars` c WHERE v.vincode = c.vincode AND v.target_id = '{$row->id}' ORDER BY v.date_added DESC LIMIT 1");

            if (mysql_num_rows($request))
            {
               $vin = mysql_fetch_object($request);
            }
         }

         $this->page .= '<td class="sm">';

         $request = mysql_query("SELECT `date_last_updated` AS `date` FROM `ccl_grabber_statuses` WHERE `target_id` = '{$row->id}' ORDER BY `id` DESC LIMIT 1");
         $last_update = mysql_fetch_object($request);

         $this->page .= ($last_update->date != null ? 'Последнее обновление: '.date('d.m.Y H:i:s', strtotime($last_update->date))
                        .'<br />Добавлено записей: <b>'.$total_cars.'</b>'
                        .(! empty($vin) ? '<br />Последнее добавление: '.date('d.m.Y H:i:s', strtotime($vin->date_added)).' ('.$vin->vincode.')' : '') : '-');

         $this->page .= '</td>';

         $this->page .= '</tr>';

         $class = ($class == 'rowA') ? 'rowA rowB' : 'rowA';
      }

      $this->page .= '</table>';

      $this->page .= '</div><br />';

      $this->page .= '<div class="hidden debug"></div>';

      $this->page .= '
      <div class="control">
        <select id="source" style="width:200px">
      ';

      foreach ($this->_config['root']['sources'] AS $key => $value)
      {
         $this->page .= '<option value="'.$key.'">'.$value.'</option>';
      }

      $this->page .= '</select>
        &nbsp;<button id="invoke">Получить данные</button>&nbsp;<button id="toindex" class="hidden">Перейти к списку</button>
      </div>
      <script type="text/javascript">
         $(".toggle-colors").click(function()
         {
            $(this).next().toggle();
            $(this).text($(this).next().is(":visible") ? "Скрыть цвета" : "Показать цвета");
         });

         $list = $("div#list");
         $invoke = $("button#invoke");
         $debug = $("div.debug");
         $toindex = $("button#toindex");
         $source = $("#source");

         requestInvoked = false;
         interval = 10 // minutes

         reload = function()
         {
            window.location.reload(true);
         }

         timeout = function()
         {
            if (requestInvoked)
            {
               $list.show()
               .removeClass("loader success")
               .addClass("error")
               .html("В процессе импорта данных произошла ошибка. Пожалуйста, повторите попытку");

               $invoke.show();
               $toindex.show();

               requestInvoked = false;
            }
         }

         timeoutObserver = setInterval(timeout, interval*60*1000);

         $invoke.click(function()
         {
            requestInvoked = true;

            $list.show()
            .removeClass("error")
            .addClass("success message loader")
            .html("Подождите, идет процесс импорта данных...");

            $invoke.hide();
            $debug.hide();
            $toindex.hide();
            $source.hide();

            $.get("/cron/'.MODULE.'/home/execute/"+$("#source").val(), function(data)
            {
               $list.hide();
               // $debug.show().html(data);
               $invoke.show();
               $toindex.show();
               $source.show();

               clearInterval(timeoutObserver);
            });
         });

         $toindex.click(function()
         {
            reload();
         });

         $debug.ajaxSend(function(evt, request, settings){
            // $(this).show().append("<br />Starting request at " + settings.url);
         }).ajaxError(function(event, request, settings){
            $(this).append("Error requesting page " + settings.url);
         }).ajaxComplete(function(request, settings){
            // $(this).append("<br />Request complete");
         }).ajaxSuccess(function(evt, request, settings){
            // $(this).append("<br />Successful request");
         });
      </script>
      ';
   }

   public function drawGrid()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->getGrid();
      }

      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function getGrid()
   {
      $this->page .= $this->getStyles();

      $id = (int) $_GET['target'];

      $query = mysql_query('SELECT COUNT(*) AS `total` FROM `ccl_grabber_cars` WHERE `target_id` = '.$id);
      $row = mysql_fetch_array($query, MYSQL_ASSOC);
      $total = (int) $row['total'];
      $pages = array();

      if ($total > 0)
      {
         if ($total > $this->per_page)
         {
            $qs = http_build_query(array
            (
               'action' => 'grid',
               'target' => (int) $_GET['target'],
               'sortby' => $_GET['sortby'],
               'order' => $_GET['order'],
            ));

            $pages = $this->pageBrowse(mysql_real_escape_string((int) $_GET['page']), MODULE, $total, '&'.$qs);
         }

         $query = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_cars` WHERE `id` = $id");
         $car = mysql_fetch_object($query);
         $name = sprintf('%s %d-%d', $car->name, $car->year_from, $car->year_to);

         $this->page .= '<script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>';
         $this->page .= '<div class="location"><a href="/?mod='.MODULE.'">'.ucfirst(MODULE).'</a> / '.$name.'</div>';
         $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
         $this->page .= '<tr class="title sortButtons">';
         $this->page .= '<td>'.$this->translate->_('№').'</td>';
         $this->page .= $this->sorterTD(MODULE, 'name', $this->translate->_('Модель'));
         $this->page .= $this->sorterTD(MODULE, 'source_id', $this->translate->_('Источник'));
         $this->page .= $this->sorterTD(MODULE, 'vincode', $this->translate->_('VIN'));
         $this->page .= $this->sorterTD(MODULE, 'vincode_date_added', $this->translate->_('VIN добавлен'));
         $this->page .= $this->sorterTD(MODULE, 'mileage', $this->translate->_('Пробег'));
         $this->page .= $this->sorterTD(MODULE, 'price', $this->translate->_('Цена'));
         $this->page .= $this->sorterTD(MODULE, 'date_auction', $this->translate->_('Дата аукциона'));
         $this->page .= $this->sorterTD(MODULE, 'exterior_code', $this->translate->_('Цвет кузова'));
         $this->page .= $this->sorterTD(MODULE, 'interior_code', $this->translate->_('Цвет салона'));
         $this->page .= $this->sorterTD(MODULE, 'date_made', $this->translate->_('Дата выпуска'));
         $this->page .= '<td>'.$this->translate->_('Опции').'</td>';
         $this->page .= '<td>'.$this->translate->_('Коды опций').'</td>';
         $this->page .= '<td>'.$this->translate->_('Ссылка').'</td>';
         $this->page .= '<td>'.$this->translate->_('Копии в кэше').'</td>';
         $this->page .= '<td>&nbsp;</td>';
         $this->page .= '</tr>';

         $sql = 'SELECT * FROM `ccl_grabber_cars` WHERE `target_id` = '.$id.' ORDER BY `'.(empty($_GET['sortby']) ? 'date_added' : $_GET['sortby']).'` '.(empty($_GET['order']) ? 'DESC' : $_GET['order']).' '.$pages['qlimit'];

         $query = mysql_query($sql) OR die('Неверно указаны параметры запроса');

         $class = "rowA rowB";

         $i = 1;

         $sources = $this->_config['root']['sources'];

         while ($row = mysql_fetch_object($query))
         {
            $exterior = (! empty($row->exterior_code)
            ? (array_key_exists($row->exterior_code, $this->_colors) ? $row->exterior_code.' - '.$this->_colors[$row->exterior_code] : $row->exterior_code)
            : (! empty($row->exterior) ? $row->exterior : '-'));

            $highlight = FALSE;

            if (! empty($row->interior_code))
            {
               $interior = array_key_exists($row->interior_code, $this->_colors) ? $row->interior_code.' - '.$this->_colors[$row->interior_code] : $row->interior_code;

               $hl = array();

               $request = $this->mysqlQuery("SELECT * FROM `ccl_grabber_admin_colors_hl` WHERE `car_id` = {$row->target_id}");

               while ($col = mysql_fetch_object($request))
               {
                  $hl[] = $col->color;
               }

               $highlight = in_array($row->interior_code, $hl);
            }
            else
            {
               $interior = (! empty($row->interior) ? $row->interior : '-');
            }

            $this->page .= '
            <tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"'.($highlight ? 'style="background-color:#C9DBFA"' : '').'>
               <td class="sm">'.$i.'</td>
               <td class="sm">'.((int) $row->picture ? '<div class="picture"></div>' : '').$row->name.'</td>
               <td class="sm">'.(array_key_exists($row->source_id, $sources) ? $sources[$row->source_id] : '-').'</td>
               <td class="sm">'.$row->vincode.'</td>';

            $this->page .= '
               <td class="sm">'.(strtotime($row->vincode_date_added) ? date('d.m.Y H:i:s', strtotime($row->vincode_date_added)) : '-').'</td>
               <td class="sm">'.($row->mileage != 0 ? $row->mileage : '-').'</td>
               <td class="sm">'.(! empty($row->price) ? $row->price : '-').'</td>
               <td class="sm">'.(! empty($row->date_auction) ? $row->date_auction : '-').'</td>
               <td class="sm">'.$exterior.'</td>
               <td class="sm">'.$interior.'</td>
               <td class="sm">'.(! empty($row->date_made) ? preg_replace('/(\d{4})(?:\.)?(\d{2})/', '$2.$1', $row->date_made) : '-').'</td>
               <td class="sm">'.(! empty($row->options) ? $row->options : '-').'</td>
               <td class="sm">';
            
               $q = mysql_query("SELECT hl FROM `ccl_grabber_admin_options` WHERE `car_id` = {$row->target_id} LIMIT 1");
               
               $options = mysql_fetch_object($q);
               
               if (trim($options->hl) != '')
               {
                  $options = explode(',', $options->hl);
                  
                  $in = array();
                  
                  foreach ($options AS $option)
                  {
                     $in[] = "'$option'";
                  }
                  
                  if ($q = mysql_query("SELECT `option` FROM `ccl_grabber_options` WHERE `vincode` = '{$row->vincode}' AND `option` IN (".implode(',', $in).") AND `exists` = 1"))
                  {
                     while ($hl = mysql_fetch_object($q))
                     {
                        $this->page .= $hl->option."<br/>";
                     }
                  }
               }
            
               $this->page .= '
               </td>
               <td class="sm">'.(! empty($row->url) ? '<a href="'.$row->url.'" target="_blank">'.$this->translate->_('Ссылка').'</a>' : '-').'</td>
               <td class="sm">';

            $base = '/gcache/'.$row->vincode;

            foreach ($this->_config['root']['sources'] AS $source)
            {
               $src = $base.'-'.strtoupper($source);

               $cached_base = $_SERVER['DOCUMENT_ROOT'].$src;

               if (file_exists($cached_base))
               {
                  $changelog_file = $cached_base.'/changelog.txt';

                  $title = (file_exists($changelog_file))
                  ? 'Обновлен '.date('d-m-Y H:i:s', file_get_contents($changelog_file))
                  : '';

                  $this->page .= '<a href="'.$src.'" title="'.$title.'" target="_blank">'.$source.'</a>&nbsp;';
               }
            }

            $this->page .= '</td>
               <td class="sm recommend"><a href="/?mod='.MODULE.'&action=recommend&id='.$row->id.'" title="Рекомендовать"><img src="/img/recommend.png" /></a></td>
            </tr>';

            $class = ($class == 'rowA') ? 'rowA rowB' : 'rowA';

            $i++;
         }

         $this->page .= '</table><br />';

         $this->page .= ( isset($pages['print'])) ? $pages['print'] : '<br />';
      }
      else
      {
         $this->page .= '<div class="error" style="margin-top:10px">'.$this->translate->_('Ничего не найдено').'</div>';
      }
   }

   public function sorterTD($module, $item, $caption)
   {
      $order = ($_GET['order'] == '' OR $_GET['order'] == 'ASC') ? 'DESC' : 'ASC';

      $qs = http_build_query(array
      (
         'mod' => $module,
         'action' => 'grid',
         'target' => (int) $_GET['target'],
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

   protected function getStyles()
   {
      return '
      <style type="text/css">
         a:link {color:#0000ff;}
         a:visited {color:#800080;}
         a:hover {color:#ff0000;}
         a:active {color:#00ff00;}
         img {border:0}
         div.message {margin-top:10px; font-weight:bold}
         div.success {color:green}
         div.error {color:red}
         div.loader {background:url(/img/ajax-loader.gif) no-repeat left; padding-left:135px}
         div.control {margin:20px 0}
         div.debug {border:1px solid #CCC; width:600px; height:300px; background-color:#fff; overflow:auto; margin-top:20px; padding:10px}
         .hidden {display:none}
         .picture {background:url(/img/picture.gif) left no-repeat; width:16px; height:16px; float:left; margin:0; padding-right:4px}
         .sm {line-height:1.5em}
         .recommend img {margin:0 5px}
         .header {background-color:#fff; padding:10px; margin:5px 0; border:2px solid #CCC; width:946px}
         button {cursor:pointer}
         .toggle-colors {cursor:pointer; border-bottom:1px dotted}
         .conditions {margin:0 0 2px 0; padding:0px}
         .conditions li {list-style:none}
         .colors {display:none}
      </style>
      ';
   }

}
