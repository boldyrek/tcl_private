<?php

class Controller extends Proto {

   protected $_config;

   const CYCLE = 45;

   public function __construct()
   {
      $this->_config = Zend_Registry::get('config')->toArray();

      parent::__construct();
   }

   public function setEndDate()
   {
      if ($this->checkAuth())
      {
         $query = mysql_query('UPDATE `ccl_mds_dates` SET `end_date` = CURRENT_DATE');
      }

      $this->redirect('/?mod=mds');
   }

   public function setStartDate()
   {
      if ($this->checkAuth())
      {
         $query = mysql_query('UPDATE `ccl_mds_dates` SET `start_date` = CURRENT_DATE, `end_date` = NULL');
      }

      $this->redirect('/?mod=mds');
   }

   public function removeSearch()
   {
      if ($this->checkAuth())
      {
         $query = mysql_query("SELECT `main` FROM `ccl_mds_searches` WHERE `id` = ".$_GET['id']." LIMIT 1");
         $result = mysql_fetch_object($query);

         if ((int) $result->main == 0)
         {
            mysql_query("DELETE FROM `ccl_mds_searches` WHERE `id` = ".$_GET['id']);
         }
      }

      $this->redirect('/?mod=mds&action=grid&id='.$_GET['pid'].'&year='.$_GET['year']);
   }

   public function drawList()
   {
      $this->page .= View::factory('header')->render();

      if ($this->checkAuth())
      {
         $this->makeTopMenu();
         $this->page .= $this->getList();
      }

      $this->page .= View::factory('footer')->render();

      $this->errorsPublisher();
      $this->publish();
   }

   public function getList()
   {
      $content .= '<div class="location">Market Days Supply</div>';

      $query = mysql_query('SELECT UNIX_TIMESTAMP(start_date) AS `start`, UNIX_TIMESTAMP(end_date) AS `end` FROM `ccl_mds_dates` LIMIT 1');

      $date = mysql_fetch_object($query);

      $must_end = (int) $date->start + (self::CYCLE*24*3600);

      $content .= '
      <div class="controls">
         начало: <b id="date-start">'.date('d-m-Y', $date->start).'</b>,
         конец: <b id="date-end">'.date('d-m-Y', ($date->end ? (int) $date->end : $must_end)).'</b></span>
         ';
      
      if (! $date->end)
      {
         $content .= (time() < $must_end)
         ? '<a href="/?mod=mds&amp;action=stop">остановить</a>'
         : '<a href="/?mod=mds&amp;action=start">запустить заново</a>';
      }
      else
      {
         $content .= '<a href="/?mod=mds&amp;action=start">запустить</a> (остановлено вручную '.date('d-m-Y', $date->end).')';
      }

      $content .= '</div>';

      $content .= '<table width="650" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
      $content .= '<thead>';
      $content .= '<tr class="title">';
      $content .= '<td width="100">'.$this->translate->_('Год выпуска').'</td>';
      $content .= '<td width="50">'.$this->translate->_('Всего').'</td>';
      $content .= '<td width="300">'.$this->translate->_('Лучшие показатели').'</td>';
      $content .= '<td>'.$this->translate->_('Последнее обновление').'</td>';
      $content .= '</tr>';
      $content .= '</thead>';
      $content .= '</table>';

      foreach ($this->_config['items'] AS $id => $item)
      {
         $content .= '<h2>'.$id.'. '.$item['name'].'</h2>';

         $content .= '<table width="650" border="0" cellspacing="0" cellpadding="0" class="list vlines" id="grid-'.$id.'">';
         $content .= '<tbody>';

         $class = "rowA rowB";

         foreach ($item['year'] AS $year)
         {
            $content .= '<tr class="'.$class.'" id="row-'.$id.'-'.$year.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">';
            $content .= '<td class="sm link" width="100"><a href="/?mod=mds&amp;action=grid&amp;id='.$id.'&amp;year='.$year.'">'.$year.'</a></td>';
            $content .= '<td class="sm total" width="50" align="center">-</td>';
            $content .= '<td class="sm best" width="300">-</td>';
            $content .= '<td class="sm date">-</td>';
            $content .= '</tr>';

            $class = ($class == 'rowA') ? 'rowA rowB' : 'rowA';
         }

         $content .= '</tbody>';
         $content .= '</table>';
      }

      $content .= '</br>';

      $content .= '
      <script type="text/javascript">
         var baseurl = "http://'.$_SERVER['SERVER_NAME'].'/cron/calc/mds/export/grid.json";
         $.getJSON(baseurl, function(json)
         {
            $.each(json, function(key, values)
            {
               $.each(values, function(year, item)
               {
                  var o = $("#grid-"+key+" tr#row-"+key+"-"+year);
                  o.children("td.total").html(item.total > 0 ? "<a href=\"/?mod=mds&action=stat&id="+key+"&year="+year+"\">"+item.total+"</a>" : item.total);

                  if (item.data)
                  {
                     var ul = document.createElement("ul");
                     $(ul).addClass("rates")
                     .append("<li><b>VPD:</b> "+item.data.vpd+"</li>")
                     .append("<li><b>MDS:</b> "+item.data.mds+"</li>")
                     .append("<li><b>Условие:</b> "+(item.data.main == 0 ? item.data.condition : "без условия")+"</li>");
                     
                     if (item.data.exception != "")
                     {
                        $(ul).append("<li><b>Исключение:</b> "+item.data.exception+"</li>")
                     }

                     o.children("td.best").html(ul);
                     o.children("td.date").text(item.data.date_added_string);
                  }
               });
            });
         });
      </script>';

      return $content;
   }

