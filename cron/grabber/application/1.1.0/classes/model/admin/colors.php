<?php defined('SYSPATH') or die('No direct script access.');

class Model_Admin_Colors extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('admin_colors')
      ->fields(array(
         'car_id' => new Field_Integer,
         'exterior' => new Field_String,
         'interior' => new Field_String,
      ));
   }

}