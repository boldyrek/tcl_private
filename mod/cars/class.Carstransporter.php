<?php

class Transporter extends Proto {

   public function drawContent()
   {
      if ($this->checkAuth())
      {
         $this->Process();
      }

      $this->errorsPublisher();
      
      $this->publish();
   }

   private function Process()
   {
      if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
      {
         $this->mysqlQuery("
            UPDATE `ccl_cars`
            SET `transporter` = '".intval($_GET['transporter'])."'
            WHERE `id` = '".intval($_GET['id'])."'
         ");
      }
   }

}