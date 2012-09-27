<?php defined('SYSPATH') or die('No direct script access.');

class Source_Adesa extends Source implements Kohana_Source {

   public function execute()
   {
      if (! $this->_login())
      {
         Kohana::$log->add(Log::ERROR, 'Unauthorized request');
         return;
      }

      ob_start();
      echo Remote::factory(Arr::get($this->_config, 'runlist_url'), $this->_remote_options);
      ob_end_clean();

      $search = Arr::get($this->_config, 'search');

      foreach (Arr::get($search, 'items') AS $search_id)
      {
         $condition = Kohana::config('search.'.$search_id);

         $target_id = Arr::get($condition, 'parent');

         $url = Arr::get($search, 'url');

         $models = array();

         foreach (Arr::get($condition['fields'], 'ml') AS $model)
         {
            $models[] = 'ml='.urlencode($model);
         }

         $models = implode('&', $models);

         unset($condition['fields']['ml']);

         $fields = http_build_query(Arr::get($search, 'fields'))
         .'&'.http_build_query(Arr::get($condition, 'fields'))
         .'&'.$models;

         $options = $this->_remote_options + array
         (
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_REFERER => $url,
         );

         $response = Remote::factory($url, $options)->execute();

         if (strpos($response, 'Error 500') !== FALSE)
         {
            Kohana::$log->add(Log::ERROR, '500 Internal Server Error');
            return;
         }

         $total = 0;
         // $pages = 1;

         if (! (bool) preg_match('/No Records Found/i', $response))
         {
            if ((bool) preg_match('#<span><font[^>]+>(\d+)</font></span> Total Vehicles?#i', $response, $matches))
               $total = (int) Arr::get($matches, 1);
         }

         $passed = 0;
         $filtered = 0;
         $added = 0;
         $cached = 0;

         if ($total > 0)
         {
            $items = $this->_parse($response, Arr::get($condition, 'filters'));

            $passed += sizeof($items);

            if (! empty($items))
            {
               foreach ($items AS $item)
               {
                  $item['source_id'] = $this->_id;
                  $item['target_id'] = $target_id;
                  $item['search_id'] = $search_id;
                  $item['options'] = $this->_get_options($this, Arr::get($item, 'url'));
                  $item['picture'] = $this->_picture_exists(Arr::get($item, 'url'));

                  Jelly::factory('cars')
                  ->set($item)
                  ->save();

                  $cached += $this->_cache(Arr::get($item, 'vincode'), Arr::get($condition, 'mark'), $target_id, Arr::get($condition, 'cache'));
               }

               if (($color = Arr::get($condition, 'colors')) !== NULL)
               {
                  $filtered = Jelly::factory('cars')
                  ->color_filter($search_id, Kohana::config('colors.'.$color));
               }

               unset($items);
            }

            $added = $passed - $filtered;

            /*
            if ($total > ($offset = Arr::get($search, 'offset')))
            {
               $pages = (int) ceil($total/$offset);
            }

            for ($i = 0; $i < $pages; $i++)
            {
               $url = 'http://www.dealerblock.ca/xamsrunlist/xsearchVehResult.jsp?order=A_VHL_RUNNUM&sortby=A_VHL_RUNNUM&cl=&sr=&km3=&vf=yes&cn3=&queryString=null&notifyme=&offset=';
               $response = Remote::factory($url.($i*$offset), $this->_remote_options)->execute();
            }
            */
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
      return Arr::get($qs, (strpos($url, $this->_config['domain']) === FALSE) ? 'VIN' : 'aiid');
   }

   protected function _parse($content, array $filters = NULL)
   {
      $output = array();

      $pattern  = '#<div class="col2">\s+<span class="ymm">\s+<a.*href="(.+)".*>\s+(.+)</a>\s+</span>';
      $pattern .= '<br>.*<br>(.*)<br>\s+';
      $pattern .= '<strong>Odo: </strong>(.+)<br>\s+';
      $pattern .= '([a-z0-9]{17}).*';
      $pattern .= '</div>';
      $pattern .= '.*<div class="col3">.*</div>\s+';
      $pattern .= '<div class="col4">\s+<strong>Auction Date: </strong>(.+)<br>.*</div>';
      $pattern .= '.*<div class="col5">';
      $pattern .= '#isU';

      try
      {
         preg_match_all($pattern, $content, $scores, PREG_SET_ORDER);

         foreach ($scores AS $score)
         {
            $score = array_map(array($this, '_clearup'), $score);

            $url = Arr::get($score, 1);

            if (strpos($url, 'http://') === FALSE)
            {
               $url = $this->_config['domain'].$url;
            }

            $mileage = str_replace(',', '', Arr::get($score, 4));
            
            $mileage = (strpos($mileage, 'K') !== FALSE
            ? floor((int) $mileage / 1.609344) // km. into ml.
            : (int) $mileage);                 // ml. into ml.

            $vincode = strtoupper(Arr::get($score, 5));
            $exterior = Arr::get($score, 3);

            $matches = array
            (
               'vincode' => $vincode,
               'mileage' => $mileage,
               'color' => $exterior,
               'series' => '',
            );

            if (Filter::factory($filters, $matches)->validate())
            {
               $output[] = array
               (
                  'date_auction' => date('Y-m-d', strtotime(Arr::get($score, 6))),
                  'name' => Arr::get($score, 2),
                  'vincode' => $vincode,
                  'mileage' => $mileage,
                  'exterior' => $exterior,
                  'url' => $url,
               );
            }
         }

         unset($scores);

         return $output;
      }
      catch (Kohana_Exception $e)
      {
         throw $e;
      }
   }

   protected function _clearup($str)
   {
      return trim($str);
   }

   protected function _picture_exists($url)
   {
      $content = Remote::factory($url, $this->_remote_options)->execute();

      if (strpos($url, $this->_config['domain']) === FALSE)
      {
         return ! preg_match('#img/NoImage.gif#', $content, $m);
      }

      return ! preg_match('/Image Not Available/', $content, $m);
   }

   protected function _login()
   {
      if ($this->_is_logged())
         return TRUE;

      $login = Arr::get($this->_config, 'login');

      $options = $this->_remote_options + array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_FOLLOWLOCATION => TRUE,
         CURLOPT_POSTFIELDS => http_build_query(Arr::get($login, 'post_fields')),
      );

      Remote::factory(Arr::get($login, 'url'), $options)->execute();

      $response = Remote::factory(Arr::get($login, 'redirect_url'), $this->_remote_options)->execute();

      return $this->_is_logged($response);
   }

   protected function _is_logged($response = FALSE)
   {
      $login = Arr::get($this->_config, 'login');

      if (! $response)
         $response = Remote::factory(Arr::get($login, 'redirect_url'), $this->_remote_options)->execute();

      if ((bool) preg_match('/'.Arr::get($login, 'ident').'/', $response))
      {
         Kohana::$log->add(Log::INFO, 'Authorized request');
         return TRUE;
      }

      return FALSE;
   }

}