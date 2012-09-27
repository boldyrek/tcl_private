<?
require_once("templates/class.{$module}Templates.php");
class PortsForm extends Proto {

	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
			$this->page .= $this->module_content;
		}
		$this->page .= $this->templates['footer'];

		$this->errorsPublisher();
		$this->publish();
	}
	function _LoadView()
	{
		$this->view=new PortsTemplates($this->root_path);
	}
	function getError()
	{
		$error='';
		if ($_SESSION['error']!='')
		{
			$error=$this->view->getError($_SESSION['error']);
			$_SESSION['error']='';
		}
		return $error;
	}
	function getPorts($id)
	{
		$request = "SELECT * FROM ccl_".ACCOUNT_SUFFIX."ports WHERE id=".$id;
		if ($res2=$this->mysqlQuery($request))
		{
			return mysql_fetch_row($res2);
		}
	}
	function getContent() {
		$this->_LoadView();
		$this->page.=$this->view->top_ports_link();
		$this->page.= $this->view->getTop();
		$this->page.= $this->view->getTitle();
		$this->page.= $this->getError();
		if (isset($_REQUEST['id']) && intval($_REQUEST['id'])!=0)
		{
			$this->view->mass=$this->getPorts($_REQUEST['id']);
		}
		$this->page.= $this->view->getForm();
		$this->page.= $this->view->getBottom();
	}

}
?>