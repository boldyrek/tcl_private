<?php defined('SYSPATH') or die('No direct script access.');

class Controller_PTM extends Controller {

   public function action_index()
   {
   }

   public function action_import()
   {
      $id = $this->request->param('id', 'all');
      $year = $this->request->param('year');

      // если не указан id (импортировать всех)
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
      Autotrader::factory($id, $year)
      ->price_to_market();
   }

}

