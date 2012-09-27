<?

class ClientsForm extends Proto {
	
	var $content;
	var $cars;
	var $id;
	var $total_cars;
	var $paid;
	var $dealers;
	var $ontheway;
	var $prepay;
	var $mode;
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
		if(!isset($_GET['hidemenu'])) $this->page .= $this->makeTopMenu();
			$this->getContent();
			$this->page .= $this->module_content;
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		
		$this->id = intval($_GET['customer_id']);
		$this->getCustomerContent();
		
		if(isset($_GET['customer_id']) and intval($_GET['customer_id'])!='' and intval($_GET['customer_id'])!='0') {
		if($this->content['id']=='') {
			die('<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Клиент с такими параметрами в базе не обнаружен').'</div>');
			
		}
		else $this->mode = '?mod=clients&sw=save&id='.intval($_GET['customer_id']);	
		}
		elseif(isset($_GET['add'])) {
		$this->mode = '?mod=clients&sw=add';
		}
		if($this->mode!='') $this->page .= $this->customerForm();

	}
	
	function customerForm() {
		$out = '';
		$paid = $this->paid['total'];
		
		if(isset($_GET['add'])) $addReciever = '<div style="text-align:right"><input type="checkbox" name="addreciever" id="addreciever" style="width:30px;"><label for="addreciever">'.$this->translate->_('добавить получателя').'</label></div>';
		else $addReciever = '';
		
		
		if($this->content['dealer']=='1') $dealer = ' checked="checked"';
		else $dealer ='';
		
		if($this->content['balance']<0) $advance=' ('.$this->translate->_('аванс').')';
		if(isset($_GET['add'])) $this->content = array();
		//скан паспорта
		if($this->content['scan']!='')
		{
			$thumb = '<img src="'.$this->root_path.'img/ccl/attached.gif" vspace="5" border="0">';
			$scan = '<div style="padding-left:50px;">
			<a href="'.$this->root_path.'photos/scan/'.ACCOUNT_SUFFIX.$this->id.'/'.$this->content['scan'].'" target="_blank">'.$thumb.'</a><br>
			<a href="'.$this->root_path.'?mod=clients&sw=delete&what=scan&name='.$this->content['scan'].'" class="delete" onclick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить прикрепленный файл?').'\')">'.$this->translate->_('удалить').'</a></div>';
		}
		else {
			$scan = '
			<form action="'.$this->root_path.'?mod=upload" method="post" class="myForm" enctype="multipart/form-data">
			<input type="file" name="file">
			<input type="hidden" name="scanImage" value="'.$this->id.'">
			<br><input type="submit" value="'.$this->translate->_('Загрузить').'" id="save"></form>';
		}
		
		// ошибки и сообщения при отправке приглашения
		
		if(isset($_GET['inv'])) $inv = $_GET['inv'];
		else $inv = '';
		switch($inv) {
			case 'sent':
				$inv_report = '<h4 class="report">'.$this->translate->_('Пользователь создан.<br>
				Приглашение клиенту успешно отправлено.').'</h4>';
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
		//#########################################################
		
	
		//список всех дилеров
		$myDealers = $this->buildSelect($this->dealers, 'mydealer', $this->content['mydealer'], 'не выбран', '10');
		
		if($inv_report!='') $out .= '<div style="width:790px; background-color:#fff;padding:10px;">'.$inv_report.'</div>';
		
		$out .= '
		<form action="'.$this->root_path.$this->mode.(isset($_GET['hidemenu'])?'&hidemenu':'').'" method="post" enctype="multipart/form-data" class="myForm">
		<div class="cont_customer">
		<div style="float:right;font-size:11px;"><a href="'.$this->root_path.'?mod=client_cars&client='.$this->content['id'].'">'.$this->translate->_('автомобили клиента').'</a></div>
		<h3>'.$this->translate->_('Клиент').'</h3>
		<table width="792" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
			<td width="113" align="right" class="title">'.$this->translate->_('ФИО').'</td>
			<td width="253" class="rowA title"><input type="text" name="name" value="'.cleanContent($this->content['name']).'" tabindex="1" /></td>
			';
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4') $out .= '<td align="right" class="title" width="150">'.$this->translate->_('доставлено на сумму').':</td>
			<td width="204" class="rowA title">'.$this->content['cars_delivered'].' ('.($this->total_cars-$this->ontheway).')</td>';
			else $out .= '<td class="title"></td>
			<td width="204" class="rowA title"></td>';
			$out .= '
			</tr>
		  <tr>
			<td align="right" class="title rowB">'.$this->translate->_('адрес').'</td>
			<td class="rowA rowB title"><input type="text" name="address" value="'.$this->content['address'].'" tabindex="2"/></td>
			';
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4') $out .= '<td align="right" class="title rowB">'.$this->translate->_('предоплата<br>за машины в пути').':</td>
			<td class="rowA rowB title">'.$this->prepay.' ('.$this->ontheway.')</td>';
			else $out .= '<td class="title rowB"></td>
			<td class="rowA rowB title"></td>';
		  $out .= '
		  </tr>
		  <tr>
			<td align="right" class="title">Name (English)</td>
			<td class="rowA title"><input type="text" name="name_en" value="'.$this->content['name_en'].'" tabindex="3"/></td>
			';
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4') $out .= '<td align="right" class="title">'.$this->translate->_('платежи').'</td>
			<td class="rowA title">'.$paid.'</td>';
			else $out .= '<td class="title"></td>
			<td class="rowA title"></td>';
			$out .= '
		  </tr>
		  <tr>
			<td align="right" class="title rowB">Address (English)</td>
			<td class="rowA rowB title"><input type="text" name="address_en" value="'.$this->content['address_en'].'" tabindex="4"/></td>';
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4') $out .= '<td align="right" class="title rowB">'.$this->translate->_('к оплате').'</td>
			<td class="rowA rowB title"><nobr>'.(isset($_GET['hidemenu'])?'':'<a href="'.$this->root_path.'?mod=clients&sw=detail&id='.$this->id.'&dealer='.$this->content['dealer'].'">').'<b>'.$this->content['balance'].'</b>'.(isset($_GET['hidemenu'])?'':'</a>').' '.$advance.' &nbsp; &nbsp;'.$this->translate->_('баланс').': '.$this->content['real_balance'].'</nobr></td>';
			else $out .= '<td class="title rowB"></td>
			<td class="rowA rowB title"></td>';
			$out .= '
		  </tr>
		  <tr>
			<td align="right" class="title">'.$this->translate->_('контакты').'</td>
			<td class="rowA title"><input type="text" name="contacts" value="'.$this->content['contacts'].'" tabindex="5" /></td>';
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4') $out .= '<td align="right" class="title">'.$this->translate->_('машины в пути').'</td>
			<td class="rowA title">'.$this->ontheway.'</td>';
			else $out .= '<td class="title"></td>
			<td class="rowA title"></td>';
		  $out .= '
		  </tr>
		  <tr>
			<td align="right" class="title rowB">'.$this->translate->_('паспорт').'</td>
			<td class="rowA rowB title"><input type="text" name="passport" value="'.$this->content['passport'].'" tabindex="6" /></td>';
			if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7' or $_SESSION['user_type']=='4') $out .= '<td align="right" class="title rowB">'.$this->translate->_('всего машин заказано').'</td>
			<td class="rowA rowB title">'.$this->total_cars.'</td>';
			else $out .= '<td class="title rowB"></td>
			<td class="rowA rowB title"></td>';
			$out .= '
		  </tr>
		  <tr>
			<td align="right" class="title">email</td>
			<td class="rowA title"><input type="text" name="email" value="'.$this->content['email'].'" tabindex="7" /></td>
			<td class="title">&nbsp;</td>
			<td class="rowA title"><table border="0" cellspacing="0" cellpadding="0">
	  <tr>
	    <td width="25" style="border:0px;"><input type="checkbox" name="dealer" id="dealer" style="border:0px;"'.$dealer.' tabindex="8" onClick="javascript:switchList(this.checked);"></td>
	    <td style="font-size:11px;border:0px;"><label for="dealer" style="cursor:hand; cursor:pointer;">'.$this->translate->_('дилер').'</label></td>
	  </tr>
	</table></td>
		  </tr>
		  <tr>
			<td class="title rowB" align="right">&nbsp;</td>
			<td class="title rowB"><input type="checkbox" name="spam" id="spam" '.($this->content['allowspam']=='1'?'checked="checked"':'').' style="width:20px;"><label for="spam">'.$this->translate->_('разрешить рассылку').'</label></td>
			<td class="title rowB" align="right">'.$this->translate->_('дилер этого клиента').':</td>
			<td class="rowA rowB title">'.$myDealers.'&nbsp;</td>
		  </tr>
		  <tr>
		    <td class="title" align="right">'.$this->translate->_('логин').':</td>
		    <td class="rowA title">';
			if($this->id!='') $out .= '<input type="text" value="'.$this->ClientID().'" name="login" tabindex="11">';
			if(isset($_GET['error'])) $out .= '<br><span style="font-size:11px; color:#f00;">'.$this->translate->_('логин').' <b>'.$_GET['error'].'</b> '.$this->translate->_('уже существует!').'</span>';
			$out .='</td>
		    <td class="title">&nbsp;</td>
		    <td class="rowA title">&nbsp;</td>
	      </tr>
		  <tr>
		    <td class="title rowB" align="right" valign="top">'.$this->translate->_('пароль').':</td>
		    <td class="rowA rowB title">';
			if($this->id!='') $out .= '<input type="text" name="password" tabindex="12">
			<br><span style="font-size:10px; color:#777;">'.$this->translate->_('пароль можно только сменить!').'</span>';
			$out.='</td>';
		    if(ACCOUNT_ID=='0') $out.='
				<td class="title rowB" align="right">'.$this->translate->_('контракт').'</td>
				<td class="rowA rowB title">'.(isset($_GET['hidemenu'])?'&nbsp;':(isset($_GET['add'])?'&nbsp;':$this->contractsList($this->content['contract_info']))).'</td>';
			else $out.='
				<td class="title rowB"></td>
				<td class="rowA rowB title"></td>';
	      $out.='</tr>
                 <tr>
                    <td class="title rowA">&nbsp;</td>
                    <td class="rowA title" colspan="3">
                       <input type="checkbox" name="autocheck" id="autocheck" style="width:20px" '.($this->content['autocheck'] ? 'checked="checked"' : '').' />&nbsp;<label for="autocheck">'.$this->translate->_('разрешить Autocheck').'</label>
                    </td>
                 </tr>
		</table>
				<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:792px">
		  <tr>
		  <td>&nbsp;</td>
		  	<td class="title" width="230">
			<input type="checkbox" name="invite_user" id="invite" style="width:20px;"><label for="invite">&nbsp;'.$this->translate->_('пригласить клиента').'</label>
			</td></tr>
			</table>
		</div>
		<table width="802" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
			<td class="title">'.(isset($_GET['add'])?'':'<a href="'.$this->root_path.'?mod=clients&sw=delete&id='.$this->id.(isset($_GET['hidemenu'])?'&hidemenu':'').'" class="delete" onclick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить этого клиента?').'\')">'.$this->translate->_('удалить').'</a>').'
			'.$addReciever.'</td>
			<td width="214" align="right" class="title"><input type="submit" name="Submit" value="'.$this->translate->_('Сохранить').'" id="save" tabindex="13" /></td>
			<td width="8" align="right" class="title"><br />
			  <br /></td>
		  </tr>
		</table>
		</form>';
		if(!isset($_GET['add'])) $out.='
		<table width="230" class="list"><tr>
		    <tr><td class="title rowB">'.$this->translate->_('копия паспорта').':</td></tr>
		    <tr><td class="rowA rowB title">'.$scan.'</td></tr>
		</table>';
		
		$out .= '
		<script>
	
			function switchList(handler) {
			document.getElementById("Listmydealer").disabled = handler;
			}
			
			switchList(document.getElementById(\'dealer\').checked);
		</script>';
		
		if(isset($_GET['hidemenu'])) $out .= '
		<script>
		function toParent() {
			if(parent.check == \'1\') {
			parent.showForm();
			}
		}
		window.onload = toParent();
		</script>
		';
		return $out;
	}
	
	private function contractsList($data) {
		$num = @mysql_num_rows($data);
		if($num>0) {
			$i=0;
			while($i<$num) {
				$line = mysql_fetch_array($data);
				$out .= ', <a href="'.$this->root_path.'?mod=contracts&sw=form&contract='.$line['id'].'">'.$line['number'].'</a>';
				$i++;
			}
		}
		
		else {
			$out = $this->translate->_('нет договоров').', <a href="'.$this->root_path.'?mod=contracts&sw=form&client='.intval($_GET['customer_id']).'">создать</a>';
		}
		return ltrim($out,', ');
	}
	
	function ClientID() {
		$sql = "
		SELECT log_name 
		FROM `ccl_".ACCOUNT_SUFFIX."usrs` 
		WHERE `type` = '2' AND `u_id` = '".$this->id."'";
		$result = @mysql_fetch_array($this->mysqlQuery($sql));
	
		return $result['log_name'];
	}
	
	private function getCustomerContent() {
		if($this->id!='' and $this->id!='0') $this->content = mysql_fetch_array($this->mysqlQuery("
		SELECT *
		FROM `ccl_".ACCOUNT_SUFFIX."customers`
		WHERE `id` = '".$this->id."'"));

		if($this->content['dealer']==1) {
			$this->cars = $this->mysqlQuery("
			SELECT id,delivered,prepay 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".$this->id."' OR `dealer` = '".$this->id."'");
		}
		else $this->cars = $this->mysqlQuery(
		"SELECT id, prepay, delivered
		FROM `ccl_".ACCOUNT_SUFFIX."cars`
		WHERE `buyer` = '".$this->id."'
		ORDER BY `delivered` ASC");
		
		$this->getBalance();

		$this->dealers = $this->mysqlQuery(
		"SELECT id, name
		FROM `ccl_".ACCOUNT_SUFFIX."customers`
		WHERE `dealer` = '1'
		ORDER BY `name` ASC");
		
		$this->content['contract_info'] = $this->mysqlQuery("
		SELECT id, number 
		FROM `ccl_".ACCOUNT_SUFFIX."contracts` 
		WHERE `client` = '".$this->id."' 
		ORDER BY `number` DESC");
		
		$scan = mysql_fetch_array($this->mysqlQuery(
		"SELECT *
		FROM `ccl_".ACCOUNT_SUFFIX."scan`
		WHERE `client` = '".$this->id."'"));
		
		$this->content['scan'] = $scan['file'];
		
	}

	function getBalance() {
		$this->total_cars = mysql_num_rows($this->cars);
		$this->ontheway = 0;
		$this->prepay = 0;
		$j=1;
		while($j<=$this->total_cars)
		{
			$line = mysql_fetch_array($this->cars);
			if($line['delivered'] == '0') { 
			$this->ontheway++;
			$this->prepay = $this->prepay + $line['prepay'];
			}
			$j++;
		}

		if($this->id) {
			$paid = mysql_fetch_array($this->mysqlQuery(
			"SELECT SUM(amount) as total
			FROM `ccl_".ACCOUNT_SUFFIX."payments` 
			WHERE `client` = '".$this->id."'"));

			$clients_paid = mysql_fetch_array($this->mysqlQuery("
			SELECT DISTINCT SUM(ccl_".ACCOUNT_SUFFIX."payments.amount) AS total 
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			INNER JOIN `ccl_".ACCOUNT_SUFFIX."payments` ON (ccl_".ACCOUNT_SUFFIX."payments.client = ccl_".ACCOUNT_SUFFIX."customers.id) 
			WHERE `mydealer` = '".$this->id."' 
			AND `client` != '".$this->id."'"));
		
			$this->paid['total'] = $paid['total']+$clients_paid['total'];
			
		}
		
	}


}

?>