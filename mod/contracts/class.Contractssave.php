<?

class ContractsSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
		
	}
	
	function Process() {
		//если ни разу не открывали контракт, сохраняем его
		$dolg = intval($_POST['sum']) - intval($_POST['paid']);	
				if($_POST['chk']!='') {
					foreach($_POST['chk'] as $k=>$v)
					{
						$cars .= $k.';';
					}
				}
	
		if($_POST['cont_id']=='') { 
			if(intval($_POST['client_type'])==2) {
				$field_names = "`name`, `address`, `passport`, `contacts`, `data_source`, `client`,";
				$field_values = "'".mysql_real_escape_string($_POST['client_info']['name'])."',
				'".mysql_real_escape_string($_POST['client_info']['address'])."',
				'".mysql_real_escape_string($_POST['client_info']['passport'])."',
				'".mysql_real_escape_string($_POST['client_info']['contacts'])."',
				'local',
				'".intval($_POST['cust_id'])."',";
			}
			elseif(intval($_POST['client_type'])==1) {
				$field_names = "`client`,`data_source`,";
				$field_values = "'".intval($_POST['cust_id'])."', 'client', ";
			}
			
			$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."contracts`
				(`id`, `number`, `date`, ".$field_names." `sum`, `agent`, `paid`, `dolg`, `car`)
				VALUES (LAST_INSERT_ID(),
				'".mysql_real_escape_string($_POST['number'])."', 
				NOW(), 
				".$field_values."
				'".mysql_real_escape_string($_POST['sum'])."',
				'".mysql_real_escape_string($_POST['agent'])."',
				'".mysql_real_escape_string($_POST['paid'])."',
				'".intval($dolg)."',
				'".$cars."')";
				$this->mysqlQuery($request);
				
				$back_link = mysql_fetch_array($this->mysqlQuery("
				SELECT max(id) AS id 
				FROM `ccl_".ACCOUNT_SUFFIX."contracts` WHERE 1"));
			}
			//если контракт уже был сохранен, обновляем
			else {
	
				
				if(intval($_POST['client_type'])==2) {
					$fields = "`name` = '".mysql_real_escape_string($_POST['client_info']['name'])."', 
					`address` = '".mysql_real_escape_string($_POST['client_info']['address'])."', 
					`passport` = '".mysql_real_escape_string($_POST['client_info']['passport'])."',
					`contacts` = '".mysql_real_escape_string($_POST['client_info']['contacts'])."',
					`data_source` = 'local',
					`client` = '".intval($_POST['cust_id'])."',";
				}
				elseif(intval($_POST['client_type'])==1) {
					$fields = "`client` = '".intval($_POST['cust_id'])."',
					`data_source` = 'client', ";
				}
				
				$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."contracts` SET
				`number` = '".mysql_real_escape_string($_POST['number'])."',
				".$fields."
				`sum` = '".mysql_real_escape_string($_POST['sum'])."',
				`agent` = '".mysql_real_escape_string($_POST['agent'])."',
				`paid` = '".intval($_POST['paid'])."',
				`dolg` = '".intval($dolg)."',
				`car` = '".$cars."' 
				WHERE `id` = '".mysql_real_escape_string(intval($_POST['cont_id']))."'";
			
				$this->mysqlQuery($request);
				$back_link['id'] = $_POST['cont_id'];
			}
			
			$this->redirect($this->root_path.'?mod=contracts&sw=form&contract='.$back_link['id']);
	}
}

?>