<?

class ExpeditorsSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
		
		if(!isset($_GET['mini']))
			{
			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."expeditors` SET  `name` = '".mysql_real_escape_string(strtoupper($_POST['name']))."',
						`address` = '".mysql_real_escape_string(strtoupper($_POST['address']))."',
						`phone` = '".mysql_real_escape_string($_POST['phone'])."',
						`email` = '".mysql_real_escape_string($_POST['email'])."'
						 WHERE `id`=".intval($_GET['id'])." LIMIT 1";
			
		 	$this->mysqlQuery($request);
		
			}
			
		elseif($_GET['mini'] == 'expservice')
			{
				$request = "INSERT into `ccl_".ACCOUNT_SUFFIX."exp_serv` (`id`, `from`, `date`, `comment`, `sum`)
							VALUES (LAST_INSERT_ID(), 
							'".intval($_GET['id'])."', 
							'".mysql_real_escape_string($_POST['date'])."', 
							'".mysql_real_escape_string(strtoupper($_POST['comment']))."', 
							'".intval($_POST['sum'])."')";
			 	$this->mysqlQuery($request);
		
				updateExpeditorBalance(intval($_GET['id']));
			}
			
		elseif($_GET['mini'] == 'exppayment')
			{
				$request = "INSERT into `ccl_".ACCOUNT_SUFFIX."exp_pay` (`id`, `to`, `date`, `comment`, `sum`)
							VALUES (LAST_INSERT_ID(), 
							'".intval($_GET['id'])."', 
							'".mysql_real_escape_string($_POST['date'])."', 
							'".mysql_real_escape_string(strtoupper($_POST['comment']))."', 
							'".intval($_POST['sum'])."')";
			 	$this->mysqlQuery($request);
		
				updateExpeditorBalance(intval($_GET['id']));
			}
		elseif($_GET['mini'] == 'saveports' and intval($_GET['id'])!='' and intval($_GET['id'])!='') {
			$ports_save = '';
			$active_ports = $_POST['port'];
			foreach ($active_ports as $k=>$v) {
				$ports_save .= $v.';';
			}
			$this->mysqlQuery("UPDATE `ccl_".ACCOUNT_SUFFIX."expeditors` SET `ports` = '".$ports_save."' WHERE `id` = '".intval($_GET['id'])."'");
		}
		$this->redirect($this->root_path.'?mod=expeditors&sw=form&exp_id='.intval($_GET['id']).'&success');
	}
}
?>