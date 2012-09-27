<?

if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id'])!=0) {
	
	$tmp = new printExport();
	$tmp->validate();

}


class printExport extends Proto {
	
	public function validate() {
			$content = mysql_fetch_array($this->mysqlQuery("
				SELECT ccl_".ACCOUNT_SUFFIX."cars.*
				FROM `ccl_".ACCOUNT_SUFFIX."cars` 
				WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".intval($_GET['id'])."' LIMIT 1"));
	
		if($this->validateOwnership($content)) {
			require_once($_SERVER['DOCUMENT_ROOT'].'/mod/certificates/class.Certificatesprint.php');
			$obj = new CertificatesPrint();
			$obj->Process();
		}
		
		else $this->redirect('/public');
	}
	
	function validateOwnership($content) {
		if($content['buyer']==$_SESSION['user_id'] or $content['dealer']==$_SESSION['user_id'] or $content['reciever']==$_SESSION['user_id']) return true;
		else return false;
	}
}

?>