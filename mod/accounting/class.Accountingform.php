<?

class AccountingForm extends Proto {
	
	var $content;
	
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
		// готовим форму платежа

		if((!isset($_GET['accounting']) or intval($_GET['accounting'])=='') and !isset($_GET['add']))
		{
			$this->redirect($this->root_path);
		}

		$_SESSION['prev_location'] = $_SESSION['last_location'];
		$_SESSION['last_location'] = $_SERVER['QUERY_STRING'];

		$error = false;

		if(!isset($_GET['add'])) {
			$this->content = @mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."accounting.*, ccl_".ACCOUNT_SUFFIX."usrs.log_name 
			FROM `ccl_".ACCOUNT_SUFFIX."accounting` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."usrs` ON (ccl_".ACCOUNT_SUFFIX."usrs.id = ccl_".ACCOUNT_SUFFIX."accounting.user_added)
			WHERE ccl_".ACCOUNT_SUFFIX."accounting.id='".intval($_GET['accounting'])."'"));

			if($this->content['id']=='' or $this->content['id']=='0') $this->redirect($this->root_path.'?mod=accounting');
			$lnk = '?mod=accounting&sw=save&id='.intval($_GET['accounting']);

			if($this->content['id']=='') { 
				$print .= '<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Платеж с такими параметрами в базе не обнаружен').'</div>';
				$error = true;
			}
		}
		else $lnk = '?mod=accounting&sw=add';

		if(!$error) {
			require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'mod/accounting/templates/accounting.form.php');

			if (isset($_GET['success'])) $print .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
			
			$this->page .= accountingForm($lnk,intval($_GET['accounting']), $this->content, $this->cars_list(),$this->purposes);
		}
	}
	function cars_list() {
		
//		$_GET['owner'] = $this->content['client'];
		$_GET['selected'] = $this->content['car'];
		require_once($_SERVER['DOCUMENT_ROOT'].'/mod/accounting/class.Accountingcars.php');
		$list = new CarsList();
		$list->getContent();
		return $list->page;
	
}
}

?>