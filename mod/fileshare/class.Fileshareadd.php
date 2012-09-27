<?
require_once("templates/class.FileshareTemplates.php");
class FileshareAdd extends Proto {
	// список идентификаторов кому доступен этот модуль и эта часть
	var $fsAccess=array(1,4,7);
	var $fsAccessName=array(1=>'Администратор',2=>'Клиент / Дилер',3=>'Оператор',4=>'Менеджер',5=>'Поставщик');
		
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
		$this->view=new FileshareTemplates($this->root_path);
		$this->view->fsAccessName=$this->fsAccessName;
	}

	function getContent() {
		$this->_LoadView();
 		if(in_array($_SESSION['user_type'],$this->fsAccess)) {
			if(isset($_FILES["uploadfile"]["tmp_name"]) && $_FILES["uploadfile"]["tmp_name"]) 
				$this->FileshareUpFile();
			$this->page .= $this->view->FileshareAddContent();
 		} else
 			$this->page .= $this->view->FileshareStubContent();
	}
	
	private function FileshareUpFile() {
//		foreach($_POST["access"] as $k=>$v) $access2[]=$this->fsAccessName[$k];
//		$access2[]="админ";
		$access2=array("Администратор","Менеджер","админ");
		$access2=implode(",",$access2);
		$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."fileshare` (`originfile`,`sizefile`,`title`,`text`,`dt_up`,`access2`,`access2delete`) VALUES ('".addslashes($_FILES["uploadfile"]["name"])."',".intval($_FILES["uploadfile"]["size"]).",'".addslashes($_POST["title"]?$_POST["title"]:$_FILES["uploadfile"]["name"])."','".addslashes($_POST["text"])."',now(),'".$access2."',".intval($_SESSION['user_type']).")");
		$id=mysql_insert_id();
		if($id) {
			move_uploaded_file($_FILES["uploadfile"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/fileshare/".$id);
			header('Location: '.$this->root_path.'?mod=fileshare'); exit;
		}
	}
}
?>