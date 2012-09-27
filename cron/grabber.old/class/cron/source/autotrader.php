<?php defined('SYSPATH') or die('No direct script access.');

/*
 * lexus rx 300 :5000 - 8000
lexus rx 330 :10000 - 16000
lexus rx 400h: 13000 - 17000
lexus gx 470 2003 - 2004 : 12000 - 17000
lexus gx 470 2005-2006: 17000 - 21000
lexus lx 470 2003-2004: 18000 - 23000
lexus lx 470 2000-2002: 9000 - 12000
toyota land cruiser 2003-2004: 15000 - 19000
honda crv 2002-2003: 6000 - 8000
hondu pilot mojete ubrat' sovsem ona ne nujna
mitsubishi montero 2001-2003: 4000 - 8000
toyota 4runner 2003-2006 :  10000 - 15000
toyota rav 4 2001-2003: 4000 - 7000
toyota highlander 2001-2003: 5000 - 8000
 */

class Cron_Source_Autotrader extends Cron implements Cron_Interface {

   protected $_config; // class config
   protected $_remote_options;

   public function __construct()
   {
      parent::__construct();

      $this->_config = $this->config->source->autotrader;
      $this->_remote_options = $this->config->core->remote->toArray() + $this->_config->remote->toArray();
   }

   public function execute($source_id)
   {
      if (! $this->login())
      {
         $message = 'Запрос не авторизован';
         $this->logger->log($message, Zend_Log::INFO);
         echo $message;
         return;
      }
      
      $output = array();

      foreach ($this->_config->search->types->toArray() AS $id)
      {
         $types = $this->config->search->types->toArray();
         $type = Arr::get($types, $id);

         $locked = $this->status->insert(array('search_type' => $type['parent'], 'source' => $source_id, 'locked' => 1));

         $url = $this->_config->search->url
         .'?'.http_build_query($this->_config->search->fields->toArray())
         .'&'.http_build_query(Arr::get($type, 'fields'));

         // Core::debug($url);

         $response = Remote::factory($url, $this->_remote_options)->execute();
         $response = $this->_clean($response);

         $total = 0;
         $pages = 1;

         if ($response == '')
         {
            $message = 'Запрос вернул пустой результат';
            $this->logger->log($message, Zend_Log::INFO);
            echo $message;
            return;
         }

         if ((bool) preg_match('#<strong>(\d+)</strong> used [listing|listings]#sU', $response, $matches))
         {
            $total = (int) Arr::get($matches, 1);
         }

         // Core::debug($total);

         $filtered = 0;
         $added = 0;
         $passed = 0;
         $new_vins = 0;

         if ($total > 0)
         {
            if ($total > ($offset=$this->_config->search->offset))
            {
               $pages = (int) ceil($total/$offset);
            }

            // Core::debug($pages);

            for ($i = 0; $i < $pages; $i++)
            {
               $next = $i*$offset;
               $new_url = $url.'&'.http_build_query(array('pager.offset' => $next, 'first_record' => ($next+1)));

               // Core::debug($i);

               $response = Remote::factory($new_url, $this->_remote_options)->execute();
               $urls = $this->get_urls($response);

               // Core::debug($urls);

               foreach ($urls AS $item_url)
               {
                  $item_url = $this->_config->domain.$item_url;
                  
                  // находим ID машины
                  $parsed_url = parse_url($item_url);
                  parse_str($parsed_url['query'], $qs);
                  $car_id = Arr::get($qs, 'car_id');

                  // ищем ID машины в кэше
                  $cache = $this->cache->unique($source_id, $car_id);

                  // запрашиваем контент страницы
                  $response = Remote::factory($item_url, $this->_remote_options)->execute();

                  $vin = 'empty';
                  // если машина не кэшировалась
                  if (! $cache)
                  {
                     $this->logger->log('ID машины '.$car_id.' не кэширован', Zend_Log::DEBUG);

                     // проверяем, защищен ли винкод каптчей
                     if (preg_match('#<span [^>]+>View VIN</span>#sU', $response))
                     {
                        $this->logger->log('Винкод защищен', Zend_Log::DEBUG);

                        $i = 0;

                        while ($i++ < 5)
                        {
                           $this->logger->log('Попытка №'.$i, Zend_Log::DEBUG);

                           $captcha = $this->_captcha();

                           $options = $this->_remote_options + array
                           (
                              CURLOPT_POST => TRUE,
                              CURLOPT_POSTFIELDS => 'kaptchaText='.$captcha,
                              CURLOPT_REFERER => $item_url,
                           );

                           // получаем новый контент
                           $response = Remote::factory($item_url.'&captcha=success', $options)->execute();

                           // если картча распознана
                           if (preg_match('#<span id="vin-disp"[^>]+>(.*)</span>#isU', $response, $match))
                           {
                              // записываем ID машины с винкодом в кэш
                              $vin = trim(Arr::get($match, 1));
                              $this->cache->insert(array('source' => $source_id, 'ident_string' => $car_id, 'vin' => $vin));
                              
                              break;
                           }
                        }
                     }
                     else // картчи нету
                     {
                        if (preg_match('#<span id="vin-disp"[^>]+>(.*)</span>#isU', $response, $match))
                        {
                           // записываем ID машины с винкодом в кэш
                           $vin = trim(Arr::get($match, 1));
                           $this->cache->insert(array('source' => $source_id, 'ident_string' => $car_id, 'vin' => $vin));
                        }
                     }
                  }
                  else
                  {
                     // если кэшировалась, то лепим в конец контента винкод для парсинга
                     $vin = $cache['vin'];
                     $response = $response.'<span id="vin-disp" style="">'.$vin.'</span>';
                  }

                  $item = $this->parse($response, Arr::get($type, 'filters'));

                  if (! empty($item))
                  {
                     $parent = Arr::get($type, 'parent');

                     $item['url'] = $item_url;
                     $item['source'] = $source_id;
                     $item['search_type'] = $parent;
                     $item['search_id'] = $id;

                     $this->car->insert($item);

                     $passed++;

                     $new_vins += $this->_cache($vin, Arr::get($type, 'mark'), $parent, Arr::get($type, 'cache'));
                  }
               }
            }

            if (($color = Arr::get($type, 'colors')) !== NULL)
            {
               $colors = $this->config->colors->toArray();
               $filtered = $this->car->color_filter($id, Arr::get($colors, $color));
            }

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

         $this->logger->log('Тип '.$id.' - '.implode(', ', $output[$id]['totals']), Zend_Log::INFO);

         $where = Database::quoteValues(array('id' => $locked['insert_id']), TRUE);
         $this->status->update(array
         (
            'date_last_updated' => date('Y-m-d H:i:s',(int) microtime(TRUE)),
            'items_added' => $added,
            'locked' => 0,
         ), $where);

         echo $this->_message($source_id, $type['parent'], $output[$id]['totals']);
      }
   }

   public function parse($content, array $filters = NULL)
   {
      $content = $this->_clean($content);

      $output = array();

      preg_match('#<h1 class="vdp" id="vdp-make-model-title" title="(.*)">.*</h1>#sU', $content, $title);
      preg_match('#<td class="car-attribute-label"><strong>Price</strong></td><td>.*\$(.*)</td>#sU', $content, $price);
      preg_match('#<td class="car-attribute-label"><strong>Mileage</strong></td><td>(.*)</td>#sU', $content, $mileage);
      preg_match('#<td class="car-attribute-label"><strong>Exterior Color</strong></td><td[^>]*>(.*)</td>#sU', $content, $outside);
      preg_match('#<td class="car-attribute-label"><strong>Interior Color</strong></td><td[^>]*>(.*)</td>#sU', $content, $inside);
      preg_match('#<span id="vin-disp"[^>]+>(.*)</span>#isU', $content, $vincode);

      $name = Arr::get($title, 1);
      $vincode = trim(strtoupper(Arr::get($vincode, 1)));
      $price = (int) str_replace(',', '', Arr::get($price, 1));
      $mileage = (int) str_replace(',', '', Arr::get($mileage, 1));
      $outside = Arr::get($outside, 1);
      $inside = Arr::get($inside, 1);

      try
      {
         $matches = array
         (
            'vincode' => $vincode,
            'millage' => $mileage,
            'color'   => $outside,
            'series' => $name,
         );

         if (Filter::factory($filters, $matches)->validate())
         // if ($matches)
         {
            $output = array
            (
               'date_added' => date('Y-m-d H:i:s'),
               'date_auction' => NULL,
               'name' => $name,
               'vin' => $vincode,
               'millage' => $mileage,
               'price' => $price,
               'color_inside_origin' => $inside,
               'color_outside_origin' => $outside,
            );
         }
      }
      catch (Exception $e)
      {
         throw $e;
      }

      return $output;
   }

   protected function get_urls($content)
   {
      $content = $this->_clean($content);
      try {
         preg_match_all('#<p class="listing-title"><a href="(.+)">.*</a></p>#sU', $content, $matches);
      } catch (Exception $e) {
         throw $e;
      }

      return Arr::get($matches, 1);
   }

   public function login()
   {
      if ($this->is_logged())
         return TRUE;
      
      $login = $this->_config->login->toArray();

      $options = $this->_remote_options + array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => $login['post_fields'],
         CURLOPT_REFERER => $login['url'],
      );

      Remote::factory($login['url'], $options)->execute(TRUE);

      Remote::factory($login['bump'].mt_rand(), $this->_remote_options)->execute(TRUE);

      $options = $this->_remote_options + array(CURLOPT_REFERER => $login['bump'].mt_rand());

      $response = Remote::factory($this->_config->domain.'/index.jsp', $options)->execute(TRUE);

      return $this->is_logged($response);
   }

   public function is_logged($response = NULL)
   {
      if ($response === NULL)
      {
         $response = Remote::factory($this->_config->domain, $this->_remote_options)->execute();
      }

      if ((bool) preg_match('/'.$this->_config->login->ident.'/', $response))
      {
         $this->logger->log('Запрос авторизован', Zend_Log::INFO);
         return TRUE;
      }

      return FALSE;
   }

   protected function _captcha()
   {
      $response = Remote::factory($this->_config->captcha->url.mt_rand(), $this->_remote_options)->execute();

      $file = $this->_config->captcha->image;

      $fp = fopen($file, 'w');
      fwrite($fp, $response);
      fclose($fp);

      $captcha = Captcha::recognize($file, $this->_config->captcha->apikey, 5, 120, TRUE, TRUE, FALSE, 0, 5);

      return preg_replace('/\s+/', '', $captcha);
   }
}