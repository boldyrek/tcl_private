<?

class ContainersAdd extends Proto {
	
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
		
		if($_POST['delivered'] == 'on') $arrived = '1';
		else $arrived = '0';
		
		$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."containers` 
		(`id`, 
		`number`, 
		`sent`, 
		`portdate`, 
		`loaddate`, 
		`rail`, 
		`bishkek`, 
		`price`, 
		`slot1`, 
		`slot2`, 
		`slot3`, 
		`slot4`,
                `slot5`,
		`arrived`, 
		`expeditor`, 
		`port`,
		`booking`,
		`sealine`,
		`platform`,
                `reciever_name`,
                `reciever_address`,
                `own`,
                `docs_ready`,
                `agent_id`)
		VALUES  
		(LAST_INSERT_ID(), 
		'".mysql_real_escape_string(strtoupper($_POST['number']))."', 
		'".mysql_real_escape_string($_POST['sent'])."', 
		'".mysql_real_escape_string($_POST['portdate'])."', 
		'".mysql_real_escape_string($_POST['loaddate'])."', 
		'".mysql_real_escape_string($_POST['rail'])."', 
		'".mysql_real_escape_string($_POST['bishkek'])."', 
		'".mysql_real_escape_string($_POST['price'])."', 
		'".mysql_real_escape_string($_POST['slot1'])."', 
		'".mysql_real_escape_string($_POST['slot2'])."', 
		'".mysql_real_escape_string($_POST['slot3'])."', 
		'".mysql_real_escape_string($_POST['slot4'])."',
                '".mysql_real_escape_string($_POST['slot5'])."',
		'".$arrived."', 
		'".mysql_real_escape_string($_POST['expeditor'])."', 
		'".mysql_real_escape_string($_POST['port'])."',
		'".mysql_real_escape_string($_POST['booking'])."',
		'".mysql_real_escape_string($_POST['sealine'])."',
		'".mysql_real_escape_string($_POST['platform'])."',
                '".mysql_real_escape_string($_POST['reciever_name'])."',
                '".mysql_real_escape_string($_POST['reciever_address'])."',
                '".intval($_POST['own'])."',
                '".intval($_POST['docs_ready'])."',
                '".intval($_POST['agent_id'])."'
		)";
		$this->mysqlQuery($request);
		
		updateExpeditorBalance(mysql_real_escape_string($_POST['expeditor']));
		$lastId = mysql_fetch_array($this->mysqlQuery("SELECT max(id) as max FROM `ccl_".ACCOUNT_SUFFIX."containers`"));
		
		$this->redirect($this->root_path.'?mod=containers&sw=form&cont_id='.$lastId['max']); 
	}
}
?>