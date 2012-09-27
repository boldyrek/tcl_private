<?php

class Model_Cache extends Model {

   protected $_table_name = 'ccl_grabber_cache';

   public function unique($source, $ident_string)
   {
      return $this->get(array('source' => $source, 'ident_string' => $ident_string), TRUE);
   }
   
}