   public function drawGrid()
   {
      $this->page .= View::factory('header')->render();

      if ($this->checkAuth())
      {
         $this->makeTopMenu();
         $this->page .= $this->getGrid();
      }

      $this->page .= View::factory('footer')->render();

      $this->errorsPublisher();
      $this->publish();
   }

   public function getGrid()
   {
      $id = (int) $_GET['id'];
      $year = (int) $_GET['year'];

      $content = '
      <div class="location">
         <a href="/?mod=mds">Market Days Supply</a> / '.$this->_config['items'][$id]['name'].' / '.$year.'
      </div>
      <div class="location">
         <form action="/?mod=mds&amp;action=search&amp;method=add" method="post" id="addform">
            <div id="caption">
               <table>
                  <!-- <caption><h2>Новый поиск</h2></caption> -->
                  <tr>
                     <td><label for="condition">Условие:</label></td>
                     <td><label for="exception">Исключение:</label></td>
                     <td colspan="2">&nbsp;</td>
                  </tr>
                  <tr>
                     <td><input type="text" name="condition" id="condition" size="40" value="" /></td>
                     <td><input type="text" name="exception" id="exception" size="40" value="" /></td>
                     <td><input type="submit" name="submit" id="submit" value="Готово" /></td>
                     <td><span id="examples">Примеры</span></td>
                  </tr>
               </table>
               <small>Допускается использование только <b>тире</b> и <b>пробелов</b></small>
            </div>
            <table class="example hidden">
               <tr>
                  <td>navigation|(awd,dvd)</td>
                  <td> = </td>
                  <td>navigation <b>OR</b> (awd <b>AND</b> dvd)</td>
               </tr>
               <tr>
                  <td>(navigation|(awd,dvd))|(navigation,awd)</td>
                  <td> = </td>
                  <td>(navigation <b>OR</b> (awd <b>AND</b> dvd)) <b>OR</b> (navigation <b>AND</b> awd)</td>
               </tr>
               <tr>
                  <td>(awd,6 cyl)|(awd,v-6 cyl)</td>
                  <td> = </td>
                  <td>(awd <b>AND</b> "6 cyl") <b>OR</b> (awd <b>AND</b> "v-6 cyl")</td>
               </tr>
            </table>
         </form>
         <div id="results" class="hidden">
            <b>Результаты</b><br />
            <b>VPD:</b> <span class="results-vpd">-</span>,
            <b>MDS:</b> <span class="results-mds">-</span>
            <b>Online:</b> <span class="results-online">-</span>
            <b>Offline:</b> <span class="results-offline">-</span>
            <br />
            <a href="'.$_SERVER['REQUEST_URI'].'">Сохранить</a>
            | <a class="results-remove" href="#">Не сохранять</a>
            | <a class="results-stat" href="#">Статистика</a>
         </div>
      </div>
      <table width="970" border="0" cellspacing="0" cellpadding="0" class="list" id="conditions">
         <thead>
            <tr class="title">
               <td>'.$this->translate->_('Условие').'</td>
               <td>'.$this->translate->_('Исключение').'</td>
               <td>'.$this->translate->_('Маниш в день').'</td>
               <td>'.$this->translate->_('MDS').'</td>
               <td>'.$this->translate->_('Online').'</td>
               <td>'.$this->translate->_('Offline').'</td>
               <td>'.$this->translate->_('Добавлен').'</td>
               <td>&nbsp;</td>
            </tr>
         </thead>
         <tbody>
         </tbody>
      </table>
      <br />';

      $content .= '
      <script type="text/javascript">
         $("#examples").click(function()
         {
            $("table.example").toggle();
         });
         
         $("#submit").click(function()
         {
            var condition = $("#condition");

            if ($.trim(condition.val()) == "")
            {
               alert("Условие не должно быть пустым");
               condition.val("").focus();
               return false;
            }

            var params =
            {
               condition:condition.val(),
               exception:$("#exception").val(),
               parent_id:'.$id.',
               year:'.$year.'
            };

            $.getJSON("http://'.$_SERVER['SERVER_NAME'].'/cron/calc/mds/export/add.json", params, function(json)
            {
               if (json.status == "OK")
               {
                  $("#addform").hide();
                  $("#results").show();
                  $(".results-vpd").text(json.vpd);
                  $(".results-mds").text(json.mds);
                  $(".results-online").text(json.online);
                  $(".results-offline").text(json.offline);
                  $(".results-remove").attr("href", "/?mod=mds&action=remove&id="+json.search_id+"&pid="+json.parent_id+"&year="+json.year);
                  $(".results-stat").attr({target:"_blank", href:"/?mod=mds&action=stat&id="+json.parent_id+"&year="+json.year+"&search="+json.search_id});
               }
               else
               {
                  alert("Запись с такими условиями уже существует!");
               }
            });
            return false;
         });
         
         var baseurl = "http://'.$_SERVER['SERVER_NAME'].'/cron/calc/mds/export/view.json";
         $.getJSON(baseurl, {id:'.$id.', year:'.$year.'}, function(json)
         {
            var rows = "";
            var c = "rowA rowB"

            $.each(json, function(id, values)
            {
               rows += "<tr class=\""+c+"\">";

               if (values.main == 0)
               {
                  rows += "<td class=\"sm\">"+values.condition+"</td>";
                  rows += "<td class=\"sm\">"+values.exception+"</td>";
               }
               else
               {
                  rows += "<td class=\"sm\" colspan=\"2\"><b>без условия (все)</b></td>";
               }

               rows += "<td class=\"sm\">"+values.vpd+"</td>";
               rows += "<td class=\"sm\"><b>"+values.mds+"</b></td>";
               rows += "<td class=\"sm\">"+values.online+"</td>";
               rows += "<td class=\"sm\">"+values.offline+"</td>";
               rows += "<td class=\"sm\">"+values.date_added_string+"</td>";
               rows += "<td class=\"sm\">";
               rows += "<a href=\"/?mod=mds&action=stat&id='.$id.'&year='.$year.'&search="+id+"\">Статистика</a>";
               
               if (values.main == 0)
               {
                  rows += " | <a class=\"remove\" href=\"/?mod=mds&action=remove&id="+id+"&pid='.$id.'&year='.$year.'\">Удалить</a>";
               }

               rows += "</td>";
               rows += "</tr>";
               
               c = (c == "rowA") ? "rowA rowB" : "rowA";
            });
            
            $(rows).appendTo("#conditions");
            
            $("a.remove").click(function()
            {
               return confirm("Вы уверены?");
            });
         });
      </script>
      ';

      return $content;
   }
   
