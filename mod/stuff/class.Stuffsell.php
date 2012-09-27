<?
class StuffSell extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		//подгружаем файл обновления балансов
		
		if($this->exists($_GET['stuff_id']) and intval($_GET['stuff_id'])!='0') {
		
	
			$res=$this->mysqlQuery("INSERT `ccl_".ACCOUNT_SUFFIX."stuff_sell` 
			SET `stuff_id` = '".intval($_GET['stuff_id'])."',
			`sell_count` = '".intval($_POST['sell_count'])."',
			`sell_price` = '".clean_txt($_POST['sell_price'])."',
			`sell_dat` = '".clean_txt($_POST['sell_dat'])."'");
			
			if ($res)
			{
				$this->mysqlQuery("UPDATE ccl_".ACCOUNT_SUFFIX."stuff SET sold=sold+".intval($_POST['sell_count'])." WHERE id='".intval($_GET['stuff_id'])."'");	
			}
			
			$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_GET['stuff_id']).'&success');
		}
		else $this->redirect($this->root_path.'?mod=stuff');
	}
}
?>