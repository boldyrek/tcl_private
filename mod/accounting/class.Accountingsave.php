<?

class AccountingSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		//if($_POST['payment_status']=='on') $status = '1';
		//else $status = '0';
		
		$amoun = intval($_POST['amount']);
		$amount = $amoun>0 ? $amoun : -$amoun;
		$status = '1';
		$paid = isset($_POST['confirm'])? 1 : 0;
		$signer = $paid>0 ? $_SESSION['user_name'] : '';
		$purpose = isset($_POST['purpose'])? intval($_POST['purpose']) : 0;
//		$signer = $paid ? intval($_SESSION['login_id']) : 0;
		
		$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."accounting` SET
					`amount` = '".$amount."',
					`comment` = '".mysql_real_escape_string(strtoupper($_POST['comment']))."',
					`date` = '".mysql_real_escape_string($_POST['date'])."',
					`signer` = '".$signer."',
					`status` = '".$status."',
					`last_edited` = NOW(),
					`car` = '".intval($_POST['car'])."',
					`paid` = '".$paid."',
					`purpose` = '".$purpose."'
					 WHERE `id`=".intval($_GET['id'])." LIMIT 1";
		
		$this->mysqlQuery($request);

		// временный фикс для добавления расходов для транспортников
		if($purpose==4)	// Если "Назначение" == "Доставка до порта" 
		{
			// то добавить в поле автомобиля "cost_to_port" (цена доставки до порта) обновленное значение
			$q=mysql_query('UPDATE `ccl_'.ACCOUNT_SUFFIX.'cars` SET `cost_to_port`='.intval($_POST['amount']).' WHERE `id`='.intval($_POST['car']));
		}
		
/*
		$client_info = mysql_fetch_array($this->mysqlQuery("
		SELECT dealer 
		FROM `ccl_customers` 
		WHERE `id` = '".intval($_POST['client'])."'"));
	
		updateBalance($_POST['client'], $client_info['dealer']);
*/
		$this->redirect($this->root_path.'?mod=accounting&sw=form&accounting='.intval($_GET['id']).'&success');
	}
}
?>