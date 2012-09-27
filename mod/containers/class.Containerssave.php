<?

class ContainersSave extends Proto {

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

		$container = intval($_GET['id']);
		mysql_query("DELETE FROM ccl_".ACCOUNT_SUFFIX."stuff_container WHERE id_cont='{$container}'");
		mysql_query("UPDATE ccl_".ACCOUNT_SUFFIX."stuff SET container='0' WHERE container='{$container}'");

		if (isset($_POST['stuff']) && count($_POST['stuff'])>0)
		{
			// delete old
			foreach ($_POST['stuff'] as $val)
			{
				$val = intval($val);
				mysql_query("INSERT INTO ccl_".ACCOUNT_SUFFIX."stuff_container SET id_cont='{$container}', id_stuff='{$val}'");
				mysql_query("UPDATE ccl_".ACCOUNT_SUFFIX."stuff SET container='{$container}', post_id='-1' WHERE id='{$val}'");
			}
		}

		if($_POST['delivered'] == 'on')
		{
			$arrived = '1';
			$car_arrived = " `delivered` = '1'";
		}
		else
		{
			$arrived = '0';
			$car_arrived =" `delivered` = '0'";
		}

		$i=1;
		while($i<=5)
		{
			if($_POST['slot'.$i]!='0')
			{

				$car_id=intval($_POST['slot'.$i]);

				$slots.=",`slot".$i."` = '".$_POST['slot'.$i]."' ";
				$query=$this->mysqlQuery("SELECT buyer, container FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".$car_id."'");
				if ($query && mysql_num_rows($query)>0)
				{
					list ($buyer, $container) = mysql_fetch_row($query);
					if ($container!=intval($_GET['id'])){
						mysql_query("
							UPDATE `ccl_".ACCOUNT_SUFFIX."cars`
							SET `container` = '".intval($_GET['id'])."', ".$car_arrived."
							WHERE `id` = '".$car_id."'");
						$query2=$this->mysqlQuery("SELECT email FROM `ccl_".ACCOUNT_SUFFIX."customers` WHERE `id` = '".$buyer."'");

						if ($query2 && mysql_num_rows($query2)>0)
						{
							list ($mail) = mysql_fetch_row($query2);
							$this->send_mail($mail,"containers",array("container"=>mysql_real_escape_string(strtoupper($_POST['number'])), "date"=>date('d.m.Y')),'mail');
						}
					} else
					{
						mysql_query("UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET $car_arrived WHERE `id` = '".$car_id."'");
					}
				}
			}
			else {
				$slots.=",`slot".$i."` = '0' ";
			}
			if(isset($_POST['hid_slot'.$i]) and $_POST['hid_slot'.$i]!=$_POST['slot'.$i])
			{
				$this->mysqlQuery("
				UPDATE `ccl_".ACCOUNT_SUFFIX."cars` 
				SET `container`='0' 
				WHERE `id`=".intval($_POST['hid_slot'.$i]));

			}
			$i++;
		}
		$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."containers` SET `number` = '".mysql_real_escape_string(strtoupper($_POST['number']))."',
					`sent` = '".mysql_real_escape_string($_POST['sent'])."',
					`portdate` = '".mysql_real_escape_string($_POST['portdate'])."',
					`loaddate` = '".mysql_real_escape_string($_POST['loaddate'])."',
					`rail` = '".mysql_real_escape_string($_POST['rail'])."',
					`bishkek` = '".mysql_real_escape_string($_POST['bishkek'])."',
					`price` = '".mysql_real_escape_string($_POST['price'])."' ".$slots.", 
					`torail` = '".intval($_POST['torail'])."',
					`arrive_time` = '".mysql_real_escape_string($_POST['arrive_time'])."', 
					`station` = '".intval($_POST['station'])."',
					`arrived` = '".$arrived."',
					`expeditor` = '".mysql_real_escape_string($_POST['expeditor'])."',
					`port` = '".mysql_real_escape_string($_POST['port'])."',
					`booking` = '".mysql_real_escape_string($_POST['booking'])."',
					`sealine` = '".mysql_real_escape_string($_POST['sealine'])."',
					`sell_price` = '".mysql_real_escape_string($_POST['sellprice'])."',
					`unload_price` = '".mysql_real_escape_string($_POST['unloadprice'])."',
					`platform` = '".mysql_real_escape_string($_POST['platform'])."',
                                        `reciever_name` = '".mysql_real_escape_string($_POST['reciever_name'])."',
                                        `reciever_address` = '".mysql_real_escape_string($_POST['reciever_address'])."',
                                        `own` = '".intval($_POST['own'])."',
                                        `docs_ready` = '".intval($_POST['docs_ready'])."',
                                        `agent_id` = '".intval($_POST['agent_id'])."'
					 WHERE `id`=".intval($_GET['id'])." LIMIT 1";

		$this->mysqlQuery($request);
		
		// ============================================================
		// Распределение стоимости контейнера по автомобилям внури него
		// ------------------------------------------------------------
		$sellp=intval($_POST['sellprice']);	// Стоимость контейнера
		if($sellp!=0 AND $sellp!='')
		{
			$cnt=0;								// Количество автомобилей в контейнере
			$a=array(intval($_POST['slot1']), intval($_POST['slot2']), intval($_POST['slot3']), intval($_POST['slot4']), intval($_POST['slot5']));
		
			foreach($a as $b) if($b>0) $cnt++;	// Подсчёт количества авто в контейнере
			foreach($a as $b)
			{					// Цикл распределения стоимости
				if($b>0)
				{
					// Проверка на существование для машины расхода со стоимости контейнера
					$qqq=mysql_fetch_assoc(mysql_query('SELECT COUNT(*) as yes FROM `ccl_'.ACCOUNT_SUFFIX.'accounting` WHERE `car`="'.$b.'" AND `purpose`=30'));
					if(intval($qqq['yes'])>0)	// mysql_num_rows(mysql_query('SELECT * FROM `ccl_'.ACCOUNT_SUFFIX.'expenses` WHERE `car`="'.$b.'" AND `purpose`=30'))
						mysql_query('UPDATE `ccl_'.ACCOUNT_SUFFIX.'accounting` SET `amount`='.-($sellp/$cnt).' WHERE `car`="'.$b.'" AND `purpose`=30');
					else
						mysql_query('INSERT INTO `ccl_'.ACCOUNT_SUFFIX.'accounting` (date,user_added,amount,car,purpose,type,paid,signer) VALUES  ("'.date('Y-m-d H:m:i').'","'.intval($_SESSION['login_id']).'",'.-($sellp/$cnt).',"'.$b.'","30","2","1","'.$_SESSION['user_name'].'")');
				}		// `paid`=1, `signer`=\''.$_SESSION['user_name'].'\'
			}
		}
		// ------------------------------------------------------------
		// Распределение завершено
		// ============================================================

        // ============================================================
		// Распределение стоимости разгрузки контейнера по автомобилям внури него
		// ------------------------------------------------------------
		$sellp=intval($_POST['unloadprice']);	// Стоимость контейнера
		if($sellp!=0 AND $sellp!='')
		{
			$cnt=0;								// Количество автомобилей в контейнере
			$a=array(intval($_POST['slot1']), intval($_POST['slot2']), intval($_POST['slot3']), intval($_POST['slot4']), intval($_POST['slot5']));

			foreach($a as $b) if($b>0) $cnt++;	// Подсчёт количества авто в контейнере
			foreach($a as $b)
			{					// Цикл распределения стоимости
				if($b>0)
				{
					// Проверка на существование для машины расхода со стоимости контейнера
					$qqq=mysql_fetch_assoc(mysql_query('SELECT COUNT(*) as yes FROM `ccl_'.ACCOUNT_SUFFIX.'accounting` WHERE `car`="'.$b.'" AND `purpose`=6'));
					if(intval($qqq['yes'])>0)	// mysql_num_rows(mysql_query('SELECT * FROM `ccl_'.ACCOUNT_SUFFIX.'expenses` WHERE `car`="'.$b.'" AND `purpose`=30'))
						mysql_query('UPDATE `ccl_'.ACCOUNT_SUFFIX.'accounting` SET `amount`='.($sellp/$cnt).' WHERE `car`="'.$b.'" AND `purpose`=6');
					else
						mysql_query('INSERT INTO `ccl_'.ACCOUNT_SUFFIX.'accounting` (date,user_added,amount,car,purpose,type,paid,signer) VALUES  ("'.date('Y-m-d H:m:i').'","'.intval($_SESSION['login_id']).'",'.($sellp/$cnt).',"'.$b.'","6","2","1","'.$_SESSION['user_name'].'")');
				}		// `paid`=1, `signer`=\''.$_SESSION['user_name'].'\'
			}
		}
		// ------------------------------------------------------------
		// Распределение завершено
		// ============================================================

		// Обновление балланса
		updateContainerBalance(array(intval($_POST['slot1']), intval($_POST['slot2']), intval($_POST['slot3']), intval($_POST['slot4']), intval($_POST['slot5'])));
		updateExpeditorBalance(mysql_real_escape_string($_POST['expeditor']));

		$this->redirect($this->root_path.'?mod=containers&sw=form&cont_id='.intval($_GET['id']).'&success');

	}
}
?>