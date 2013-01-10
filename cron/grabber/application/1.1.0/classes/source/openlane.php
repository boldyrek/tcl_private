<?php defined('SYSPATH') or die('No direct script access.');

class Source_Openlane extends Source implements Kohana_Source {

   public function execute()
   {
      if (! $this->_login())
      {
         Kohana::$log->add(Log::ERROR, 'Unauthorized request');
         return;
      }

      $search = Arr::get($this->_config, 'search');

      foreach (Arr::get($search, 'items') AS $search_id)
      {
         $condition = Kohana::config('search.'.$search_id);

         $target_id = Arr::get($condition, 'parent');

         $url = Arr::get($search, 'url').Arr::get($condition, 'search_id');

         $response = Remote::factory($url, $this->_remote_options)->execute();

         $total = 0;

         if ((bool) preg_match('#<span style="font-weight:bold;">(.+)&nbsp; Results Found</span>#isU', $response, $matches))
         {
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
      return Arr::get($qs, 'vehicleId');
   }

   protected function _parse($content, array $filters = NULL)
   {
      $content = HTML::cleanup($content);

      $output = array();

      $pattern = '#';
      $pattern .= '<span class="ymms">.*<a.*href="(.+)".*>(.+)</a>.*</span>'; // link, name

      $pattern .= '.*<tr class="searchResultTitle subtitle">.*<td colspan="9">.*';
      $pattern .= '([A-Z0-9]{17}).*Ext: (.*)&nbsp;.*Int: (.*)&nbsp;'; // VIN & colors
      $pattern .= '.*</td>.*</tr>';

      $pattern .= '.*<tr class="search_records">';
      $pattern .= '.*<td valign="top" class="image">.*<img src="(.*)".*>.*</td>';

      $pattern .= '.*<td valign="top">.*';
      $pattern .= '(\d+(?:,\d+)?) mi'; // milage
      $pattern .= '.*</td>';

      $pattern .= '.*<td valign="top">.*';
      $pattern .= '(?:.+)'; // proximity
      $pattern .= '</td>';

      $pattern .= '.*<td valign="top" nowrap>.*';
      $pattern .= '(?:.+)'; // damages
      $pattern .= '</td>';

      $pattern .= '.*<td valign="top">.*';
      $pattern .= '(.*)'; // price
      $pattern .= '&nbsp;.*</td>';

      $pattern .= '.*<td class="lastcol" valign="top">.*';
      $pattern .= '(\d+h \d+m)'; // time left
      $pattern .= '.*</td>.*';

      $pattern .= '.*</tr>';

      $pattern .= '#isU';

      try {
         preg_match_all($pattern, $content, $scores, PREG_SET_ORDER);

         foreach ($scores AS $item)
         {
            $name = Arr::get($item, 2, 'unknown');
            $vincode = strtoupper(Arr::get($item, 3));
            $mileage = (int) str_replace(',', '', Arr::get($item, 7));
            $exterior = trim(Arr::get($item, 4, ''));

            $price = trim(Arr::get($item, 8));

            if ($price != '')
            {
               $price = trim(str_replace(array('<br/>', ',', '$'), array('-', ''), $price), '-');
            }

            $date_auction = preg_replace('/(\d+)h (\d+)m/', '$1 hours $2 minutes', Arr::get($item, 9));
            $date_auction = date('Y-m-d H:i', strtotime($date_auction));

            $matches = array
            (
               'vincode' => $vincode,
               'mileage' => $mileage,
               'color'   => $exterior,
               'series' => $name,
            );

            if (Filter::factory($filters, $matches)->validate())
            {
               $output[] = array
               (
                  'date_auction' => $date_auction,
                  'name' => $name,
                  'vincode' => $vincode,
                  'mileage' => $mileage,
                  'price' => $price,
                  'exterior' => $exterior,
                  'interior' => trim(Arr::get($item, 5, '')),
                  'url' => $this->_config['domain'].Arr::get($item, 1),
                  'picture' => ! preg_match('/imagenotavailable/', Arr::get($item, 6))
               );
            }
         }

         unset($scores);

         return $output;

      } catch (Kohana_Exception $e) {
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
         CURLOPT_POSTFIELDS => http_build_query(Arr::get($login, 'login_post_fields')),
         CURLOPT_REFERER => Arr::get($login, 'home_url'),
      );

      Remote::factory(Arr::get($login, 'login_auth_url'), $options)->execute(TRUE);

      $options = $this->_remote_options + array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => http_build_query(Arr::get($login, 'home_post_fields')),
         CURLOPT_REFERER => Arr::get($login, 'login_auth_url'),
      );

      Remote::factory(Arr::get($login, 'home_url'), $options)->execute(TRUE);

      $options = $this->_remote_options + array
      (
         CURLOPT_REFERER => Arr::get($login, 'login_auth_url'),
      );

      Remote::factory(Arr::get($login, 'login_slogin_url'), $options)->execute(TRUE);

      $options = $this->_remote_options + array
      (
         CURLOPT_REFERER => Arr::get($login, 'login_auth_url'),
      );

      $response = Remote::factory(Arr::get($login, 'home_url'), $options)->execute(TRUE);

      return $this->_is_logged($response);
   }

   protected function _is_logged($response = FALSE)
   {
      $login = Arr::get($this->_config, 'login');

      if (! $response)
         $response = Remote::factory(Arr::get($login, 'home_url'), $this->_remote_options)->execute();

      if ((bool) preg_match('/'.Arr::get($login, 'ident').'/', $response))
      {
         Kohana::$log->add(Log::INFO, 'Authorized request');
         return TRUE;
      }

      return FALSE;
   }

}