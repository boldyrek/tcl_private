<?php

class carsToBuycheckvin extends Proto {

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
         $output = '';
         
         $list = '<ul style="margin:0; padding:0">%s</ul>';
         $item = '<li style="list-style:none;margin:3px 0;padding:0"><a href="%s" target="_blank">%s</a></li>';

         $data = array();

         $query = $this->mysqlQuery("SELECT * FROM `ccl_carstobuy` WHERE `vin` = '".$_GET['vincode']."' ORDER BY `id` DESC");

         if (mysql_num_rows($query))
         {
            while ($row = mysql_fetch_object($query))
            {
               $data['ctb-'.$row->id] = sprintf($item, '/?mod=carstobuy&sw=form&car_id='.$row->id, $row->model);
            }
         }

         unset($query, $row);

         $query = $this->mysqlQuery("SELECT * FROM `ccl_cars` WHERE `frame` = '".$_GET['vincode']."' ORDER BY `id` DESC");

         if (mysql_num_rows($query))
         {
            while ($row = mysql_fetch_object($query))
            {
               $data['c-'.$row->id] = sprintf($item, '/?mod=cars&sw=form&car_id='.$row->id, $row->model);
            }
         }

         if (! empty($data))
         {
            $output = sprintf($list, implode('', $data));
         }

         echo $output;
      }
   }

}
