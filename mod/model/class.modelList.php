<?
require_once("templates/class.{$module}Templates.php");
class ModelList extends Proto {

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
		$_SESSION['error']='';
	}
	function getContent() {
		$this->_LoadView();

		$total_items = mysql_fetch_array($this->mysqlQuery(" SELECT COUNT(id) AS total  FROM `ccl_".ACCOUNT_SUFFIX."model` "));
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items['total']);
		}
		$request = "SELECT x.id, x.name, y.name as marka FROM ccl_".ACCOUNT_SUFFIX."model as x, ccl_".ACCOUNT_SUFFIX."marka as y WHERE x.marka_id=y.id";

		//задаем параметры таблицы
		$cols[1] = array('name' => 'marka', 	'caption' => $this->view->fieldList[0]);
		$cols[2] = array('name' => 'name', 	'caption' => $this->view->fieldList[1]);

		$this->page .= $this->view->top_model_link();
		$this->page .= $this->buildList($request, $cols, 'model', 'model', 'id', $pages['qlimit']);
		$this->page .= $pages['print'];
	}
}

?>