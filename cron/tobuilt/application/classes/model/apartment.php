<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_Apartment extends ORM {

   protected $_table_names_plural = FALSE;
   protected $_belongs_to = array('house' => array());
   protected $_created_column = array(
      'column' => 'date_added',
      'format' => 'Y-m-d H:i:s'
   );

}