<?php defined('SYSPATH') or die('No direct script access.');

class Source_Autotrader extends Source implements Kohana_Source {

   const ATTEMPTS = 15;

   public function execute()
   {
      /*
      if (! $this->_login())
      {
         Kohana::$log->add(Log::ERROR, 'Unauthorized request');
         return;
      }
      */

      $search = Arr::get($this->_config, 'search');

      foreach (Arr::get($search, 'items') AS $search_id)
      {
         $condition = Kohana::config('search.'.$search_id);

         $target_id = Arr::get($condition, 'parent');

         $url = Arr::get($search, 'url')
         .'?'.http_build_query(Arr::get($search, 'fields'))
         .'&'.http_build_query(Arr::get($condition, 'fields'));

         $response = Remote::factory($url, $this->_remote_options)->execute();
         $response = HTML::cleanup($response);

         $total = 0;
         $pages = 1;

         if ($response == '')
         {
            Kohana::$log->add(Log::ERROR, 'Request returns empty result');
            return;
         }

         if ((bool) preg_match('#<strong[^>]+>(\d+)</strong> used [listing|listings]#sU', $response, $matches))
         {
            $total = (int) Arr::get($matches, 1);
         }

         $filtered = 0;
         $added = 0;
         $passed = 0;
         $cached = 0;

         if ($total > 0)
         {
            if ($total > ($offset = Arr::get($search, 'offset')))
            {
               $pages = (int) ceil($total/$offset);
            }

            for ($i = 0; $i < $pages; $i++)
            {
               $next = $i*$offset;

               $new_url = $url.'&'.http_build_query(array('pager.offset' => $next, 'first_record' => ($next+1)));

               $response = Remote::factory($new_url, $this->_remote_options)->execute();

               $urls = $this->_get_urls($response);

               foreach ($urls AS $item_url)
               {
                  $item_url = Arr::get($this->_config, 'domain').$item_url;

                  // находим ID машины
                  $car_id = $this->get_ident($item_url);

                  // ищем ID машины в кэше
                  $cache = Jelly::select('cache')
                  ->where('source_id', '=', $this->_id)
                  ->and_where('ident', '=', $car_id)
                  ->limit(1)
                  ->execute();

                  // запрашиваем контент страницы
                  $response = Remote::factory($item_url, $this->_remote_options)->execute();

                  $options = '';
                  $vincode = '';

                  // если машина не кэшировалась
                  if (! $cache->loaded())
                  {
                     Jelly::factory('cache')
                     ->set(array
                     (
                        'source_id' => $this->_id,
                        'ident' => $car_id
                     ))
                     ->save();

                     // проверяем, защищен ли винкод каптчей
                     if (preg_match('#<span [^>]+>View VIN</span>#sU', $response))
                     {
                        Kohana::$log->add(Log::INFO, 'VIN unavilable. Trying to decode...');

                        $i = 0;

                        try
                        {
                           while ($i++ < self::ATTEMPTS)
                           {
                              Kohana::$log->add(Log::INFO, 'Attempt #'.$i);

                              $options = $this->_remote_options + array
                              (
                                 CURLOPT_POST => TRUE,
                                 CURLOPT_POSTFIELDS => 'kaptchaText='.$this->_captcha(),
                                 CURLOPT_REFERER => $item_url,
                              );

                              // получаем новый контент
                              $response = Remote::factory($item_url.'&captcha=success', $options)->execute();

                              // если картча распознана
                              if (preg_match('#<span id="vin-disp"[^>]+>(.*)</span>#iU', $response, $match))
                              {
                                 Kohana::$log->add(Log::INFO, 'VIN decoded');

                                 // записываем ID машины с винкодом в кэш
                                 $vincode = trim(Arr::get($match, 1));

                                 Jelly::update('cache')
                                 ->where('source_id', '=', $this->_id)
                                 ->and_where('ident', '=', $car_id)
                                 ->set(array('vincode' => $vincode))
                                 ->execute();

                                 break;
                              }
                           }
                        }
                        catch (Kohana_Exception $e)
                        {
                           throw $e;
                        }
                     }
                     else // каптчи нету
                     {
                        if (preg_match('#<span id="vin-disp"[^>]+>(.*)</span>#isU', $response, $match))
                        {
                           // записываем ID машины с винкодом в кэш
                           $vincode = trim(Arr::get($match, 1));

                           Jelly::update('cache')
                           ->where('source_id', '=', $this->_id)
                           ->and_where('ident', '=', $car_id)
                           ->set(array('vincode' => $vincode))
                           ->execute();
                        }
                     }

                     // находим опции
                     $options = $this->_parse_options($response);
                     
                     Jelly::update('cache')
                     ->where('source_id', '=', $this->_id)
                     ->and_where('ident', '=', $car_id)
                     ->set(array('options' => $options))
                     ->execute();
                  }
                  else
                  {
                     $cache = $cache->as_array();

                     $options = Arr::get($cache, 'options');

                     // если кэшировалась, то лепим в конец контента винкод для парсинга
                     $vincode = Arr::get($cache, 'vincode');
                     $response = $response.'<span id="vin-disp" style="">'.$vincode.'</span>';
                  }

                  $item = $this->_parse($response, Arr::get($condition, 'filters'));

                  if (! empty($item))
                  {
                     $item['url'] = $item_url;
                     $item['source_id'] = $this->_id;
                     $item['target_id'] = $target_id;
                     $item['search_id'] = $search_id;
                     $item['options'] = $options;

                     Jelly::factory('cars')
                     ->set($item)
                     ->save();

                     $passed++;

                     $cached += $this->_cache($vincode, Arr::get($condition, 'mark'), $target_id, Arr::get($condition, 'cache'));
                  }
               }

               unset($urls);
            }

            if (($color = Arr::get($condition, 'colors')) !== NULL)
            {
               $filtered = Jelly::factory('cars')
               ->color_filter($search_id, Kohana::config('colors.'.$color));
            }

            $added = $passed - $filtered;
         }

         Jelly::factory('statuses')
         ->set(array
         (
            'target_id' => $target_id,
            'source_id' => $this->_id,
            'items_added' => $added,
         ))
         ->save();

         $this->_log($target_id, array
         (
            'found: '.$total,
            'pass primary filters: '.$passed,
            'did not pass the filter by color: '.$filtered,
            'added: '.$added,
            'new vincodes: '.$cached
         ));
      }
   }

