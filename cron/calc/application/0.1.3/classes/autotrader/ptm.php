<?php defined('SYSPATH') or die('No direct script access.');

class Autotrader_PTM extends Autotrader {

   public function execute()
   {
      $this->count_totals();
      
      $data = $this->_data();

      $i = 0;

      // если машины есть
      if (! empty($data))
      {
         // стираем старые
         Jelly::delete('ptm')
         ->where('parent_id', '=', $this->_id)
         ->and_where('year', '=', $this->_year)
         ->execute();

         // записываем новые
         foreach ($data AS $url => $values)
         {
            // и пробег и цена обязательны
            if ($values['price'] != '')
            {
               $i++;

               Jelly::factory('ptm')
               ->set(array
               (
                  'parent_id' => $this->_id,
                  'year' => $this->_year,
                  'name' => $values['name'],
                  'url' => $url,
                  'mileage' => $values['mileage'],
                  'price' => $values['price']
               ))
               ->save();
            }
         }
      }

      $item = $this->_item();

      Kohana::$log->add(Kohana_Log::INFO, 'PTM. :name, :year: found - :found, passed - :passed',
         array(
            ':name' => $item['name'],
            ':year' => $this->_year,
            ':found' => count($data),
            ':passed' => $i
         ));
   }

   public function search($search)
   {
      $output = array();

      if (is_array($search))
      {
         $data = Jelly::factory('ptm')
         ->search($search, $this->collection());
      }
      else
      {
         $data = $this->collection()->execute();
      }

      if ($data !== FALSE AND $data->count())
      {
         $output = $data->as_array();
      }

      return $output;
   }

   public function collection()
   {
      $collection = Jelly::select('ptm')
      ->select('ptm.*', array(
         DB::expr("CONCAT_WS(', ', title, detalis, technical)"),
         Jelly_Model::DETALIS
      ))
      ->join('calc_cache', 'inner')
      ->on('url', '=', 'calc_cache.url')
      ->where('parent_id', '=', $this->_id)
      ->and_where('year', '=', $this->_year);
      //->group_by('price');

      return $collection;
   }

}
