<?
require_once("templates/class.{$module}Templates.php");
class PostList extends Proto {

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
		$this->view=new PostTemplates($this->root_path);
		$_SESSION['error']='';
	}
	function getContent() {
		$this->_LoadView();

		$total_items = mysql_fetch_array($this->mysqlQuery(" SELECT COUNT(id) AS total  FROM `ccl_".ACCOUNT_SUFFIX."post` "));
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items['total']);
		}
		$request = "SELECT * FROM ccl_".ACCOUNT_SUFFIX."post";
		//задаем параметры таблицы
		$cols[1] = array('name' => 'name', 	'caption' => $this->view->fieldList[0]);

		$this->page .= $this->view->top_post_link();
		$this->page .= $this->buildList($request, $cols, 'post', 'post', 'id', $pages['qlimit']);
		$this->page .= $pages['print'];
	}
}

?>