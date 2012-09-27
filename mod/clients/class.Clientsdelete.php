<?

class ClientsDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		require($_SERVER['DOCUMENT_ROOT'].'/inc/ftp_functions.php');

		if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id'])!='0' and !isset($_GET['what'])) {
			$request = "DELETE from `ccl_".ACCOUNT_SUFFIX."customers` WHERE id=".intval($_GET['id']);
				$this->mysqlQuery($request);
			
				if(isset($_GET['hidemenu'])) $redir = '?mod=clients&sw=add&hidemenu';
				else $redir = '?mod=clients';
				header('Location: '.$this->root_path.$redir);
		}
		elseif($_GET['what']=='scan' and $_GET['name']!='') {
			$table = 'scan';
			$request = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."scan` WHERE `file` = '".mysql_real_escape_string($_GET['name'])."' LIMIT 1";
			$item = mysql_fetch_array($this->mysqlQuery($request));
		
			del_img($table, $item['id'], $item['client'], 'scan', $item['file'], $this->root_path, $this->ftp_host, $this->ftp_user, $this->ftp_pass);
			header('Location: '.$this->root_path.'?mod=clients&sw=form&customer_id='.$item['client']);
		}
		else $this->redirect($this->root_path.'?mod=clients');

	}
}
?>