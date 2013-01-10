<?php defined('SYSPATH') or die('No direct script access.');

class Model_Admin_Options extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('admin_options')
      ->fields(array(
         'car_id' => new Field_Integer,
         'condition' => new Field_String,
         'exception' => new Field_String,
         'hl' => new Field_String
      ));
   }

}
