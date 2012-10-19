<?

class CarriagesDelete extends Proto {

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

        if (empty($info)){
            $this->redirect($this->root_path.'?mod=carriages');
            die;
        }
        
        $sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET `carriage`='' WHERE `carriage`='".intval($_GET['id'])."'";
        $res = $this->mysqlQuery($sql);
        
        $sql="DELETE FROM `ccl_".ACCOUNT_SUFFIX."carriage` WHERE `id`='".intval($_GET['id'])."'";
        $res = $this->mysqlQuery($sql);
        
        
        $this->redirect($this->root_path.'?mod=carriages');
        die;
            

        
        
        
    }
    
    
}
?>