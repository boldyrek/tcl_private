<?php defined('SYSPATH') or die('No direct script access.');

class Cron_Source_Exporttrader extends Cron implements Cron_Interface {
   
   protected $_config; // class config
   protected $_remote_options;
   
   public function __construct()
   {
      parent::__construct();
      
      $this->_config = $this->config->source->exporttrader;
      // merge base & class remote options
      $this->_remote_options = $this->config->core->remote->toArray() + $this->_config->remote->toArray();
   }
   
   public function execute($source_id)
   {
      if (! $this->login())
      {
         $message = get_class($this).'Запрос не авторизован';
         $this->logger->log($message, Zend_Log::INFO);
         echo $message;
         return;
      }
      
      $output = array();
      
      // для каждого ресурса может быть определенные или все типы поиска
      foreach ($this->_config->search->types->toArray() AS $id)
      {
         // запрашиваем базовые типы
         $types = $this->config->search->types->toArray();
         // определем тип
         $type = Arr::get($types, $id);
         
         // блокируем статус
         $locked = $this->status->insert(array('search_type' => $type['parent'], 'source' => $source_id, 'locked' => 1));
         
         // строим ссылку для GET-запроса на ET
         $url = $this->_config->search->url
         .'?'.http_build_query($this->_config->search->fields->toArray()) // custom fields (class)
         .'&'.http_build_query(Arr::get($type, 'fields')); // type fields (base)
         
         // шлем запрос
         $response = Remote::factory($url, $this->_remote_options)->execute();
         
         $total = 0;
         $pages = 1;
         
         if ($response === '')
            throw new Exception('Запрос вернул пустой результат');
         
         // проверяем, есть ли совпадения с критериями запроса
         if (! (bool) preg_match('/Please change you search criteria/i', $response))
         {
            // определяем количество записей
            if ((bool) preg_match('/Total units found(?:.*)<span(?:[^>]+)>(\d+)<\/span>/i', $response, $matches))
               $total = (int) Arr::get($matches, 1);
         }
         
         $filtered = 0;
         $added = 0;
         $passed = 0;
         $new_vins = 0;
         
         if ($total > 0)
         {            
            // определяем количество страниц
            if ($total > $this->_config->search->per_page)
            {
               $pages = (int) ceil($total/$this->_config->search->per_page);
            }
            
            // шлем "постраничный" запрос 
            for ($i = 1; $i <= $pages; $i++)
            {
               $response = Remote::factory($url.'&nmbpg='.$i, $this->_remote_options)->execute();
               
               // получаем распарсенные записи
               $items = $this->parse($response, Arr::get($type, 'filters'));
               
               // прошли через основные фильтры
               $passed += sizeof($items);

               foreach ($items AS $item)
               {
                  // добавляем в массив тип поиска
                  $item['source'] = $source_id;
                  $item['search_type'] = $type['parent'];
                  $item['search_id'] = $id;
                  
                  // пишем массив в таблицу
                  $this->car->insert($item);
                  
                  // кэшируем VIN-код
                  $new_vins += $this->_cache(Arr::get($item, 'vin'), Arr::get($type, 'mark'), $type['parent'], Arr::get($type, 'cache'));
               }
            }
            
            // проводим через фильтр цветов, если цвета заданы
            if (($color = Arr::get($type, 'colors')) !== NULL)
            {
               $colors = $this->config->colors->toArray();
               // $filtered = $this->car->color_filter($type['parent'], Arr::get($colors, $color));
               $filtered = $this->car->color_filter($id, Arr::get($colors, $color));
            }
            
            // считаем, сколько в итоге добавлено в таблицу
            $added = $passed - $filtered;
         }
         
         $output[$id]['totals'] = array
         (
            'найдено: '.$total,
            'прошли первичные фильтры: '.$passed,
            'не прошли фильтр по цветам: '.$filtered,
            'добавлено: '.$added,
            'новых вин-кодов: '.$new_vins
         );
         
         // записываем в лог
         $this->logger->log('Тип '.$id.' - '.implode(', ', $output[$id]['totals']), Zend_Log::INFO);
         
         // обновляем статус (разблокировка)
         $where = Database::quoteValues(array('id' => $locked['insert_id']), TRUE);
         $this->status->update(array
         (
            'date_last_updated' => date('Y-m-d H:i:s',(int) microtime(TRUE)),
            'items_added' => $added,
            'locked' => 0,
         ), $where);
         
         echo $this->_message($source_id, $type['parent'], $output[$id]['totals']);
      }
      
      // Core::debug($output);
   }
   
