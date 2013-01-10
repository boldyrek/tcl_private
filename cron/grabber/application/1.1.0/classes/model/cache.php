<?php defined('SYSPATH') or die('No direct script access.');

class Model_Cache extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('cache')
      ->fields(array(
         'id' => new Field_Primary,
         'source_id' => new Field_Integer,
         'ident' => new Field_String,
         'vincode' => new Field_String,
         'options' => new Field_String
      ));
   }

}