<?
require_once("templates/class.{$module}Templates.php");
class ServicesList extends Proto {

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
		$this->view=new ServicesTemplates($this->root_path);
		$_SESSION['error']='';
	}
	function getContent() {
		$this->_LoadView();
		
		$total_items = mysql_fetch_array($this->mysqlQuery(" SELECT COUNT(id) AS total  FROM `ccl_".ACCOUNT_SUFFIX."services` "));
		if($total_items['total']>$this->per_page)
		{
			$pages = $this->pageBrowse($_GET['page'], $_GET['mod'], $total_items['total']);
		}
		if($_SESSION['user_type']=='5') $where = "ccl_".ACCOUNT_SUFFIX."containers.expeditor = '4'";
		else $where = '1';
		$request = "
		SELECT *
		FROM ccl_".ACCOUNT_SUFFIX."services
		WHERE ".$where;

		//задаем параметры таблицы
		$cols[1] = array('name' => 'item', 	'caption' => $this->view->fieldList[0],	'width' => '90');
		$cols[2] = array('name' => 'description','caption' => $this->view->fieldList[1],'width' => '250');
		$cols[3] = array('name' => 'cost', 'caption' => $this->view->fieldList[2],'width' => '250');
		$cols[4] = array('name' => 'quantity', 'caption' => $this->view->fieldList[3], 'width' => '250');

		$this->page .= $this->view->top_services_link();
		$this->page .= $this->buildList($request, $cols, 'services', 'services', 'id', $pages['qlimit']);
		$this->page .= $pages['print'];
	}
}

?>