<?
require($_SERVER['DOCUMENT_ROOT']."/inc/ftp_functions.php");
class Upload extends Proto {
	
	var $fileTypes = array(array("name" => "image/jpeg","type" => "jpg"),
		array("name" => "text/html","type" => "html"),
		array("name" => "text/plain","type" => "txt"),
		array("name" => "image/gif","type" => "gif"),
		array("name" => "application/octet-stream","type" => "rar"),
		array("name" => "message/rfc822","type" => "mht"),
		array("name" => "image/png","type" => "png"),
		array("name" => "image/bmp","type" => "bmp"),
		array("name" => "application/pdf", "type" => "pdf"),
		array("name" => "application/vnd.ms-excel", "type" => "xls"),
		array("name" => "application/msword", "type" => "word"),
		);
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		if(isset($_FILES['file']['type']) and $_FILES['file']['type']!='') {
	
			if(isset($_POST['certImage']))
			{
				
				$table = 'stuff_cert';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='')	
				{
					upload_img($table, $typo, 'cert_', '', mysql_real_escape_string($_POST['certImage']), 'stuff', '/', $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_POST['certImage']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_POST['certImage']));
			}
			
			elseif(isset($_POST['scanImage']))
			{
				$table = 'scan';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='')	
				{
					upload_img($table, $typo, '', 'scan/', intval($_POST['scanImage']), 'client', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=clients&sw=form&customer_id='.intval($_POST['scanImage']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=clients&sw=form&customer_id='.intval($_POST['scanImage']).'&error=wrong_file_type');
			}
			
			elseif(isset($_POST['uploadInspection'])) {
				$table = 'stuff_inspections';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='') {
					upload_img($table, $typo, 'insp_', '', intval($_POST['uploadInspection']), 'stuff', '/', $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_POST['uploadInspection']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_POST['uploadInspection']).'&error=unknown_file_type');
				
			}
			
			elseif(isset($_POST['uploadAdddoc'])) {
				$table = 'stuff_adddoc';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='') {
					upload_img($table, $typo, 'adoc_', '', intval($_POST['uploadAdddoc']), 'stuff', '/', $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_POST['uploadAdddoc']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_POST['uploadAdddoc']).'&error=unknown_file_type');
				
			}
			
		}
		else $this->redirect($this->root_path);
		
		
	}

	function checkFileType() {
		$file_type = '';
		$i=0;
		while($i<=count($this->fileTypes)) {
			if($_FILES['file']['type'] == $this->fileTypes[$i]['name']) { 
			$file_type = $this->fileTypes[$i]['type'];
			break;
			}
			$i++;
		}
		return $file_type;
	}
	
	function defineTypo($incoming) {
		$length = strlen($incoming);
			
		$typo = strtolower(substr($incoming, $length-3, 3));
		
		return $typo;
	}
}
?>