   public function get_ident($url)
   {
      parse_str(Arr::get(parse_url($url), 'query'), $qs);
      return Arr::get($qs, 'car_id');
   }

   protected function _get_urls($content)
   {
      try {
         preg_match_all('#<p class="listing-title">.*<a.*href="(.+)">.*</a>.*</p>#sU', $content, $matches);
         return Arr::get($matches, 1);
      } catch (Kohana_Exception $e) {
         throw $e;
      }
   }

   protected function _parse($content, array $filters = NULL)
   {
      $content = HTML::cleanup($content);

      preg_match('#<h1 class="vdp" id="vdp-make-model-title" title="(.*)">.*</h1>#sU', $content, $title);
      preg_match('#<td class="car-attribute-label"><strong>Price</strong></td><td>.*\$(.+)(?:\s+<span[^>]+>.*</span>)?</td>#sU', $content, $price);
      preg_match('#<td class="car-attribute-label"><strong>Mileage</strong></td><td>(.*)</td>#sU', $content, $mileage);
      preg_match('#<td class="car-attribute-label"><strong>Exterior Color</strong></td><td[^>]*>(.*)</td>#sU', $content, $exterior);
      preg_match('#<td class="car-attribute-label"><strong>Interior Color</strong></td><td[^>]*>(.*)</td>#sU', $content, $interior);
      preg_match('#<span id="vin-disp"[^>]+>(.*)</span>#isU', $content, $vincode);

      $name = Arr::get($title, 1);
      $price = Arr::get($price, 1);
      $vincode = trim(strtoupper(Arr::get($vincode, 1)));
      $mileage = (int) str_replace(',', '', Arr::get($mileage, 1));
      $exterior = Arr::get($exterior, 1);

      try
      {
         $matches = array
         (
            'vincode' => $vincode,
            'mileage' => $mileage,
            'color'   => $exterior,
            'series' => $name,
         );

         if (Filter::factory($filters, $matches)->validate())
         {
            return array
            (
               'name' => $name,
               'vincode' => $vincode,
               'mileage' => $mileage,
               'price' => $price,
               'interior' => Arr::get($interior, 1),
               'exterior' => $exterior,
               'picture' => preg_match('/<div id="carouselSEODiv"[^>]+>/', $content)
            );
         }
      }
      catch (Kohana_Exception $e)
      {
         throw $e;
      }
   }

   protected function _login()
   {
      if ($this->_is_logged())
         return TRUE;

      $login = Arr::get($this->_config, 'login');

      $options = $this->_remote_options + array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => Arr::get($login, 'post_fields'),
         CURLOPT_REFERER => Arr::get($login, 'url'),
      );

      Remote::factory(Arr::get($login, 'url'), $options)->execute(TRUE);

      $bump = Arr::get($login, 'bump');

      Remote::factory($bump.mt_rand(), $this->_remote_options)->execute(TRUE);

      $options = $this->_remote_options + array(CURLOPT_REFERER => $bump.mt_rand());

      $response = Remote::factory(Arr::get($this->_config, 'domain').'/index.jsp', $options)->execute(TRUE);

      return $this->_is_logged($response);
   }

   protected function _is_logged($response = FALSE)
   {
      if (! $response)
         $response = Remote::factory(Arr::get($this->_config, 'domain'), $this->_remote_options)->execute();

      if ((bool) preg_match('/'.Arr::get($this->_config['login'], 'ident').'/', $response))
      {
         Kohana::$log->add(Log::INFO, 'Authorized request');
         return TRUE;
      }

      return FALSE;
   }

   protected function _captcha()
   {
      $captcha = Arr::get($this->_config, 'captcha');

      $response = Remote::factory(Arr::get($captcha, 'url').mt_rand(), $this->_remote_options)->execute();

      $file = Arr::get($captcha, 'image_file');

      $fp = fopen($file, 'w');
      fwrite($fp, $response);
      fclose($fp);

      $captcha_response = Captcha::recognize($file, Arr::get($captcha, 'apikey'), 5, 120, TRUE, TRUE, FALSE, 0, 5);

      $captcha_response = preg_replace('/\s+/', '', $captcha_response); // remove whitespaces

      return substr($captcha_response, 0, 5); // return first 5 symbols
   }
}