<?

class PaymentsForm extends Proto {
	
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

		if((!isset($_GET['payment']) or intval($_GET['payment'])=='') and !isset($_GET['add']))
		{
			$this->redirect($this->root_path);
		}
		
		$_SESSION['prev_location'] = $_SESSION['last_location'];
		$_SESSION['last_location'] = $_SERVER['QUERY_STRING'];
		
		$error = false;
		
		if(!isset($_GET['add'])) {
			$this->content = @mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."payments.*, ccl_".ACCOUNT_SUFFIX."customers.name, ccl_".ACCOUNT_SUFFIX."customers.dealer, ccl_".ACCOUNT_SUFFIX."usrs.log_name 
			FROM `ccl_".ACCOUNT_SUFFIX."payments` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."payments.client) 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."usrs` ON (ccl_".ACCOUNT_SUFFIX."usrs.id = ccl_".ACCOUNT_SUFFIX."payments.user_added)
			WHERE ccl_".ACCOUNT_SUFFIX."payments.id='".intval($_GET['payment'])."'"));
			if($this->content['id']=='' or $this->content['id']=='0') $this->redirect($this->root_path.'?mod=payments');
			$lnk = '?mod=payments&sw=save&id='.intval($_GET['payment']);
			
			if($this->content['id']=='') { 
				$print .= '<div class="warn" style="width:900px;">Ошибка! Платеж с такими параметрами в базе не обнаружен</div>';
				$error = true;
			}
		}
		else $lnk = '?mod=payments&sw=add';
		
		if(!$error) {
			require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'mod/payments/templates/payment.form.php');
						
			if (isset($_GET['success'])) $print .='<h4 class="report">Изменения сохранены</h4>';
			
			$this->page .= paymentForm($lnk,
			intval($_GET['payment']), $this->content, 
			$this->getCustomersList(), $this->cars_list());
		}
	}
	function cars_list() {
		
		$_GET['owner'] = $this->content['client'];
		$_GET['selected'] = $this->content['car'];
		require_once($_SERVER['DOCUMENT_ROOT'].'/mod/payments/class.Paymentscars.php');
		$list = new CarsList();
		$list->getContent();
		return $list->page;
	
}
}

?>