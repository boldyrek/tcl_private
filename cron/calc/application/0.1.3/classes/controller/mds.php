<?php defined('SYSPATH') or die('No direct script access.');

class Controller_MDS extends Controller {

   public function action_update()
   {
      foreach (Kohana::config('config.items') AS $key => $values)
      {
         foreach ($values['year'] AS $y)
         {
            $search = Jelly::select('mds_searches')
            ->where('parent_id', '=', $key)
            ->where('year', '=', $y)
            ->where('main', '=', 1)
            ->limit(1)
            ->execute();

            if (! $search->loaded())
            {
               Jelly::factory('mds_searches')
               ->set(array('parent_id' => $key, 'year' => $y, 'main' => 1))
               ->save();
            }
         }
      }
   }

   public function action_import()
   {
      $id = $this->request->param('id', 'all');
      $year = $this->request->param('year');

      if ($id == 'all')
      {
         // для всех моделей
         foreach (Kohana::config('config.items') AS $key => $values)
         {
            // для всех годов
            foreach ($values['year'] AS $y)
            {
               $this->_import($key, $y);
            }
         }
      }
      else
      {
         if ($year)
         {
            $this->_import($id, (int) $year);
         }
         else
         {
            foreach (Kohana::config('config.items.'.$id.'.year') AS $y)
            {
               $this->_import($id, $y);
            }
         }
      }
   }

   protected function _import($id, $year)
   {
      $mds = new Autotrader_MDS($id, $year);
      
      $mds->execute();
   }

}

