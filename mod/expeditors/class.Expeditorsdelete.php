<?

class ExpeditorsDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		if(!isset($_GET['what'])) {
			$request = "DELETE from `ccl_".ACCOUNT_SUFFIX."expeditors` WHERE id=".intval($_GET['id']);
			$this->mysqlQuery($request);
			
			$this->redirect($this->root_path.'?mod=expeditors');
		}
		
		elseif($_GET['what']=='expservice') {
		$request = "DELETE from `ccl_".ACCOUNT_SUFFIX."exp_serv` WHERE id=".intval($_GET['id']);
			$this->mysqlQuery($request);
		
			//обновляем баланс экпедитора
			updateExpeditorBalance(intval($_GET['parent']));
			
			$this->redirect($this->root_path.'?mod=expeditors&sw=form&exp_id='.intval($_GET['parent']));
		}
			
			//удаление платежа экспедитору
		elseif($_GET['what']=='exppayment') {
			$request = "DELETE from `ccl_".ACCOUNT_SUFFIX."exp_pay` WHERE id=".intval($_GET['id']);
			$this->mysqlQuery($request);
		
			//обновляем баланс экспедитора
			updateExpeditorBalance(intval($_GET['parent']));
			
			$this->redirect($this->root_path.'?mod=expeditors&sw=form&exp_id='.intval($_GET['parent']));
		}
	}
}
?>