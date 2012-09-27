<?

class ContainersDelete extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		if(!isset($_GET['what'])) {
			//подгружаем файл обновления балансов
			require($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');
			
			// определяем кто связан с этим контейнером, автомобили и экспедитора
			$slots = mysql_fetch_array($this->mysqlQuery("
			SELECT slot1,slot2,slot3,slot4,slot5,expeditor
			FROM `ccl_".ACCOUNT_SUFFIX."containers` 
			WHERE id='".intval($_GET['id'])."'"));
			
				if($slots['slot1']+$slots['slot2']+$slots['slot3']+$slots['slot4']+$slots['slot5']!=0)
				{
					$n=1;
					while($n<=5)
					{
						// отцепляем контейнер от автомобиля
						if($slots['slot'.$n]!=0) $this->mysqlQuery("
						UPDATE `ccl_".ACCOUNT_SUFFIX."cars` 
						SET container='0',delivered='0' 
						WHERE id='".$slots['slot'.$n]."'");
						$n++;
					}
				}
				
				$request = "DELETE FROM ccl_".ACCOUNT_SUFFIX."stuff_container WHERE id_cont='".intval($_GET['id'])."'";
				$this->mysqlQuery($request);
				
				$request = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."containers` 
				WHERE id=".intval($_GET['id']);
				$this->mysqlQuery($request);
			
				updateExpeditorBalance($slots['expeditor']);
				
				$owners = $this->mysqlQuery("
				SELECT DISTINCT buyer 
				FROM `ccl_".ACCOUNT_SUFFIX."cars` 
				WHERE `id` = '".intval($slots['slot1'])."' OR
				`id` = '".intval($slots['slot2'])."' OR 
				`id` = '".intval($slots['slot3'])."' OR 
				`id` = '".intval($slots['slot4'])."' OR
                                `id` = '".intval($slots['slot5'])."'
				ORDER BY buyer ASC");
				$num = mysql_num_rows($owners);
				
				$i=1;
				if($num>0) {
					while($i<=$num) {
					$line = mysql_fetch_array($owners);
					$buyer[$i] = $line['buyer'];
					$i++;
					}
				}	
				if(count($buyer)>0) {
					$request = "SELECT id,dealer 
					FROM `ccl_".ACCOUNT_SUFFIX."customers` WHERE ";
					$i=1;
					while($i<=count($buyer)) {
					$request .= "`id` = '".$buyer[$i]."'";
					if(count($buyer)>$i) $request .= " OR";
					$i++;
					}
					$request .= " ORDER BY id ASC";
					
					$owners_dealers = $this->mysqlQuery($request);
			
				}
				$i=1;
				$num = @mysql_num_rows($owners_dealers);
				if($num>0) {
					while($i<=$num) {
						$line = mysql_fetch_array($owners_dealers);
						updateBalance($line['id'],$line['dealer']);
						$i++;
					}
				}
				
			$this->redirect($this->root_path.'?mod=containers');
		}
		elseif($_GET['what']=='file') {
			require($_SERVER['DOCUMENT_ROOT']."/inc/ftp_functions.php");
			$id = mysql_fetch_array($this->mysqlQuery("SELECT id, container FROM `ccl_".ACCOUNT_SUFFIX."container_files` WHERE `file` = '".$_GET['name']."'"));
			del_img('container_files', $id['id'], $id['container'], 'containers', $_GET['name'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
			$this->redirect($this->root_path.'?mod=containers&sw=form&cont_id='.$id['container']);
		}
	}
}
?>