<?php

class Controller extends Proto {

   protected $_config;

   const POSITION = 4;

   public function __construct()
   {
      $this->_config = Zend_Registry::get('config')->toArray();

      parent::__construct();
   }

   public function removeSearch()
   {
      if ($this->checkAuth())
      {
         $query = mysql_query("DELETE FROM `ccl_ptm_searches` WHERE `id` = ".$_GET['id']);
      }

      $this->redirect('/?mod=ptm&action=grid&id='.$_GET['pid'].'&year='.$_GET['year']);
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
      $content .= '<div class="location">Price To Market</div>';
      $content .= '<table width="350" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
      $content .= '<thead>';
      $content .= '<tr class="title">';
      $content .= '<td width="100">'.$this->translate->_('Годы выпуска').'</td>';
      $content .= '<td width="50">'.$this->translate->_('Всего').'</td>';
      $content .= '<td>'.$this->translate->_('Последнее обновление').'</td>';
      $content .= '</tr>';
      $content .= '</thead>';
      $content .= '</table>';

      foreach ($this->_config['items'] AS $id => $item)
      {
         $content .= '<h2>'.$id.'. '.$item['name'].'</h2>';

         $content .= '<table width="350" border="0" cellspacing="0" cellpadding="0" class="list vlines" id="grid-'.$id.'">';
         $content .= '<tbody>';

         $class = "rowA rowB";
         
         foreach ($item['year'] AS $year)
         {
            $content .= '<tr class="'.$class.'" id="row-'.$id.'-'.$year.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">';
            $content .= '<td class="sm" width="100"><a href="/?mod=ptm&amp;action=grid&amp;id='.$id.'&amp;year='.$year.'">'.$year.'</a></td>';
            $content .= '<td class="sm total" width="50" align="center">-</td>';
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
         var baseurl = "http://'.$_SERVER['SERVER_NAME'].'/cron/calc/ptm/export/grid.json";
         $.getJSON(baseurl, function(json)
         {
            $.each(json, function(key, values)
            {
               $.each(values, function(year, values)
               {
                  var o = $("#grid-"+key+" tr#row-"+key+"-"+year);
                  o.children("td.total").html(values.total > 2 ? "<a href=\"/?mod=ptm&action=chart&id="+key+"&year="+year+"\">"+values.total+"</a>" : values.total);
                  o.children("td.date").text(values.last_update);
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
      
      $content .= '<div class="location"><a href="/?mod=ptm">Price To Market</a> / '.$this->_config['items'][$id]['name'].' / '.$year.'</div>';
      $content .= '
      <div class="location">
         <form action="/?mod=ptm&amp;action=add" method="post" id="addform">
            <div id="caption">
               <table>
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
            <b>Всего:</b> <span class="results-total">-</span>,
            <b>Цена:</b> <span class="results-price">-</span>,
            <b>Пробег:</b> <span class="results-mileage">-</span>
            <br />
            <a href="'.$_SERVER['REQUEST_URI'].'">Сохранить</a>
            | <a class="results-remove" href="#">Не сохранять</a>
            | <a class="results-chart" href="#">Статистика</a>
            <!--
            <table border="0" cellspacing="0" cellpadding="0" width="200">
               <tbody>
                <tr>
                   <td align="right">Equation:</td>
                   <td><b>Y = <span class="Slope">0</span>*x + <span class="YInt">0</span></b></td>
                </tr>
                <tr>
                   <td align="right"><b>T:</b></td>
                   <td><span class="SlopeTVal">0</span></td>
                </tr>
                <tr>
                   <td align="right"><b>Prob &gt; T:</b></td>
                   <td><span class="SlopeProb">0</span></td>
                </tr>
               </tbody>
            </table>
            -->
         </div>
      </div>
      ';
      $content .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list" id="conditions">';
      $content .= '<thead>';
      $content .= '<tr class="title">';
      $content .= '<td>'.$this->translate->_('Условие').'</td>';
      $content .= '<td>'.$this->translate->_('Исключение').'</td>';
      $content .= '<td>'.$this->translate->_('Всего').'</td>';
      // $content .= '<td>'.$this->translate->_('Цена').'</td>';
      // $content .= '<td>'.$this->translate->_('Пробег').'</td>';
      $content .= '<td>&nbsp;</td>';
      $content .= '</tr>';
      $content .= '</thead>';
      $content .= '<tbody></tbody>';
      $content .= '</table>';

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

            $.getJSON("http://'.$_SERVER['SERVER_NAME'].'/cron/calc/ptm/export/add.json", params, function(json)
            {
               if (json.status == "OK")
               {
                  $("#addform").hide();
                  $("#results").show();
                  $(".results-total").text(json.total);
                  $(".results-price").text((json.total ? json.ptm.price : "-"));
                  $(".results-mileage").text((json.total ? json.ptm.mileage : "-"));
                  $(".results-remove").attr("href", "/?mod=ptm&action=remove&id="+json.search_id+"&pid="+json.parent_id+"&year="+json.year);
                  $(".results-chart").attr({target:"_blank", href:"/?mod=ptm&action=chart&id="+json.parent_id+"&year="+json.year+"&search="+json.search_id});
               }
               else
               {
                  alert("Запись с такими условиями уже существует!");
               }
            });
            
            return false;
         });
         
         var baseurl = "http://'.$_SERVER['SERVER_NAME'].'/cron/calc/ptm/export/view.json";
         $.getJSON(baseurl, {id:'.$id.', year:'.$year.'}, function(json)
         {
            var rows = "";
            var c = "rowA rowB"

            $.each(json, function(key, values)
            {
               rows += "<tr class=\""+c+"\">";
               rows += "<td class=\"sm\">"+values.condition+"</td>";
               rows += "<td class=\"sm\">"+values.exception+"</td>";
               rows += "<td class=\"sm\">"+values.total+"</td>";
               // rows += "<td class=\"sm\">"+(values.ptm ? values.ptm.price : "-")+"</td>";
               // rows += "<td class=\"sm\">"+(values.ptm ? values.ptm.mileage : "-")+"</td>";
               rows += "<td class=\"sm\">";
               rows += (values.total > 2 ? "<a href=\"/?mod=ptm&action=chart&id='.$id.'&year='.$year.'&search="+key+"\">Статистика</a> | " : "");
               rows += "<a class=\"remove\" href=\"/?mod=ptm&action=remove&id="+key+"&pid='.$id.'&year='.$year.'\">Удалить</a>";
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

   public function drawChart()
   {
      $this->page .= View::factory('header')->render();

      if ($this->checkAuth())
      {
         $this->makeTopMenu();
         $this->page .= $this->getChart();
      }

      $this->page .= View::factory('footer')->render();

      $this->errorsPublisher();
      $this->publish();
   }

   public function getChart()
   {
      $id = (int) $_GET['id'];
      $year = (int) $_GET['year'];
      $search = isset($_GET['search']) ? (int) $_GET['search'] : null;

      $content = '
      <div class="location">
         <a href="/?mod=ptm">Price To Market</a>
         / '.$this->_config['items'][$id]['name'].'
         / <a href="/?mod=ptm&amp;action=grid&amp;id='.$id.'&amp;year='.$year.'">'.$year.'</a>';

      if ($search !== null)
      {
         $query = mysql_query("SELECT `condition`, `exception` FROM `ccl_ptm_searches` WHERE `id` = ".$search." LIMIT 1");
         $row = mysql_fetch_object($query);

         $content .= ' / условие: <b>'.$row->condition.'</b> '.($row->exception ? ', исключение: <b>'.$row->exception.'</b>' : '');
      }

      $content .= '</div>';

      $content .= '<div id="grid-wrapper" class="hidden">';
      $content .= '<div id="grid-table">';
      $content .= '<table width="370" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
      $content .= '<thead>';
      $content .= '<tr class="title">';
      $content .= '<td>'.$this->translate->_('№').'</td>';
      $content .= '<td>'.$this->translate->_('Модель').'</td>';
      $content .= '<td>'.$this->translate->_('Цена').'</td>';
      $content .= '<td>'.$this->translate->_('Пробег').'</td>';
      $content .= '</tr>';
      $content .= '</thead>';
      $content .= '<tbody></tbody>';
      $content .= '</table>';

      $content .= '<table class="list" id="equation" border="0" cellspacing="0" cellpadding="0" width="370">
                   <tbody>
                      <tr class="rowA">
                         <td align="right" class="sm">Equation:</td>
                         <td class="sm"><b>Y = <span class="Slope">0</span>*x + <span class="YInt">0</span></b></td>
                      </tr>
                      <tr class="rowA">
                         <td align="right" class="sm"><b>T:</b></td>
                         <td class="sm"><span class="SlopeTVal">0</span></td>
                      </tr>
                      <tr class="rowA">
                         <td align="right" class="sm"><b>Prob &gt; T:</b></td>
                         <td class="sm"><span class="SlopeProb">0</span></td>
                      </tr>
                   </tbody>
                   </table>
      ';

      $content .= '</div>';

      $content .= '<div id="grid-chart-wrapper">';
      $content .= '  <div id="grid-chart">';
      $content .= '     <div id="grid-chart-loader"><img src="/img/chart-ajax-loader.gif" alt="" /><br />Please wait. Loading chart...</div>';
      $content .= '  </div>';
      $content .= '</div>';

      $content .= '<div class="clear"></div>';
      $content .= '</div><br />';

      $content .= '
      <script type="text/javascript" src="https://www.google.com/jsapi"></script>
      <script type="text/javascript">
         google.load("visualization", "1", {packages:["corechart"]});
         google.setOnLoadCallback(renderChart);

         /*
         function initChart(json)
         {
            var data = new google.visualization.DataTable();
            data.addColumn("number", "X");
            data.addColumn("number", "Price / Mileage");
            data.addColumn("number", "Price to market");

            var rows = "";
            var highlight_id = "row-"+json.ptm.price+"-"+json.ptm.mileage;

            $.each(json.data, function(i, item)
            {
               var row_id = "row-"+item.price+"-"+item.mileage;

               rows += "<tr class=\""+(row_id == highlight_id ? "highlight " : "")+"rowA\" id=\""+row_id+"\">";
               rows += "<td class=\"sm\">"+(i+1)+"</td>";
               rows += "<td class=\"sm\"><a href=\"http://www.autotrader.ca/"+item.url+"/?ms=trucks_vans\" target=\"_blank\">"+item.name+"</a></td>";
               rows += "<td class=\"sm\">"+item.price+"</td>";
               rows += "<td class=\"sm\">"+item.mileage+"</td>";
               rows += "</tr>";

               data.addRow([parseInt(item.price), parseInt(item.mileage), null]);
            });

            $(rows).appendTo("#grid-table table.vlines");

            data.addRow([json.ptm.price, null, json.ptm.mileage]);

            return data;
         }
         */
         
         function initChart(json)
         {
            var data = new google.visualization.DataTable();
            data.addColumn("number", "Price");
            data.addColumn("number", "Price / Mileage");
            data.addRows(json.data.length);

            var rows = "";

            $.each(json.data, function(i, item)
            {
               var row_id = "row-"+item.price+"-"+item.mileage;

               rows += "<tr class=\"rowA"+(item.mileage == 0 ? " highlight" : "")+"\" id=\""+row_id+"\">";
               rows += "<td class=\"sm\">"+(i+1)+"</td>";
               rows += "<td class=\"sm\"><a href=\"http://www.autotrader.ca/"+item.url+"/?ms=trucks_vans\" target=\"_blank\">"+item.name+"</a></td>";
               rows += "<td class=\"sm\">"+item.price+"</td>";
               rows += "<td class=\"sm\">"+(item.mileage == 0 ? "-" : item.mileage)+"</td>";
               rows += "</tr>";

               data.setValue(i, 0, parseInt(item.price));
               data.setValue(i, 1, parseInt(item.mileage));
            });

            $(rows).appendTo("#grid-table table.vlines");

            return data;
         }

         function renderChart()
         {
            var baseurl = "http://'.$_SERVER['SERVER_NAME'].'/cron/calc/ptm/export/chart.json";
            var params = {id:'.$id.', year:'.$year.($search !== null ? ', search:'.$search : '').'};
            $.getJSON(baseurl, params, function(json)
            {
               if ($.isEmptyObject(json) == false)
               {
                  $("#grid-wrapper").show();

                  $("table#equation span.YInt").text(json.SLR.YInt);
                  $("table#equation span.Slope").text(json.SLR.Slope);
                  $("table#equation span.SlopeTVal").text(json.SLR.SlopeTVal);
                  $("table#equation span.SlopeProb").text(json.SLR.SlopeProb);

                  var data = initChart(json);
                  var chart = new google.visualization.ScatterChart(document.getElementById("grid-chart"));
                  chart.draw(data,
                  {
                     width: 600,
                     height: 400,
                     // title: "Mileage vs. Price comparison",
                     hAxis: {title: "Price", minValue: 0, maxValue: json.max.price},
                     vAxis: {title: "Mileage", minValue: 0, maxValue: json.max.mileage},
                     legend: "top",
                     pointSize: 8
                  });
               }
               else
               {
                  $("#grid-wrapper").show().addClass("error").html("Ничего не найдено");
               }
            });
         }
      </script>
      ';

      return $content;
   }

}