   public function parse($content, array $filters = NULL)
   {
      // удаляем все переносы
      $content = preg_replace('/(\n|\r)+/', '', $content);
               
      $output = array();
      
      $pattern  = '/';
      $pattern .= '<td class=bgwt colspan=2 style=\'text-align: left;\'><a href=\'';
      $pattern .= '(.*)'; // url
      $pattern .= '\'(?:[^>]+)>';
      $pattern .= '(.*)'; // marl, model, name
      $pattern .= '<\/a>.*<\/td>';
      $pattern .= '<td class=bgwt style=\'text-align: right;\'>';
      $pattern .= '(\d+)';
      $pattern .= '<\/td><td class=bglg>';
      $pattern .= '(.+)'; // color
      $pattern .= '<\/td>';
      $pattern .= '.*<b>(?:Listing Ends:\s|)';
      $pattern .= '(\d{2}\/\d{2}\/\d{4})'; // auction date
      $pattern .= '<\/b>.*<td class=bgwt style=\'background: url\("im\/auctionslogo\/';
      $pattern .= '([a-z]+)'; // auction
      $pattern .= '.gif"\) center top no-repeat;\'><img[^>]+><\/td>';
      $pattern .= '.*<td style=\'text-align: left; padding-left: 3px;\'>';
      $pattern .= '(.*)'; // possible price
      $pattern .= '<span>VIN:&nbsp;';
      $pattern .= '([A-Z0-9]{0,17})'; // VIN
      $pattern .= '<\/span>.*<\/td>';
      $pattern .= '/isU';
      
      try
      {
         preg_match_all($pattern, $content, $scores, PREG_SET_ORDER);
         
         foreach ($scores AS $score)
         {
            $millage = (int) Arr::get($score, 3);
            $color = Arr::get($score, 4);
            $auction = Arr::get($score, 6);
            $vincode = Arr::get($score, 8);
            $price = 0;

            if (preg_match('#<div align=right><a [^>]+>Buy&nbsp;now&nbsp;\$(.+)</a>.*</div>#isU', Arr::get($score, 7), $_price))
            {
               $price = Arr::get($_price, 1);
            }

            // количество фильтов в каждом типе должно быть одинаковым!
            $matches = array
            (
               'vincode' => $vincode,
               'millage' => $millage,
               'color'   => $color,
               'series' => '',
               'auction' => $auction,
            );
            
            if (Filter::factory($filters, $matches)->validate())
            {
               list($color_outside, $color_inside) = explode('/', $color);
               
               $output[] = array
               (
                  'auction_type' => $auction,
                  'date_added' => date('Y-m-d H:i:s'),
                  'date_auction' => date('Y-m-d', strtotime(Arr::get($score, 5))),
                  'name' => Arr::get($score, 2),
                  'vin' => $vincode,
                  'millage' => $millage,
                  'price' => $price,
                  'color_inside_origin' => $color_inside,
                  'color_outside_origin' => $color_outside,
                  'url' => Arr::get($score, 1),
               );
            }
         }
      }
      catch (Exception $e)
      {
         throw $e;
      }
      
      return $output;
   }
   
   public function login()
   {      
      // проверка авторизации
      if ($this->is_logged())
         return TRUE;
         
      // получаем каптчу
      $captcha = $this->_captcha();
      
      $login = $this->_config->login->toArray();
      
      // добавляем в POST-запрос поле с каптчей
      $login['post_fields']['sendcaptcha'] = $captcha;
      
      // дополнительные опции для запроса
      $options = array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => $login['post_fields'],
      );
      
      // объединяем опции
      $options = $this->_remote_options + $options;
      
      // шлем первый запрос (на ET происходит внутренний редирект)
      Remote::factory($login['url'], $options)->execute();
      
      $this->logger->log('Авторизация: запрос №1 прошел', Zend_Log::INFO);
      
      // шлем второй запрос (после внутреннего редиректа на ET)
      $response = Remote::factory($login['url'], $this->_remote_options)->execute();
      
      $this->logger->log('Авторизация: запрос №2 прошел', Zend_Log::INFO);
      
      return $this->is_logged($response);
   }
   
   public function is_logged($response = NULL)
   {
      if ($response === NULL)
      {
         $response = Remote::factory($this->_config->login->url, $this->_remote_options)->execute();
      }
      
      if ((bool) preg_match('/'.$this->_config->login->ident.'/', $response))
      {
         $this->logger->log('Запрос авторизован', Zend_Log::INFO);
         return TRUE;
      }
      
      return FALSE;
   }
   
   /**
    * Считывает содержание каптчи из www.exporttrader.com
    * и шлет запрос на ее распознание.
    *
    * @access protected
    * @return (string)
    */
   protected function _captcha()
   {      
      // считываем капту текущей сессии
      $response = Remote::factory($this->_config->captcha->url, $this->_remote_options)->execute();
      
      // указываем путь до файла изображения
      $file = $this->_config->captcha->image;
      
      // записываем каптчу в файл
      $fp = fopen($file, 'w');
      fwrite($fp, $response);
      fclose($fp);
      
      $this->logger->log('Каптча получена и записана в локальный файл', Zend_Log::INFO);
      
      $captcha = Captcha::recognize($file, $this->_config->captcha->apikey);
      
      if (FALSE === $captcha)
         throw new Exception('Каптча не распознана');
      
      $this->logger->log('Каптча распознана: '.$captcha, Zend_Log::INFO);
      
      return $captcha;
   }
}