<?

class InvoicesSave extends Proto {
	var $id;
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

	}

	function setVars()
	{
		$this->id=((isset($_REQUEST['id']))?intval($_REQUEST['id']):false);
	}
	function Process() {
		$this->setVars();
	//	print_r ($_POST); die;
		if (isset($_REQUEST['id']))
		$this->Update();
		else $this->Insert();
		$this->redirect($this->root_path.'?mod=invoices&sw=form&id='.$this->id.'&success');
		// сохранение инвойса
	}
	function Update()
	{
		$sql="UPDATE `ccl_".ACCOUNT_SUFFIX."invoices` SET
		`carid`='".intval($_POST['carId'])."',
		`client`='".intval($_POST['client'])."',
		`number`='".clean_txt($_POST['number'])."',
		`date`='".clean_txt($_POST['dat'])."',
		`subitog`='".floatval($_POST['subitog'])."',
		`opl`='".floatval($_POST['opl'])."',
		`itog`='".floatval($_POST['itog'])."',
		`access`='".(isset($_POST['clientaccess'])?"1":"0")."'
		 WHERE `id`='".$this->id."'";
		
		if ($this->mysqlQuery($sql))
		$this->InsertServ();
	}
	function Insert()
	{
		$sql="INSERT INTO `ccl_".ACCOUNT_SUFFIX."invoices` (`carid`,`client`,`number`,`date`,`subitog`,`opl`,`itog`,`access`)
		VALUES ('".intval($_POST['carId'])."', '".intval($_POST['client'])."', '".clean_txt($_POST['number'])."', '".clean_txt($_POST['dat'])."', '".floatval($_POST['subitog'])."', '".floatval($_POST['opl'])."', '".floatval($_POST['itog'])."','".(@$_POST['clientaccess']?"1":"0")."')";
		if ($this->mysqlQuery($sql))
		{
			$this->id=mysql_insert_id();
			$this->InsertServ();
		}
	}
	function InsertServ()
	{
		$this->delServ($this->id);
		foreach ($_POST['serv_opt'] as $num=>$id)
		{
			if ($id==-1) continue ;
			$sql="INSERT into `ccl_".ACCOUNT_SUFFIX."invoices_services` (`invoice_id`, `item_id`, `num`, `description`, `cost`, `quantity`, `summ`) VALUES ('".$this->id."', '".intval($id)."', '".intval($num)."', '".clean_area($_POST['serv_soll'][$num])."', '".floatval($_POST['cost'][$num])."', '".intval($_POST['quantity'][$num])."', '".(floatval($_POST['cost'][$num])*intval($_POST['quantity'][$num]))."')";
			$this->mysqlQuery($sql);
		}
	}
	function delServ($id)
	{
		$sql="DELETE FROM `ccl_".ACCOUNT_SUFFIX."invoices_services` WHERE `invoice_id`='".$id."'";
		$this->mysqlQuery($sql);
	}


}
?>