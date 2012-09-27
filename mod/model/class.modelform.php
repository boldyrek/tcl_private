<?
require_once("templates/class.{$module}Templates.php");
class ModelForm extends Proto {

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
		$this->view=new ModelTemplates($this->root_path);
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
	function getModel($id)
	{
		$request = "SELECT * FROM ccl_".ACCOUNT_SUFFIX."model WHERE id=".$id;
		if ($res2=$this->mysqlQuery($request))
		{
			return mysql_fetch_row($res2);
		}
	}

	function get_marka_list($id)
	{
		$this->marka=$this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."marka` ORDER BY name");

		$marka_list = buildSelect($this->marka, 'marka_id', $id, $this->translate->_('не выбрано'), '1');
		return $marka_list;
	}

	function getContent() {
		$this->_LoadView();
		$this->page.=$this->view->top_model_link();
		$this->page.= $this->view->getTop();
		$this->page.= $this->view->getTitle();
		$this->page.= $this->getError();
		if (isset($_REQUEST['id']) && intval($_REQUEST['id'])!=0)
		{
			$this->view->mass=$this->getModel($_REQUEST['id']);
			$marka=$this->view->mass[2];
		}
		else {

			$marka=0;
		}
		$this->page.= $this->view->getForm($this->get_marka_list($marka));
		$this->page.= $this->view->getBottom();
	}

}
?>