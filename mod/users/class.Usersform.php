<?

class UsersForm extends Proto {
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		
		$error = false;
		
		if(!isset($_GET['add'])) {
			$request = 'SELECT * 
			FROM `ccl_'.ACCOUNT_SUFFIX.'usrs` 
			WHERE id ='.intval($_GET['id']);
			$content = mysql_fetch_array($this->mysqlQuery($request));
			
			$lnk = '?mod=users&sw=save&id='.intval($_GET['id']);
			
			if($content['id']=='') { 
				$this->page .= '<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Пользователь с такими параметрами в базе не обнаружен').'</div>';
				$error = true;
			}
		}
		elseif(isset($_GET['add'])) {
			$lnk = '?mod=users&sw=add';
		}
		if(!$error) {
			// warning! changed!
			//$user_types = $this->user_types;
			
			require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'mod/users/templates/user.form.php');
			$clients = $this->getCustomersList();
			$transporters = $this->getTransportersList();
			$expeditors = $this->getExpeditorsList();
			
			if (isset($_GET['success'])) $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
		
//			$this->page .= userForm($lnk, $content, $clients);
			$this->page .= userForm($lnk, $content, $clients, $transporters,$expeditors, $user_types);
		}
		else $this->redirect($this->root_path);
	}
	
}

?>