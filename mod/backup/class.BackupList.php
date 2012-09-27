<?
require_once("templates/class.BackupTemplates.php");
class BackupList extends Proto {
	var $backupAClean=true; // автоудаление
	
	var $backupCntCleanT=16; // лимит в корзине %)
 	var $backupCntClean=8; // лимит копий
 	var $backupCntCleanA=4; // лимит автосохранений

	public function drawContent() {
 		if($this->backupAClean) $this->dumpDBclean();
		
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
		
 		$this->page .= $this->view->BackupDBtplListContent($this->dumpDBlist());
	}
	
	private function dumpDBlist(){
		$i=0;
		$d = dir($_SERVER["DOCUMENT_ROOT"]."/backup/");
		while (false !== ($entry = $d->read()))
			if($entry[0]!="." && substr($entry,-3)=="sql") {
				list($n,$t)=explode(".",$entry);
				$backupname[$n]=array($this->dumpDBbyte2kmg(filesize($_SERVER["DOCUMENT_ROOT"]."/backup/".$entry)),($t=="auto"?true:false));
			}
		$d->close();
		krsort($backupname); reset($backupname);
		
		return $this->view->BackupDBtplList($backupname);
	}
	
	private function dumpDBbyte2kmg($b){
		if ($b<1024) return $b." B";
		if ($b<1048576) return number_format($b/1024,2)." Kb";
		return number_format($b/1048576,2)." Mb";
	}
	
	private function dumpDBclean(){
		$d = dir($_SERVER["DOCUMENT_ROOT"]."/backup/");
		while (false !== ($entry = $d->read())) {
			if($entry[0]!=".") {
				$t=explode(".",$entry);
				if($t[1]=="auto") $NA[]=$entry; else $N[]=$entry;
			}
			if($entry[0]=="."  && substr($entry,-4)==".sql") $Ntrash[]=$entry;
		}
		$d->close();
		
		rsort($NA); rsort($N); rsort($Ntrash);
		
		if (sizeof($NA)>$this->backupCntCleanA)
			for($i=$this->backupCntCleanA,$s=sizeof($NA);$i<$s;$i++)
				rename ($_SERVER["DOCUMENT_ROOT"]."/backup/".$NA[$i],$_SERVER["DOCUMENT_ROOT"]."/backup/.".$NA[$i]);
				
		if (sizeof($N)>$this->backupCntClean)
			for($i=$this->backupCntClean,$s=sizeof($N);$i<$s;$i++)
				rename ($_SERVER["DOCUMENT_ROOT"]."/backup/".$N[$i],$_SERVER["DOCUMENT_ROOT"]."/backup/.".$NA[$i]);
		
		if (sizeof($Ntrash)>$this->backupCntCleanT)
			for($i=$this->backupCntCleanT,$s=sizeof($Ntrash);$i<$s;$i++)
				unlink($_SERVER["DOCUMENT_ROOT"]."/backup/".$Ntrash[$i]);
	}
}

?>