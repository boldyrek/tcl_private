<?php defined('SYSPATH') or die('No direct script access.');

class Controller_PTM_Export extends Controller_JSON {

   const POSITION = 4;

   public function action_grid()
   {
      foreach (Kohana::config('config.items') AS $id => $values)
      {
         foreach ($values['year'] AS $year)
         {
            $ptm = new Autotrader_PTM($id, $year);

            $query = $ptm->collection();
            
            $result = $query->execute();

            if (($total = $result->count()))
            {
               $clone = clone $query;
               
               $date = $clone
               ->order_by('date_added', 'DESC')
               ->limit(1)
               ->execute()
               ->current();

               $this->_body[$id][$year] = array(
                  'total' => $total,
                  'last_update' => ($total ? date(parent::DATE_FORMAT, $date->get('date_added')) : '-')
               );
            }
         }
      }
   }

   public function action_view()
   {
      $searches = Jelly::select('ptm_searches')
      ->where('parent_id', '=', $this->_id)
      ->and_where('year', '=', $this->_year)
      ->order_by('id', 'DESC')
      ->execute();

      if ($searches->count())
      {
         foreach ($searches->as_array() AS $search)
         {
            $data = $this->_data($search);

            if (! empty($data))
            {
               $this->_body[$search['id']] = $data;
            }
         }
      }
   }

   public function action_chart()
   {
      if ($this->_search !== NULL)
      {
         $search = Jelly::select('ptm_searches')
         ->where('id', '=', $this->_search)
         ->limit(1)
         ->execute();

         $data = $this->_data($search->as_array(), TRUE);
      }
      else
      {
         $data = $this->_data(NULL, TRUE);
      }

      $this->_body = $data;
   }

   public function action_add()
   {
      $this->_id = $this->request->query('parent_id');

      $check = Jelly::select('ptm_searches')
      ->where('condition', '=', $this->request->query('condition'))
      ->and_where('exception', '=', $this->request->query('exception'))
      ->and_where('parent_id', '=', $this->_id)
      ->and_where('year', '=', $this->_year)
      ->limit(1);

      if (! $check->count())
      {
         $model = Jelly::factory('ptm_searches');
         $model->set($this->request->query());
         $model->save();

         $search = Jelly::select('ptm_searches', $model->id);

         $this->_body = $this->_data($search->as_array());

         $this->_body += array(
            'search_id' => $model->id,
            'parent_id' => $this->_id,
            'year' => $this->_year,
            'status' => 'OK'
         );
      }
   }

   /**
    * @param mixed $search
    * @param boolen $include_data
    * @return array
    */
   protected function _data($search = NULL, $include_data = FALSE)
   {
      $ptm = new Autotrader_PTM($this->_id, $this->_year);

      $data = $ptm->search($search);

      $total = count($data);

      $output = array('total' => $total);

      if ($search !== NULL)
      {
         $output['condition'] = $search['condition'];
         $output['exception'] = (string) $search['exception'];
      }

      /**
       * хак: писать в вывод, если кол-во записей больше 2
       * причина описана ниже
       */
      if ($total > 2)
      {
         $buffer = array();

         foreach ($data AS $item)
         {
            $buffer[$item['price']] = $item['mileage'];
         }

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

         $output += array(
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

         if ($include_data)
         {
            $output['data'] = $data;
            /*
            foreach ($data AS $item)
            {
               $output['data'][$item['url']] = $item['name'];
            }
            */
         }
      }

      return $output;
   }

}
