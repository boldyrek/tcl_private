<?php

class Core extends Proto {

   protected $_config;

   public function __construct()
   {
      $this->_config = Zend_Registry::get('config');

      ini_set('display_errors', 'on');

      set_exception_handler(array($this, 'exception_handler'));

      parent::__construct();
   }

   public function exception_handler(Exception $exception)
   {
      echo 'Uncaught exception: '.$exception->getMessage();
      echo '<pre>';
      print_r($exception->getTraceAsString());
      // print_r($exception->getTrace());
      echo '</pre>';
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
      $this->page .= '<div class="location">'.ucfirst(MODULE).' / Типы поиска</div>';

      $this->page .= '<div id="list">';
      $this->page .= '<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">';
      $this->page .= '<tr class="title sortButtons">';
      $this->page .= '<td width="30">'.$this->translate->_('№').'</td>';
      $this->page .= '<td>'.$this->translate->_('Название').'</td>';
      $this->page .= '<td>'.$this->translate->_('Условия поиска').'</td>';
      $this->page .= '<td>'.$this->translate->_('Активность').'</td>';
      $this->page .= '</tr>';

      $class = "rowA rowB";

      foreach ($this->_config['root']['targets'] AS $id => $target)
      {
         $query = mysql_query("SELECT COUNT(id) AS `total` FROM `ccl_grabber_cars` WHERE `target_id` = '{$id}'") or die(mysql_error());
         $cars = mysql_fetch_object($query);
         $total_cars = (int) $cars->total;

         // var_dump($cars); exit;

         $this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">';
         $this->page .= '<td class="sm">'.$id.'</td>';
         $this->page .= '<td class="sm">';
         $this->page .= ( $total_cars > 0) ? '<a href="/?mod='.MODULE.'&amp;action=grid&amp;target='.$id.'" title="'.$target['name'].'">'.$target['name'].'</a>' : $target['name'];
         $this->page .= '</td>';

         $this->page .= '<td class="sm">';

         if (isset($target['cond']))
         {
            $this->page .= '<ul>';
            foreach ($target['cond'] AS $key => $value)
            {
               $this->page .= '<li><b>'.$key.':</b> '.$value.'</li>';
            }
            $this->page .= '<ul>';
         }
         else
         {
            $this->page .= '-';
         }

         $this->page .= '</td>';

         $vin = null;

         if ($total_cars > 0)
         {
            $query = mysql_query("SELECT v.vincode, v.date_added FROM `ccl_grabber_vins` v, `ccl_grabber_cars` c WHERE v.vincode = c.vincode AND v.target_id = '{$id}' ORDER BY v.date_added DESC LIMIT 1");

            if (mysql_num_rows($query))
            {
               $vin = mysql_fetch_object($query);
            }
         }

         $this->page .= '<td class="sm">';

         $query = mysql_query("SELECT `date_last_updated` AS `date` FROM `ccl_grabber_statuses` WHERE `target_id` = '{$id}' ORDER BY `id` DESC LIMIT 1");
         $last_update = mysql_fetch_object($query);

         // var_dump($vin); exit;

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

         $this->page .= '<script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>';
         $this->page .= '<div class="location"><a href="/?mod='.MODULE.'">'.ucfirst(MODULE).'</a> / '.$this->_config['root']['targets'][$id]['name'].'</div>';
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
         $this->page .= '<td>'.$this->translate->_('Ссылка').'</td>';
         $this->page .= '<td>&nbsp;</td>';
         $this->page .= '</tr>';

         $sql = 'SELECT * FROM `ccl_grabber_cars` WHERE `target_id` = '.$id.' ORDER BY `'.(empty($_GET['sortby']) ? 'date_added' : $_GET['sortby']).'` '.(empty($_GET['order']) ? 'DESC' : $_GET['order']).' '.$pages['qlimit'];

         $query = mysql_query($sql) OR die('Неверно указаны параметры запроса');

         $class = "rowA rowB";

         $i = 1;

         $sources = $this->_config['root']['sources'];
         $colors = $this->_config['colors'];
         $search_ids = $this->_config['search'];

         while ($row = mysql_fetch_object($query))
         {
            $color_id = $search_ids[$row->search_id]['colors'];

            $exterior = (! empty($row->exterior_code) ? '['.$row->exterior_code.'] '.$colors[$color_id]['exterior'][$row->exterior_code] : (!empty($row->exterior) ? $row->exterior : '-'));

            $highlight = FALSE;

            if (! empty($row->interior_code))
            {
               $interior = '['.$row->interior_code.'] '.$colors[$color_id]['interior'][$row->interior_code];

               if (isset($colors[$color_id]['hl']))
               {
                  $highlight = in_array($row->interior_code, $colors[$color_id]['hl']);
               }
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
               <td class="sm">'.(! empty($row->url) ? '<a href="'.$row->url.'" target="_blank">'.$this->translate->_('Ссылка').'</a>' : '-').'</td>
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
      </style>
      ';
   }

}
