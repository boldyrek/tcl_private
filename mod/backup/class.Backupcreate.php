<?
require_once("templates/class.BackupTemplates.php");
class BackupCreate extends Proto {

 	var $backupCollation='cp1251';
	var $backupLimit=1024; // лимит в Кб
	
	var $backupRetCount=0;
	var $backupRetSize=0;
	var $backupRestoreCnt=0;
	var $backupRestoreSize=0;
	
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
		
		if(isset($_GET['erasefile']) && isset($_GET["id"])) {
			$this->dumpDBerase(intval($_GET["id"]));
		} elseif(isset($_GET['store'])) {
			$this->dumpDBstore(false,intval($_POST["times"]));
		} elseif(isset($_GET['restore'])) {
			if (isset($_POST["id"]) && is_numeric($_POST["id"])) {
				$this->dumpDBrestore($_SERVER["DOCUMENT_ROOT"]."/backup/".intval($_POST["id"]).".sql");
			}
		} elseif(isset($_GET['restorefile'])) {
			if (is_uploaded_file($_FILES['restorefile']['tmp_name'])) {
				$file=$_SERVER["DOCUMENT_ROOT"]."/backup/restore.".time().".sql";
				move_uploaded_file($_FILES['restorefile']['tmp_name'], $file);
				$this->dumpDBrestore($file);
				unlink($file);
			}
		}
		
		$this->view->backupRetCount=$this->backupRetCount;
		$this->view->backupRetSize=$this->backupRetSize;
		$this->view->backupRestoreCnt=$this->backupRestoreCnt;
		$this->view->backupRestoreSize=$this->backupRestoreSize;
		
