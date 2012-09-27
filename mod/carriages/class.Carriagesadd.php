<?

class CarriagesAdd extends Proto {

    public $lastInsertId = false;

    public function drawContent() {
        if($this->checkAuth()) {
            $this->Process();
        }

        $this->errorsPublisher();
        $this->publish();
    }

    private function Process() {

        if (empty($_POST) || !$_POST['number']){
            $this->redirect($this->root_path.'?mod=carriages&sw=form&add');
            die;
        }


        $request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."carriage`
		(`number`,
         `loaddate`,
         `arrive_date`,
         `station`,
         `slot1`,
         `slot2`,
         `slot3`,
         `slot4`,
         `slot5`,
         `arrived`,
         `treking`,
         `treking_date`)
		 VALUES  
		( 
		'".mysql_real_escape_string(strtoupper($_POST['number']))."', 
		'".mysql_real_escape_string($_POST['loaddate'])."', 
		'".mysql_real_escape_string($_POST['arrive_date'])."', 
		'".mysql_real_escape_string($_POST['station'])."', 
		'".intval($_POST['slot1'])."', 
		'".intval($_POST['slot2'])."', 
		'".intval($_POST['slot3'])."', 
		'".intval($_POST['slot4'])."', 
		'".intval($_POST['slot5'])."', 
		'".intval($_POST['arrived'])."', 
		'".mysql_real_escape_string(($_POST['treking']))."', 
		NOW())";

        $this->mysqlQuery($request);

        $this->lastInsertId = mysql_insert_id();

        for ($i=1; $i<=5; $i++){
            $this->updateCars($_POST['slot'.$i]);
        }

        $this->redirect($this->root_path.'?mod=carriages');
        die;

    }

    private function updateCars($carId=false){
        if (!$carId || !is_numeric($carId)){
            return ;
        }

        $sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($carId)."'";
        $res = $this->mysqlQuery($sql);
        $carInfo = mysql_fetch_assoc($res);

        if (empty($carInfo)){
            return ;
        }

        $sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET `carriage`='".$this->lastInsertId."' WHERE `id`='".$carInfo['id']."'";
        $res = $this->mysqlQuery($sql);

        $sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."customers` WHERE `id`='".$carInfo['buyer']."'";


        $res = $this->mysqlQuery($sql);
        $ownerInfo = mysql_fetch_assoc($res);

        if ($ownerInfo['email']){
            $number = mysql_real_escape_string(strip_tags($_POST['number']));
            require($_SERVER['DOCUMENT_ROOT'].'/mod/carriages/templates/mail.php');

            $this->sMail($ownerInfo['email'], $text, 'Автомобиль погружен в вагон');
        }

    }
    
    
}
?>