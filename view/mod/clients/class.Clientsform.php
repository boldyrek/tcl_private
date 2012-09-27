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
			die('<div class="warn" style="width:900px;">Ошибка! Клиент с такими параметрами в базе не обнаружен</div>');
			
		}
		else $this->mode = '?mod=clients&sw=save&id='.intval($_GET['customer_id']);	
		}
		elseif(isset($_GET['add'])) {
		$this->mode = '?mod=clients&sw=add';
		}
		if($this->mode!='') $this->page .= $this->customerForm();

	}
	
	function customerForm() {
		
		$paid = $this->paid['total'];
		
		if($this->content['dealer']=='1') $dealer = ' checked="checked"';
		else $dealer ='';
		
		if($this->content['balance']<0) $advance=" (аванс)";
		if(isset($_GET['add'])) $this->content = array();
		//скан паспорта
		if($this->content['scan']!='')
		{
			$thumb = '<img src="'.$this->root_path.'img/ccl/attached.gif" vspace="5" border="0">';
			$scan = '<div style="padding-left:50px;">
			<a href="'.$this->root_path.'photos/scan/'.ACCOUNT_SUFFIX.$this->id.'/'.$this->content['scan'].'" target="_blank">'.$thumb.'</a><br>
			</div>';
		}
		else {
			$scan = '';
		}
		
		//список всех дилеров
		$myDealers = $this->buildSelect($this->dealers, 'mydealer', $this->content['mydealer'], 'не выбран', '10');
		
		$out = '
		<div class="cont_customer">
		<h3>Клиент</h3>
		<table width="792" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
			<td width="150" align="right" class="title">ФИО</td>
			<td class="rowA title"><b>'.cleanContent($this->content['name']).'</b></td>
	
			</tr>
		  <tr>
			<td align="right" class="title rowB">адрес</td>
			<td class="rowA rowB title">'.$this->content['address'].'</td>

		  </tr>
		  <tr>
			<td align="right" class="title">Name (English)</td>
			<td class="rowA title">'.$this->content['name_en'].'</td>

		  </tr>
		  <tr>
			<td align="right" class="title rowB">Address (English)</td>
			<td class="rowA rowB title">'.$this->content['address_en'].'</td>
	
		  </tr>
		  <tr>
			<td align="right" class="title">контакты</td>
			<td class="rowA title">'.$this->content['contacts'].'</td>

		  </tr>
		  <tr>
			<td align="right" class="title rowB">паспорт</td>
			<td class="rowA rowB title">'.$this->content['passport'].'</td>

		  </tr>
		  <tr>
			<td align="right" class="title">email</td>
			<td class="rowA title">'.$this->content['email'].'</td>

		  </tr>
		  <tr>
			<td class="title rowB">&nbsp;</td>
			<td class="title rowB">&nbsp;</td>

		  </tr>
		
		</table>
		</div>
		<table width="230" class="list"><tr>
		    <tr><td class="title rowB">копия паспорта:</td></tr>
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
			$out = 'нет договоров, <a href="'.$this->root_path.'?mod=contracts&sw=form&client='.intval($_GET['customer_id']).'">создать</a>';
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