		$this->page .= $this->view->BackupCreateContent();
	}
	
	private function dumpDBstore($autostore=false,$times=false) {
		if(!$times) $times=time();
		$_dumpdb_file=$_SERVER["DOCUMENT_ROOT"]."/backup/".$times.($autostore?".auto":"").".sql";
		
		if(is_file($_dumpdb_file)) {
			header('Location: '.$this->root_path.'?mod=backup');
			exit;
		}
		
		$_dumpdb_sum=0;
// 		$q=$this->mysqlQuery("SHOW TABLE STATUS");
		$q=mysql_query("SHOW TABLE STATUS");
		while($r=mysql_fetch_assoc($q)) {
			$_dumpdb_sum+=$r["Rows"];
			
			if (!empty($r['Collation']) && preg_match("/^([a-z0-9]+)_/i", $r['Collation'], $m))
				$tmp=$m[1];
			else 
				$tmp=$this->backupCollation;
						
			$_dumpdb_tbl[$r["Name"]]=array($r["Rows"],$r["Avg_row_length"],$tmp,$r["Engine"]);
		}
		
		$fp=fopen($_dumpdb_file, "a");
		foreach($_dumpdb_tbl as $k=>$v) {
			if($v[0]<=floor(($this->backupLimit*1024)/$v[1])) $s=$v[0]; else $s=floor(($this->backupLimit*1024)/$v[1]);
// 			$this->mysqlQuery("SET NAMES '{$v[2]}'");
			mysql_query("SET NAMES '{$v[2]}'");
// 			$result = $this->mysqlQuery("SHOW CREATE TABLE `{$k}`");
			$result = mysql_query("SHOW CREATE TABLE `{$k}`");
			$tab = mysql_fetch_array($result);
		//	$tab = preg_replace('/(default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP|DEFAULT CHARSET=\w+|COLLATE=\w+|character set \w+|collate \w+)/i', '/*!40101 \\1 */', $tab);
			fwrite($fp, "DROP TABLE IF EXISTS `{$k}`;\n{$tab[1]};\n\n");
		
			if (in_array($v[3], $_dumpdb_tblex)) continue;
			
			for($j=0;$j<$v[0];$j+=$s) {
				$str="";
// 				$q=$this->mysqlQuery("SELECT * FROM `{$k}` LIMIT {$j},{$s}");
				$q=mysql_query("SELECT * FROM `{$k}` LIMIT {$j},{$s}");
				if(!isset($_dumpdb_type[$k])) {
					$fields = mysql_num_fields($q);
					for ($i=0; $i<$fields; $i++) {
						$_dumpdb_type[$k][]=mysql_field_type($q,$i);
						$_dumpdb_field[$k][]=mysql_field_flags($q,$i);
					}
				}
				while($r=mysql_fetch_row($q)) {
					for ($i=0;$i<sizeof($r); $i++) {
						// определение типа данных
						if (!isset($r[$i]) || is_null($r[$i])) {
							$r[$i] = 'NULL';
						} elseif ($_dumpdb_type[$k][$i]=="int") {
							$r[$i] = $r[$i];
						} elseif (stristr($_dumpdb_field[$k][$i], 'BINARY')
							&& $_dumpdb_type[$k][$i] != 'datetime'
							&& $_dumpdb_type[$k][$i] != 'date'
							&& $_dumpdb_type[$k][$i] != 'time'
							&& $_dumpdb_type[$k][$i] != 'timestamp'
						) {
							if (empty($r[$i]) && $r[$i] != '0') {
								$r[$i] = '\'\'';
							} else {
								$r[$i] = '0x'.bin2hex($r[$i]);
							}
						} else {
							$r[$i] = '\''.str_replace($search, $replace, mysql_real_escape_string($r[$i])).'\'';
						}
					}
					$str.="INSERT INTO `{$k}` VALUES (".implode(",",$r).");\n";
				}
				fwrite($fp,$str);
				mysql_free_result($q);
			}
			fwrite($fp,"\n");
		}
		fclose($fp);
		
		$this->backupRetCount=$_dumpdb_sum;
		$this->backupRetSize=$this->dumpDBbyte2kmg(filesize($_dumpdb_file));
		
		return true;
	}
	
	private function dumpDBrestore($file) {
// 		if (!isset($_POST["id"]) || !is_numeric($_POST["id"])) return false;
		if(substr($file,-4)!=".sql") {
			header('Location: '.$this->root_path.'?mod=backup');
			exit;
		}
 		if(!$this->dumpDBstore(1)) return false;
			
		$sumcnt=$j=$cnt=0;
		$t="";
		$_CHARSETB=false;
		$Fsize = filesize($file)-1;
		$handle = fopen($file, "r");
		while (!feof($handle)) {
			$t = fgets($handle, 4096);
			$t=rtrim($t,"\r\n");
			$cnt+=strlen($t);
			@$sql[$j].=$t;
			if($t[strlen($t)-1]!=";") continue;
			$j++;

			if (($cnt>($this->backupLimit*1024) || $Fsize==ftell($handle)) && $t[strlen($t)-1]==";") {
				for($i=0,$s=sizeof($sql);$i<$s;$i++) {
					if(empty($sql[$i])) continue;
					if (preg_match("/CREATE TABLE/i", $sql[$i])) {
						if (preg_match("/(CHARACTER SET|CHARSET)[=\s]+(\w+)/i", $sql[$i], $charset)) {
							$_CHARSET=$charset[2]; $_CHARSETB=true;
						}
					}
					if ($_CHARSETB==true) { 
// 						$this->mysqlQuery("SET NAMES '{$_CHARSET}'"); $_CHARSETB=false;
						mysql_query("SET NAMES '{$_CHARSET}'"); $_CHARSETB=false;  
					}
// 					$this->mysqlQuery($sql[$i]);
					mysql_query($sql[$i]);
				}
				$sumcnt+=$s;
				$j=$cnt=0; unset($buffer); //очистка буффера и счетчиков
			}
		}
		fclose($handle);
 		$this->backupRestoreCnt=$sumcnt;
 		$this->backupRestoreSize=$this->dumpDBbyte2kmg($Fsize);
		return true;
	}
	
	private function dumpDBerase($fid){
		
		$d = dir($_SERVER["DOCUMENT_ROOT"]."/backup/");
		while (false !== ($entry = $d->read()))
			if($entry[0]!="." && substr($entry,-3)=="sql") {
				list($n,$t)=explode(".",$entry);
				if($n==$fid) {
					rename($_SERVER["DOCUMENT_ROOT"]."/backup/".$entry,$_SERVER["DOCUMENT_ROOT"]."/backup/.".$entry);
					header('Location: '.$this->root_path.'?mod=backup');
					exit;
				}
				$backupname[$n]=array($this->dumpDBbyte2kmg(filesize($_SERVER["DOCUMENT_ROOT"]."/backup/".$entry)),($t=="auto"?true:false));
			}
		$d->close();

	}
	
	private function dumpDBbyte2kmg($b){
		if ($b<1024) return $b." B";
		if ($b<1048576) return number_format($b/1024,2)." Kb";
		return number_format($b/1048576,2)." Mb";
	}	
}
?>