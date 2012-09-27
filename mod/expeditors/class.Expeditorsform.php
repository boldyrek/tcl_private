<?

class ExpeditorsForm extends Proto {
	
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
		
		if (isset($_GET['success'])) $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
		
		$error = false;
		if(!isset($_GET['add'])) {
			$content = mysql_fetch_array($this->mysqlQuery(
			"SELECT * FROM `ccl_".ACCOUNT_SUFFIX."expeditors`
			WHERE `id` = '".intval($_GET['exp_id'])."'"));
			$lnk = '?mod=expeditors&sw=save&id='.intval($_GET['exp_id']);
			if($content['id']=='') { 
				$this->page .= '<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Экспедитор с такими параметрами в базе не обнаружен').'</div>';
				$error = true;
			}
		}
		elseif(isset($_GET['add'])) {
			$lnk = '?mod=expeditors&sw=add';
		}
		if(!$error) {
			$ports = $this->mysqlQuery(
			"SELECT * FROM `ccl_".ACCOUNT_SUFFIX."ports`
			WHERE 1 ORDER BY `name` ASC");
			
			require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'mod/expeditors/templates/expeditor.form.php');
			
			$this->page .= expForm($lnk, 
			$content,
			$this->mysqlQuery(
			"SELECT id,price,number
			FROM `ccl_".ACCOUNT_SUFFIX."containers` WHERE `expeditor` = '".intval($_GET['exp_id'])."'"),
			$this->root_path,
			$ports);
		}
	}
	
}

?>