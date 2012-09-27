<?
class CarsSell extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		if($this->exists($_GET['car_id']) and intval($_GET['car_id'])!='0') {
		
			$isSellerDealer = $_POST['isDealer'.$_POST['owner']];
			$isBuyerDealer = $_POST['isDealer'.$_POST['newOwner']];
			$sellerDealer = $_POST['myDealer'.$_POST['owner']];
			$buyerDealer = $_POST['myDealer'.$_POST['newOwner']];
	
			$this->mysqlQuery("UPDATE `ccl_".ACCOUNT_SUFFIX."cars` 
			SET `buyer` = '".intval($_POST['newOwner'])."',
			`total` = '".mysql_real_escape_string($_POST['carNewPrice'])."',
			`dealer` = '".($isBuyerDealer=='1'?intval($_POST['newOwner']):mysql_real_escape_string($buyerDealer))."'
			WHERE `id` = '".intval($_GET['car_id'])."'");
			
			$priceDiff = intval($_POST['carNewPrice']) - intval($_POST['oldPrice']);
			
			$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."payments` (`id`, `client`, `amount`, `comment`, `date`, `user_added`, `status`) 
			VALUES 
			(LAST_INSERT_ID(),
			 '".intval($_POST['owner'])."',
			 '".$priceDiff."',
			 'ЗА ПРОДАЖУ А/М ".mysql_real_escape_string(strtoupper($_POST['carFrame']))."',
			 '".$_POST['sellDate']."',
			 '".$_SESSION['login_id']."',
			 '1')");
					
			updateBalance($_POST['owner'],$isSellerDealer);
			updateBalance($_POST['newOwner'],$isBuyerDealer);
			if($isSellerDealer=='0' and $sellerDealer!='0') updateBalance($sellerDealer,1);
			if($isBuyerDealer=='0' and $buyerDealer!='0') updateBalance($buyerDealer,1);
				
			$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_GET['car_id']).'&success');
		}
		else $this->redirect($this->root_path.'?mod=cars');
	}
}
?>