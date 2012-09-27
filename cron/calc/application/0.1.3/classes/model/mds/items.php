<?php defined('SYSPATH') or die('No direct script access.');

class Model_MDS_Items extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('mds_items')
      ->fields(array(
         'id' => new Field_Primary,
         'parent_id' => new Field_Integer,
         'year' => new Field_Integer,
         'name' => new Field_String,
         'url' => new Field_String,
         'is_new' => new Field_Integer(array('default' => 1)),
         'sold' => new Field_Integer(array('default' => 0)),
         'date_added' => new Field_Timestamp(array(
            'auto_now_create' => TRUE,
            'format' => 'Y-m-d'
         )),
         'date_sold' => new Field_Timestamp(array('format' => 'Y-m-d')),
      ));
   }

}