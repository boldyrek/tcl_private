<?

class ContractsAct extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
		
	}
	
	function Process() {
		// АКТ ПРИЕМКИ К КОНТРАКТУ

		if(isset($_GET['cont_id']) and intval($_GET['cont_id'])!='' and intval($_GET['cont_id'])!='0') {
						
			$month = array();	
			$month['1'] = 'января'; 
			$month['2'] = 'февраля';
			$month['3'] = 'марта';
			$month['4'] = 'апреля'; 
			$month['5'] = 'мая';
			$month['6'] = 'июня'; 
			$month['7'] = 'июля'; 
			$month['8'] = 'августа';
			$month['9'] = 'сентября';
			$month['10'] = 'октября';
			$month['11'] = 'ноября';
			$month['12'] = 'декабря';
			$this_day = date('d');
			$this_month = $month[ltrim(date('m'),'0')];
			$this_year = date('y');
				
			$content = mysql_fetch_array($this->mysqlQuery("SELECT 
			ccl_".ACCOUNT_SUFFIX."contracts.number, ccl_".ACCOUNT_SUFFIX."contracts.date, ccl_".ACCOUNT_SUFFIX."customers.name, ccl_".ACCOUNT_SUFFIX."customers.passport  
			FROM `ccl_".ACCOUNT_SUFFIX."contracts`
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` 
			ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."contracts.client)
			WHERE ccl_".ACCOUNT_SUFFIX."contracts.id = '".intval($_GET['cont_id'])."' LIMIT 1"));
			$contract_date = explode('-', $content['date']);
			
			require($_SERVER['DOCUMENT_ROOT']."/mod/contracts/templates/act.php");
			$filename = $this->root_path.'export/act.doc';
			
			//пишем текст акта в выходной файл
			if (is_writable($_SERVER['DOCUMENT_ROOT'].$filename)) {
			
				if (!$handle = fopen($_SERVER['DOCUMENT_ROOT'].$filename, 'w')) {
			        echo "Cannot open file ($filename)";
			        exit;
				}
			
			   if (fwrite($handle, $out) === FALSE) {
			       echo "Cannot write to file ($filename)";
			       exit;
				}
			   
			   fclose($handle);
			} 
			else  echo "The file $filename is not writable";
			
			$this->redirect($filename);
		}
		else $this->redirect($this->root_path.'?mod=contracts');

	}
}
?>