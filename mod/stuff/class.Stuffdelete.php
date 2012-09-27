<?
class StuffDelete extends Proto {
	
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
		// работа с файлами
		require($_SERVER['DOCUMENT_ROOT'].'/inc/ftp_functions.php');
		
		//удаляем сам автомобиль
		if(!isset($_GET['what']) and isset($_GET['id'])) {
		
			if($_GET['container']!='0')
			{
				mysql_query("DELETE FROM ccl_".ACCOUNT_SUFFIX."stuff_container WHERE id_cont='".intval($_GET['container'])."'");
			}
/*			$related = mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."cars.supplier, ccl_cars.buyer, ccl_customers.dealer 
			FROM `ccl_cars` 
			LEFT JOIN `ccl_customers` 
			ON (ccl_cars.buyer = ccl_customers.id) 
			WHERE ccl_cars.id = '".intval($_GET['id'])."'"));*/
			
			$this->mysqlQuery("DELETE from `ccl_".ACCOUNT_SUFFIX."stuff` WHERE id='".intval($_GET['id'])."'");
			
			// удаляем комментарии к товару
			$this->mysqlQuery("DELETE * FROM `ccl_".ACCOUNT_SUFFIX."stuff_comment` WHERE `stuff_id` = '".intval($_GET['id'])."'");
			
/*			updateBalance($related['buyer'], $related['dealer']);
			updateSupplierBalance($related['supplier']);*/
			
			$this->redirect($this->root_path.'?mod=stuff');
		}
		
		elseif($_GET['what']=='photo' and $_GET['name']!=='') {
			$request = "
			SELECT * 
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_photos` 
			WHERE `file` ='".mysql_real_escape_string($_GET['name'])."'";
		
			$photo = mysql_fetch_array($this->mysqlQuery($request));
			$table = 'stuff_photos';
			if($photo['stuff']!=''){
				if($_GET['chk']==$photo['stuff']) {
						del_img($table, $photo['id'], $photo['stuff'], 'photo', $photo['file'], '', $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
						$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.$photo['stuff']);
				}
				else $this->redirect($this->root_path.'?mod=stuff');
			}
			else $this->redirect($this->root_path.'?mod=stuff');
		}
		elseif($_GET['what']=='certificate' and $_GET['name']!='') {
			$table = 'stuff_cert';
			$item = mysql_fetch_array($this->mysqlQuery("
			SELECT * 
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_cert` 
			WHERE `file` = '".mysql_real_escape_string($_GET['name'])."' 
			LIMIT 1"));
		
			del_img($table, $item['id'], $item['stuff'], '', $item['file'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
			$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.$item['stuff']);
		}
		
		elseif($_GET['what']=='inspection_file' and $_GET['name']!='') {
			$table = 'stuff_inspections';
			$item = mysql_fetch_array($this->mysqlQuery("
			SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_inspections`
			WHERE `file` = '".mysql_real_escape_string($_GET['name'])."'
			LIMIT 1"));
			
			del_img($table, $item['id'], $item['stuff'], '', $item['file'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
			$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.$item['stuff']);
		}

		elseif($_GET['what']=='adddoc_file' and $_GET['name']!='') {
			$table = 'stuff_adddoc';
			$item = mysql_fetch_array($this->mysqlQuery("
			SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."stuff_adddoc`
			WHERE `file` = '".mysql_real_escape_string($_GET['name'])."'
			LIMIT 1"));
			
			del_img($table, $item['id'], $item['stuff'], '', $item['file'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
			$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.$item['stuff']);
		}
		elseif($_GET['what']=='sell' and $_GET['name']!='') {

			$res=$this->mysqlQuery("SELECT sell_count FROM ccl_".ACCOUNT_SUFFIX."stuff_sell WHERE id=".intval($_GET['name']));
			if ($res && mysql_num_rows($res)>0)
			{
				list($sell_count)=mysql_fetch_array($res);
				
			}
			mysql_query("DELETE FROM ccl_".ACCOUNT_SUFFIX."stuff_sell WHERE id=".intval($_GET['name']));
			$this->mysqlQuery("UPDATE ccl_".ACCOUNT_SUFFIX."stuff SET sold=sold-".$sell_count." WHERE id='".intval($_GET['stuff_id'])."'");
			$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_GET['stuff_id']));
		}

		else $this->redirect($this->root_path.'?mod=stuff');
	}
}

?>