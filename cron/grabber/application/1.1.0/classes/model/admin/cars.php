<?php defined('SYSPATH') or die('No direct script access.');

class Model_Admin_Cars extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('admin_cars')
      ->fields(array(
         'id' => new Field_Primary,
         'name' => new Field_String,
         'year_from' => new Field_Integer,
         'year_to' => new Field_Integer,
         'mileage' => new Field_Integer,
         'vincode' => new Field_String,
         'active' => new Field_Boolean
      ));
   }

}