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
            if ((bool) preg_match('#<b><font[^>]+>(\d+)</font>&nbsp;Total Vehicles</b>#i', $response, $matches))
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
      return Arr::get($qs, 'VIN');
   }

   protected function _parse($content, array $filters = NULL)
   {
      $content = HTML::cleanup($content);

      $output = array();

      $pattern = '#';
      $pattern .= '<tr\s+bgcolor="(?:ffffff|f0f0f0)">';
      $pattern .= '<td align="center" nowrap>.*</td>';
      $pattern .= '<td align="center" >(.*)</td>'; // year
      $pattern .= '<td align="center" >(.*)</td>'; // mark
      $pattern .= '<td align="left">(.*)</td>'; // model
      $pattern .= '<td align="left">(.*)</td>'; // model extras
      $pattern .= '<td align="center">.*</td>';
      $pattern .= '<td align="left">.*</td>';
      $pattern .= '<td align="right">(.*)</td>'; // millage
      $pattern .= '<td align="center">(.*)</td>'; // outside color
      $pattern .= '<td align="center">(.*)</td>'; // VIN
      $pattern .= '<td align="center">(.*)</td>'; // auction date
      $pattern .= '<td align="center">.*</td>';
      $pattern .= '<td nowrap align="center">.*</td>';
      $pattern .= '<td align="center">(.*)</td>'; // url
      $pattern .= '.*';
      $pattern .= '</tr>';
      $pattern .= '#sU';

      try
      {
         preg_match_all($pattern, $content, $scores, PREG_SET_ORDER);

         foreach ($scores AS $score)
         {
            $score = array_map(array($this, '_clearup'), $score);
            
            if (preg_match('#<a\s+.*href=[\'"]([^\'"]+)[\'"].*</a>#sU', Arr::get($score, 9), $url)) // temp. hack
            {
               $url = Arr::get($url, 1);
               
               if (strpos($url, 'http://') === FALSE)
               {
                  $url = Arr::get($this->_config, 'domain').$url;
               }

               $mileage = Arr::get($score, 5);
               $mileage = (int) (strpos($mileage, 'K') !== FALSE
               ? floor((int) $mileage / 1.609344) // km. into ml.
               : (int) $mileage);                 // ml. into ml.

               $vincode = Arr::get($score, 7);
               $exterior = Arr::get($score, 6);

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
                     'date_auction' => date('Y-m-d', strtotime(Arr::get($score, 8))),
                     'name' => Arr::get($score, 1).' '.Arr::get($score, 2).' ' .Arr::get($score, 3).' '.Arr::get($score, 4),
                     'vincode' => $vincode,
                     'mileage' => $mileage,
                     'exterior' => $exterior,
                     'url' => $url
                  );
               }
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
      return trim(str_replace(array('&nbsp;'), ' ', $str));
   }

   protected function _login()
   {
      if ($this->_is_logged())
         return TRUE;

      $login = Arr::get($this->_config, 'login');

      $options = $this->_remote_options + array
      (
         CURLOPT_POST => TRUE,
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