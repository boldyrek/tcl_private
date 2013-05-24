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
         'state' =>new Field_String,
         'url' => new Field_String,
         'picture' => new Field_Integer(array('default' => 0)),
      ));
   }

   protected function _is_intcolors_empty ($int_colors) {
       if (is_array($int_colors)) {
           $cnt = 0;
           foreach ($int_colors as $key => $value) {
               if (!empty($value)) $cnt++;
           }
           
           return ($cnt <= 0);
       } else {
           return empty($int_colors);
       }
   }
   
   public function color_filter($search_id, array $colors)
   {
      $found = 0;

      $matches = array();
      
      foreach ($colors AS $ext => $ints)
      {
          
         if (!$this->_is_intcolors_empty ($ints)) {
             $match = Jelly::select('cars')
             ->where('exterior_code', '=', trim($ext))
             ->where('interior_code', 'IN', $ints)
             ->where('search_id', '=', $search_id)
             ->execute()
             ->as_array();
         } else {
             $match = Jelly::select('cars')
             ->where('exterior_code', '=', trim($ext))
             ->where('search_id', '=', $search_id)
             ->execute()
             ->as_array();             
         }

         foreach ($match AS $item)
         {
            $matches[] = $item;
         }
      }

      $all = Jelly::delete('cars')
      ->where('search_id', '=', $search_id)
      ->execute();

      foreach ($matches AS $item)
      {
         Jelly::factory('cars')
         ->set($item)
         ->save();
      }

      $found = count($matches);

      unset($matches);

      return ($all-$found);
   }

}
