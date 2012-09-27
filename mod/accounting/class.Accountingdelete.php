<?

class AccountingDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."accounting` WHERE id=".intval($_GET['id']);
		$res = $this->mysqlQuery($sql);
		$accountingInfo = mysql_fetch_assoc($res);
		
		$sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE id='".$accountingInfo['car']."'";
		$res = $this->mysqlQuery($sql);
		$carInfo = mysql_fetch_assoc($res);
		
				
		$request = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."accounting` WHERE id=".intval($_GET['id']);
		$this->mysqlQuery($request);
		
		//Обновляем баланс клиента в списке
		updateBalance($carInfo['buyer'], 0);

//		updateBalance(intval($_GET['client']), $_GET['dealer']);
		if(isset($_SESSION['prev_location']) and $_SESSION['prev_location']!='') $back = '?'.$_SESSION['prev_location'];
		else $back = '?mod=payments';
		$this->redirect($this->root_path.$back);
	}
}
?>