<?

class CertificatesSave extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
		
	}
	
	function Process() {
		if(intval($_POST['cert_id'])!='' and intval($_POST['cert_id'])!=0) {
		$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."certificates` 
		SET `car` = '".intval($_POST['car_id'])."',
		`frame_model` = '".mysql_real_escape_string(strtoupper($_POST['frame_model']))."',
		`engine_model` = '".mysql_real_escape_string($_POST['engine_model'])."',
		`car_brutto` = '".mysql_real_escape_string($_POST['car_brutto'])."',
		`fuel_type` = '".mysql_real_escape_string($_POST['fuel_type'])."',
		`form_number` = '".mysql_real_escape_string($_POST['form_number'])."',
		`class_number` = '".mysql_real_escape_string($_POST['class_number'])."',
		`car_length` = '".mysql_real_escape_string($_POST['car_length'])."',
		`car_width` = '".mysql_real_escape_string($_POST['car_width'])."',
		`car_height` = '".mysql_real_escape_string($_POST['car_height'])."',
		`front_weight` = '".mysql_real_escape_string($_POST['front_weight'])."',
		`back_weight` = '".mysql_real_escape_string($_POST['back_weight'])."',
		`cert_reg_number` = '".mysql_real_escape_string($_POST['cert_reg_number'])."',
		`uchet_nomer` = '".mysql_real_escape_string($_POST['uchet_nomer'])."',
		`car_reg_number` = '".mysql_real_escape_string($_POST['car_reg_number'])."',
		`reg_cancel_date` = '".mysql_real_escape_string($_POST['reg_cancel_date'])."',
		`export_date` = '".mysql_real_escape_string($_POST['export_date'])."',
		`supplier_name` = '".mysql_real_escape_string($_POST['supplier_name'])."',
		`supplier_address` = '".mysql_real_escape_string($_POST['supplier_address'])."',
		`term_date` = '".mysql_real_escape_string($_POST['term_date'])."',
		`classification` = '".mysql_real_escape_string($_POST['classification'])."',
		`exp_type` = '".mysql_real_escape_string($_POST['exp_type'])."',
		`car_type` = '".mysql_real_escape_string($_POST['car_type'])."'	WHERE `car` = '".intval($_POST['car_id'])."'";
		}
		else {
		$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."certificates` 
		(`id`,
		`car`,
		`frame_model`,
		`engine_model`,
		`car_brutto`,
		`fuel_type`,
		`form_number`,
		`class_number`,
		`car_length`,
		`car_width`,
		`car_height`,
		`front_weight`,
		`back_weight`,
		`cert_reg_number`,
		`uchet_nomer`,
		`car_reg_number`,
		`reg_cancel_date`,
		`export_date`,
		`supplier_name`,
		`supplier_address`,
		`term_date`,
		`classification`,
		`exp_type`,
		`car_type`) VALUES
		(LAST_INSERT_ID(),
		'".intval($_POST['car_id'])."',
		'".mysql_real_escape_string($_POST['frame_model'])."',
		'".mysql_real_escape_string($_POST['engine_model'])."',
		'".mysql_real_escape_string($_POST['car_brutto'])."',
		'".mysql_real_escape_string($_POST['fuel_type'])."',
		'".mysql_real_escape_string($_POST['form_number'])."',
		'".mysql_real_escape_string($_POST['class_number'])."',
		'".mysql_real_escape_string($_POST['car_length'])."',
		'".mysql_real_escape_string($_POST['car_width'])."',
		'".mysql_real_escape_string($_POST['car_height'])."',
		'".mysql_real_escape_string($_POST['front_weight'])."',
		'".mysql_real_escape_string($_POST['back_weight'])."',
		'".mysql_real_escape_string($_POST['cert_reg_number'])."',
		'".mysql_real_escape_string($_POST['uchet_nomer'])."',
		'".mysql_real_escape_string($_POST['car_reg_number'])."',
		'".mysql_real_escape_string($_POST['reg_cancel_date'])."',
		'".mysql_real_escape_string($_POST['export_date'])."',
		'".mysql_real_escape_string($_POST['supplier_name'])."',
		'".mysql_real_escape_string($_POST['supplier_address'])."',
		'".mysql_real_escape_string($_POST['term_date'])."',
		'".mysql_real_escape_string($_POST['classification'])."',
		'".mysql_real_escape_string($_POST['exp_type'])."',
		'".mysql_real_escape_string($_POST['car_type'])."')";
		}
		$this->mysqlQuery($request);
			
		$this->redirect($this->root_path.'?mod=certificates&sw=form&car='.intval($_POST['car_id']).'&success');
	}
}

?>