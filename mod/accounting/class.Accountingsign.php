<?

require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');
class SignAccounting extends Proto {
	
	public function drawContent() {
		
		if($this->checkAuth()) {
			$this->Sign();
		}
		$this->debug_mode = false;
		$this->track_queries = false;
		$this->exec_time = false;
		$this->errorsPublisher();
		$this->publish();
	}

// Вызывается AJAX-ом. Подтверждает указанный расход
	function Sign()
	{
		// Если id расхода accounting_id пришёл по GET запросу и больше 0, то продолжать
		if(isset($_GET['accounting_id']) and intval($_GET['accounting_id'])>0)
		{
			// SQL запрос на обновление статуса
			$sql='UPDATE `ccl_'.ACCOUNT_SUFFIX.'accounting` SET `paid`=1, `signer`=\''.$_SESSION['user_name'].'\' WHERE `id`='.intval($_GET['accounting_id']).' LIMIT 1';
			$r=mysql_query($sql);	// Выполняем запрос
			if(mysql_affected_rows($r)>=0)	// Если запрос изменил строки в базе данных, то
				echo $_SESSION['user_name'];	// Возвращаем имя пользователя подтвердившего запрос
			else
				echo 'error';   // Иначе возвращаем маркер ошибки
		}
		else
			echo 'error';
	}
}

?>