<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_House extends ORM {

   protected $_table_names_plural = FALSE;
   protected $_has_many = array('apartments' => array());
   protected $_created_column = array(
      'column' => 'date_added',
      'format' => 'Y-m-d H:i:s'
   );

}