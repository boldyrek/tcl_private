<?
require($_SERVER['DOCUMENT_ROOT']."/inc/ftp_functions.php");
class Upload extends Proto {
	
	var $fileTypes = array(
		array("name" => "image/jpeg","type" => "jpg"),
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
			if(isset($_POST['foto_number']) and isset($_POST['owner']))
			{
				
				$table = 'cars_photos';
				$i = $_POST['foto_number'];
				if (eregi('jpeg',$_FILES['file']['type'])) $file_type = 'jpg';
				elseif(eregi('gif',$_FILES['file']['type'])) $file_type = 'gif';
				elseif(eregi('png',$_FILES['file']['type'])) $file_type = 'png';
				else $this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['owner']));
				if($file_type!='')
				{
					//upload_img();
					upload_img($table, $file_type, '', '', intval($_POST['owner']), 'car', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['owner']).'&success');
				}
			}
			
			elseif(isset($_POST['certImage']))
			{
				
				$table = 'cars_cert';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='')	
				{
					//$this->removePreviousCertificates(intval($_POST['certImage']));
					upload_img($table, $typo, 'cert_', '', mysql_real_escape_string($_POST['certImage']), 'car', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['certImage']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['certImage']));
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
			// modified 
			elseif(isset($_POST['autocheck']))
			{
				$table = 'autocheck';

				$_FILES['file']['name'] = 'AutoCheck_'.$_FILES['file']['name'];
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='')	
				{
					upload_img($table, $typo, 'achk_', '', intval($_POST['autocheck']), 'car', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['autocheck']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['autocheck']).'&error=wrong_file_type');
			}
			// modified 
			elseif(isset($_POST['carfax']))
			{
				$table = 'autocheck';

				$_FILES['file']['name'] = 'CarFax_'.$_FILES['file']['name'];
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='')	
				{
					upload_img($table, $typo, 'carf_', '', intval($_POST['carfax']), 'car', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['carfax']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['carfax']).'&error=wrong_file_type');
			}
			
			elseif(isset($_POST['uploadInspection'])) {
				$table = 'inspections';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='') {
					upload_img($table, $typo, 'insp_', '', intval($_POST['uploadInspection']), 'car', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['uploadInspection']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['uploadInspection']).'&error=unknown_file_type');
				
			}
			
			elseif(isset($_POST['uploadAdddoc'])) {
				$table = 'adddoc';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='') {
					upload_img($table, $typo, 'adoc_', '', intval($_POST['uploadAdddoc']), 'car', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['uploadAdddoc']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_POST['uploadAdddoc']).'&error=unknown_file_type');
				
			}
			
			elseif(isset($_POST['container'])) {
				$table = 'container_files';
				
				$typo = $this->defineTypo($_FILES['file']['name']);
				
				if($typo!='') {
					
					upload_img($table, $typo, '', 'containers/', intval($_POST['container']), 'container', $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
					$ins_id = mysql_insert_id();
					$query = "UPDATE `ccl_".ACCOUNT_SUFFIX."container_files` SET `filename` = '".mysql_real_escape_string($_POST['title'])."' WHERE `id` = '".$ins_id."'";
					if($_POST['title']!='') $this->mysqlQuery($query);
					
					$this->redirect($this->root_path.'?mod=containers&sw=form&cont_id='.intval($_POST['container']).'&success');
				}
				else $this->redirect($this->root_path.'?mod=containers&sw=form&cont_id='.intval($_POST['container']).'&error=unknown_file_type');
				
			}
			else $this->redirect($this->root_path);
		}
		else $this->redirect($this->root_path);
		
		
	}
	function removePreviousCertificates($id) {
			if($id!='' and $id!=0) {
				$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."cars_cert` WHERE `car` = '".$id."'");
			}
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