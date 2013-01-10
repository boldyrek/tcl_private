<?php defined('SYSPATH') or die('No direct script access.');

class Model_Statuses extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('statuses')
      ->fields(array(
         'id' => new Field_Primary,
         'target_id' => new Field_Integer,
         'source_id' => new Field_Integer,
         'date_last_updated' => new Field_Timestamp(array(
            'auto_now_create' => TRUE,
            'format' => 'Y-m-d H:i:s'
         )),
         'items_added' => new Field_Integer(array('default' => 0))
      ));
   }

}