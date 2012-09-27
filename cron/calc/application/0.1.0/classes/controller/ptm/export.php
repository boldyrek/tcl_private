<?php defined('SYSPATH') or die('No direct script access.');

class Controller_PTM_Export extends Controller_JSON {

   const POSITION = 4;
   const CYCLE = 45;

   public function action_grid()
   {
      foreach (Kohana::config('config.items') AS $id => $values)
      {
         foreach ($values['year'] AS $year)
         {
            $this->_body[$id][$year] = $this->_data($id, $year, $this->request->query('tag_id'));
         }
      }
   }

   public function action_chart()
   {
      $id = $this->request->query('id');
      $year = $this->request->query('year');
      $tags = $this->request->query('tag');
      
      $this->_body = $this->_data($id, $year, $tags, TRUE);
   }

   /**
    * @param integer $id
    * @param integer $year
    * @param integer $tag_id
    * @param boolen $include_data
    * @return array
    */
   protected function _data($id, $year, $tags = NULL, $include_data = FALSE)
   {
      $output = array();

      // выбираем по id модели и году
      $select = Jelly::select('ptm')
      ->where('parent_id', '=', $id)
      ->and_where('year', '=', $year)
      ->group_by('price')
      ->order_by('price', 'ASC');

      // запрос по id тэга
      if ($tags !== NULL)
      {
         $select
         ->join('calc_cache', 'inner')
         ->on('url', '=', 'calc_cache.url')
         ->and_where('calc_cache.tag_id', 'IN', (! is_array($tags) ? array($tags) : $tags));
      }

      $query = $select->execute();

      // Debug::vars($query); exit;

      /**
       * хак: писать в вывод, если кол-во записей больше 2
       * причина описана ниже
       */
      if (($total=$query->count()) AND $total > 2)
      {
         $data = $query->as_array();

         $buffer = $query->as_array('price', 'mileage');

         $size = count($buffer);

         // ключи массива - цены
         $prices = array_keys($buffer);
         // значения - пробег
         $mileages = array_values($buffer);

         /**
          * обнаружен баг в класса SLR
          * ErrorException [ Warning ]: Division by zero Line:207
          * в случае, если массив содержит только 2 значения
          * пока небольшой хак: по дефулту все значения нулевые
          */
         $slr_values = array(
            'YInt' => 0,
            'Slope'=> 0,
            'SlopeTVal' => 0,
            'SlopeProb' => 0
         );

         // затраим вычисление без эксепшенов
         try
         {
            $slr = new SLR($mileages, $prices, 95);

            $slr_values = array(
               'YInt' => sprintf(SLR::FORMAT, $slr->YInt),
               'Slope'=> sprintf(SLR::FORMAT, $slr->Slope),
               'SlopeTVal' => sprintf(SLR::FORMAT, $slr->SlopeTVal),
               'SlopeProb' => sprintf('%01.4f', $slr->SlopeProb)
            );
         }
         catch (ErrorException $e)
         {
            // TODO
         }
         catch (Kohana_Exception $e)
         {
            // TODO
         }

         // сортируем массивы для получения максимальных значений
         sort($prices, SORT_NUMERIC);
         sort($mileages, SORT_NUMERIC);

         /**
          * вычисляем позицию
          * если кол-во элементов массива меньше 4
          * то 4-ая цена и есть последняя цена массива
          */
         $position = ($size < self::POSITION) ? ($size-1) : (self::POSITION-1);

         $output = array(
            'max' => array(
               'price' => $prices[$size-1],
               'mileage' => (int) $mileages[$size-1]
            ),
            'ptm' => array(
               'price' => $prices[$position],
               'mileage' => (int) $buffer[$prices[$position]]
            ),
            'SLR' => $slr_values
         );

         // данные нужны только для графика
         if ($include_data)
         {
            $output['data'] = $data;

            // тэги
            foreach ($data AS $key => $array)
            {
               $tags = Jelly::select('calc_cache')
               ->select('ptm_tags.*')
               ->join('ptm_tags', 'inner')
               ->on('tag_id', '=', 'ptm_tags.id')
               ->where('url', '=', $array['url'])
               ->execute()
               ->as_array('id', 'value');

               $output['data'][$key]['tags'] = implode(', ', $tags);

            }
         }

         // дата записи последних
         $output['last_update'] = Jelly::select('ptm')
         ->where('parent_id', '=', $id)
         ->and_where('year', '=', $year)
         ->order_by('date_added', 'DESC')
         ->limit(1)
         ->execute()
         ->get('date_added');

         unset($data, $buffer);
      }

      return $output;
   }

}
