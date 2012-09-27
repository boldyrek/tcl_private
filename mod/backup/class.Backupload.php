<?
require_once("templates/class.BackupTemplates.php");
class BackupLoad extends Proto {

 	var $backupCollation='cp1251';
	var $backupLimit=1024; // лимит в Кб
	
	var $backupRetCount;
	var $backupRetSize;
	var $backupRestoreCnt;
	var $backupRestoreSize;
	
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
		$this->view=new BackupTemplates($this->root_path,$this->lang);
	}

	function getContent() {
		$this->_LoadView();
		
		$this->page .= $this->view->BackupLoadContent();
	}
}
?>