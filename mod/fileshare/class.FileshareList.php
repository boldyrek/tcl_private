<?
require_once("templates/class.FileshareTemplates.php");
class FileshareList extends Proto {
	// список идентификаторов кому доступен этот модуль и эта часть
	var $fsAccess=array(1,4,7);
	var $fsAccessName=array(1=>'Администратор',2=>'Клиент / Дилер',3=>'Оператор',4=>'Менеджер',5=>'Поставщик');
	
	var $per_page=20;
	var $fsPages;
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
	}

	function getContent() {
 		$this->_LoadView();
 		if(in_array($_SESSION['user_type'],$this->fsAccess)) {
 			if(isset($_GET["id"]) && intval($_GET["id"])) 
 				$this->fsDownload(intval($_GET["id"]));
 				
 			if(isset($_GET["delete"]) && intval($_GET["delete"])) 
 				$this->fsDelete(intval($_GET["delete"]));
 				
	  		$this->page .= $this->view->FileshareListContent($this->fsList(),$this->fsPages);
	  	} else
	  		$this->page .= $this->view->FileshareStubContent();
	}
	
	// построение списка файлов
	function fsList(){
		if($_SESSION['user_type']!=7 && $_SESSION['user_type']!=1)
			$W="WHERE `access2` LIKE '%".$this->fsAccessName[$_SESSION['user_type']]."%'";
		else $W="";
		
		$total_items=mysql_fetch_array($this->mysqlQuery("SELECT COUNT(*) FROM `ccl_".ACCOUNT_SUFFIX."fileshare`"));
		
		if($total_items[0]>$this->per_page)
			$this->fsPages = $this->pageBrowse(intval($_GET['page']), $_GET['mod'], $total_items[0]);
			
		$q=$this->mysqlQuery("SELECT *,UNIX_TIMESTAMP(`dt_up`) as dt_up FROM `ccl_".ACCOUNT_SUFFIX."fileshare` {$W} ORDER BY `dt_up` DESC {$this->fsPages['qlimit']}");
		
		if (mysql_num_rows($q)) 
			while($l=mysql_fetch_assoc($q)) { 
				$l["sizefile"]=$this->fsbyte2kmg($l["sizefile"]);
				$l["title"]=stripslashes($l["title"]);
				$l["text"]=stripslashes($l["text"]);
				$R[]=$l; 
			}
		
		return $this->view->FileshareListTPL(@$R);
	}
	
	// вспомогательная функция - перевод байтов в КБ, Мб, Гб
	private function fsbyte2kmg($b){
		if ($b<1024) return $b." B";
		if ($b<1048576) return number_format($b/1024,2)." Kb";
		return number_format($b/1048576,2)." Mb";
	}
	
	// скачивание файла
	private function fsDownload($id){
		if($_SESSION['user_type']!=7 && $_SESSION['user_type']!=1)
			$W="`access2` LIKE '%".$this->fsAccessName[$_SESSION['user_type']]."%' AND";
		else $W="";

		$q=$this->mysqlQuery("SELECT `originfile` FROM `ccl_".ACCOUNT_SUFFIX."fileshare` WHERE {$W} `id`=".$id);
		if (mysql_num_rows($q)) {
			$l=mysql_fetch_assoc($q);
			header("Content-type: application/octet-stream");
			header("Content-Disposition: attachment; filename=".stripslashes($l["originfile"]));
			readfile($_SERVER["DOCUMENT_ROOT"]."/fileshare/".$id);
			exit;
		}
	}
	
	// удаление файлов
	private function fsDelete($id){
 		if($_SESSION['user_type']!=7 && $_SESSION['user_type']!=1)
			$W="`access2delete` LIKE '%".$this->fsAccessName[$_SESSION['user_type']]."%' AND";
 		else $W="";	
		$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."fileshare` WHERE {$W} `id`=".$id);
		if(is_file($_SERVER["DOCUMENT_ROOT"]."/fileshare/".$id))
			unlink($_SERVER["DOCUMENT_ROOT"]."/fileshare/".$id);
		header('Location: '.$this->root_path.'?mod=fileshare'); exit;
	}
}

?>