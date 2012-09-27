<?
class car {
	
	var $clients;
	var $suppliers;
	var $container;
	var $content;
	var $files;
	var $cert;
	var $invoice;
	var $car_id;
	var $ports;
	 
	function getContent() {
		return $this->content;
	}
	
	function getAdditional() {
		$this->clients = dtlQuery('ccl_'.ACCOUNT_SUFFIX.'customers', 'name, id, dealer, mydealer', '', '', 'name', 'ASC', '');
		$this->suppliers = dtlQuery('ccl_'.ACCOUNT_SUFFIX.'suppliers', 'name, id', '', '', 'name', 'ASC', '');
		$this->container = @mysql_fetch_array(dtlQuery('ccl_'.ACCOUNT_SUFFIX.'containers', 'id,number,bishkek,arrived', 'id', $this->content['container'], '', '', ''));
		$this->files = dtlQuery('ccl_'.ACCOUNT_SUFFIX.'cars_photos', '*', 'car', $this->car_id, '', '', '');
		$this->cert = mysql_fetch_array(dtlQuery('ccl_'.ACCOUNT_SUFFIX.'cars_cert', '*', 'car', $this->car_id,'','',''));
		$this->invoice = mysql_fetch_array(mysql_query("SELECT id,file FROM `ccl_".ACCOUNT_SUFFIX."invoices` WHERE `cars` LIKE '%".$this->car_id.";%' LIMIT 1"));

		$this->content['cert'] = $this->cert['file'];
		$this->content['invoice_file'] = $this->invoice['id'];
		if($this->content['supplier']!=0) $this->ports = dtlQuery('ccl_'.ACCOUNT_SUFFIX.'ports', '*', 'supplier', $this->content['supplier'], 'name', 'ASC', '');
	}
}

?>