<?php defined('SYSPATH') or die('No direct script access.');

class Model_PTM_Searches extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('ptm_searches')
      ->fields(array(
         'id' => new Field_Primary,
         'parent_id' => new Field_Integer,
         'year' => new Field_Integer,
         'condition' => new Field_String,
         'exception' => new Field_String,
      ));
   }

}