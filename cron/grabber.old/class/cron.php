<?php defined('SYSPATH') or die('No direct script access.');

class Cron extends Cron_Abstarct {
   
   public function run($source_id = NULL)
   {
      $sources = $this->config->core->sources->toArray();
      $alones = $this->config->core->alones->toArray();

      // $this->logger->log('Запуск задачи', Zend_Log::INFO);

      if ($source_id == NULL)
      {
         // чистим таблицу
         $this->car->truncate();

         // чистим статусы
         $this->status->truncate();

         // запускаем крон каждого класса
         foreach ($sources AS $key => $class)
         {
            if (! in_array($key, $alones))
            {
               $this->_run_source($class, $key);
            }
         }
      }
      else
      {
         if (($class = Arr::get($sources, $source_id)) !== NULL)
         {
            $this->_run_source($class, $source_id);
         }
         else
            throw new Exception('Undefined source ID '.$source_id);
      }
      
      // $this->logger->log('Завершение задачи', Zend_Log::INFO);
   }

   protected function _run_source($class, $key)
   {
      $this->logger->log(str_repeat('-', 20).' '.$class, Zend_Log::INFO);
      Cron::factory($class)->execute($key);
   }
   
   protected function _message($source, $parent, array $debug)
   {
      $types = $this->config->core->search->types->toArray();
      $sources = $this->config->core->sources->toArray();
      
      $html  = Arr::get($types[$parent], 'name').' - '.Arr::get($sources, $source);
      
      $html .= '<ul>';
      
      foreach ($debug AS $message)
      {
         $html .= '<li>'.$message.'</li>';
      }
      
      $html .= '</ul>';
      
      return $html;
   }
   
   /**
    * Проверяет, есть ли в таблице VIN-кодов, запрощенный код.
    * Если такового нет, то запрашивает информацию у japancats.ru.
    * (Если есть, то считается, что информация ранее запрашивалась)
    * Записывает полученные данные в таблицу VIN-кодов и обновляет таблицу авто.
    *
    * @access protected
    * @param (string) $vincode
    * @param (string) $mark
    * @param (int) $search_type
    * @param (boolean) $cache
    * @return (int)
    */
   protected function _cache($vincode, $mark, $search_type, $cache = TRUE)
   {
      $i = 0;
      
      if (! empty($vincode))
      {
         $binds = array();
         
         // запрашиваем код из таблицы (кэша)
         $vin = $this->vin->find($vincode);

         // кода нету в таблице
         if (FALSE == $vin)
         {
            $_data = array
            (
               'vin' => $vincode,
               'search_type' => $search_type,
               'date_added' => date('Y-m-d H:i:s')
            );
            
            // если кэширование разрешено
            if ($cache == TRUE)
            {
               $driver = (strtolower($mark) == 'lexus') ? 'lexuspartsnow' : 'japancats';

               // запрашиваем данные
               $data = Vincode::factory($driver)->get($vincode, $mark);
               
               if (FALSE !== $data)
               {
                  // обединяем полученные данные с текущими
                  $_data = array_merge($data, $_data);
                  
                  // данные для записи в таблицу машин
                  $binds = $_data;
               }
            }
            
            // записываем в таблицу кодов
            $this->vin->insert($_data);
            
            $i++;
         }
         else 
         {
            $binds = $vin;
         }

         if (! empty($binds))
         {
            // забираем только нужные поля
            $binds = array
            (
               'date_made' => $binds['date_made'],
               'vin_date_added' => $binds['date_added'],
               'color_inside' => $binds['color_inside'],
               'color_outside' => $binds['color_outside'],
            );
            
            // обновляем таблицу машин
            $where = Database::quoteValues(array('vin' => $vincode), TRUE);
            $this->car->update($binds, $where);
         }
      }
      
      // кол-во добавленных кодов
      return $i;
   }
}