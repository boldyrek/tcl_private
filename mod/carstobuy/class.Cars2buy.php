<?

class Cars2buy extends Proto {
	
	var  $template = array();
	var  $tplCache = array();
	var $today;
	
	function getTemplate($path, $name) {
		if(!$this->cachedTemplate($name)) {
			$tpl = $_SERVER['DOCUMENT_ROOT'].'/mod/carstobuy/tpl/'.$path;
			
			if(file_exists($tpl)) {
				//echo $tpl;
				$this->template[$name] = file_get_contents($tpl);
				
				return true;
			}
			else return false;
		}
		else $this->template[$name] = $this->tplCache[$name];
	}
	
	function cachedTemplate($name) {
		if(@$this->tplCache[$name]!='') return true;
		else return false;
	}
	
	function pierceTemplate($find, $replace, $tpl) {
		$this->template[$tpl] = str_replace($find,$replace,$this->template[$tpl]);
	}
	
	function spawnTemplate($tpl) {
		return @$this->template[$tpl];
	}
	
         function MainList()
         {
            $this->today = date('Y-m-d');

            $vincode = trim($_GET['vincode']);

            if (isset($vincode) AND $vincode != '')
            {
               $out = '<div style="width:950px;"><div class="cars2buyItem">';
               
               if (preg_match('#[A-Za-z0-9]{17}#', $vincode))
               {
                  $query = $this->mysqlQuery("
                     SELECT *
                     FROM `ccl_".ACCOUNT_SUFFIX."carstobuy`
                     WHERE `vin` = '".$vincode."'
                     ORDER BY `id` DESC
                     LIMIT 1
                  ");

                  $data = mysql_fetch_array($query, MYSQLI_ASSOC);

                  if ($data)
                  {
                     $this->getTemplate('cars2buy.listItem.tpl', 'listItem');
                     $this->pierceTemplate('%id%', $data['id'], 'listItem');
                     $this->pierceTemplate('%list_item%', ' id="ctb_content"', 'listItem');
                     $this->pierceTemplate('%date%', $data['date'], 'listItem');
                     $this->pierceTemplate('%model%', ($data['model'] != '' ? $data['model'] : 'NONAME!'), 'listItem');
                     $this->pierceTemplate('%color%', $data['body'], 'listItem');
                     $this->pierceTemplate('%lane%', $data['lane'], 'listItem');
                     $this->pierceTemplate('%vin%', $data['vin'], 'listItem');
                     $this->pierceTemplate('%auctionname%', $data['auctionname'], 'listItem');
                     $this->pierceTemplate('%run%', $data['run'], 'listItem');
                     $this->pierceTemplate('%time%', $data['time'], 'listItem');
                     $this->pierceTemplate('%comment%', $data['other'], 'listItem');
                     $this->pierceTemplate('%price%', $data['maxprice'].'+++', 'listItem');
                     $this->pierceTemplate('%trans_price%', $this->translate->_('цена'), 'listItem');
                     $this->pierceTemplate('%years%', $data['years'], 'listItem');
                     $this->pierceTemplate('%list_item%', ' id="ctb_list_item"', 'listItem');
                     $this->pierceTemplate('%trans_realy-delete%', $this->translate->_('Вы действительно хотите удалить этот автомобиль?'), 'listItem');
                     $this->pierceTemplate('%trans_delete%', $this->translate->_('удалить'), 'listItem');
                     $out .= $this->spawnTemplate('listItem');
                  }
                  else
                  {
                     $out .= $this->translate->_('Ничего не найдено');
                  }
               }
               else
               {
                  $out .= $this->translate->_('Ничего не найдено');
               }

               $out .= '</div></div>';

               return $out;
            }
            else
            {
               // грузит в общую переменную template содержимое шаблона
               if ($this->getTemplate('cars2buy.list.tpl', 'body'))
               {
                  $this->pierceTemplate('%today%', $this->buyToday(), 'body'); // заменяет %name% на содержимое второй переменной в шаблоне template
                  $this->pierceTemplate('%next%', $this->buyNext(), 'body');
                  $this->pierceTemplate('%expired%', $this->buyExpired(), 'body');
                  $this->pierceTemplate('%planned%', $this->buyPlanned(), 'body');
               }

               return $this->spawnTemplate('body');
            }
         }
	
	function buyToday() {
		$out = '';
		$grey=false;
		
		$list = $this->arrayFromSql("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."carstobuy`
									WHERE `archive` = 0
									AND `date` = '".$this->today."'");
		foreach ($list as $id=>$data) {
			$this->getTemplate('cars2buy.listFullItem.tpl', 'listItem');
			$this->pierceTemplate('%id%',$data['id'],'listItem');
			$this->pierceTemplate('%list_item%',($grey?' id="ctb_list_item" class="ctb_big"':' class="ctb_big"'),'listItem');
			$this->pierceTemplate('%date%',$data['date'],'listItem');
			$this->pierceTemplate('%model%',($data['model']!=''?$data['model']:'NONAME!'),'listItem');
			$this->pierceTemplate('%color%',$data['body'],'listItem');
                        $this->pierceTemplate('%lane%',$data['lane'],'listItem');
                        $this->pierceTemplate('%vin%',$data['vin'],'listItem');
                        $this->pierceTemplate('%auctionname%',$data['auctionname'],'listItem');
                        $this->pierceTemplate('%run%',$data['run'],'listItem');
			$this->pierceTemplate('%time%',$data['time'],'listItem');
                        $this->pierceTemplate('%comment%',$data['other'],'listItem');
			$this->pierceTemplate('%price%',$data['maxprice'],'listItem');
			$this->pierceTemplate('%trans_price%',$this->translate->_('цена'),'listItem');
                        $this->pierceTemplate('%years%',$data['years'],'listItem');
			$out .= $this->spawnTemplate('listItem');
			if($grey==false) $grey=true;
			else $grey=false;
		}
		if($out!='') {
			$this->getTemplate('cars2buy.listToday.tpl', 'today');
			$this->pierceTemplate('%list%', $out, 'today');
			$this->pierceTemplate('%date%', date('Y-m-d'), 'today');
			$this->pierceTemplate('%trans_today%',$this->translate->_('Cегодня'),'today');
			return $this->spawnTemplate('today');		
		}
	
	}
	
	function groupByDate($date, $list) {
		$this->getTemplate('cars2buy.dategroup.tpl', 'dgroup');
		$this->pierceTemplate('%date%',$date,'dgroup');
		$this->pierceTemplate('%list%',$list,'dgroup');
		return $this->spawnTemplate('dgroup');
	}
	
	function buyNext() {

		$out = '';
		$datelist = array();
		$lastdate = '';
		$list = $this->arrayFromSql("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."carstobuy`
									WHERE `archive` = 0
									AND `date` > '".$this->today."'
									ORDER BY `date` ASC");
		foreach ($list as $id=>$data) {
			$this->getTemplate('cars2buy.listItem.tpl', 'listItem');
			$this->pierceTemplate('%id%',$data['id'],'listItem');
			$this->pierceTemplate('%list_item%',($grey?' id="ctb_list_item" class="ctb_big"':' class="ctb_big"'),'listItem');
			$this->pierceTemplate('%date%',$data['date'],'listItem');
			$this->pierceTemplate('%model%',($data['model']!=''?$data['model']:'NONAME!'),'listItem');
			$this->pierceTemplate('%color%',$data['body'],'listItem');
                        $this->pierceTemplate('%lane%',$data['lane'],'listItem');
                        $this->pierceTemplate('%vin%',$data['vin'],'listItem');
                        $this->pierceTemplate('%auctionname%',$data['auctionname'],'listItem');
                        $this->pierceTemplate('%run%',$data['run'],'listItem');
			$this->pierceTemplate('%time%',$data['time'],'listItem');
                        $this->pierceTemplate('%comment%',$data['other'],'listItem');
			$this->pierceTemplate('%price%',$data['maxprice'],'listItem');
			$this->pierceTemplate('%trans_price%',$this->translate->_('цена'),'listItem');
                        $this->pierceTemplate('%years%',$data['years'],'listItem');
			$this->pierceTemplate('%trans_realy-delete%',$this->translate->_('Вы действительно хотите удалить этот автомобиль?'),'listItem');
			$this->pierceTemplate('%trans_delete%',$this->translate->_('удалить'),'listItem');
			$datelist[$data['date']] .= $this->spawnTemplate('listItem');
		}
		
		foreach ($datelist as $date=>$list) {
			$out .= $this->groupByDate($date, $list);
		}
				
		if($out!='') {
			$this->getTemplate('cars2buy.listNext.tpl', 'next');
			$this->pierceTemplate('%list%', $out, 'next');
			$this->pierceTemplate('%trans_next%',$this->translate->_('Следующие к покупке'),'next');
			return $this->spawnTemplate('next');		
	}
	}
	
         function buyExpired()
         {
            $out = '';

            $datesQuery = $this->mysqlQuery("
               SELECT DISTINCT(date)
               FROM `ccl_carstobuy`
               WHERE `date` < CURRENT_DATE()
               ORDER BY `date` DESC
            ");

            while ($row = mysql_fetch_object($datesQuery))
            {
               $list = $this->arrayFromSql("
                  SELECT *
                  FROM `ccl_".ACCOUNT_SUFFIX."carstobuy`
                  WHERE `archive` = 0
                  AND `date` = '".$row->date."'
               ");

               $timestamp = strtotime($row->date);
               // $yesterday = mktime(0, 0, 0, date('m'), date('d')-1, date('Y'));

               $date_text = ($timestamp ? date('d.m.Y', $timestamp) : 'Unknown date');

               $out .= '<div style="margin-bottom:20px">';
               $out .= '<div style="background-color:#FF6666;color:#fff;padding:1px 5px"><h2>'.$date_text.'</h2></div>';

               foreach ($list as $id => $data)
               {
                  $this->getTemplate('cars2buy.listItem.tpl', 'listItem');
                  $this->pierceTemplate('%id%', $data['id'], 'listItem');
                  $this->pierceTemplate('%list_item%', ($grey ? ' id="ctb_list_item" class="ctb_big"' : ' class="ctb_big"'), 'listItem');
                  $this->pierceTemplate('%date%', $data['date'], 'listItem');
                  $this->pierceTemplate('%model%', ($data['model'] != '' ? $data['model'] : 'NONAME!'), 'listItem');
                  $this->pierceTemplate('%color%', $data['body'], 'listItem');
                  $this->pierceTemplate('%lane%', $data['lane'], 'listItem');
                  $this->pierceTemplate('%vin%', $data['vin'], 'listItem');
                  $this->pierceTemplate('%auctionname%', $data['auctionname'], 'listItem');
                  $this->pierceTemplate('%run%', $data['run'], 'listItem');
                  $this->pierceTemplate('%time%', $data['time'], 'listItem');
                  $this->pierceTemplate('%comment%', $data['other'], 'listItem');
                  $this->pierceTemplate('%price%', $data['maxprice'].'+++', 'listItem');
                  $this->pierceTemplate('%trans_price%', $this->translate->_('цена'), 'listItem');
                  $this->pierceTemplate('%years%', $data['years'], 'listItem');
                  $this->pierceTemplate('%list_item%', ' id="ctb_list_item"', 'listItem');
                  $this->pierceTemplate('%trans_realy-delete%', $this->translate->_('Вы действительно хотите удалить этот автомобиль?'), 'listItem');
                  $this->pierceTemplate('%trans_delete%', $this->translate->_('удалить'), 'listItem');
                  $out .= $this->spawnTemplate('listItem');
               }

               $out .= '</div>';
            }

            // echo $out; exit;
            
            if ($out != '')
            {
               $this->getTemplate('cars2buy.listExpired.tpl', 'expired');
               $this->pierceTemplate('%trans_expired%', $this->translate->_('Просроченные'), 'expired');
               $this->pierceTemplate('%list%', $out, 'expired');
               return $this->spawnTemplate('expired');
            }
         }
	
	function buyPlanned() {
		
		$out = '';
		$list = $this->arrayFromSql("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."carstobuy`
									WHERE `archive` = 0
									AND `date` = '0000-00-00'");
		foreach ($list as $id=>$data) {
			$this->getTemplate('cars2buy.listItem.tpl', 'listItem');
			$this->pierceTemplate('%id%',$data['id'],'listItem');
			$this->pierceTemplate('%date%','','listItem');
			$this->pierceTemplate('%model%',($data['model']!=''?$data['model']:'NONAME!'),'listItem');
			$this->pierceTemplate('%color%',$data['body'],'listItem');
                        $this->pierceTemplate('%lane%',$data['lane'],'listItem');
                        $this->pierceTemplate('%vin%',$data['vin'],'listItem');
                        $this->pierceTemplate('%auctionname%',$data['auctionname'],'listItem');
                        $this->pierceTemplate('%run%',$data['run'],'listItem');
			$this->pierceTemplate('%time%',$data['time'],'listItem');
                        $this->pierceTemplate('%comment%',$data['other'],'listItem');
			$this->pierceTemplate('%price%',$data['maxprice'],'listItem');
			$this->pierceTemplate('%trans_price%',$this->translate->_('цена'),'listItem');
                        $this->pierceTemplate('%years%',$data['years'],'listItem');
			$this->pierceTemplate('%list_item%',' id="ctb_list_item"','listItem');
			$this->pierceTemplate('%trans_realy-delete%',$this->translate->_('Вы действительно хотите удалить этот автомобиль?'),'listItem');
			$this->pierceTemplate('%trans_delete%',$this->translate->_('удалить'),'listItem');
			$out .= $this->spawnTemplate('listItem');
		}
		if($out!='') {
			$this->getTemplate('cars2buy.listPlanned.tpl', 'planned');
			$this->pierceTemplate('%trans_planned%', $this->translate->_('Планируемые'), 'planned');
			$this->pierceTemplate('%list%', $out, 'planned');
			return $this->spawnTemplate('planned');		
		}
	}
}

?>