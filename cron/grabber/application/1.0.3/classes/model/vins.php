<?php defined('SYSPATH') or die('No direct script access.');

class Model_Vins extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('vins')
      ->fields(array(
         'id' => new Field_Primary,
         'vincode' => new Field_String,
         'target_id' => new Field_Integer,
         'interior_code' => new Field_String,
         'exterior_code' => new Field_String,
         'date_made' => new Field_String,
         'date_added' => new Field_Timestamp(array(
            'format' => 'Y-m-d H:i:s',
         ))
      ));
   }

}