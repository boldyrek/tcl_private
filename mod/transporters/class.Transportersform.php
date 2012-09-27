<?
require($_SERVER['DOCUMENT_ROOT'].'/bin/balance.php');
class TransportersForm extends Proto {
	
	private $content;
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		// готовим данные для формы транспортника
		$root_path = $this->root_path;
		if((!isset($_GET['sup_id']) or intval($_GET['sup_id'])=='') and !isset($_GET['add']))
		{
			$this->redirect($this->root_path.'');
			exit;
		}
		
		$error = false;
		
		if(!isset($_GET['add'])) {
			updateTransporterBalance(intval($_GET['sup_id']));
			$content = mysql_fetch_array($this->mysqlQuery("
			SELECT * 
			FROM `ccl_".ACCOUNT_SUFFIX."transporters`
			WHERE `id` = '".intval($_GET['sup_id'])."'"));
			
			$lnk = '?mod=transporters&sw=save&id='.intval($_GET['sup_id']);
		
			if($content['id']=='') { 
				$this->errorHandler .= '<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Транспортник с такими параметрами в базе не обнаружен').'</div>';
				$error = true;
			}
		}
		else {
			$lnk = '?mod=transporters&sw=save&add';
			}
		
		if(!$error) {
			
			require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'mod/transporters/templates/transporter.form.php');
			
			if (isset($_GET['success'])) $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
			
			$this->page .= supForm($lnk, 
			
			$content,
			
			$this->mysqlQuery(
			"SELECT ccl_".ACCOUNT_SUFFIX."cars.id,model,frame,ccl_".ACCOUNT_SUFFIX."accounting.amount as `costtp`, ccl_".ACCOUNT_SUFFIX."accounting.date as `date` FROM ccl_".ACCOUNT_SUFFIX."cars
			RIGHT JOIN ccl_".ACCOUNT_SUFFIX."accounting
			ON(ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."accounting.car AND ccl_".ACCOUNT_SUFFIX."accounting.purpose = 4)
			WHERE `transporter` = '".intval($_GET['sup_id'])."'
			ORDER BY ccl_".ACCOUNT_SUFFIX."accounting.date DESC")
			/*
			$this->mysqlQuery(
			"SELECT id,model,frame,cost_to_port 
			FROM `ccl_cars` WHERE `transporter` = '".intval($_GET['sup_id'])."'")
			*/
			);
		}
	}
	
}
?>