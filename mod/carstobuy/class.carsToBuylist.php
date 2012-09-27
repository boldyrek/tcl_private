<?

class carsToBuylist extends Proto {

   public function drawContent()
   {
      $this->page .= $this->templates['header'];
      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->page .= $this->makeList();
         $this->page .= $this->module_content;
      }
      $this->page .= $this->templates['footer'];

      $this->errorsPublisher();
      $this->publish();
   }

   function makeList()
   {
      $this->page .= '
         <div class="location">
            <table>
               <tr>
                  <td class="title" style="border:0px;">'.$this->translate->_('Машины к покупке').' &nbsp;|&nbsp; <a href="'.$this->root_path.'?mod=carstobuy&sw=form&add">'.$this->translate->_('Добавить').'</a>&nbsp;</td>
                  <td class="title" style="border:0px;" align="left">
                     <form action="/" method="get">
                     <input type="hidden" name="mod" value="carstobuy" />
                     '.$this->translate->_('Поиск по VIN').': <input name="vincode" value="'.trim((isset($_GET['vincode']) ? htmlspecialchars($_GET['vincode']) : '')).'" maxlength="17" />&nbsp;<input type="submit" value="'.$this->translate->_('искать').'" />
                     </form>
                  </td>
                  <!-- <td align="right" class="title" style="border:0px;"><a href="/?mod=carstobuy&sw=archive">Архив</a></td>-->
               </tr>
            </table>
         </div>';

      require('class.Cars2buy.php');

      $obj = new Cars2buy();
      
      return $obj->MainList();
   }

   function getContent()
   {
      //настройки списка
      $item_link = $this->root_path.'?mod=carstobuy&sw=form&car_id='; //ссылка на форму редактирования
      $add_link = $this->root_path.'?mod=carstobuy&sw=form&add'; // добавление нового автомобиля


      $total_items = mysql_fetch_array($this->mysqlQuery("SELECT COUNT(id) AS total FROM ccl_".ACCOUNT_SUFFIX."carstobuy"));

      if ($total_items['total'] > $this->per_page)
      {
         $pages = $this->pageBrowse(mysql_real_escape_string($_GET['page']), mysql_real_escape_string($_GET['mod']), $total_items['total']);
      }


      // показывать или не показывать купленные машины
      if (isset($_GET['showall']))
      {
         if (intval($_GET['showall']) == '1')
            $_SESSION['carstobuy']['showall'] = true;
         else
            $_SESSION['carstobuy']['showall'] = false;
      }
      if ($_SESSION['carstobuy']['showall'])
      {
         $local_filter = '';
      }
      else
      {
         $local_filter = " WHERE `status` = '0'";
      }

      //сортировка
      $order_list = $this->defineSort('sort_carstobuy', 'ccl_'.ACCOUNT_SUFFIX.'carstobuy.date DESC'); //добавляем сортировку в запрос
      $this->sortDeco('sort_carstobuy'); //выводим указатель того, что сейчас сортируется и направление сортировки
      //основной запрос в базу
      $request = "
			SELECT * FROM ccl_".ACCOUNT_SUFFIX."carstobuy".$local_filter." ORDER BY ".$order_list.$pages['qlimit'];

      $content = $this->mysqlQuery($request);
      $num = @mysql_num_rows($content);

      $this->page .= '<div class="location">
			<table width="100%">
			<tr>
				<td class="title" style="border:0px;">'.$this->translate->_('Машины к покупке').' &nbsp;|&nbsp; <a href="'.$this->root_path.'?mod=carstobuy&sw=form&add">'.$this->translate->_('Добавить').'</a>&nbsp;</td>
				<td align="right" class="title" style="border:0px;"> <input type="checkbox" name="show_all" id="show_all" style="cursor:hand; cursor:pointer;"'.($_SESSION['carstobuy']['showall'] ? ' checked="checked"' : '').' onclick="document.location=\'/?mod=carstobuy&showall='.($_SESSION['carstobuy']['showall'] ? '0' : '1').'\'">  <label for="show_all">'.$this->translate->_('показывать купленные').'</label></td>
			</tr>
			</table>		
			</div>
						
				<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
			 	<tr class="title sortButtons">
					'.$this->sorterTD('carstobuy', 'date', $this->translate->_('дата'), '73').'
					'.$this->sorterTD('carstobuy', 'lane', $this->translate->_('номер линии'), '200').'
                                        '.$this->sorterTD('carstobuy', 'run', $this->translate->_('номер лота'), '200').'
                                        '.$this->sorterTD('carstobuy', 'vin', $this->translate->_('VIN'), '200').'
                                        '.$this->sorterTD('carstobuy', 'time', $this->translate->_('время'), '200').'
                                        '.$this->sorterTD('carstobuy', 'model', $this->translate->_('название'), '200').'
					'.$this->sorterTD('carstobuy', 'maxprice', $this->translate->_('макс. цена'), '60').'
					'.$this->sorterTD('carstobuy', 'years', $this->translate->_('год(а)'), '').'
				</tr>';

      $class = "rowA rowB";
      $i = 1;
      while ($i <= $num)
      {
         $line = mysql_fetch_array($content);
         $j = 1;
         if ($line['status'] == '1')
            $class = "greenTR";
         $this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$item_link.$line['id'].'\'">
						<td class="sm">'.$line['date'].'&nbsp;</td>
						<td class="sm">'.$line['lane'].'&nbsp;</td>
                                                <td class="sm">'.$line['run'].'&nbsp;</td>
                                                <td class="sm">'.$line['vin'].'&nbsp;</td>
                                                <td class="sm">'.$line['model'].'&nbsp;</td>
						<td class="sm">'.$line['body'].'&nbsp;</td>
						<td class="sm">'.$line['salon'].'&nbsp;</td>
						<td class="sm">'.$line['maxprice'].'&nbsp;</td>
						<td class="sm">'.$line['years'].'&nbsp;</td>
					</tr>';
         $i++;
         if ($class == "rowA")
            $class = "rowA rowB"; else
            $class="rowA";
      }

      $this->page .= '</table>
			'.$pages['print'];

      // пустой список
      if ($num == '0')
         $this->page .= '<div class="green">'.$this->translate->_('по вашему запросу ничего не найдено').'</div>';
   }

}

?>