<?

require_once('class.Carscomment.php');

class CarsForm extends Proto {

   var $clients;
   var $transporters;
   var $container;
   var $content;
   var $files;
   var $invoice;
   var $car_id;
   var $ports;
   var $cr;
   var $inspection;
   var $payments;

   public function drawContent()
   {
      $this->page .= $this->templates['header'];
      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->getContent();
      }
      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   public function getContent()
   {
      //require_once ('templates/form.car.php');

      if (!isset($_GET['add']))
      {
         $this->car_id = intval($_GET['car_id']);

         $sql = "SELECT ccl_".ACCOUNT_SUFFIX."cars.*,ccl_".ACCOUNT_SUFFIX."cars.sold as car_is_sold,ccl_".ACCOUNT_SUFFIX."customers.name, ccl_".ACCOUNT_SUFFIX."customers.dealer as isdealer, ccl_".ACCOUNT_SUFFIX."customers.id as buyer_id,
         ccl_".ACCOUNT_SUFFIX."forsale.id as sell_id, ccl_".ACCOUNT_SUFFIX."forsale.price as sell_price,  ccl_".ACCOUNT_SUFFIX."forsale.dealer_price as dealer_sell_price, ccl_".ACCOUNT_SUFFIX."forsale.comment as sell_comment, ccl_".ACCOUNT_SUFFIX."forsale.active_through as sell_active_through, ccl_".ACCOUNT_SUFFIX."forsale.sold as sold, ccl_".ACCOUNT_SUFFIX."transporters.name as sup_name
         FROM `ccl_".ACCOUNT_SUFFIX."customers`
         RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
         ON (ccl_".ACCOUNT_SUFFIX."customers.id=ccl_".ACCOUNT_SUFFIX."cars.buyer)
         LEFT JOIN `ccl_".ACCOUNT_SUFFIX."forsale`
         ON (ccl_".ACCOUNT_SUFFIX."forsale.car = ccl_".ACCOUNT_SUFFIX."cars.id)
         LEFT JOIN `ccl_".ACCOUNT_SUFFIX."transporters`
         ON (ccl_".ACCOUNT_SUFFIX."transporters.id = ccl_".ACCOUNT_SUFFIX."cars.transporter)
         WHERE ccl_".ACCOUNT_SUFFIX."cars.id ='".$this->car_id."'";

         $this->content = @mysql_fetch_array($this->mysqlQuery($sql));
      }
      else
      {
         $this->car_id = '0';
         $this->content['id'] = '0';
         $this->content['ready'] = '0';
         $this->content['type'] = 1;
         $this->content['country_id'] =121;
      }

      if ($this->content['id'] == '')
      {
         $this->page .= '<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Автомобиль с такими параметрами в базе не обнаружен').'</div>';
      }
      else
      {
         if (isset($_GET['success']))
            $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
         $this->getAdditional();
         $this->page .= $this->car_edit();
         if ($this->permissions['cars'] == 1)
         {
            $comment = new CarsComment();
            $comment->setCarOwnerId($this->content['buyer'], $this->content['reciever'], $this->content['dealer']);
            $this->page .= $comment->getContent();
         }
      }
   }

   private function getAdditional()
   {
      $this->clients = $this->getCustomersList();

      $this->transporters = $this->mysqlQuery(
                      "SELECT id,name
		FROM `ccl_".ACCOUNT_SUFFIX."transporters` 
		WHERE 1 ORDER BY `name` ASC");
      if ($this->exists($this->content['container']) and $this->content['container'] != '0')
      {
         $this->container = @mysql_fetch_array($this->mysqlQuery(
                                 "SELECT id,number,bishkek,arrived
			FROM `ccl_".ACCOUNT_SUFFIX."containers`
			WHERE `id` = ".$this->content['container']));
      }

      $this->marka = $this->mysqlQuery(
                      "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."marka` ORDER BY `name` ASC");

      if ($this->exists($this->content['car_model']) and $this->content['car_model'] != '0')
      {
         $this->model = $this->mysqlQuery(
                         "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."model` WHERE marka_id=".$this->content['car_marka']." ORDER BY `name` ASC");
      }
      if (!isset($_GET['add']))
      {
         $this->files = $this->mysqlQuery(
                         "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos`
			WHERE `car` = '".$this->car_id."' ORDER BY `folder` DESC, `id` DESC");

         $this->expeditorphoto = $this->mysqlQuery(
                         "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."expeditors_photo`
			WHERE `car` = '".$this->car_id."' ORDER BY `id` DESC");

         $this->cr = $this->mysqlQuery(
                         "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_cert`
			WHERE `car` = '".$this->car_id."' ORDER BY `id` DESC");

         $this->invoice = mysql_fetch_array($this->mysqlQuery("
			SELECT id
			FROM `ccl_".ACCOUNT_SUFFIX."invoices` 
			WHERE `carid` 
			= '".$this->car_id.";' LIMIT 1"));

         $this->content['invoice_file'] = $this->invoice['id'];
         $this->ports = $this->mysqlQuery(
                         "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."ports` 
			WHERE 1
			ORDER BY `name` ASC");

         $this->places = $this->place; // Местонахождения автомобилей
         $this->tplaces = $this->tplace; // Местонахождения тайтлов ?!?!?!

         $this->places3 = $this->mysqlQuery(
                         "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."places`");


         $this->inspection = $this->mysqlQuery(
                         "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."inspections`
			WHERE ccl_".ACCOUNT_SUFFIX."inspections.car = '".$this->car_id."'
			ORDER BY `id` DESC");

         $this->adddoc = $this->mysqlQuery(
                         "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."adddoc`
			WHERE ccl_".ACCOUNT_SUFFIX."adddoc.car = '".$this->car_id."'
			ORDER BY `id` DESC");

         $this->payments = mysql_fetch_array($this->mysqlQuery("
			SELECT SUM(amount) as amount FROM `ccl_".ACCOUNT_SUFFIX."accounting`
			WHERE `car` = '".$this->car_id."' AND `type`=1"));

         $this->autocheck = $this->mysqlQuery(
                         "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."autocheck`
			WHERE ccl_".ACCOUNT_SUFFIX."autocheck.car = '".$this->car_id."'
			ORDER BY `id` DESC");
      }
   }

   function car_edit()
   {
      //адрес формы
      if (!isset($_GET['add']))
         $form_link = '&sw=save&id='.$this->car_id;
      else
         $form_link = '&sw=add';


      //проверка, может быть форма открывается после попытки добавить автомобиль с уже имеющимся в базе вин кодом
      if (isset($_GET['exists']))
      {
         $this->page .= '<div class="warn" style="width:660px;">'.$this->translate->_('Автомобиль с таким вин кодом').' ('.$_SESSION['car_exists']['frame'].')
			<br>'.$this->translate->_('уже есть в базе').': '.$_SESSION['car_exists']['model'].', '.$this->translate->_('владелец').' '.$_SESSION['car_exists']['buyer'].'!</div>';
         $content = $_SESSION['carForm'];
      }


      //формирование списка покупателей и дилеров
      $num = @mysql_num_rows($this->clients);
      $i = 1;
      while ($i <= $num)
      {
         $line = mysql_fetch_array($this->clients);
         //все клиенты
         $clients_list.='<option value="'.$line['id'].'"';

         if ($this->content['buyer_id'] == $line['id'])
         {
            $clients_list.=' selected="selected"';
            $ownerInfo = '<input type="hidden" value="'.$line['id'].'" name="owner" id="owner">
				<input type="hidden" value="'.$line['id'].'" name="buyer">
				'.stripslashes($line['name']);
            if ($this->content['isdealer'] == 1)
               $carsDealer = '<input type="hidden" name="dealer" value="'.$this->content['buyer_id'].'">
				';
            else
               $carsDealer = '<input type="hidden" name="dealer" value="'.$line['mydealer'].'">
				';
         }

         $clients_list.='>'.stripslashes($line['name']).'</option>
			';

         $hiddenFields .= '
			<input type="hidden" name="isDealer'.$line['id'].'" value="'.$line['dealer'].'"><input type="hidden" name="myDealer'.$line['id'].'" value="'.$line['mydealer'].'">';

         $i++;
      }
      $recievers_list = $this->getRecieversList();
      if ($this->content['buyer'] == '0' or isset($_GET['add']))
      {
         $car_owner = '<select name="buyer" tabindex="1" id="customerSelect">
		    <option value="0" selected="selected"> '.$this->translate->_('не выбран').' </option>
			'.$clients_list.'
		      </select>';
      }
      else
         $car_owner = '<span id="customerSelect"></span>'.
                 $ownerInfo;
      //#######################################
      //фотографии автомобиля
      $photos = array();
      $num = @mysql_num_rows($this->files);
      if ($num > 0)
      {
         $j = 1;
         while ($j <= $num)
         {
            $line = mysql_fetch_array($this->files);

            if ($line['id'] == $this->content['top_photo'])
               $top_bg = '#d2f4d9';
            else
               $top_bg = '';
            $photos[$line['folder']] .= $this->wrapFile('
				<input type="radio" name="top_photo" value="'.$this->car_id.'" onClick="saveTopPhoto(\''.$line['id'].'\')" '.($line['id'] == $this->content['top_photo'] ? ' checked="checked"' : '').'>
				<a href="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/thumb/'.$line['file'].'" border="0"></a>
				<!--'.($line['filename'] != '' ? "<div style='font-size:9px;'>{$line['filename']}</div>" : '').'-->
				<a href="'.$this->root_path.'?mod=cars&sw=delete&what=photo&name='.$line['file'].'&chk='.$this->car_id.'" class="delete" onclick="return confirm(\''.$this->translate->_('Действительно удалить?').'\')">'.$this->translate->_('удалить').'</a>', $top_bg);
            $j++;
         }

         foreach ($this->photo_folders as $k => $v)
         {

            if ($photos[$k] != '')
            {
               $photo_list .= '
                  <table id="form-pictures" width="100%" cellspacing="0" cellpadding="0" style="border-bottom:1px solid #ccc; margin-bottom:10px;">
                  <tr>
                     <td bgcolor="#eeeeee" align="center" class="rowB">'.$v.'</td>
                  </tr>
		  <tr>
                     <td>'.$photos[$k].'</td>
                  </tr>
                  </table>';
            }
         }
      }
      else
         $photo_list = '';



      //фотографии автомобиля от экспедитора
      if (mysql_num_rows($this->expeditorphoto))
      {
         $expeditorphoto = '<table width="100%" class="list">
			<tr class="rowB"><td class="title"><b>'.$this->translate->_('Фотографии экспедитора').'</b><tr><td>';
         while ($line = mysql_fetch_array($this->expeditorphoto))
         {
            $expeditorphoto .= $this->wrapFile('<a href="'.$this->root_path.'upload/expeditors_photo/'.$line['file'].'.jpg" target="_blank" alt="'.$line['descr'].'"><img src="'.$this->root_path.'upload/expeditors_photo/'.$line['file'].'.thumb.jpg" border=0></a><br><a href="'.$this->root_path.'?mod=cars&sw=delete&what=expeditorsphoto&name='.$line['file'].'&car='.$this->car_id.'" class="delete" onclick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить это фото?').'\')">'.$this->translate->_('удалить').'</a>');
         }
         $expeditorphoto .= '</table>';
      } else
         $expeditorphoto = '';


      //#######################################
      //Отчет о состоянии автомобиля или CR
      $num = @mysql_num_rows($this->cr);
      $cr_list = '';
      if ($num > 0)
      {
            $index = 1;
         while ($line = mysql_fetch_array($this->cr))
         {
            $ico = fileIco($line['file']);
            $cr_list .= $this->wrapFile('<a href="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/'.$line['file'].'" target="_blank">
				<img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a>
				'.($line['filename'] != '' ? "<div class='fileBlockText'>{$line['filename']}</div>" : '').'
				<a href="'.$this->root_path.'?mod=cars&sw=delete&what=certificate&name='.$line['file'].'" class="delete" onclick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить Отчет о состоянии автомобиля или CR?').'\')">'.$this->translate->_('удалить').'</a>');
             if ($index%5==0) $adddoc_file .="<div class='clearbox'></div>";
             $index++;
         }
      }
      //#######################################
      //форма выбора поставщиков
      $sup_list = buildSelect($this->transporters, 'transporter', $this->content['transporter'], $this->translate->_('не выбран'), '15');
      $place_list1 = buildSelectArray($this->places, 'place1', $this->content['place_id1'], $this->translate->_('пусто поле'), '17');
      $place_list2 = buildSelectArray($this->tplaces, 'place2', $this->content['place_id2'], $this->translate->_('пусто поле'), '18');
      $place_list3 = buildSelect($this->places3, 'place3', $this->content['place_id3'], $this->translate->_('не выбрано'), '19');
      //#######################################
      $marka = buildSelect($this->marka, 'car_marka', $this->content['car_marka'], $this->translate->_('не выбрано'), '2');
      if ($this->model)
      {
         $model = buildSelect($this->model, 'car_model', $this->content['car_model'], $this->translate->_('не выбрано'), '3');
      }
      else
      {
         $model = buildSelectArray(array(), 'car_model', $this->content['car_model'], $this->translate->_('не выбрано'), '3');
      }


      //вывод портов для выбора в зависимости от поставщика

      $num = @mysql_num_rows($this->ports);
      if ($num > 0)
      {
         $ports_list = buildSelect($this->ports, 'port', $this->content['port'], $this->translate->_('не выбран'), '16');
      }
      else
         $ports_list = $this->translate->_(' нет портов! ');


      //#######################################
      //готовим ссылку на инвойс этого автомобиля

      if ($this->content['invoice_file'] != '')
      {
         $invoiceLink .= '
				<a href="'.$this->root_path.'?mod=invoices&sw=file&inv_id='.$this->content['invoice_file'].'" target="_blank"><img src="'.$this->root_path.'img/ccl/doc_ico.gif" align="absmiddle" hspace="3" border="0">'.$this->translate->_('инвойс').'</a><br>';
      }
      else
         $invoiceLink = $this->translate->_('нет инвойса');


      //#############################
      //дней со дня покупки
      if (isset($_GET['add']))
         $this->content['buy_date'] = date("Y-m-d");
      else
      {
         if ($this->content['buy_date'] != '' and $this->content['buy_date'] != '0000-00-00')
         {
            if ($this->container['bishkek'] != '0000-00-00' and $this->container['arrived'] == '1')
            {
               $carArriveDate = explode('-', $this->container['bishkek']);
            }
            else
               $carArriveDate = explode('-', date("Y-m-d"));
            $buyDate = explode("-", $this->content['buy_date']);


            $startPeriod = mktime(0, 0, 0, $buyDate[1], $buyDate[2], $buyDate[0]);
            $endPeriod = mktime(0, 0, 0, $carArriveDate[1], $carArriveDate[2], $carArriveDate[0]);

            $daysPassed = round(($endPeriod - $startPeriod) / (60 * 60 * 24));

            $pastTime = calc_period($startPeriod, $endPeriod);

            /*
              //считаем сколько месяцев прошло
              $month = 0;
              while($daysPassed>30) {
              if($daysPassed>30) {
              $months++;
              $daysPassed = $daysPassed-30;
              }
              }
             */

            $monthLang = array(
               '1' => $this->translate->_('месяц'),
               '2' => $this->translate->_('месяца'),
               '3' => $this->translate->_('месяца'),
               '4' => $this->translate->_('месяца'),
               '5' => $this->translate->_('месяцев'),
               '6' => $this->translate->_('месяцев'),
               '7' => $this->translate->_('месяцев'),
               '8' => $this->translate->_('месяцев'),
               '9' => $this->translate->_('месяцев'),
               '10' => $this->translate->_('месяцев'),
               '11' => $this->translate->_('месяцев'),
               '12' => $this->translate->_('месяцев'));

            $daysLang = array(
               '0' => $this->translate->_('дней'),
               '1' => $this->translate->_('день'),
               '2' => $this->translate->_('дня'),
               '3' => $this->translate->_('дня'),
               '4' => $this->translate->_('дня'),
               '5' => $this->translate->_('дней'),
               '6' => $this->translate->_('дней'),
               '7' => $this->translate->_('дней'),
               '8' => $this->translate->_('дней'),
               '9' => $this->translate->_('дней'),
            );

            if (strlen($pastTime[3]) == 1)
               $daysText = $daysLang[$pastTime[3]];
            elseif (strlen($pastTime[3]) > 1)
            {
               if (substr($pastTime[3], 0, 1) == 1)
                  $daysText = $this->translate->_('дней');
               else
                  $daysText = $daysLang[substr($pastTime[3], 1, 1)];
            }

            $daysPassed = $this->translate->_('с покупки прошло').': '.($pastTime[4] != 0 ? '<b>'.$pastTime[4].'</b> '.$monthLang[$pastTime[4]].' '.$this->translate->_('и').' ' : '').'<b>'.$pastTime[3].'</b> '.$daysText;
         }
      }
      //####################
      // дней со дня готовности до дня, когда машину забрал транспортник
      if ($this->content['ready'] == '1')
      {
         if ($this->content['pickedup'] == '0')
         {

            $readyDate = explode("-", $this->content['date_ready']);

            $daysFromReadyPassed = round((mktime(0, 0, 0, date('m'), date('d'), date('Y')) - mktime(0, 0, 0, $readyDate[1], $readyDate[2], $readyDate[0])) / (60 * 60 * 24));

            //считаем сколько месяцев прошло
            $months = 0;
            while ($daysFromReadyPassed > 30)
            {
               if ($daysFromReadyPassed > 30)
               {
                  $months++;
                  $daysFromReadyPassed = $daysFromReadyPassed - 30;
               }
            }

            if (strlen($daysFromReadyPassed) == 1)
               $daysFromReadyText = $daysLang[$daysFromReadyPassed];
            elseif (strlen($daysFromReadyPassed) > 1)
            {
               if (substr($daysFromReadyPassed, 0, 1) == 1)
                  $daysFromReadyText = 'дней';
               else
                  $daysText = $daysLang[substr($daysFromReadyPassed, 1, 1)];
            }
            $daysFromReadyPassed = $this->translate->_('прошло').': '.($months != 0 ? '<b>'.$months.'</b> '.$monthLang[$months].' и ' : '').'<b>'.$daysFromReadyPassed.'</b> '.$daysFromReadyText;
         }
      }
      //####################################
      // заметка об автомобиле
      if ($_SESSION['user_type'] == '1' or $_SESSION['user_type'] == '7')
      {
         $admin_note = '<tr>
			<td class="title rowB rowA" align="right"><input type="checkbox" style="width:15px;" name="ready" '.($this->content['ready'] == '1' ? ' checked="checked"' : '').' id="ready"></td>
			<td class="rowA rowB title"><label for="ready" onclick="document.getElementById(\'date_ready\').style.display=\'\';setReadyDate();">'.($this->content['ready'] == '1' ? '<b>' : '').$this->translate->_('готова к отправке').'</label><br>
			<div id="date_ready" '.($this->content['ready'] == '0' ? 'style="display:none;"' : '').'>'.$this->translate->_('дата готовности').':<br>
			<input type="text" name="date_ready" id="readyDate" value="'.$this->content['date_ready'].'" style="width:90%;">
			<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'readyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></div>
			'.$daysFromReadyPassed.'</td>
			<td class="title rowA rowB" colspan="2"><textarea name="notice" rows="1">'.$this->content['notice'].'</textarea></td>
		  </tr>';
      }
      else
         $admin_note = '';
      //#######################
      //считаем сумму расходов на автомобиль
      $carExpences = 0;

      $carExpences =
              $this->content['price_jp']
              + $this->content['aucfee']
              + $this->content['dealer_comission']
              + $this->content['inspection']
              + $this->content['cost_to_port']
              + $this->content['cost_to_destination']
              + $this->content['unload']
              + $this->content['insurance']
              + $this->content['other'];
      //#######################
      // список аукицонов
      $aucList = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."auctions` WHERE 1 ORDER BY name ASC");
      $aucSelect = $this->buildSelect($aucList, 'auction', $this->content['auction'], $this->translate->_('не выбран'), '15');
      //##########################
      // файл после-продажной инспекции
      if (mysql_num_rows($this->inspection) > 0)
      {
        $index=1;
         while ($line = mysql_fetch_array($this->inspection))
         {

            $ico = fileIco($line['file']);

            $inspection_file .= $this->wrapFile('
			<a href="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a><br>
			'.($line['filename'] != '' ? "<div class='fileBlockText'>{$line['filename']}</div>" : '').'
			<a href="'.$this->root_path.'?mod=cars&sw=delete&what=inspection_file&name='.$line['file'].'" class="delete" onclick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить После Продажную инспекцию?').'\')">'.$this->translate->_('удалить').'</a>');
             if ($index%5==0) $adddoc_file .="<div class='clearbox'></div>";
             $index++;
         }
      }

      // файлы AutoCheck
      if (mysql_num_rows($this->autocheck) > 0)
      {
          $index=1;
         while ($line = mysql_fetch_array($this->autocheck))
         {

            $ico = fileIco($line['file']);

            $autocheck_file .= $this->wrapFile('
			<a href="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a><br>
			'.($line['filename'] != '' ? "<div class='fileBlockText'>{$line['filename']}</div>" : '').'
			<a href="'.$this->root_path.'?mod=cars&sw=delete&what=autocheck_file&name='.$line['file'].'" class="delete" onclick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить этот файл?').'\')">'.$this->translate->_('удалить').'</a>');
             if ($index%5==0) $adddoc_file .="<div class='clearbox'></div>";
             $index++;
         }
      }

      // файл Сопроводительный документ
      if (mysql_num_rows($this->adddoc) > 0)
      {
            $index=1;
         while ($line = mysql_fetch_array($this->adddoc))
         {

            $ico = fileIco($line['file']);

            $adddoc_file .= $this->wrapFile('
			<a href="'.$this->root_path.'photos/'.ACCOUNT_SUFFIX.$this->car_id.'/'.$line['file'].'" target="_blank"><img src="'.$this->root_path.'img/ico/'.$ico.'" vspace="5" border="0"></a><br>
			'.($line['filename'] != '' ? "<div class='fileBlockText'>{$line['filename']}</div>" : '').'
			<a href="'.$this->root_path.'?mod=cars&sw=delete&what=adddoc_file&name='.$line['file'].'" class="delete" onclick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить Сопроводительный документ?').'\')">'.$this->translate->_('удалить').'</a>');
             if ($index%5==0) $adddoc_file .="<div class='clearbox'></div>";
                 $index++;
         }
      }

      //#####################
      if (isset($_GET['inv']))
         $inv = $_GET['inv'];
      else
         $inv = '';
      switch ($inv)
      {
         case 'sent':
            $inv_report = '<h4 class="report">'.$this->translate->_('Пользователь создан.<br>Приглашение клиенту успешно отправлено.').'</h4>';
            break;
         case 'notsent':
            $inv_report = '<div class="notice">'.$this->translate->_('Пользователь создан.<br>Приглашение клиенту отправить НЕ удалось!').'</div>';
            break;
         case 'error':
            $inv_report = '<div class="notice">'.$this->translate->_('Пользователь не создан! <br>Приглашение клиенту НЕ было отправлено! <br>Возможно у него не указаны email или имя.').'</div>';
            break;
         case 'exists':
            $inv_report = '<h4 class="report">'.$this->translate->_('У этого клиента уже есть логин. Приглашение не требуется.').'</h4>';
            break;
         case 'no':
            $inv_report = '';
            break;
         default:
            $inv_report = '';
            break;
      }




      $purpose = '<select name="purpose" id="purpose">'; // onchange="puttext(\'purpose\',\'comment\');"

      $purposes = $this->purposes;

      if ($_SESSION['user_type'] == 12)
      {
         $purposes = array(4 => $purposes[4]);
      }

      foreach ($purposes as $a => $b)
      {
         $purpose.='<option value="'.$a.'" '.($content['purpose'] == $a ? 'selected="selected"' : '').'>'.$b.'</option>'."\n";
      }
      $purpose.='</select>';

      // Calculating total payments for car
      $full_pays = mysql_fetch_assoc($this->mysqlQuery("
			SELECT SUM(amount) as 'total_paid'
			FROM ccl_".ACCOUNT_SUFFIX."accounting
			WHERE type = 1 AND car = ".intval($this->car_id)));
      $total_paid = isset($full_pays['total_paid']) ? $full_pays['total_paid'] : '0';
      $update_payments = $this->mysqlQuery("UPDATE ccl_".ACCOUNT_SUFFIX."cars SET paid_total = ".$total_paid." WHERE id='".intval($this->car_id)."' LIMIT 1");

      // Form to add Debit or Credit. Calls from link above accountings list
      $add_accounting = '<div id="addAccounting" style="display:none;width:230px;position:absolute;border:2px solid #f55;margin-top:110px;margin-left:400px;">
		<form action="?mod=accounting&sw=add&return='.$this->car_id.'" class="myForm" method="post">
		<table class="list" width="230">
		<tr class="rowA"><td colspan="2"><img src="/img/ccl/r_ex.gif" align="right" style="cursor:hand; cursor:pointer;float:right;" onclick="document.getElementById(\'addAccounting\').style.display=\'none\';showSelects();">
		<b>'.$this->translate->_('Запись по автомобилю (расход-приход)').'</b></td></tr>
		<tr class="rowB">
			<td colspan=2><table>
				<tr><td><label for="type_pay">'.$this->translate->_('Приход').'</label></td><td><input type="radio" name="acctype" value="1" id="type_pay" style="width:auto" onclick="document.getElementById(\'confirm\').checked=true" /></td></tr>
				<tr><td><label for="type_exp">'.$this->translate->_('Расход').'</label></td><td><input type="radio" name="acctype" value="2" id="type_exp" checked style="width:auto" onclick="document.getElementById(\'confirm\').checked=false" /></td></tr>
				</table>
			</td>
		</tr>
		<tr class="rowA">
			<td>'.$this->translate->_('Сумма').':</td>
			<td>'.$this->translate->_('Дата').':</td>
		</tr>
		<tr class="rowB">
			<td><input type="input" name="amount" style="font-weight:bold;"></td>
			<td><img src="img/ccl/cal.gif" border=0 onclick="show_calendar(\'payDate\', \'\', myDateFormat);" style="margin-left:-22px;float:right;cursor:hand;cursor:pointer;"><input type="text" name="date" id="payDate" value="'.date('Y-m-d').'" tabindex="3" style="width:78%;" />
			</td>
		</tr>
		<tr class="rowA">
			<td colspan="2"><div align="center"><input type="checkbox" name="confirm" id="confirm" value="0" style="width:20px"> <label for="confirm">'.$this->translate->_('Оплачено?').'</label></div></td>
		</tr>
		<tr class="rowA">
			<td colspan="2">'.$this->translate->_('Назначение расхода').'</td>
		</tr>
		<tr class="rowB">
			<td colspan="2">'.$purpose.'</td>
		</tr>
		<tr class="rowA">
			<td colspan="2">'.$this->translate->_('Комментарий').':</td>
		</tr>
		<tr class="rowB">
			<td colspan="2"><input type="text" name="comment" value=""></td>
		</tr>
		
		<tr class="rowA"><td colspan="2" align="center"><input type="submit" value="'.$this->translate->_('Добавить').'" id="save"></td></tr>
		</table>	
		<input type="hidden" name="car" value="'.$this->car_id.'">
		<!-- <input type="hidden" name="client" value="'.$this->content['buyer'].'"> -->
		</form>
		</div>
		';

      //###############################################
//		Создание списка Бухгалтерии для текущей машины
      if (!isset($_GET['add']))
      {
         $sum_exp = 0; // сумма
         $acclist = '<table width="100%" cellspacing="0" cellpadding="3" class="list" style="border:0px;">';
         
         $eq = $this->mysqlQuery('
            SELECT `ccl_'.ACCOUNT_SUFFIX.'accounting`.*,`ccl_'.ACCOUNT_SUFFIX.'usrs`.`log_name`
            FROM `ccl_'.ACCOUNT_SUFFIX.'accounting`
            LEFT JOIN `ccl_'.ACCOUNT_SUFFIX.'usrs`
               ON `ccl_'.ACCOUNT_SUFFIX.'accounting`.`user_added` = `ccl_'.ACCOUNT_SUFFIX.'usrs`.`id`
            WHERE `car`='.$this->car_id.'
            '.($_SESSION['user_type'] == 12 ? " AND `purpose` = '4' " : '').'
            ORDER BY `type` ASC, `date` DESC
         ');

         $acclist.='<tr class="rowA"><th colspan="3" align="center" style="text-decoration:underline;">'.$this->translate->_('Балланс по автомобилю').'</th><td colspan="2"><div style="float:right;"><img src="img/ccl/bl_plus.gif" align="absmiddle" hspace=5 border="0" style="cursor:hand; cursor:pointer;" alt="'.$this->translate->_('Добавить услугу').'" onclick="document.getElementById(\'addAccounting\').style.display=\'\';"> <a onclick="document.getElementById(\'addAccounting\').style.display=\'\';hideSelects();" style="cursor:hand;cursor:pointer;">'.$this->translate->_('<b>добавить</b>').'</a></div></td></tr>';
         if (mysql_num_rows($eq))
         {
            $acclist.='<tr class="rowA" align="center" style="font-style:italic">
				<td width="70">'.$this->translate->_('Дата').'</td><td width="50">'.$this->translate->_('Сумма').'</td><td width="65">'.$this->translate->_('Создал').'</td><td>'.$this->translate->_('Подтв.').'</td><td>'.$this->translate->_('Оплачено').'</td></tr>
				<td colspan="5">
				<div style="height:250px;maxheight:100px;overflow:auto;">
				<table width="100%" cellspacing="0" cellpadding="3" class="list" style="border:0px;">';
            $cnt = 1;
            while ($er = mysql_fetch_assoc($eq)) // Создание списка
            {
//					$rowtype = ($cnt%2) ? ' class="rowA" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\'rowA\'"' : ' class="rowB" onmouseover="this.className=\'rowB hovered\'" onmouseout="this.className=\'rowB\'"';
//					$rowtype = ($cnt%2) ? ' class="rowAred" onmouseover="this.className=\'rowAred hovered\'" onmouseout="this.className=\'rowAred\'"' : ' class="rowBred" onmouseover="this.className=\'rowBred hovered\'" onmouseout="this.className=\'rowBred\'"';
               if ($er['type'] == '2')
               {
                  $rowtype = ($cnt % 2) ? ' class="rowAred" onmouseover="this.className=\'rowAred hovered\'" onmouseout="this.className=\'rowAred\'"' : ' class="rowBred" onmouseover="this.className=\'rowBred hovered\'" onmouseout="this.className=\'rowBred\'"';
               }
               else
               {
                  $rowtype = ($cnt % 2) ? ' class="rowAgreen" onmouseover="this.className=\'rowAgreen hovered\'" onmouseout="this.className=\'rowAgreen\'"' : ' class="rowBgreen" onmouseover="this.className=\'rowBgreen hovered\'" onmouseout="this.className=\'rowBgreen\'"';
               }

               $actype = $er['type'] == '2' ? '<b>'.$this->translate->_('P').'</b> ' : '<b>'.$this->translate->_('П').'</b> ';  // Символ означающий расход или платёжь. (Временно)

               $paid = $er['paid'] ? $this->translate->_('да') : $this->translate->_('нет');
               // Если оплата не подтверждена то вывести чекбокс для быстрого подтверждения
               if ($er['type'] == '2')
               {
                  $signer = ($er['signer'] != '' AND $er['signer'] != ' ') ? $er['signer'] : ' <input type="checkbox" onclick="send_accounting_confirm('.$er['id'].')"> ';
               }
               else
               {
                  $signer = '';
               }
               // Генерация элемента списка
               $acclist.='<tr'.$rowtype.' title="'.$this->purposes[$er['purpose']].' '.$er['comment'].'" align="center" style="cursor:hand; cursor:pointer;">
								<td onclick="window.location=\'?mod=accounting&sw=form&accounting='.$er['id'].'\'" width="70">'.$actype.$er['date'].'</td>
								<td onclick="window.location=\'?mod=accounting&sw=form&accounting='.$er['id'].'\'" width="50"><b>'.$er['amount'].'</b></td>
								<td onclick="window.location=\'?mod=accounting&sw=form&accounting='.$er['id'].'\'" width="65">'.$er['log_name'].'</td>
								<td><div id="s'.$er['id'].'">'.$signer.'</div></td><td><div id="'.$er['id'].'">'.$paid.'</div></td></tr>';
               $cnt++;
            }
            $eeq = $this->mysqlQuery('SELECT SUM(amount) as pays FROM `ccl_'.ACCOUNT_SUFFIX.'accounting` WHERE `car`='.$this->car_id.' AND `type`=1');
            if (mysql_num_rows($eeq))
            {
               $eer = mysql_fetch_assoc($eeq);
               $sum_pays = intval($eer['pays']);
            }
            else
               $sum_pays=0;

            $eeq = $this->mysqlQuery('SELECT SUM(amount) as exps FROM `ccl_'.ACCOUNT_SUFFIX.'accounting` WHERE `car`='.$this->car_id.' AND `type`=2');
            if (mysql_num_rows($eeq))
            {
               $eer = mysql_fetch_assoc($eeq);
               $sum_exps = intval($eer['exps']);
            }
            else
               $sum_exps=0;

            $ballance = $sum_pays - $sum_exps;

            $acclist.='</table></div></td>';
         }
         else
            $acclist.='<tr class="rowA" align="center"><td colspan="5" style="color:navy">'.$this->translate->_('Нет данных для данного автомобиля').' </td></tr>';
         // кнопка вызова формы добавления нового расхода
         // <div style="float:right;"><img src="img/ccl/bl_plus.gif" align="absmiddle" hspace=5 border="0" style="cursor:hand; cursor:pointer;" alt="Добавить услугу" onclick="document.getElementById(\'addExpense\').style.display=\'\';"> <a onclick="document.getElementById(\'addExpense\').style.display=\'\';hideSelects();" style="cursor:hand;cursor:pointer;">добав. <b>расход</b></a></div>

         if ($_SESSION['user_type'] != 12)
         {
            $acclist.='<tr class="rowA"><td style="color:navy" colspan="5">'.$this->translate->_('всего расходов').': <b>'.$sum_exps.'</b></td></tr>'; // добавляет в конце таблицы сумму расходов
            $acclist.='<tr class="rowB"><td style="color:navy" colspan="5">'.$this->translate->_('балланс').': <b>'.intval($this->content['total'] - $sum_pays).'</b></td></tr>'; // добавляет в конце таблицы балланс
         }

         $acclist.='</table>';
      }

      // Checking queue to post to forums and generating JS code to operate
      $queue_js = '';
      $queue_tmp = '';
      $result_divs = '';
      $i = 0;
      $pqueue = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."posting_queue` WHERE posted = 0");
      if (mysql_num_rows($pqueue))
         while ($qitem = mysql_fetch_assoc($pqueue))
         {
            switch ($qitem['forum'])
            {
               case 'webcars':
                  $queue_tmp = 'http://tcl.makmalauto.com/?mod=cars&sw=webcarssave&mode=autopost&car_id='.$qitem['carid'];
                  //$queue_tmp = 'http://localhost/ajax/sleeper.php?sleep='.rand(3,9);
                  break;
               case 'bbctkz': // bb.ct.kz posting script needs 2 car ids sale_is and tcl_base_id (parent)
                  $queue_tmp = 'http://tcl.makmalauto.com/?mod=repost_kaz&type=car&sale='.$qitem['carid'].'&parent='.$qitem['parent'];
                  break;
               case 'kolesakz':
                  $queue_tmp = 'http://tcl.makmalauto.com/kolesa.kz/kolesa-kz.php?car_id='.$qitem['carid'];
                  break;
            }
            $queue_js .= 'queue['.$i.'] = "'.$queue_tmp."\";\n"; // generating js array to make ajax get requests
            $result_divs .= '<div id="result_box'.$i.'" style="border:solid black 1px;max-width:600px;max-height:250px;height:20px;margin:3px;padding:3px;"></div><br>';
            $i++;
            // Marking queue item as posted
            $this->mysqlQuery("UPDATE `ccl_".ACCOUNT_SUFFIX."posting_queue` SET posted=1 WHERE id=".$qitem['id']);
         }

      $queue_script = '
		<!-- Posting Queueing -->
		<!-- Posting Results Div -->
		<div id="results_bar" style="display:none;z-index:990;position:fixed;top:0px;left:10%;padding:0px;margin:0px;width:200px;height:45px;background-color:lightblue;text-align:center;-moz-border-radius:5px;border-radius:5px;">
			Posting process results. <span onclick="$(\'#result_bar_inner\').slideToggle(\'fast\');" style="text-decoration:underline;cursor:pointer;">Click here to view results.</span>
			<div id="result_bar_inner" style="display:none;background-color:lightblue;width:600px;height:350px;">
				<br>'.$result_divs.'<br>
			</div>
		</div>

		<!-- Posting Div -->
		<div id="posting_bar" style="display:none;z-index:1000;position:fixed;top:0px;right:40%;padding:0px;margin:0px;width:300px;height:150px;background:URL(\'/img/posting_block.png\');text-align:center;-moz-border-radius:10px, 10px, 0px,0px;border-radius:10px, 10px, 0px,0px;">
			<br><br>
			<br><br><img id="posting_loading" src="/img/now_posting.gif" \><br>
		</div>
		<script language="JavaScript">
			var queuecount = '.$i.'
			var getcount = 0
			var queue = new Array();
			'.$queue_js.'
			if(queuecount>0){
				$(\'#posting_bar\').slideDown(\'slow\');
				for (var s in queue){
					//document.getElementById("posting_bar").innerHTML = document.getElementById("posting_bar").innerHTML + queue[s] + "<br>";
					getcount++;
					$.get(queue[s], {} ,
						function(data){
							getcount--;
							if(getcount==0) { $(\'#posting_bar\').slideUp(\'slow\'); }
							$("#results_bar"+s).html(data);
							$("#results_bar").slideDown(\'fast\');
						}
					)
				}
			}
			//$("#results_bar").slideDown(\'fast\');
		</script>
		';

      //###############################################
      /* 		<td class="rowB title">'.($this->content['total']-$this->content['paid']-$this->payments['amount']).'</td> */
      //###############################################
      $inbishkek = $this->container['arrived'] != 0 ? $this->translate->_('доставлена с').': <b>'.$this->container['bishkek'].'</b>' : $this->translate->_('в пути');
      $smallColWidth = '150';
      $this->page .= '
		<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
		<script src="'.$this->root_path.'js/datepicker.js"></script>
		<script src="'.$this->root_path.'js/jquery-1.4.2_min.js"></script>
		<script>
		
		var debug = false;
		
		$(document).ready(function(){
  			$("#Listcar_marka").change( function() {
	  			$.get("/ajax/model.php", { marka_id: $(this).val() },
  					function(data){
    					if (data.indexOf("error") == -1)
    					{
    						$("#model_holder").html(\'<select id="Listcar_model" tabindex="3" name="car_model">\'+data+"</select>");
    					}
  						if (debug) alert("Data Loaded: " + data);
  					});
			});

		});

		
		function showAddCustomer() {
			document.getElementById(\'customerSelect\').style.display=\'none\';
			document.getElementById(\'fog\').style.display=\'\';
			document.getElementById(\'addCustomerContainer\').style.display=\'\';
			document.getElementById(\'addCustomer\').src=\''.$this->root_path.'?mod=clients&sw=form&add&hidemenu\';
		}
		function finishAddCustomer() {
			document.getElementById(\'fog\').style.display=\'none\';
			document.getElementById(\'addCustomerContainer\').style.display=\'none\';
			document.getElementById(\'addCustomer\').style.display=\'none\';
			document.location=document.location;
		}
		var check = \'1\';
		
		function showForm() {
			document.getElementById(\'addCustomer\').style.display=\'\';
		}
		var cexp;
		function countCarExpenses() {
			cexp = parseFloat(document.getElementById(\'aucprice\').value)
			+parseFloat(document.getElementById(\'aucfee\').value)
			+parseFloat(document.getElementById(\'dealer_comission\').value)
			+parseFloat(document.getElementById(\'inspection\').value)
			+parseFloat(document.getElementById(\'cost_to_port\').value)
			+parseFloat(document.getElementById(\'cost_to_destination\').value)
			+parseFloat(document.getElementById(\'unload\').value)
			+parseFloat(document.getElementById(\'insurance\').value)
			+parseFloat(document.getElementById(\'other\').value);
			document.getElementById(\'carExpenses\').innerHTML = cexp;
		}
		function setReadyDate() {
			document.getElementById(\'readyDate\').value = \''.date('Y-m-d').'\';
		}
		var id;
		function saveTopPhoto(id) {
					
			$.get("/?mod=cars&sw=settopphoto", { top_photo: id, car: \''.$this->car_id.'\' },
			  function(data){
			  });
			 
		}
		
		var file_id = 1;
		var next_file;
		var holder;
		function addUploadFile() {
			next_file = file_id + 1;
			holder = \'up_\'+file_id;

			document.getElementById(holder).innerHTML = \'<input type="file" name="file_\'+next_file+\'"><span id="up_\'+next_file+\'">&nbsp;</span>\';
			file_id++;
		}
		
		function switchHidden(name) {
			var trig;
			if(document.getElementById(name).checked)	trig = 1;
			else trig = 0;
			document.getElementById(name+\'_trigger\').value=trig;
			
		}
		
		function hideSelects() {
			document.getElementById(\'Listtransporter\').style.display=\'none\';
			document.getElementById(\'Listport\').style.display=\'none\';
		}
		
		function showSelects() {
			document.getElementById(\'Listtransporter\').style.display=\'\';
			document.getElementById(\'Listport\').style.display=\'\';
		}
		function jump2payments() {
			document.forms.showPayments.submit();
		}
		';

      if (ACCOUNT_ID == '0')
      {
         $this->page .= '
		function doDieselRePost(sale, parent) {
			document.getElementById(\'repostInfo\').innerHTML = "<img src=img/loading.gif>";
			$.get("/?mod=repost&type=car", { sale: sale, parent: parent },
			  function(data){
			  	document.getElementById(\'repostInfo\').innerHTML = data;
			  });
                }
                function doDieselkgRePost(sale, parent) {
			document.getElementById(\'repostdieselkg\').innerHTML = "<img src=img/loading.gif>";
			$.get("/?mod=repostdkg&type=car", { sale: sale, parent: parent },
			  function(data){
			  	document.getElementById(\'repostdieselkg\').innerHTML = data;
			  });
		}
		function doTestRePost(sale, parent, price) {
			document.getElementById(\'reposttest\').innerHTML = "<img src=img/loading.gif>";
			$.get("/?mod=repost_test&type=car", { sale: sale, parent: parent, price: price },
			  function(data){
			  	document.getElementById(\'reposttest\').innerHTML = data;
			  });
		}
		function doKazRePost(sale, parent, price) {
			document.getElementById(\'repostkaz\').innerHTML = "<img src=img/loading.gif>";
			$.get("/?mod=repost_kaz&type=car", { sale: sale, parent: parent, price: price },
			  function(data){
			  	document.getElementById(\'repostkaz\').innerHTML = data;
			  });
		}
		
		function doKolesaRePost(sale) {
			document.getElementById(\'repostkolesa\').innerHTML = "<img src=img/loading.gif>";
			$.get("/kolesa.kz/kolesa-kz.php", { car_id: sale },
			  function(data){
			  	document.getElementById(\'repostkolesa\').innerHTML = data;
			  });
		}
		';
      }
      $this->page .= '
		function send_confirm(c){
			var a = document.getElementById(c);
			var b = document.getElementById(\'s\'+c);
			b.innerHTML=\'<span style="background-color:#FAFF9E">'.$this->translate->_('Подтв....').'</span>\';
			$.get("/?mod=expenses&sw=sign&", { expense_id: c },
			  function(data){
			  	if(data!=\'error\'){
					b.innerHTML = data;
					a.innerHTML = \'<span style="background-color:#7AFF8C">&nbsp;&nbsp;&nbsp;'.$this->translate->_('да').'&nbsp;&nbsp;&nbsp;</span>\';
				}
				else{
					b.innerHTML = \'<span style="background-color:#FF96AD">&nbsp;&nbsp;&nbsp;'.$this->translate->_('Ошибка!').'&nbsp;&nbsp;&nbsp;</span>\';
					a.innerHTML = \'<span style="background-color:#FF96AD">&nbsp;&nbsp;'.$this->translate->_('неизвестно').'&nbsp;&nbsp;</span>\';
				}
			  });
		}
		
		function send_accounting_confirm(c){
			var a = document.getElementById(c);
			var b = document.getElementById(\'s\'+c);
			b.innerHTML=\'<span style="background-color:#FAFF9E">'.$this->translate->_('Подтв....').'</span>\';
			$.get("/?mod=accounting&sw=sign&", { accounting_id: c },
			  function(data){
			  	if(data!=\'error\'){
					b.innerHTML = data;
					a.innerHTML = \'<span style="background-color:#7AFF8C">&nbsp;&nbsp;&nbsp;'.$this->translate->_('да').'&nbsp;&nbsp;&nbsp;</span>\';
				}
				else{
					b.innerHTML = \'<span style="background-color:#FF96AD">&nbsp;&nbsp;&nbsp;'.$this->translate->_('Ошибка!').'&nbsp;&nbsp;&nbsp;</span>\';
					a.innerHTML = \'<span style="background-color:#FF96AD">&nbsp;&nbsp;'.$this->translate->_('неизвестно').'&nbsp;&nbsp;</span>\';
				}
			  });
		}
		
		function openCraigsPost(url)
		{
			window.open(url,\'Craigslist.com_posting_form\',\'width=400,height=450,menubar=yes,status=yes,location=no,toolbar=no,scrollbars=yes\');
		}

		
		</script>
		'.$queue_script.'
		'/* .$add_payment.$add_expense */.$add_accounting.'
				<form class="myForm" action="'.$this->root_path.'?mod=cars'.$form_link.'" method="post">
		'.$inv_report.'
		<div class="cont_car" style="width:960px;height:540px;">
		<h3>'.$this->translate->_('Автомобиль').'</h3>
		<div style="position:absolute; margin-left: 651px; margin-top:3px; width:309px; background-color:#fff;">';

      if ($_SESSION['user_type'] != 12)
      {
         $this->page .= '
             <div id="money_things">
             <table width="100%">
             <tr>
                     <td align="right" class="rowA title" width="180">'.$this->translate->_('всего за автомобиль').'</td>
                     <td class="rowA title"><input type="text" name="total" value="'.$this->content['total'].'" tabindex="11">
                     <input type="hidden" name="lastPrice" value="'.$this->content['total'].'" style="font-weight:bold"></td>
             </tr>
             <tr>
                     <td align="right" class="rowA title">'.$this->translate->_('Оплачено').'</td>
                     <td class="rowB"><b><a onclick="jump2payments();" href="#">'.$this->payments['amount'].'</a></b></td>
             </tr>
      <!--
             <tr>
                     <td align="right" class="rowA title">'.$this->translate->_('баланс').'</td>
                     <td class="rowB"><b>'.($this->content['total'] - $this->content['paid'] - $this->payments['amount']).'</b></td>
             </tr>
      -->
             </table>

             </div>';
      }
      $this->page .= $acclist;
      
      if ($_SESSION['user_type'] != 12)
      {
         $this->page .= '
            <hr>
            <table width="100%">
               <tr>
                  <td align="right" width="180" class="rowA title">'.$this->translate->_('чистая прибыль').'</td>
                  <td class="rowB"><b>'.(intval($this->content['total']) - $sum_exps).'</b></td>
               </tr>
            </table>';
       }

      if (ACCOUNT_ID == '0')
      {
         if ($_SESSION['user_type'] != 12)
         {
            $this->page .= '<div align="center" style="margin:0px;padding:0px;"><a class="rowA" onclick="if(document.getElementById(\'old_expenses\').style.display==\'none\') {document.getElementById(\'old_expenses\').style.display=\'block\'} else{document.getElementById(\'old_expenses\').style.display=\'none\'}" style="cursor:hand; cursor:pointer; font-size:9px;">старый список расходов (показать/убрать)</a></div>';
         }

         $this->page .= '
			<div id="old_expenses" style="display:none">
			<table width="269" cellspacing="0" cellpadding="3" class="list" style="border:2px solid blue;">
			<tr class="rowB title"><td colspan="2"><b>'.$this->translate->_('Расходы на автомобиль').'</b></td></tr>
			<tr class="rowA title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('цена в Америке на аукционе').'</td>
				<td><input type="text" name="price_jp" value="'.$this->content['price_jp'].'" tabindex="10" id="aucprice" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr class="rowB title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('Аукционный сбор').'</td>
				<td><input type="text" name="aucfee" value="'.$this->content['aucfee'].'" id="aucfee" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr class="rowA title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('Комиссия дилера').'</td>
				<td><input type="text" name="dealer_comission" value="'.$this->content['dealer_comission'].'" id="dealer_comission" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr class="rowB title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('ПП инспекция').'</td>
				<td><input type="text" name="inspection" value="'.$this->content['inspection'].'" id="inspection" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr><td colspan="2" class="title">&nbsp;</td></tr>
			<tr class="rowA title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('Доставка до порта').'</td>
				<td><input type="text" name="cost_to_port" value="'.$this->content['cost_to_port'].'" id="cost_to_port" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr class="rowB title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('Доставка до места назначения').'</td>
				<td><input type="text" name="cost_to_destination" value="'.$this->content['cost_to_destination'].'" id="cost_to_destination" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr><td colspan="2" class="title">&nbsp;</td></tr>
			<tr class="rowA title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('Разгрузка').'</td>
				<td><input type="text" name="unload" value="'.$this->content['unload'].'" id="unload" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr class="rowB title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('Страховка').'</td>
				<td><input type="text" name="insurance" value="'.$this->content['insurance'].'" id="insurance" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr><td colspan="2" class="title">&nbsp;</td></tr>
			<tr class="rowA title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('Прочее').'</td>
				<td><input type="text" name="other" value="'.$this->content['other'].'" id="other" onchange="javascript:countCarExpenses();"></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr class="rowB title">
				<td width="'.$smallColWidth.'">'.$this->translate->_('ВСЕГО').':</td>
				<td id="carExpenses">'.$carExpences.'</td></tr>
			</table>
			</div>';
      }
      $this->page .= '</div>';

      if ($_SESSION['user_type'] == 12)
      {
         $this->page .= '
             <script type="text/javascript">
                $(function()
                {
                   $("#form-primary input, #form-secondary input, #form-secondary select, #form-secondary textarea")
                   .attr("disabled", "disabled");
                   
                   $("#form-primary select").not("#Listtransporter, #Listplace2")
                   .attr("disabled", "disabled");

                   $("#form-pictures input, #form-pictures a.delete").remove();

                   $("#save-transporter").click(function()
                   {
                      var list = $("#Listtransporter");
                      
                      if (list.val() != 0)
                      {
                         $.get("/", {mod:"cars",sw:"transporter",id:'.$this->car_id.',transporter:list.val()}, function(response)
                         {
                            alert("'.$this->translate->_('Транспортник успешно сохранен').'");
                         });
                      }
                      else
                      {
                         alert("'.$this->translate->_('Пожалуйста выберите транспортника').'");
                      }
                   });

                   $("#save-place2").click(function()
                   {
                      var list = $("#Listplace2");

                      if (list.val() != 0)
                      {
                         $.get("/", {mod:"cars",sw:"place2",id:'.$this->car_id.',place:list.val()}, function(response)
                         {
                            alert("'.$this->translate->_('Тайтл успешно сохранен').'");
                         });
                      }
                      else
                      {
                         alert("'.$this->translate->_('Пожалуйста выберите тайтл').'");
                      }
                   });
                });
             </script>
             ';
      }

      $this->page .= '
		<table width="650" border="0" cellpadding="0" cellspacing="0" class="list" id="form-primary">
		  <tr>
		    <td align="right" class="title" width="110">
		    <div style="width:90px; float:right;">'.captionLink($this->content['buyer'], '0', ($_SESSION['user_type'] == 1 ? $this->root_path.'?mod=clients&sw=detail&id='.$this->content['buyer'] : $this->root_path.'?mod=clients&sw=form&customer_id='.$this->content['buyer']), '<strong>'.$this->translate->_('владелец').'</strong>').'</div>
		    <div style="float:left;"><img src="'.$this->root_path.'img/ccl/add.gif" border="0" style="cursor:pointer;" alt="'.$this->translate->_('Добавить клиента').'" title="'.$this->translate->_('Добавить клиента').'" onclick="javascript:showAddCustomer();"></div></td>
		    <td class="title" style="border:1px solid #ACE3AC;" width="200">
			'.$car_owner.'
			'.$hiddenFields.'
			'.$carsDealer.'
			  </td>
			<td align="right" class="title" width="80">'.$this->translate->_('дата покупки').'</td>
			<td width="180" class="rowB title"><input type="text" name="buy_date" value="'.$this->content['buy_date'].'" tabindex="9" id="buyDate" style="width:90%;" />
			<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'buyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;">
			<br>'.$daysPassed.'</td>
		  </tr>
		  <tr>
		    <td align="right" class="rowA title">
		    <div style="width:90px; float:right;">
		    '.captionLink($this->content['reciever'], '0', $this->root_path.'?mod=recievers&sw=form&id='.$this->content['reciever'], $this->translate->_('получатель')).'
		    </div>
		     <div style="float:left;"><a href="'.$this->root_path.'?mod=recievers&sw=form&add"><img src="'.$this->root_path.'img/ccl/add.gif" border="0" style="cursor:pointer;" alt="'.$this->translate->_('Добавить получателя').'" title="'.$this->translate->_('Добавить получателя').'"></a></div></td>
		    <td class="rowA title">
		    '.$this->getRecieversList().'
			</td>
			<td align="right" class="rowA title" colspan=2>'.$inbishkek.'</td>
<!--			<td class="rowA title">&nbsp;</td> -->
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">'.$this->translate->_('Название').'</td>
		    <td class="rowB"><b>'.$this->content['model'].'</b></td>
			<td class="rowB title" align="right" class="title">&nbsp;</td>
			<td class="rowB">&nbsp;</td>
			
		</td>
		  </tr>		
		    <tr>
		    <td align="right" class="rowA title">'.$this->translate->_('Марка автомобиля').':<br> '.$this->translate->_('Модель').': </td>
		    <td class="rowA title">'.$marka.'<div id="model_holder">'.$model.'</div></td>
			<td  align="left" class="rowA title"><a href="/?mod=marka&sw=form" target="_blank"><img src="'.$this->root_path.'img/ccl/add.gif" border="0"vspace="3" alt="'.$this->translate->_('Добавить марку').'" title="'.$this->translate->_('Добавить марку').'"></a>
			<br>
			<a href="/?mod=model&sw=form" target="_blank"><img src="'.$this->root_path.'img/ccl/add.gif" border="0" vspace="3" alt="'.$this->translate->_('Добавить модель').'" title="'.$this->translate->_('Добавить модель').'"></a></td>
			<td align="right" class="title">&nbsp;</td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">'.$this->translate->_('вин код').'</td>
		    <td class="rowB title"><input type="text" name="frame" value="'.$this->content['frame'].'" tabindex="4" /></td>
			<td align="right" class="title rowB">&nbsp;</td>
			<td class="rowB title"><input type="checkbox" name="car_is_sold" id="car_is_sold" style="width:auto;"'.($this->content['car_is_sold'] == '1' ? ' checked="checked"' : '').'><label for="car_is_sold">'.$this->translate->_('Автомобиль продан (для клиента)').'</label></td>
		  </tr>
		  <tr>
		    <td align="right" class="rowA title">'.$this->translate->_('дата выпуска').'</td>
		    <td class="rowA title"><input type="text" name="year" value="'.$this->content['year'].'" tabindex="5" /></td>
			<td align="right" class="title">&nbsp;</td>
			<td class="rowA title">&nbsp;</td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">'.$this->translate->_('объем двигателя').'</td>
		    <td class="rowB title"><input type="text" name="engine" value="'.$this->content['engine'].'" maxlength="4" tabindex="6"/></td>';

                 if ($_SESSION['user_type'] != 12)
                 {
                    $this->page .= '
                       <td align="right" class="title rowB">'.$this->translate->_('цена в инвойсе').'</td>
		       <td class="rowA rowB title"><input type="text" name="invoice" value="'.$this->content['invoice'].'" tabindex="13" /></td>';
                 }
                 else
                 {
                    $this->page .= '<td colspan="2" class="title rowB">&nbsp;</td>';
                 }

               $this->page .= '
		  </tr>
		  <tr>
		    <!--
			<td align="right" class="title">'.$this->translate->_('вес').'</td>
		    <td class="rowA title"><input type="text" name="weight" value="'.$this->content['weight'].'" tabindex="7" style="width:50px;">кг &nbsp;
		    &nbsp;&nbsp;&nbsp;объем: <input type="text" name="volume" value="'.$this->content['volume'].'" tabindex="8" style="width:50px;">м<sup>3</sup></td> -->
			<td>&nbsp;</td><td>&nbsp;</td>
			<td align="right" class="title">'.captionLink($this->content['container'], '0', '?mod=containers&sw=form&cont_id='.$this->container['id'], $this->translate->_('контейнер')).'</td>
			<td class="rowA title">'.$this->container['number'].'</td>
		  </tr>
                  <tr valign="top">
                     <td align="right" class="rowB title">'.$this->translate->_('пробег').'</td>
                     <td class="rowB title" align="left">
                        <input type="text" name="milage" value="'.$this->content['milage'].'" />
                        <div style="margin-top:5px">
                           <input style="width:auto;margin:0" type="checkbox" id="show_mileage" name="show_mileage" '.(intval($this->content['show_mileage']) ? 'checked="checked"' : '').' />&nbsp;<label for="show_mileage">'.$this->translate->_('показывать на сайте').'</label>
                        </div>
                     </td>
                     <td class="title rowB" align="right">'.captionLink($this->content['transporter'], '0', $this->root_path.'?mod=transporters&sw=form&sup_id='.$this->content['transporter'], $this->translate->_('транспортник')).'</td>
                     <td class="rowB title" align="right">';

                     if ($_SESSION['user_type'] == '5')
                     {
                        $this->page .= '<input type="hidden" value="'.$this->content['transporter'].'" name="transporter">'.$this->content['sup_name'];
                     }
                     else
                     {
                        $this->page .= $sup_list;
                        
                        if ($_SESSION['user_type'] == 12)
                        {
                           $this->page .= '<a id="save-transporter" href="javascript:;">'.$this->translate->_('сохранить').'</a>';
                        }
                     }

                  $this->page .= '</td>
                  </tr>
                  ';
      
                  $umQuery = $this->mysqlQuery('SELECT * FROM `ccl_user_mileage` WHERE `car_id` = '.$this->content['id']);

                  if (mysql_num_rows($umQuery))
                  {
                     $umResult = mysql_fetch_object($umQuery);

                     $this->page .= '
                     <tr>
                        <td align="right" class="rowB title">'.$this->translate->_('пробег клиента').'</td>
                        <td class="rowB title"><b style="color:'.($umResult->active ? 'green' : 'red').'">'.intval($umResult->mileage).'</b></td>
                        <td colspan="2" class="rowB title">&nbsp;</td>
                     </tr>
                     ';
                  }

                  $this->page .= '
		  	  <tr>
			<td class="title"></td>
			<td class="rowA title">'.$invoiceLink.'</td>
			<td class="rowA title" align="right">'.$this->translate->_('порт').'</td>
			<td class="title">'.$ports_list.'</td>
		  </tr>
                  <tr>
                     <td align="right" class="title rowB">'.$this->translate->_('цена для инвойса').'</td>
                     <td class="rowA rowB title"><input type="text" name="invoice_price" id="invoice_price" value="'.$this->content['invoice_price'].'" /></td>
                     <td align="right" class="title rowB">'.$this->translate->_('номер инвойса').'</td>
                     <td class="rowA rowB title"><input type="text" name="invoice_number" id="invoice_number" value="'.$this->content['invoice_number'].'" /></td>
                  </tr>
		<!--
			<tr>
			<td align="right" class="title rowB">'.$this->translate->_('дата реальной доставки').'</td>
			<td class="rowA rowB title">
				<input type="text" name="date_realydeliver" id="realyDate" value="'.($this->content['date_realydeliver'] != "0000-00-00" ? $this->content['date_realydeliver'] : "").'" style="width:90%;">
				<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'realyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;">
			
			</td>
			<td align="right" class="title rowB">'.$this->translate->_('статус транспортировки').'</td>
			<td class="rowA rowB title">'.($this->content['pickedup'] == '1' && $this->content['transstatus'] == '0' ? '<b>забрал</b>' : '').($this->content['transstatus'] == '1' ? '<b>доставлена</b>' : '').'</td>
		  </tr>
		-->
		  <tr>
			<td class="title rowB">&nbsp;</td>
			<td class="rowA rowB title">&nbsp;</td>
			<td class="title rowB">&nbsp;</td>
			<td class="rowA rowB title">'.($this->content['delivered'] == '1' ? $this->translate->_('доставлена') : '').'</td>
		  </tr>
		  
		  <tr>
			<td align="right" class="title">'.$this->translate->_('Местонахождение автомобиля').'</td>
			<td class="rowA title">'.$place_list1.'</td>
			<td align="right" class="title">'.$this->translate->_('Тайтл').'</td>
			<td class="rowA title" align="right">'.$place_list2;

                  if ($_SESSION['user_type'] == 12)
                  {
                     $this->page .= '<a id="save-place2" href="javascript:;">'.$this->translate->_('сохранить').'</a>';
                  }

                  $countries_sql = $this->mysqlQuery('SELECT * FROM `countries` ORDER BY id ASC');
                  $countries = array();
                  while ($row = mysql_fetch_object($countries_sql))
                  {
                     // $countries[] = '<option '.((empty($this->content['country_id']) && $row->id == 121) ? 'selected="selected"' : ($this->content['country_id'] == $row->id ? 'selected="selected"' : '')).' value="'.$row->id.'">'.$row->country.'</option>';
                     $countries[] = '<option style="padding-left:18px; background:url(/img/flags/'.strtolower($row->iso2).'.gif) left no-repeat" '.($this->content['country_id'] == $row->id ? 'selected="selected"' : '').' value="'.$row->id.'">'.$row->country.'</option>';
                  }
                  $countries_select = '<select name="country_id">'.implode('', $countries).'</select>';

                  $this->page .= '</td>
		  </tr>
		  

		  <tr>
			<td align="right" class="title rowB">'.$this->translate->_('Аукцион').'</td>
			<td class="rowA title rowB">'.$aucSelect.'</td>
			<td align="right" class="title rowB">
				<div style="float:left"><a href="/?mod=auctions&sw=form&add" target="_blank"><img src="/img/ccl/add.gif" alt="Add auction" title="Add auction" border="0" vspace="3"></a></div>
				'.$this->translate->_('место<br>назначения').'</td>
			<td class="rowA title rowB">'.$place_list3.'</td>
		  </tr>
		 '.$admin_note.'
                  <tr>
			<td align="right" class="title rowA">'.$this->translate->_('Страна продавца').'</td>
			<td class="rowA title">'.$countries_select.'</td>
			<td class="title rowA"></td>
			<td class="rowA title"></td>
		  </tr>
		</table>
		</div>
		'.(isset($_GET['add']) ? '
		<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:968px">
		  <tr>
		  <td>&nbsp;</td>
		  	<td class="title" width="230">
			<input type="checkbox" name="invite_user" id="invite" style="width:20px;"><label for="invite">&nbsp;'.$this->translate->_('пригласить клиента').'</label>
			</td></tr>
			</table>
		' : '');

                if ($_SESSION['user_type'] != 12)
                {
                   $this->page .= '
                   <table border="0" cellpadding="0" cellspacing="0" class="list" style="width:968px">
                     <tr>
                           <td class="title"><a href="'.$this->root_path.'?mod=cars&sw=delete&id='.intval($this->car_id).'&container='.$this->content['container'].'" class="delete" onClick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить этот автомобиль?').'\')">'.$this->translate->_('удалить').'</a></td>
                           <td class="title" align="center">'.$this->typeSwitch().'</td>
                           <td><input type="button" onclick="document.getElementById(\'sellCar\').style.display=\'\'" value="'.$this->translate->_('Продать').'" id="save" style="width:100px;margin-right:30px;color:#999;"></td>
                           <td width="214" align="right" class="title">
                           <input type="hidden" name="allow_inspection" value="'.$this->content['allow_inspection'].'" id="insp_allow_trigger">
                           <input type="hidden" name="allow_cr" value="'.$this->content['allow_cr'].'" id="cr_allow_trigger">
                           <input type="hidden" name="allow_codocs" value="'.$this->content['allow_codocs'].'" id="codocs_allow_trigger">
                           <input type="hidden" name="allow_carfax" value="'.$this->content['allow_carfax'].'" id="carfax_allow_trigger">
                           <input type="hidden" name="last_owner" value="'.$this->content['buyer_id'].'">
                           <input type="submit" name="Submit" value="'.$this->translate->_('Сохранить').'" id="save" tabindex="17" /></td>
                           <td width="8" align="right" class="title"><br />
                                   <br /></td>
                     </tr>
                   </table>';
                }
                
                $this->page .= '</form>';

      if ($this->car_id != 0)
      {
         $list = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."tpl` WHERE type='c' ORDER BY name");
         $tpl_list = buildSelect($list, 'tpl', 0, 'Не указан', '17');
         if ($list && mysql_num_rows($list) > 0)
         {
            $this->page .= '<script language="JavaScript">
				$(document).ready(function()
					{
						$(\'#Listtpl\').change( function()
						 	{
							var id=$("#Listtpl option:selected").val();
							if (id>0){
								$.get("/ajax/gettpl.php?id="+id, function(data){
									$("#sellComment").val(data);
									});
								} else 	$("#sellComment").val("");
						 	
						 	});
					});
			
					</script>';
         }
         //выставить автомобиль на продажу
         $this->page .= '<form style="margin:0px;" action="'.$this->root_path.'?mod=cars&sw=forsale&car_id='.$this->car_id.'" method="post">
		<!-- Correcting table -->
		<table><tr><td> 
			<table width="702" border="0" cellpadding="0" cellspacing="0" class="list" id="form-secondary">
			<tr class="rowB title">
				<td><input type="checkbox" name="sell" id="sell" onClick="document.getElementById(\'sellOptions\').style.display=\'\'"'.($this->content['sell_id'] == '' ? '' : ' checked="checked"').'> <label for="sell">'.$this->translate->_('выставить на продажу').'</label> &nbsp; &nbsp;
				<a href="'.$this->root_path.'?mod=tpl&type=c">'.$this->translate->_('Редактировать шаблоны').'</a>

				</td>
				</tr>
				<tr class="rowB title"><td align="right">
					<div id="sellOptions"'.($this->content['sell_id'] == '' ? ' style="display:none"' : '').'>
					<table width="100%" >
					<tr class="rowB">
						<td colspan="2" valign=top>
							'.$this->translate->_('шаблон').': '.$tpl_list.'
							<br>
							<!-- <a href="#" onclick="doDieselRePost('.$this->content['sell_id'].', '.$this->car_id.');">Выложить на Diesel еще раз</a>
							<br><span id="_repostInfo"></span> -->
							<br>
							'.$this->translate->_('цена').': <br>
							<input type="text" name="sellPrice" size="6" style="border:1px solid #bbb; width:150px;" value="'.$this->content['sell_price'].'">
							<br><br>
							'.$this->translate->_('диллерская цена').': <br>
							<input type="text" name="dealer_sell_price" size="6" style="border:1px solid #bbb; width:150px;" value="'.$this->content['dealer_sell_price'].'">
							<br><br>
							
						</td>
						<td colspan="2">
							'.$this->translate->_('комментарий').': 
							<textarea name="sellComment" id="sellComment" style="border:1px solid #bbb;width:100%;height:100px;" >'.$this->content['sell_comment'].'</textarea>
						
						</td>
						
					</tr>
					<tr>
						<td width="103">
							
							
						</td>
						<td>
							<input type="checkbox" name="sold" id="sold_check"'.($this->content['sold'] == 1 ? ' checked="checked"' : '').'><label for="sold_check">'.$this->translate->_('продан').'</label>
						</td>
						<td>
							'.$this->translate->_('выставить до').': &nbsp;<input type="text" name="post_till" value="'.$this->content['sell_active_through'].'" id="posttillDate" style="border:1px solid #bbb;"><img src="img/ccl/cal.gif" border=0 onclick="show_calendar(\'posttillDate\', \'\', myDateFormat);" style="cursor:hand;cursor:pointer;">
						</td>
						<td align="right">&nbsp;<input type="hidden" name="sell_id" value="'.($this->content['sell_id'] == '' ? 0 : $this->content['sell_id']).'">
							<input type="submit" value="'.$this->translate->_('Сохранить').'">&nbsp;</td>
					</tr>
					</table>
				</div></td>
			</tr>
			
			</table></form> 
		</td><td> <!-- Correcting table -->';

         if (ACCOUNT_ID == '0' && $_SESSION['user_type'] != 12)
         {
            $this->page .= '<!-- Forum posting table -->
			<table width="255" height="191" class="list" cellspacing="0" cellpadding="0">
			<tr class="rowB title" height="18">
			<th height="18">Поместить обьявление на форумы:</th>
			</tr>
			<tr class="rowB title" valign="top">
			<td>
				<!-- bb.ct.kz posting -->
				<span class="lnk" onclick="doKazRePost('.$this->content['sell_id'].', '.$this->car_id.', document.getElementById(\'price\').value);">Разместить на bb.ct.kz</span>
				&nbsp;&nbsp;&nbsp;
				цена: <input type="text" id="price" name="price" size="5" style="border:1px solid #bbb; font-size:9px" value="'.$this->content['sell_price'].'" disabled="disabled">
				<br><span id="repostkaz"></span>
				<br>
				<!-- Diesel posting -->
				<span class="lnk" onclick="doDieselRePost('.$this->content['sell_id'].', '.$this->car_id.');">Разместить на <b>Diesel.elcat.kg</b> (<i>повторно</i>)</span>
				<br><span id="repostInfo"></span>
				<br>
				<!-- Diesel.kg posting -->
				<span class="lnk" onclick="doDieselkgRePost('.$this->content['sell_id'].', '.$this->car_id.');">Разместить на <b>Diesel.kg</b> <!--(<i>повторно</i>)--></span>
				<br><span id="repostdieselkg"></span>
				<br>
				<!-- Kolesa.kz posting -->
				<span class="lnk" onclick="doKolesaRePost('.$this->content['sell_id'].');">Разместить на <b>Kolesa.kz</b></span>
				<br><span id="repostkolesa" ></span>
				<br>
				<!-- craigslist.com posting -->
				<span class="lnk" onclick="openCraigsPost(\'\/craigslist\/post.php?car_id='.$this->content['sell_id'].'\');">Разместить на <b>craigslist.com</b></span>
				<br>
				<br>
				<!-- OVE.com posting -->
				<a href="/?mod=cars&sw=oveform&car_id='.$this->car_id.'">Разместить на <b>OVE.com</b></a>

				<br>
				<br>
				<!-- webcars.kg posting -->
				<a href="/?mod=cars&sw=webcars&car_id='.$this->car_id.'">Разместить на <b>Webcars.kg</b></a>

				<br>
				<br>
				<!-- webcars.kg QUICK posting -->
				<a href="/?mod=cars&sw=webcarssave&mode=autopost&car_id='.$this->car_id.'" target="_blank">Разместить БЫСТРО на <b>Webcars.kg</b></a>
				<br><span id="webcarspost"></span>
				
				
				<br>
				<!-- repair posting -->
				<a href="/?mod=cars&sw=repair&car_id='.$this->car_id.'">Разместить в <b>ремонтной базе</b></a>
				
			</td>
			</tr>		
			</table>';
         }
         $this->page .= '</td></tr></table>';
         /*
           // Post to TEST Forum link
           <span class="lnk" onclick="doTestRePost('.$this->content['sell_id'].', '.$this->car_id.', document.getElementById(\'price\').value);">Выложить на Test Forum</span>
           &nbsp;&nbsp;&nbsp;
           цена: <input type="text" id="price" name="price" size="5" style="border:1px solid #bbb; font-size:9px" value="'.$this->content['sell_price'].'" disabled="disabled">
           <br><span id="reposttest"></span>
           <br>

          */
         //################################

         $this->page .= '<div class="cont" style="width:960px;">
			<table border="0" width="955" class="list">
			<tr>
			<td width="725" valign="top">
				<table width="100%" class="list">
			<tr class="rowB">
				<td class="title"><b>'.$this->translate->_('Фотографии').'</b>
				</td>
				<td align="right" nowrap><a href="/?mod=cars&sw=allphotos&car_id='.$this->car_id.'" target="_blank" class="delete">[+] '.$this->translate->_('все фотографии').'</a></td>
			</tr>
			<tr>
				<td colspan="2">
				'.$photo_list.'				
				</td>
			</tr>
			<tr class="rowB">
				<td class="title"><b>'.$this->translate->_('Отчет о состоянии автомобиля или CR').'</b></td>
				<td><input type="checkbox" '.($this->content['allow_cr'] == 1 ? ' checked="checked"' : '').' id="cr_allow" onchange="switchHidden(\'cr_allow\')"> <label for="cr_allow">'.$this->translate->_('показывать на сайте').'</label></td>
			</tr>
			<tr>
				<td class="rowA title" valign="top" colspan="2">
				
				'.$cr_list.'
				
				</td>
			</tr>

			<tr class="rowB">
				<td class="title" valign="bottom"><b>'.$this->translate->_('ПП инспекция').'</b></td>
				<td><input type="checkbox" '.($this->content['allow_inspection'] == 1 ? ' checked="checked"' : '').' id="insp_allow" onchange="switchHidden(\'insp_allow\')"> <label for="insp_allow">'.$this->translate->_('показывать клиенту').'</label></td>
			</tr>
			<tr>
			<td valign="top" colspan="2">'.$inspection_file.'</td></tr>	
			
			<tr class="rowB">
				<td class="title" width="200"><b>'.$this->translate->_('Сопроводительные документы').'</b></td>
				<td><input type="checkbox" '.($this->content['allow_codocs'] == 1 ? ' checked="checked"' : '').' id="codocs_allow" onchange="switchHidden(\'codocs_allow\')"> <label for="codocs_allow">'.$this->translate->_('показывать клиенту').'</label></td></tr>
			<tr><td class="rowA title" valign="top" colspan="2">'.($_SESSION['user_type'] != 12 ? $adddoc_file : '').'</td></tr>

			<tr class="rowB">
				<td class="title" width="200"><b>AutoCheck / CarFax</b></td>
				<td><input type="checkbox" '.($this->content['allow_carfax'] == 1 ? ' checked="checked"' : '').' id="carfax_allow" onchange="switchHidden(\'carfax_allow\')"> <label for="carfax_allow">'.$this->translate->_('показывать на сайте').'</label></td></tr>
			<tr><td class="rowA title" valign="top" colspan="2">'.$autocheck_file.'</td></tr>

				</table>
			</td>
			<td valign="top">
			<table width="100%" class="list">
			<tr>
			<tr><td class="title"><b>'.$this->translate->_('Загрузить').'...</b></td></tr>
				<td class="title">
				'.$this->translate->_('Фотографии').':<br>

                <div style="display:none;border:2px solid orange;padding:2px;" id="advanced_multi_uploader">
                    <b>Advanced multifile uploader</b> works on Firefox 3.6+ and Chrome only<br><br>
                    <form action="'.$this->root_path.'?mod=multiuploadadv" method="post" class="myForm" enctype="multipart/form-data">
                    '.$this->folderSelect().'<br>
                    <input type="file" name="carphotos[]" multiple=""/><br>
                    <input type="hidden" name="owner" value="'.$this->car_id.'">
                    <input type="hidden" name="type" value="car">
                    <input type="submit" name="Submit" value="'.$this->translate->_('Загрузить').'" id="save" /></form>
                </div>
                <br>
                <script type="text/javascript">
                    if (/Firefox[\/\s](\d+\.\d+)/.test(navigator.userAgent)){ //test for Firefox/x.x or Firefox x.x (ignoring remaining digits);
                        var ffversion=new Number(RegExp.$1) // capture x.x portion and store as a number
                        if (ffversion>=3.6)
                            document.getElementById("advanced_multi_uploader").style.display="block";
                    }
                    if (/Chrome[\/\s](\d+\.\d+)/.test(navigator.userAgent)){ //test for Chrome/x.x or Chrome x.x (ignoring remaining digits);
                        var chversion=new Number(RegExp.$1) // capture x.x portion and store as a number
                        if (chversion>=5.0)
                            document.getElementById("advanced_multi_uploader").style.display="block";
                    }
                </script>

				<form action="'.$this->root_path.'?mod=multiupload" method="post" class="myForm" enctype="multipart/form-data">
				'.$this->folderSelect().'<br>
				<input type="file" name="file_1"><br>
				<span id="up_1">&nbsp;</span>
				<input type="button" onclick="addUploadFile();" name="add" value="+" style="margin-top:5px;margin-bottom:5px;">
				<input type="hidden" name="foto_number" value="1">
				<input type="hidden" name="owner" value="'.$this->car_id.'">
				<input type="hidden" name="type" value="car">
				<input type="submit" name="Submit" value="'.$this->translate->_('Загрузить').'" id="save" /></form>
				</td>
			</tr>
			<tr><td class="title">
			'.$this->translate->_('Отчет (CR)').':<br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="certImage" value="'.$this->car_id.'">
			<br><input type="submit" value="'.$this->translate->_('Загрузить').'" id="save"></form></td></tr>
			<tr><td class="title">

			<span style="color:black">AutoCheck:</span><br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="autocheck" value="'.$this->car_id.'">
			<br><input type="submit" value="'.$this->translate->_('Загрузить').'" id="save"></form>
		<br>
			<span style="color:black">CarFax:</span><br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="carfax" value="'.$this->car_id.'">
			<br><input type="submit" value="'.$this->translate->_('Загрузить').'" id="save"></form>

			</td></tr>
			<tr><td class="title">
			'.$this->translate->_('ПП инспекция').':<br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="uploadInspection" value="'.$this->car_id.'">
			<input type="submit" name="Submit" value="'.$this->translate->_('Загрузить').'" id="save" /></form></td>
			</tr>
			
			<tr><td class="title">
			'.$this->translate->_('Сопроводительные документы').':<br>
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="uploadAdddoc" value="'.$this->car_id.'">
			<input type="submit" name="Submit" value="'.$this->translate->_('Загрузить').'" id="save" /></form></td>
			</tr>

			
			</table>
			</td></tr></table>
			</div>';

         $this->page .= ( $expeditorphoto ? '<div class="cont_customer" style="width:694px;">'.$expeditorphoto.'</div>' : '');
      }

      //скрытая форма добавления клиента
      $this->page .= '
		<div style="position:absolute;left:0px;top:0px;display:none;z-index:74;width:100%;height:100%;cursor:arrow;background-color:#fff;filter:alpha(opacity=50);-moz-opacity: 0.5;-khtml-opacity: 0.5;opacity: 0.5;" id="fog">
		</div>
		
		<div style="display:none; position:absolute; top:110px; left:120px; width:835px; height:490px; background-color:#fff; z-index:76; border:1px solid #0056ca; font:menu;	background-image:url(\'img/ccl/loading.gif\');background-position:center;	background-repeat:no-repeat;" id="addCustomerContainer">
		<div style="position:absolute; right:60px;margin-top:4px;"><img src="'.$this->root_path.'img/ccl/new_client_button.gif" alt="'.$this->translate->_('Добавить еще одного клиента').'" title="'.$this->translate->_('Добавить еще одного клиента').'" onclick="document.getElementById(\'addCustomer\').src=\''.$this->root_path.'?mod=clients&sw=form&add&hidemenu\';document.getElementById(\'addCustomer\').style.display=\'none\'; this.src=\''.$this->root_path.'img/ccl/new_client_button_on.gif\'" style="cursor:pointer;" onMouseOut="this.src=\''.$this->root_path.'img/ccl/new_client_button.gif\'"></div>
		<img src="'.$this->root_path.'img/ccl/r_ex.gif" style="position:absolute; right:5px; top:5px;cursor:pointer;" alt="'.$this->translate->_('Закрыть окно').'" title="'.$this->translate->_('Закрыть окно').'" onclick="javascript:finishAddCustomer();">
		<iframe style="display:none;  width:830px; height:450px; margin:5px; margin-top:25px; z-index:77;" scrolling="no" frameborder="0" marginheight="0" marginwidth="0" id="addCustomer" src=""></iframe>
		</div>';
      //###################################
      //скрытая форма продажи автомобиля другому клиенту
      $this->page .= '<div style="display:none;width:240px;position:absolute; left:705px; top:400px; background-color:#fff; border:1px solid #bbb; padding:5px; background-color:#c33" id="sellCar">
		<form style="margin:0px;" action="'.$this->root_path.'?mod=cars&sw=sell&car_id='.$this->content['id'].'" method="post" class="myForm" name="sellCarForm" id="sellCarForm">
		<table border="0" cellspacing="1" cellpadding="2" width="99%" class="list">
		<tr class="title"><td>
		<b>'.$this->translate->_('Продажа автомобиля').'</b><img src="'.$this->root_path.'img/ccl/r_ex.gif" style="position:absolute; right:15px; top:9px;cursor:pointer;" alt="'.$this->translate->_('Закрыть окно').'" title="'.$this->translate->_('Закрыть окно').'" onclick="document.getElementById(\'sellCar\').style.display=\'none\';"></td></tr>
		<tr class="title"><td><span style="color:#008800"><b>'.$this->translate->_('Продавец').':<b></span><br> '.$ownerInfo.'<br></td></tr>
		<tr class="title"><td><span style="color:#990000"><b>'.$this->translate->_('Покупатель').':</b></span> <select name="newOwner" id="newOwner">'.$clients_list.'	</select><br></td></tr>
		<tr class="title"><td>'.$this->translate->_('Цена').': <input type="text" name="carNewPrice" style="width:150px;" id="carNewPrice"></td></tr>
		<tr><td><br><input type="button" name="sendForm" value="'.$this->translate->_('Отправить').'" id="save" onclick="if(confirm(\''.$this->translate->_('Вы действительно хотите сменить владельца автомобиля?').'\')){checkSellCarForm();}">
		<br></td></tr>
		</table>
		'.$hiddenFields.'
		<input type="hidden" name="oldPrice" value="'.$this->content['total'].'">
		<input type="hidden" name="carFrame" value="'.$this->content['frame'].'">
		<input type="hidden" name="sellDate" value="'.date('Y-m-d').'">
		</form>
		</div>
		<script>
		function checkSellCarForm() {
			var owner = document.getElementById("owner").value;
			var newOwner = document.getElementById("newOwner").value;
		
			var newPrice = document.getElementById("carNewPrice").value;
			
			if(owner == newOwner) {
				alert(\''.$this->translate->_('Покупатель и продавец одно лицо!').'\');
				exit;
			}
			else { 
				if(newPrice==\'\') {
					alert(\''.$this->translate->_('Не заполнено поле ЦЕНА!').'\');
					exit;
				}
				else document.getElementById("sellCarForm").submit();
			
			}
		}
		</script>
		<form class="myForm" name="showPayments" action="?mod=accounting&filter" method="post"><input type="hidden" name="searchCar" value="'.$this->car_id.'"><input type="hidden" name="searchClient" value="'.$this->content['buyer'].'"></form>';
   }

   function getRecieversList()
   {
      $list = $this->mysqlQuery("SELECT id,name FROM `ccl_".ACCOUNT_SUFFIX."recievers` WHERE 1 ORDER BY name ASC");
      $out = '<select name="reciever">
		<option value="0"';
      if ($this->content['reciever'] == 0)
         $out .= ' selected="selected"';
      $out .= '>'.$this->translate->_('не выбран').'</option>';

      while ($line = mysql_fetch_array($list))
      {

         $out .= '<option value="'.$line['id'].'"';
         if ($this->content['reciever'] == $line['id'])
         {
            $out.=' selected="selected"';
         }
         $out.='>'.stripslashes($line['name']).'</option>
			';
      }
      $out .= '</select>';
      return $out;
   }

   function wrapFile($in, $bg='')
   {
      if ($bg != '')
         $wrap_bg = 'background-color:'.$bg;
      return '<div style="float:left; width:128px; padding:2px; border:1px solid #ddd;text-align:center; margin-right:4px; margin-bottom:5px;'.$wrap_bg.'">
		'.$in.'</div>';
   }

   function typeSwitch()
   {
      $out = array();
      switch ($this->content['type'])
      {
         case 1:
            $out['order'] = ' checked="checked"';
            break;
         case 2:
            $out['sale'] = ' checked="checked"';
            break;
         case 3:
            $out['cancel'] = ' checked="checked"';
            break;
      }

      return '<span style="color:#555;">
		<input type="radio" name="type" value="1" id="type_order"'.$out['order'].' style="width:20px;"><label for="type_order">'.$this->translate->_('заказ').'</label>
		<input type="radio" name="type" value="2" id="type_sale"'.$out['sale'].' style="width:20px;"><label for="type_sale">'.$this->translate->_('на продажу').'</label>
		<input type="radio" name="type" value="3" id="type_cancel"'.$out['cancel'].' style="width:20px;"><label for="type_cancel">'.$this->translate->_('отказ').'</label>
		</span>';
   }

   function folderSelect()
   {
      $out = '
		
		<select name="folder" style="width:120px;float:right;">';
      foreach ($this->photo_folders as $k => $v)
      {
         $out .= '
			<option value="'.$k.'">'.$v.'</option>';
      }
      $out .= '</select>'.$this->translate->_('раздел').':<br>';
      return $out;
   }

}

?>