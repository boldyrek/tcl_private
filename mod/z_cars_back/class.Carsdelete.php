<?
class CarsDelete extends Proto {
	
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
				$container = mysql_fetch_array($this->mysqlQuery("
				SELECT slot1,slot2,slot3,slot4 
				FROM `ccl_".ACCOUNT_SUFFIX."containers` 
				WHERE id='".intval($_GET['container'])."'"));
				$n=1;
				while($n<=4)
				{
					if($container['slot'.$n]==$_GET['id']) $this->mysqlQuery("
					UPDATE `ccl_".ACCOUNT_SUFFIX."containers` 
					SET `slot".$n."` = '0' 
					WHERE `id`='".intval($_GET['container'])."'");
					$n++;
				}
			}
			$related = mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."cars.supplier, ccl_".ACCOUNT_SUFFIX."cars.buyer, ccl_".ACCOUNT_SUFFIX."customers.dealer 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` 
			ON (ccl_".ACCOUNT_SUFFIX."cars.buyer = ccl_".ACCOUNT_SUFFIX."customers.id) 
			WHERE ccl_".ACCOUNT_SUFFIX."cars.id = '".intval($_GET['id'])."'"));
			
			$request = "
			DELETE from `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE id='".intval($_GET['id'])."'";
			$this->mysqlQuery($request);
			
			// удаляем комментарии к этой машине
			$this->mysqlQuery("
			DELETE * FROM `ccl_".ACCOUNT_SUFFIX."car_comment`
			WHERE `car_id` = '".intval($_GET['id'])."'");
			
			updateBalance($related['buyer'], $related['dealer']);
			updateSupplierBalance($related['supplier']);
			
			$this->redirect($this->root_path.'?mod=cars');
		}
		
		elseif($_GET['what']=='photo' and $_GET['name']!=='') {
			$request = "
			SELECT * 
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` 
			WHERE `file` ='".mysql_real_escape_string($_GET['name'])."'";
		
			$photo = mysql_fetch_array($this->mysqlQuery($request));
			$table = 'cars_photos';
			if($photo['car']!=''){
				if($_GET['chk']==$photo['car']) {
						del_img($table, $photo['id'], $photo['car'], 'photo', $photo['file'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
						$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.$photo['car']);
				}
				else $this->redirect($this->root_path.'?mod=cars');
			}
			else $this->redirect($this->root_path.'?mod=cars');
		}
		elseif($_GET['what']=='certificate' and $_GET['name']!='') {
			$table = 'cars_cert';
			$item = mysql_fetch_array($this->mysqlQuery("
			SELECT * 
			FROM `ccl_".ACCOUNT_SUFFIX."cars_cert` 
			WHERE `file` = '".mysql_real_escape_string($_GET['name'])."' 
			LIMIT 1"));
		
			del_img($table, $item['id'], $item['car'], '', $item['file'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
			$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.$item['car']);
		}
		
		elseif($_GET['what']=='inspection_file' and $_GET['name']!='') {
			$table = 'inspections';
			$item = mysql_fetch_array($this->mysqlQuery("
			SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."inspections`
			WHERE `file` = '".mysql_real_escape_string($_GET['name'])."'
			LIMIT 1"));
			
			del_img($table, $item['id'], $item['car'], '', $item['file'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
			$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.$item['car']);
		}

		elseif($_GET['what']=='adddoc_file' and $_GET['name']!='') {
			$table = 'adddoc';
			$item = mysql_fetch_array($this->mysqlQuery("
			SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."adddoc`
			WHERE `file` = '".mysql_real_escape_string($_GET['name'])."'
			LIMIT 1"));
			
			del_img($table, $item['id'], $item['car'], '', $item['file'], $this->root_path, $this->ftp_host,  $this->ftp_user,  $this->ftp_pass);
			$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.$item['car']);
		}

		
		elseif($_GET['what']=='expeditorsphoto' and $_GET['name']!=='') {
		
			$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."expeditors_photo` WHERE `file`='".addslashes($_GET["name"])."'");
			if(mysql_affected_rows()) {
				unlink($_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/".strtr($_GET["name"],".","").".thumb.jpg");
				unlink($_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/".strtr($_GET["name"],".","").".jpg");
				$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.$_GET['car']);
			} else $this->redirect($this->root_path.'?mod=cars');
		}
		else $this->redirect($this->root_path.'?mod=cars');
	}
}

?>