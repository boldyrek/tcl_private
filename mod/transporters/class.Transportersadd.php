<?

class TransportersAdd extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		if(isset($_GET['add']) and $_GET['add']!='' and intval($_GET['id'])!='' and intval($_GET['id'])!='0') {
			
			if($_GET['add']=='port' and isset($_GET['id'])) {
			$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."ports` (`id`, `name`, `transporter`)
					VALUES (LAST_INSERT_ID(),
					'".mysql_real_escape_string(strtoupper($_POST['newPort']))."',
					'".intval($_GET['id'])."')";
					$this->mysqlQuery($request);
			}
			elseif($_GET['add'] == 'supservice')
			{
				$request = "INSERT into `ccl_".ACCOUNT_SUFFIX."sup_serv` (`id`, `from`, `date`, `comment`, `sum`)
							VALUES (LAST_INSERT_ID(), 
							'".intval($_GET['id'])."', 
							'".mysql_real_escape_string($_POST['date'])."', 
							'".mysql_real_escape_string(strtoupper($_POST['comment']))."', 
							'".intval($_POST['sum'])."')";
			 	$this->mysqlQuery($request);
		
				updateTransporterBalance(intval($_GET['id']));
			}
				
			elseif($_GET['add'] == 'suppayment')
			{ 
				$request = "INSERT into `ccl_".ACCOUNT_SUFFIX."sup_pay` (`id`, `to`, `sum`, `date`, `comment`)
							VALUES (LAST_INSERT_ID(), 
							'".intval($_GET['id'])."', 
							'".str_replace(',','.',intval($_POST['sum']))."',
							'".mysql_real_escape_string($_POST['date'])."', 
							'".mysql_real_escape_string(strtoupper($_POST['comment']))."')";
			 	$this->mysqlQuery($request);
		
				updateTransporterBalance(intval($_GET['id']));
			}
		}
		$this->redirect($this->root_path.'?mod=transporters&sw=form&sup_id='.intval($_GET['id'])); 
	}
}
?>