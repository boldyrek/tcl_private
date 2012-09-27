<?php

class Colors extends Proto {

   public function index()
   {
      
   }

   public function add()
   {
      $result = $this->mysqlQuery("INSERT INTO `ccl_grabber_admin_colors_ref` VALUES ('', '{$_GET['code']}', '{$_GET['desc']}', '{$_GET['is_ext']}')");
      echo json_encode(array('params' => $_GET, 'result' => $result, 'id' => mysql_insert_id()));
      exit;
   }

   public function delete()
   {
      $result = $this->mysqlQuery("DELETE FROM `ccl_grabber_admin_colors_ref` WHERE `id` = {$_GET['id']}");
      echo json_encode(array('params' => $_GET, 'result' => $result));
      exit;
   }

}
