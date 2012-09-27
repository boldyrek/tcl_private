<?

class customer {
	var $content;
	var $cars;
	var $id;
	var $total_cars;
	var $paid;
	var $dealers;
	var $ontheway;
	var $prepay;
		
	function getContent() {
		if($this->id!='' and $this->id!='0') $this->content = mysql_fetch_array(dtlQuery('ccl_'.ACCOUNT_SUFFIX.'customers', '*', 'id', $this->id, '', '', ''));

		if($this->content['dealer']==1) {
			$this->cars = mysql_query("SELECT id,delivered,prepay FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `buyer` = '".$this->id."' or `dealer` = '".$this->id."'");
		}
		else $this->cars = dtlQuery('ccl_'.ACCOUNT_SUFFIX.'cars', 'id, prepay, delivered', 'buyer', $this->id, 'delivered', 'ASC', '');
		$this->getBalance();

		$this->dealers = dtlQuery('ccl_'.ACCOUNT_SUFFIX.'customers', 'id,name', 'dealer', '1', 'name', 'ASC', '');
		$this->content['contract_info'] = mysql_query("SELECT id, number 
		FROM `ccl_".ACCOUNT_SUFFIX."contracts` 
		WHERE `client` = '".$this->id."' 
		ORDER BY `number` DESC");
		
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
			$paid = mysql_fetch_array(dtlQuery('ccl_'.ACCOUNT_SUFFIX.'accounting', 'SUM(amount) as total', 'client', $this->id, '', '', ''));
			$clients_paid = mysql_fetch_array(mysql_query("
			SELECT DISTINCT SUM(ccl_".ACCOUNT_SUFFIX."accounting.amount) AS total
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			INNER JOIN `ccl_".ACCOUNT_SUFFIX."accounting` ON (ccl_".ACCOUNT_SUFFIX."accounting.client = ccl_".ACCOUNT_SUFFIX."customers.id)
			WHERE `mydealer` = '".$this->id."' 
			AND `client` != '".$this->id."' AND `ccl_".ACCOUNT_SUFFIX."accounting`.type = '1'"));
		
			$this->paid['total'] = $paid['total']+$clients_paid['total'];
			
			$scan = mysql_fetch_array(dtlQuery('ccl_'.ACCOUNT_SUFFIX.'scan', '*', 'client', $this->id, '', '', ''));
			$this->content['scan'] = $scan['file'];	
		}
		
	}

}

?>