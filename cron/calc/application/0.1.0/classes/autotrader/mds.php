<?php defined('SYSPATH') or die('No direct script access.');

class Autotrader_MDS extends Autotrader {

   const CYCLE  = 45;

   /**
    * Проверка даты истечения расчетов.
    * 
    * @return object $this
    */
   public function check_dates()
   {
      $dates = Jelly::select('mds_dates')
      ->execute()
      ->current();

      if ($dates->start_date !== NULL)
      {
         // вычисляем дату конца исследования
         $deadline = (! $dates->end_date) // если исследование еще не остановлено вручную
         ? $dates->start_date + (self::CYCLE*3600*24) // cycle * 24 hours
         : $dates->end_date;

         // если время перешагнуло дату конца
         if (time() > $deadline)
         {
            throw new Kohana_Exception('MDS calculation period has expired in :date',
               array(':date' => date('d-m-Y', $deadline)));
         }
      }

      return $this;
   }

   /**
    * Обновляет таблицу.
    * 
    * @return object $this
    */
   public function update_table()
   {
      // массив данных
      $data = $this->_data();

      if (! empty($data))
      {
         // выбираем все для запрашиваемого $id
         $db_data = Jelly::select('mds_items')
         ->where('parent_id', '=', $this->_id)
         ->and_where('year', '=', $this->_year)
         ->execute();

         Debug::vars('table:', $db_data->count());

         if ($db_data->count())
         {
            // делаем машины старыми
            Jelly::update('mds_items')
            ->where('parent_id', '=', $this->_id)
            ->and_where('year', '=', $this->_year)
            ->set(array('is_new' => 0))
            ->execute();

            $db_items = array();

            // строим массив урлов из таблицы
            foreach ($db_data AS $db_item)
            {
               $db_items[] = $db_item->url;
            }

            // строим массив урлов из полученных данных
            $data_items = array_keys($data);

            // вычисляем проданные
            $sold_items = array_diff($db_items, $data_items);

            Debug::vars('sold:', $sold_items);

            // обновляем таблицу
            foreach ($sold_items AS $url)
            {
               Jelly::update('mds_items')
               ->where('url', '=', $url)
               ->set(array('sold' => 1))
               ->execute();
            }

            // вычисляем новые
            $new_items = array_diff($data_items, $db_items);

            Debug::vars('new:', $new_items);

            // пишем новые в таблицу
            foreach ($new_items AS $url)
            {
               Jelly::factory('mds_items')
               ->set(array
               (
                  'parent_id' => $this->_id,
                  'year' => $this->_year,
                  'name' => $data[$url]['name'],
                  'url' => $url
               ))
               ->save();
            }
         }
         else
         {
            // просто пишем в таблицу
            foreach ($data AS $url => $values)
            {
               Jelly::factory('mds_items')
               ->set(array
               (
                  'parent_id' => $this->_id,
                  'year' => $this->_year,
                  'name' => $values['name'],
                  'url' => $url
               ))
               ->save();
            } // foreach
         } // if...else
      }

      return $this;
   }

   public function execute($search_id = NULL)
   {
      $this->count_totals();

      $this->check_dates();
      
      $this->update_table();

      if ($search_id === NULL)
      {
         $searches = Jelly::select('mds_searches')
         ->where('parent_id', '=', $this->_id)
         ->and_where('year', '=', $this->_year)
         ->execute();

         if ($searches->count())
         {
            foreach ($searches->as_array() AS $search)
            {
               $this->count_values($search);
            }
         }
      }
      else
      {
         $search = Jelly::select('mds_searches', $search_id);

         if ($search->loaded())
         {
            $this->count_values($search->as_array());
         }
      }
   }

   /**
    * @param array $search
    * @return array
    */
   public function count_values(array $search)
   {
      // данные до поиска
      $collection = $this->collection();

      $vpd = 0;
      $mds = 0;

      if ($collection->count())
      {
         // получаем результат для заданного посика
         $total = Jelly::factory('mds_items')
         ->search($search, $collection);

         // считаем общее количество по поиску
         $total = $total->count();

         // клон нужен для подсчета проданных
         $clone = clone $collection;

         // новый билдер для проданных
         $sold_collection = $clone
         ->where('sold', '=', TRUE);

         // получаем результат по проданным для заданного посика
         $total_sold = Jelly::factory('mds_items')
         ->search($search, $sold_collection);

         // считаем количество по проданным
         $total_sold = $total_sold->count();

         if (($vpd = round(($total_sold/self::CYCLE), 2)) > 0)
         {
            $mds = round(($total/$vpd), 2);
         }
      }

      // Debug::vars('vpd:', $vpd, 'mds:', $mds);

      $values = array(
         'parent_id' => $this->_id,
         'search_id' => $search['id'],
         'year' => $this->_year,
         'vpd' => $vpd,
         'mds' => $mds,
      );

      Jelly::factory('mds')
      ->set($values)
      ->save();

      return $values;

      /*
      $item = $this->_item();

      Kohana::$log->add(Kohana_Log::INFO, 'MDS. :name, :year: vpd - :vpd, mds - :mds',
         array(
            ':name' => $item['name'],
            ':year' => $this->_year,
            ':vpd' => $vpd,
            ':mds' => $mds
         ));
      */
   }

   /**
    * Возвращает коллекцию объединенных данных
    * из таблицы машин и таблицы кэша.
    *
    * @param integer $id
    * @param integer $year
    * @return object Jelly_Builder
    */
   public function collection()
   {
      $collection = Jelly::select('mds_items')
      ->select('mds_items.*', array(
         DB::expr("CONCAT_WS(', ', title, detalis, technical)"),
         Model_MDS_Items::DETALIS
      ))
      ->join('calc_cache', 'inner')
      ->on('url', '=', 'calc_cache.url')
      ->where('parent_id', '=', $this->_id)
      ->and_where('year', '=', $this->_year);

      // Debug::vars($collection);

      return $collection;
   }

   /**
    * Находит онлайновые/оффлайновые машины
    * по критериям посика.
    * 
    * @param integer $search_id
    * @param boolean $online
    * @return array
    */
   public function online($search_id, $online = TRUE)
   {
      $search = Jelly::select('mds_searches', $search_id);

      $collection = $this->collection()
      ->where('sold', '=', ! (bool) $online);

      $data = Jelly::factory('mds_items')
      ->search($search->as_array(), $collection);

      return $data->as_array('url', 'name');
   }

}
