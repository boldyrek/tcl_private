<?

class PaymentsDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		$request = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."payments` 
		WHERE id=".intval($_GET['id']);
		$this->mysqlQuery($request);
	
		updateBalance(intval($_GET['client']), $_GET['dealer']);
		if(isset($_SESSION['prev_location']) and $_SESSION['prev_location']!='') $back = '?'.$_SESSION['prev_location'];
		else $back = '?mod=payments';
		
		$this->redirect($this->root_path.$back);
	}
}
?>