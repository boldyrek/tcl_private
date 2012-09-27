<?php defined('SYSPATH') or die('No direct script access.');

class Controller_MDS_Export extends Controller_JSON {
   
   public function action_grid()
   {
      foreach (Kohana::config('config.items') AS $id => $values)
      {
         foreach ($values['year'] AS $year)
         {
            $select = Jelly::select('mds')
               ->where('parent_id', '=', $id)
               ->and_where('year', '=', $year);

            $total = Jelly::select('mds_items')
               ->select(array(DB::expr('COUNT(*)'), 'total'))
               ->where('parent_id', '=', $id)
               ->and_where('year', '=', $year)
               ->limit(1)
               ->execute()
               ->get('total');

            $max_date_select = clone $select;

            $max_date = $max_date_select
               ->select(array(DB::expr('MAX(`date_added`)'), 'max_date'))
               ->limit(1)
               ->execute()
               ->get('max_date');

            $data_select = clone $select;

            $data = $data_select
               ->select('mds.*',
                  array('mds_searches.condition', 'condition'),
                  array('mds_searches.exception', 'exception'),
                  array('mds_searches.main', 'main')
               )
               ->join('mds_searches')
               ->on('mds.search_id', '=', 'mds_searches.id')
               ->and_where('date_added', '=', $max_date)
               ->and_where('mds', '!=', 0)
               ->order_by('mds', 'ASC')
               ->limit(1)
               ->execute();

            $values = array('total' => (int) $total);

            if ($data->loaded())
            {
               $_data = $data->as_array();

               $values['data'] = $_data;
               $values['data']['condition'] = $data->get('condition');
               $values['data']['exception'] = (string) $data->get('exception');
               $values['data']['main'] = (int) $data->get('main');
               $values['data']['date_added_string'] = date(parent::DATE_FORMAT, $_data['date_added']);

            }

            $this->_body[$id][$year] = $values;
         }
      }
   }

   public function action_view()
   {
      $searches = Jelly::select('mds_searches')
         ->where('parent_id', '=', $this->_id)
         ->and_where('year', '=', $this->_year)
         ->order_by('id', 'DESC')
         ->execute();

      if ($searches->count())
      {
         $mds = new Autotrader_MDS($this->_id, $this->_year);
         
         foreach ($searches AS $search)
         {
            // на вывод отправляем последний не нулевой результат
            $data = Jelly::select('mds')
               ->where('parent_id', '=', $this->_id)
               ->and_where('year', '=', $this->_year)
               ->and_where('search_id', '=', $search->id)
               // ->and_where('mds', '!=', 0)
               // ->order_by('mds', 'ASC')
               ->order_by('id', 'DESC')
               ->limit(1)
               ->execute();

            if ($data->loaded())
            {
               $_data = $data->as_array();;

               $values = $_data;
               $values['condition'] = $search->condition;
               $values['exception'] = (string) $search->exception;
               $values['main'] = (int) $search->main;
               $values['online'] = count($mds->online($search->id, TRUE));
               $values['offline'] = count($mds->online($search->id, FALSE));
               $values['date_added_string'] = date(parent::DATE_FORMAT, $_data['date_added']);

               $this->_body[$search->id] = $values;
            }
         }
      }
   }

   public function action_stat()
   {
      $mds = new Autotrader_MDS($this->_id, $this->_year);

      $online  = $mds->online($this->_search, TRUE);
      $offline = $mds->online($this->_search, FALSE);

      $this->_body = array(
         'online' => array( // в наличии
            'items' => $online,
            'count' => count($online)
         ),
         'offline' => array( // проданные
            'items' => $offline,
            'count' => count($offline)
         )
      );
   }

   public function action_add()
   {
      $this->_id = $this->request->query('parent_id');
      
      $check = Jelly::select('mds_searches')
         ->where('condition', '=', $this->request->query('condition'))
         ->and_where('exception', '=', $this->request->query('exception'))
         ->and_where('parent_id', '=', $this->_id)
         ->and_where('year', '=', $this->_year)
         ->limit(1);

      if (! $check->count())
      {
         $model = Jelly::factory('mds_searches');
         $model->set($this->request->query());
         $model->save();

         $search = Jelly::select('mds_searches', $model->id);

         $mds = new Autotrader_MDS($this->_id, $this->_year);

         $this->_body = $mds->count_values($search->as_array());

         $this->_body += array(
            'online' =>count($mds->online($model->id, TRUE)),
            'offline' => count($mds->online($model->id, FALSE)),
            'status' => 'OK'
         );
      }
   }

}
