<?php

class View {

   protected $_file;
   protected $_data;

   public function __construct($file, $data = NULL)
   {
      $this->_file = $file;
      $this->_data = $data;
   }

   public static function factory($file, $data = NULL)
   {
      return new View($file, $data);
   }

   public function __toString()
   {
      return $this->render();
   }

   public function render()
   {
      if ($this->_data)
      {
         extract($this->_data, EXTR_SKIP);
      }
      
      ob_start();

      try
      {
         include SYSPATH.'/views/'.$this->_file.'.php';
      }
      catch (Exception $e)
      {
         ob_end_clean();
         throw $e;
      }

      return ob_get_clean();
   }

}