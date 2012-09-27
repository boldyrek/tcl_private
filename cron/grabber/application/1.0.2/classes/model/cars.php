<?php defined('SYSPATH') or die('No direct script access.');

class Model_Cars extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('cars')
      ->fields(array(
         'id' => new Field_Primary,
         'source_id' => new Field_Integer,
         'target_id' => new Field_Integer,
         'search_id' => new Field_Integer,
         'date_added' => new Field_Timestamp(array(
            'auto_now_create' => TRUE,
            'format' => 'Y-m-d H:i:s'
         )),
         'date_auction' => new Field_String,
         'name' => new Field_String,
         'vincode' => new Field_String,
         'vincode_date_added' => new Field_Timestamp(array(
            'format' => 'Y-m-d H:i:s'
         )),
         'mileage' => new Field_Integer(array('default' => 0)),
         'price' => new Field_String,
         'options' => new Field_String,
         'interior_code' => new Field_String,
         'exterior_code' => new Field_String,
         'interior' => new Field_String,
         'exterior' => new Field_String,
         'date_made' => new Field_String,
         'url' => new Field_String,
         'picture' => new Field_Integer(array('default' => 0)),
      ));
   }

   public function color_filter($search_id, array $colors)
   {
      $query = Jelly::delete('cars')
      ->where_open()
      ->where('exterior_code', 'IS', NULL)
      ->or_where('interior_code', 'IS', NULL);

      if (isset($colors['exterior']))
      {
         $query->or_where('exterior_code', 'NOT IN', array_keys($colors['exterior']));
      }

      if (isset($colors['interior']))
      {
         $query->or_where('interior_code', 'NOT IN', array_keys($colors['interior']));
      }

      $query
      ->where_close()
      ->and_where('search_id', '=', $search_id);

      return $query->execute();
   }

}