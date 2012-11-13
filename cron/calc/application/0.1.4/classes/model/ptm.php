<?php defined('SYSPATH') or die('No direct script access.');

class Model_PTM extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('ptm')
      ->fields(array(
         'id' => new Field_Primary,
         'parent_id' => new Field_Integer,
         'year' => new Field_Integer,
         'name' => new Field_String,
         'url' => new Field_String,
         'mileage' => new Field_Integer,
         'price' => new Field_Integer,
         'date_added' => new Field_Timestamp(array(
            'auto_now_create' => TRUE,
            'format' => 'Y-m-d'
         )),
      ));
   }

}