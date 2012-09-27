<?

require($_SERVER['DOCUMENT_ROOT']."/inc/ftp_functions.php");

class multiUpload extends Proto {
	
	var $file_type;
	var $owner_id;
	var $owner_type;
	var $table;

	function getFileList() {
		$list = $_FILES;
		
		$this->owner_id = intval($_POST['owner']);
		$this->owner_type = htmlspecialchars($_POST['type']);
		$this->table = ($this->owner_type=='car'?'cars':$this->owner_type).'_photos';

		if(count($list)>0) {
			foreach ($list as $k=>$v) {
				$this->file_type = $this->defineTypo($v['name']);
				if($this->file_type=='jpg' or $this->file_type=='jpeg' or $this->file_type=='gif' or $this->file_type=='png') {
					$_FILES['file']['tmp_name'] = $v['tmp_name'];
					$_FILES['file']['name'] = $v['name'];
					
					$this->putOneFile();
				}
			}
		}

                $this->redirect(((isset($_POST['uploader']) && $_POST['uploader'] == 'client') ? '/public' : '')."/?mod=".($this->owner_type=='car'?'cars':$this->owner_type)."&sw=form&".$this->owner_type."_id=".$this->owner_id);
	}
	
	function putOneFile() {
		return upload_img($this->table, $this->file_type, '', '', $this->owner_id, $this->owner_type, $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
	}
	
	function defineTypo($incoming) {
		$length = strlen($incoming);
			
		$typo = strtolower(substr($incoming, $length-4, 4));
		
		return ltrim($typo,'.');
	}
}

?>