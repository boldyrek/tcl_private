<?

class TransportersDelete extends Proto {
	
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
			$this->mysqlQuery("DELETE from `ccl_".ACCOUNT_SUFFIX."transporters` WHERE id=".intval($_GET['id']));
		
			$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."ports` WHERE `transporter` = '".intval($_GET['id'])."'");
		
			$this->redirect($this->root_path.'?mod=transporters');
		}
		//удаление услуги поставщика
		elseif($_GET['what'] == 'supservice') {
		
			$request = "DELETE from `ccl_".ACCOUNT_SUFFIX."sup_serv` WHERE id=".intval($_GET['id']);
			$this->mysqlQuery($request);
		
			//обновляем баланс поставщика
			updateSupplierBalance(intval($_GET['parent']));
			
			$this->redirect($this->root_path.'?mod=transporters&sw=form&sup_id='.intval($_GET['parent']));
		}
		
		//удаление платежа поставщику
		elseif($_GET['what'] == 'suppayment') {
			
			$request = "DELETE from `ccl_".ACCOUNT_SUFFIX."sup_pay` WHERE id=".intval($_GET['id']);
			$this->mysqlQuery($request);
			
			//обновляем баланс поставщика
			updateSupplierBalance(intval($_GET['parent']));
			
			$this->redirect($this->root_path.'?mod=transporters&sw=form&sup_id='.intval($_GET['parent']));
		}
		
		elseif($_GET['what'] == 'port') {
			//удаление порта поставщика
			
			$request = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."ports` WHERE `id` = '".intval($_GET['id'])."' and `transporter` = '".intval($_GET['parent'])."'";
			$this->mysqlQuery($request);
		
			$this->redirect($this->root_path.'?mod=transporters&sw=form&sup_id='.intval($_GET['parent']));
		}
	}
}
?>