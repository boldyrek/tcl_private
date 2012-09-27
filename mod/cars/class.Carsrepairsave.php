<?php

class Repairsave extends Proto {
    const SECRET_WORD = 'dsjk434343d934LIsi23983';


    private $car_info, $car_id, $_post, $files, $photolist, $full_data;
    private $_result;
    //const SEND_URI = 'http://repair/cars/import/';
    const SEND_URI = 'http://repair.web.weltkind.com/cars/import/';

    public function drawContent() {
        if($this->checkAuth()) {
            $this->Process();
        }

        $this->errorsPublisher();
        $this->publish();
    }

    private function Process() {

        if (isset($_GET['mode']) && $_GET['mode']=='delete'){
            $this->_delete();
            return ;
        }

        if (isset($_GET['mode']) && $_GET['mode']=='markassold'){
            $this->_markAsSold();
            return ;
        }

        if (empty($_POST)){
            die ('No post parametrs');
        }
        $this->_post = $_POST;

        $this->carInfo();

        $fullPhotos = array();
        if (!empty($_POST['photos'])){
            foreach ($_POST['photos'] as $num=>$photo){
                if ($photo!=''){
                    $fullPhotos[]='http://'.$_SERVER['HTTP_HOST'].'/photos/'.$this->car_id.'/'.$photo;
                }
            }
        }

        $this->_post['images'] = implode(';',$fullPhotos);


        unset($this->_post['save']);

        $this->_send(self::SEND_URI);

        if ($this->getResult()->state=='success' && is_numeric($this->getResult()->message)){
            $id = $this->getResult()->message;
            $this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."repair_sent` (`tcl_id`, `repair_id`, `data`) VALUES('".$this->car_id."', '".$id."', '".serialize($this->_post)."')");
            header('Location: /?mod=cars&sw=repair&car_id='.$this->car_id);
            exit;
        }

    }

    private function getPhotosPaths(){
        $this->files = $this->mysqlQuery(
        "SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos`
			WHERE `car` = '".$this->car_info['id']."' ORDER BY `folder` DESC, `id` ASC LIMIT 6");

        //$list = '';
        // Put photos links to array
        $alist = array();
        while($p = mysql_fetch_assoc($this->files)){
            //$list .= 'http://tcl.makmalauto.com/photos/'.$this->car_id.'/'.$p['file'].';';
            $alist[] = 'http://tcl.makmalauto.com/photos/'.$this->car_id.'/'.$p['file'].';';
        }
        // Tiny array resort
        $tmp_link = array_shift($alist);
        array_push($alist,$tmp_link);
        $list = implode($alist);

        $list = rtrim($list, ';');
        $this->photolist = $list;
        $_POST['photos'] = $list;
        return $list;
    }


    private function _send($url){
        $postString = '';

        foreach ($this->_post as $key=>$val){
            $postString.=$key.'='.$val.'&';
        }

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST , 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS , $postString);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION  ,1);
        curl_setopt($ch, CURLOPT_HEADER      ,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER  ,1);  // RETURN THE CONTENTS OF THE CALL
        curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
        $this->_result = curl_exec($ch);
        //		$this->_result = iconv('utf-8', 'windows-1251',$this->_result);

    }

    public function getResult(){

        $result = json_decode($this->_result);

        if (!isset($result->state)){
            echo "Неизвестная ошибка";
            print_r($this->_result);
            die;

        }

        if ($result->state!='success'){
            echo $result->message; die;

        }

        return $result;
    }

    private function carInfo(){
        if(intval($_GET['car_id'])!=0) {
            $this->car_info = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car_id'])."'"));
            $this->car_id = $this->car_info['id'];
        }
        else {header('Location: /?mod=cars');
        exit;
        }
    }

    private function _delete(){
        $this->carInfo();

        if (!$this->car_id){
            die("Ошибка при удалении");
        }

        $this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."repair_sent` WHERE `tcl_id`='".$this->car_id."'");
        header('Location: /?mod=cars&sw=repair&car_id='.$this->car_id);
        exit;

    }

    private function _markAsSold(){
        $this->carInfo();

        $sql = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."repair_sent` WHERE `tcl_id`='".$this->car_info['id']."'";
        $tmp = $this->mysqlQuery($sql);

        $repairInfo = mysql_fetch_assoc($tmp);

        if (empty($repairInfo)){
            die ('Машина с уазанным ID не была отпарвлена в базу');
        }

        $this->_post['secret-key'] = self::SECRET_WORD;
        $this->_post['car_id'] = $repairInfo['repair_id'];
        $this->_send(self::SEND_URI .'mark-as-sold');

        if ($this->getResult()->state == 'success'){
            $sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."repair_sent` SET `sold` = '1' WHERE `tcl_id`='".$this->car_info['id']."'";
            $this->mysqlQuery($sql);
        }

        header('Location: /?mod=cars&sw=repair&car_id='.$this->car_id);
        exit;

    }



}
?>