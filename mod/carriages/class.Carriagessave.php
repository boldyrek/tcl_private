<?

class CarriagesSave extends Proto {

    public function drawContent() {
        if($this->checkAuth()) {
            $this->Process();
        }

        $this->errorsPublisher();
        $this->publish();
    }

    private function Process() {

        $sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."carriage` WHERE `id`='".intval($_GET['id'])."'";
        $res = $this->mysqlQuery($sql);
        $info = mysql_fetch_assoc($res);

        $sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."carriage` SET
	    `number` = '".mysql_real_escape_string(strtoupper($_POST['number']))."', 
		`loaddate` = '".mysql_real_escape_string($_POST['loaddate'])."', 
		`arrive_date` = '".mysql_real_escape_string($_POST['arrive_date'])."', 
		`station` = '".mysql_real_escape_string($_POST['station'])."', 
		`slot1` = '".intval($_POST['slot1'])."', 
		`slot2` = '".intval($_POST['slot2'])."', 
		`slot3` = '".intval($_POST['slot3'])."', 
		`slot4` = '".intval($_POST['slot4'])."', 
		`slot5` = '".intval($_POST['slot5'])."', 
		`slot6` = '".intval($_POST['slot6'])."', 
		`slot7` = '".intval($_POST['slot7'])."', 
		`slot8` = '".intval($_POST['slot8'])."', 
		`slot9` = '".intval($_POST['slot9'])."', 
		`slot10` = '".intval($_POST['slot10'])."', 
		`arrived` = '".intval($_POST['arrived'])."'";

        if ($info['treking']!=$_POST['treking']){
            $sql.="
	        ,
	        `treking` = '".mysql_real_escape_string($_POST['treking'])."', 
	        `treking_date`  = NOW() 
	        ";
        }

        $sql.=" WHERE `id`='".intval($_GET['id'])."'";


        $this->mysqlQuery($sql);

        $sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET `carriage`='' WHERE `carriage`='".intval($_GET['id'])."'";
        $res = $this->mysqlQuery($sql);
    

        for ($i=1; $i<=10; $i++){
            $this->updateCars($_POST['slot'.$i]);
        }

        $this->redirect($this->root_path.'?mod=carriages&sw=form&cont_id='.intval($_GET['id']).'&success');
    }


    private function updateCars($carId=false){
        if (!$carId || !is_numeric($carId)){
            return ;
        }
        $sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET `carriage`='".intval($_GET['id'])."' WHERE `id`='".$carId."'";
        $res = $this->mysqlQuery($sql);
    }


}
?>