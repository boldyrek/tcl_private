<?php defined('SYSPATH') or die('No direct script access.');

class Source {

   protected $_config;
   protected $_remote_options;
   protected $_id;

   public function __construct($id)
   {
      // check source ID
      if (! Kohana::config('root.sources.'.$id))
         throw new Kohana_Exception('Source with id :id not found', array(':id' => $id));

      // get source config
      $config = Kohana::config('sources.'.$id);

      // set source config
      $this->_config = $config;
      
      // set source remote options
      $this->_remote_options = $config['remote_options'] + Kohana::config('root.remote_options');

      // set source id
      $this->_id = $id;
   }

   public static function instance($id)
   {
      static $instance;

      empty($instance) AND $instance = new Source($id);

      return $instance;
   }

   public function run()
   {
      // define class
      $class = 'Source_'.Kohana::config('root.sources.'.$this->_id);

      // get instance
      $source = new $class($this->_id);

      Kohana::$log->add(Log::INFO, str_repeat('-', 10).$class.' ('.PHP_SAPI.')');

      // execute
      $source->execute();
   }

   public function reset()
   {
      Jelly::delete('cars')
      ->where('source_id', '=', $this->_id)
      ->execute();

      Jelly::delete('statuses')
      ->where('source_id', '=', $this->_id)
      ->execute();

      return $this;
   }

   protected function _parse_options($content)
   {
      $content = preg_replace('#<(style|script).*>(.*)</(style|script)>#isU', '', $content);

      $content = strip_tags($content);
      
      $pattern = '/\b('.implode('|', Kohana::config('root.options')).')\b/i';

      if (preg_match_all($pattern, $content, $matches))
      {
         return implode(', ', array_unique(Arr::get($matches, 1)));
      }

      return '';
   }

   protected function _get_options(Source $source, $url)
   {
      $ident = $source->get_ident($url);

      $cache = Jelly::select('cache')
      ->where('source_id', '=', $this->_id)
      ->and_where('ident', '=', $ident)
      ->limit(1)
      ->execute();

      $options = '';

      if (! $cache->loaded())
      {
         $content = Remote::factory($url, $this->_remote_options)->execute();

         $options = (method_exists($source, 'parse_options'))
            ? $source->parse_options($content)
            : $this->_parse_options($content);

         Jelly::factory('cache')
         ->set(array
         (
            'source_id' => $this->_id,
            'ident' => $ident,
            'options' => $options,
         ))
         ->save();
      }
      else
      {
         $options = Arr::get($cache->as_array(), 'options');
      }

      return $options;
   }

   protected function _log($target_id, array $debug)
   {
      Kohana::$log->add(Log::INFO, Kohana::config('root.targets.'.$target_id.'.name').' - '.implode(', ', $debug));
   }

   protected function _cache($vincode, $mark, $target_id, $cache = TRUE)
   {
      $i = 0;

      if (! empty($vincode))
      {
         $values = array();

         // запрашиваем код из таблицы (кэша)
         $vin = Jelly::select('vins')
         ->where('vincode', '=', $vincode)
         ->limit(1)
         ->execute();

         // кода нету в таблице
         if (! $vin->loaded())
         {
            $_data = array
            (
               'vincode' => $vincode,
               'target_id' => $target_id, // последний добавленный винкод для типа поиска
               'date_added' => date('Y-m-d H:i:s')
            );

            // если кэширование разрешено
            if ($cache == TRUE)
            {
               // определяем драйвер декодера винкода
               switch (strtolower($mark))
               {
                  case 'lexus':
                     $driver = 'lexuspartsnow';
                     break;

                  case 'toyota':
                     $driver = 'toyotapartszone'; // epcdata
                     break;

                  default:
                     $driver = 'japancats';
                     break;
               }

               // запрашиваем данные
               $data = Vincode::factory($driver)->get($vincode, $mark);

               if (FALSE !== $data)
               {
                  // обединяем полученные данные с текущими
                  $_data = array_merge($data, $_data);

                  // данные для записи в таблицу машин
                  $values = $_data;
               }
            }
            else
            {
               $values = $_data;
            }

            // записываем в таблицу кодов
            Jelly::factory('vins')
            ->set($_data)
            ->save();

            $i++;
         }
         else
         {
            $values = $vin->as_array();
         }

         if (! empty($values))
         {
            $select = Jelly::select('cars')
            ->where('vincode', '=', $vincode)
            ->limit(1)
            ->execute();

            $binds = array
            (
               'vincode_date_added' => Arr::get($values, 'date_added'),
               'interior_code' => Arr::get($values, 'interior_code'),
               'exterior_code'=> Arr::get($values, 'exterior_code'),
               'date_made' => Arr::get($values, 'date_made')
            );

            // обновляем таблицу машин
            Jelly::factory('cars')
            ->set($binds)
            ->save($select->id);
         }
      }

      // кол-во добавленных кодов
      return $i;
   }

   public function  __destruct()
   {
   }
   
}