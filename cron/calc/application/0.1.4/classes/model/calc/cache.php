<?php defined('SYSPATH') or die('No direct script access.');

class Model_Calc_Cache extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('calc_cache')
      ->fields(array(
         'id' => new Field_Primary,
         'add_date' => new Field_String,
         'url' => new Field_String,
         'title' => new Field_String,
         'detalis' => new Field_Text,
         'technical' => new Field_Text,
         'features' => new Field_Text,
      ));
   }

}