   public function drawStat()
   {
      $this->page .= View::factory('header')->render();

      if ($this->checkAuth())
      {
         $this->makeTopMenu();
         $this->page .= $this->getStat();
      }

      $this->page .= View::factory('footer')->render();

      $this->errorsPublisher();
      $this->publish();
   }

   public function getStat()
   {
      $id = (int) $_GET['id'];
      $year = (int) $_GET['year'];
      $search = isset($_GET['search']) ? (int) $_GET['search'] : null;

      $content = '
      <div class="location">
         <a href="/?mod=mds">Market Days Supply</a> /
         '.$this->_config['items'][$id]['name'].' /
         <a href="/?mod=mds&amp;action=grid&amp;id='.$id.'&amp;year='.$year.'">'.$year.'</a>';

      if ($search !== null)
      {
         $query = mysql_query("SELECT `condition`, `exception` FROM `ccl_mds_searches` WHERE `id` = ".$search." LIMIT 1");
         $row = mysql_fetch_object($query);

         if ((string) $row->condition != '')
         {
            $content .= '/ условие: <b>'.$row->condition.'</b> '.($row->exception ? ', исключение: <b>'.$row->exception.'</b>' : '');
         }
      }

      $content .= '</div>';

      $content .= '
      <div id="grid-wrapper">
         <div id="grid-col1">
            <table width="100%" id="table-online" border="0" cellspacing="0" cellpadding="0" class="list">
               <caption>Online (<span id="online-count">0</span>)</caption>
               <thead>
                  <tr class="title">
                     <td>№</td>
                     <td>Модель</td>
                     <td>Записан</td>
                  </tr>
               </thead>
               <tbody></tbody>
            </table>
         </div>
         <div id="grid-col2">
            <table width="100%" id="table-offline" border="0" cellspacing="0" cellpadding="0" class="list">
               <caption>Offline (<span id="offline-count">0</span>)</caption>
               <thead>
                  <tr class="title">
                     <td>№</td>
                     <td>Модель</td>
                     <td width="70">Записан</td>
                     <td width="70">Продан</td>
                  </tr>
               </thead>
               <tbody></tbody>
            </table>
         </div>
         <div class="clear"></div>
      </div>
      <br />
      ';

      $content .= '
      <script type="text/javascript">
         function buildRows(data, online)
         {
            var rows = "";
            var c = "rowA rowB"

            $.each(data, function(i, values)
            {
               rows += "<tr class=\""+c+"\">";
               rows += "<td class=\"sm\">"+parseInt(i+1)+"</td>";
               rows += "<td class=\"sm\"><a href=\"http://www.autotrader.ca/"+values.url+"/?ms=trucks_vans\" target=\"_blank\">"+values.name+"</a></td>";
               rows += "<td class=\"sm\">"+values.date_added+"</td>";

               if (! online)
               {
                  rows += "<td class=\"sm\">"+(values.date_sold ? values.date_sold : "-")+"</td>";
               }

               rows += "</tr>";

               c = (c == "rowA") ? "rowA rowB" : "rowA";
            });

            return rows;
         }
         
         params = {id:'.$id.', year:'.$year.($search !== null ? ', search:'.$search : '').'};

         $.getJSON("http://'.$_SERVER['SERVER_NAME'].'/cron/calc/mds/export/stat.json", params, function(json)
         {
            var onlineRows = buildRows(json.online.items, true);
            $(onlineRows).appendTo("#table-online");
            $("#online-count").text(json.online.count);

            var offlineRows = buildRows(json.offline.items);
            $(offlineRows).appendTo("#table-offline");
            $("#offline-count").text(json.offline.count);
         });
      </script>
      ';
      
      return $content;
   }
   
}
