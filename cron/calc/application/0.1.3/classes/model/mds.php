<?php defined('SYSPATH') or die('No direct script access.');

class Model_MDS extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('mds')
      ->fields(array(
         'id' => new Field_Primary,
         'parent_id' => new Field_Integer,
         'search_id' => new Field_Integer,
         'year' => new Field_Integer,
         'vpd' => new Field_Float,
         'mds' => new Field_Float,
         'date_added' => new Field_Timestamp(array(
            'auto_now_create' => TRUE,
            'format' => 'Y-m-d'
         )),
      ));
   }

}