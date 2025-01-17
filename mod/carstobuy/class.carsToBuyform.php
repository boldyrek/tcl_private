<?

class carsToBuyform extends Proto {

	var $clients;
	var $suppliers;
	var $container;
	var $content;
	var $files;
	var $cert;
	var $invoice;
	var $car_id;
	var $ports;

	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];

		$this->errorsPublisher();
		$this->publish();
	}

	public function getContent() {
		$this->content['date'] = date('Y-m-d');
		if(!isset($_GET['add'])) {
			$this->car_id = intval($_GET['car_id']);

			$this->content = @mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."carstobuy.*,ccl_".ACCOUNT_SUFFIX."customers.name, ccl_".ACCOUNT_SUFFIX."customers.id as buyer_id
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."carstobuy` 
			ON (ccl_".ACCOUNT_SUFFIX."customers.id=ccl_".ACCOUNT_SUFFIX."carstobuy.client) 
			WHERE ccl_".ACCOUNT_SUFFIX."carstobuy.id ='".$this->car_id."'"));

		}
		else {
			$this->car_id = '0';
			$this->content['id'] = '0';
		}

		if($this->content['id']=='') {
			$this->page .= '<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Автомобиль с такими параметрами в базе не обнаружен').'</div>';
		}
		else {
			if (isset($_GET['success'])) $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
			$this->getAdditional();
			$this->page .= $this->car_edit();
		}
	}

	private function getAdditional() {
		$this->clients = $this->getCustomersList();
	}

        private function LoadFormData(&$content, &$founds_by_vin) {
                if (!isset($_SESSION['ccl_carstobuy_add_FormData'])) {
                    return false;
                }
                
                $founds_by_vin = $_SESSION['ccl_carstobuy_add_FormData']['founds_by_vin'];
                
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['model'])) $content['model'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['model']; else $content['model'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['body'])) $content['body'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['body']; else $content['body'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['auctionname'])) $content['auctionname'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['auctionname']; else $content['auctionname'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['lane'])) $content['lane'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['lane']; else $content['lane'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['run'])) $content['run'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['run']; else $content['run'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['time'])) $content['time'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['time']; else $content['time'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['vin'])) $content['vin'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['vin']; else $content['vin'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['salon'])) $content['salon'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['salon']; else $content['salon'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['maxprice'])) $content['maxprice'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['maxprice']; else $content['maxprice'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['url'])) $content['url'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['url']; else $content['url'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['years'])) $content['years'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['years']; else $content['years'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['prepay'])) $content['prepay'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['prepay']; else $content['prepay'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['client'])) $content['client'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['client']; else $content['client'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['other'])) $content['other'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['other']; else $content['other'] = '';
                if (isset($_SESSION['ccl_carstobuy_add_FormData']['post']['date'])) $content['date'] = $_SESSION['ccl_carstobuy_add_FormData']['post']['date']; else $content['date'] = '';
                
                unset($_SESSION['ccl_carstobuy_add_FormData']);
                
                return true;
        }


        protected function warning_message_car_exist($founds_by_vin) {
            $this->page .= '<div style="position:absolute; margin-left: 705px; margin-top:0px; padding-left: 5px; width:260px; background-color:#fff;">';
            $this->page .= '<h2 style="color: red;">'.$this->translate->_('Внимание!').'</h2>';
            $this->page .= '<strong>'.$this->translate->_('Автомобили с таким VIN кодом уже присутствуют в БД').'</strong>';
            foreach ($founds_by_vin as $indx => $rec) {
                if ($rec['table_name'] == 'carstobuy') {
                    $link = '<a href="/?mod=carstobuy&sw=form&car_id='.$rec['id'].'" target="_blank">'.(trim($rec['model']) == '' ? $rec['vin'] : trim($rec['model'])).'</a>';
                } else {
                    $link = '<a href="/?mod=cars&sw=form&car_id='.$rec['id'].'" target="_blank">'.(trim($rec['model']) == '' ? $rec['frame'] : trim($rec['model'])).'</a>';
                }
                $this->page .= '<p>'.$link.'</p>';
            }
            $this->page .= '</div>';
        }
        
	function car_edit() {

		//адрес формы
		if(!isset($_GET['add'])) $form_link = '&sw=save&id='.$this->car_id;
		else $form_link = '&sw=add';

                $founds_by_vin = array();
                if ($this->LoadFormData(&$this->content, &$founds_by_vin)) {
                    $this->warning_message_car_exist($founds_by_vin);
                    $form_link = $form_link.'&second='.urlencode($this->content['vin']);
                }
                
		//формирование списка покупателей и дилеров
		$num = @mysql_num_rows($this->clients);
		$i=1;
		$clients_list = '<select name="client">
		<option value="0"';
		if($this->content['client']=='0') $clients_list .= ' selected="selected"';
		$clients_list .= '> '.$this->translate->_('- не выбран -').' </option>';
		while($i<=$num)
		{
			$line = mysql_fetch_array($this->clients);
			//все клиенты
			$clients_list.='<option value="'.$line['id'].'"';
			
			if($this->content['buyer_id'] == $line['id']) {
				$clients_list.=' selected="selected"';
			}

			$clients_list.='>'.stripslashes($line['name']).'</option>
			';

			$i++;
		}
		$clients_list .= '</select>';

		//#######################################

		$this->page .= '
                <script src="/js/jquery-1.4.2_min.js"></script>
                <script src="'.$this->root_path.'js/datepicker.js"></script>
		<script type="text/javascript">
                   myDateFormat = new Array("yyyy-mm-dd");
                   $(function()
                   {
                      var vincode = $("#to");
                      
                      vincode.blur(function()
                      {
                         if ($.trim(vincode.val()) == "")
                         {
                            alert("Empty string");
                            vincode.focus();
                            return;
                         }

                         vincode.addClass("input-ajax-loader");

                         $.get("/", {mod:"carstobuy", sw:"checkvin", vincode:vincode.val()}, function(response)
                         {
                            vincode.removeClass("input-ajax-loader");
                            
                            $("#vincode-result")
                            .html(response)
                            .show();
                         });
                      });
                   });
                </script>
		<form class="myForm" action="'.$this->root_path.'?mod=carstobuy'.$form_link.'" method="post">
		<div class="cont_car">
		<h3>'.$this->translate->_('Автомобиль к покупке').'</h3>
		<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">

                  <tr>
		    <td align="right" class="title" width="100">'.$this->translate->_('Название').'</td>
		    <td class="title" width="210"><input type="text" id="model" name="model" value="'.$this->content['model'].'" tabindex="1" /></td>
			<td align="right" class="title">'.$this->translate->_('дата').'</td>
			<td width="225" class="rowA title"><input type="text" id="date" name="date" value="'.$this->content['date'].'" tabindex="9" id="buyDate" style="width:90%;" /><img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'buyDate\', \'\', myDateFormat);" class="datePicker" style="margin:0px;margin-bottom:-3px;">
			<br></td>
		  </tr>
                  <tr>
		    <td align="right" class="rowB title">'.$this->translate->_('Время аукциона').'</td>
		    <td class="rowB title"><input type="text" id="time" name="time" value="'.$this->content['time'].'" tabindex="2" /></td>
			<td align="right" class="rowB title">'.$this->translate->_('Название аукциона').'</td>
			<td class="rowB title"><input type="text" id="auctionname" name="auctionname" value="'.$this->content['auctionname'].'" tabindex="2" /></td>
		  </tr>
                  <tr>
		    <td align="right" class="rowA title">'.$this->translate->_('Номер линии').'</td>
		    <td class="rowB title"><input type="text" id="lane" name="lane" value="'.$this->content['lane'].'" tabindex="2" /></td>
                        <td align="right" class="rowA title">'.$this->translate->_('Макс. цена покупки<br> на аукционе').'</td>
                        <td cmod=carstobuy&sw=form&car_id=1&successlass="rowA title"><input type="text" id="maxprice" name="maxprice" value="'.$this->content['maxprice'].'" tabindex="2" /></td>
		  </tr>
                   <tr>
		    <td align="right" class="rowB title">'.$this->translate->_('Номер лота').'</td>
		    <td class="rowB title"><input type="text" id="run" name="run" value="'.$this->content['run'].'" tabindex="2" /></td>
			<td align="right" class="rowB title">'.$this->translate->_('Год(а)').'</td>
		    <td class="rowB title"><input type="text" id="years" name="years" value="'.$this->content['years'].'" tabindex="4" /></td>
		  </tr>
                  <tr>
		    
			<td align="right" class="rowA title">'.$this->translate->_('VIN код').'</td>
			<td class="rowB title">
                           <input type="text" name="vin" value="'.$this->content['vin'].'" tabindex="12" id="to" />
                           <!-- <img id="check-vincode" src="/img/check.png" alt="'.$this->translate->_('проверить VIN').'" title="'.$this->translate->_('проверить VIN').'" style="padding-left:3px;cursor:pointer" /> -->
                           <div id="vincode-result" style="display:none"></div>
                           <input type="hidden" name="lastPrepay" value="'.$this->content['vin'].'">
                        </td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">'.$this->translate->_('Клиент').'</td>
		    <td class="rowA title">'.$clients_list.'</td>
			<td align="right" class="rowB title"></td>
			<td class="rowB title"><input type="checkbox" name="bought" '.($this->content['status']=='1'?' checked="checked"':'').' id="bought" style="width:15px;"> <label for="bought">'.$this->translate->_('куплена').'</label></td>
		  </tr>
		   <tr>
		    <td align="right" class="rowA title">'.$this->translate->_('Прочее').'</td>
		    <td class="rowB title" colspan="3">
			<textarea id="other" name="other" rows=10>'.$this->content['other'].'</textarea></td>

		  </tr>
	 
		</table>
		</div>
		<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:702px">
		  <tr>
			<td class="title"><a href="'.$this->root_path.'?mod=carstobuy&sw=delete&id='.intval($this->car_id).'" class="delete" onClick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить этот автомобиль?').'\')">'.$this->translate->_('удалить').'</a>
			</td>
			<td width="214" align="right" class="title">
			<input type="submit" name="Submit" value="'.$this->translate->_('Сохранить').'" id="save" tabindex="17" /></td>
		 </tr>
		  	  
		</table>
		</form>
		';
	}
}

?>