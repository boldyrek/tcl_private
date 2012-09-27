<?
require_once("templates/class.InvoicesTemplates.php");
class InvoicesForm extends Proto {
	var $id;
	var $info;
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
	private function setVars()
	{
		$this->id=((isset($_REQUEST['id']))?intval($_REQUEST['id']):false);
		if ($this->id!=false) $this->view->id=$this->id;
	}
	protected function getContent()
	{
		$this->_LoadView();
		$this->setVars();
		$this->getInfo();
		$this->getTop();
		$this->view->selectCar=$this->getCarArray();
		$this->view->clientId=$this->info['client'];
		$this->view->customersList = $this->getCustomersList();
		$this->view->servArr=$this->getServiceList();

		$this->page.= $this->view->getForm($this->root_path."?mod=invoices&sw=save", $this->info);
		$this->page.= $this->view->getBottom();


	}
	function getInfo()
	{
		if ($this->id==false) return false;
		else {
			$sql="SELECT * from `ccl_".ACCOUNT_SUFFIX."invoices` where `id`='".$this->id."'";
			if ($res=$this->mysqlQuery($sql)){
				$tmp=mysql_fetch_assoc($res);
				$this->info=$tmp;
				$this->info['serv_list']=$this->getInvServList($tmp['id']);
				return $arr;
			}
			else return false;
		}
	}
	function getInvServList($inv_id)
	{
		$sql="SELECT * from `ccl_".ACCOUNT_SUFFIX."invoices_services` WHERE `invoice_id`='".$inv_id."' ORDER BY `num`";
		if ($res=$this->mysqlQuery($sql)){
			while ($tmp=mysql_fetch_assoc($res))
			$arr[]=$tmp;
		}
		if (!empty($arr)) return $arr;
		else return false;
	}
	function getTop()
	{
		$this->page.=$this->view->top_services_link();
		$this->page.= $this->view->getTop();
		$this->page.= $this->view->getTitle();
		$this->page.= $this->getError();
		$this->page.= $this->view->getJquery();
	}


	function _LoadView()
	{
		$this->view=new InvoicesTemplates($this->root_path, $this->lang);
	}
	function getError()
	{
		$error='';
		if (isset($_SESSION['error']) && $_SESSION['error']!='')
		{
			$error=$this->view->getError($_SESSION['error']);
			$_SESSION['error']='';
		}
		return $error;
	}

	function getCarArray()
	{
//		if($this->info['client']!='0') $filter = "WHERE `buyer` = '".$this->info['client']."' OR `reciever` = '".$this->info['client']."'";
//		else $filter = 
		$filter = "WHERE `id` NOT IN (SELECT carid FROM ccl_".ACCOUNT_SUFFIX."invoices WHERE `carid`!='".$this->info['carid']."')";
		
		$sql="SELECT id, model, frame FROM ccl_".ACCOUNT_SUFFIX."cars ".$filter." ORDER BY id DESC";
		
		if ($res=$this->mysqlQuery($sql))
		{
			while($tmp=mysql_fetch_assoc($res))
			$arr[]=$tmp;
		}
		return $arr;
	}
	function getServiceList()
	{
		$sql="select * from `ccl_".ACCOUNT_SUFFIX."services` ORDER BY `item`";
		if ($res=$this->mysqlQuery($sql))
		{
			while($tmp=mysql_fetch_assoc($res))
			$arr[]=$tmp;
		}
		return $arr;
	}

	function getCustomersList() {
		
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."customers`
		ORDER BY `name` ASC";
		
		$out = $this->mysqlQuery($sql);
		return $out;
	}
	
}
?>