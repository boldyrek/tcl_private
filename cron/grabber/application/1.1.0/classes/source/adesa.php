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

      $search = $this->_config['search'];

      foreach ($this->_get_active_items($search['items']) AS $search_id)
      {
         $condition = Kohana::config('search.'.$search_id);

         $car_id = $condition['parent'];

         $models = array();

         foreach (Arr::get($condition['fields'], 'ml') AS $model)
         {
            $models[] = 'ml='.urlencode($model);
         }

         $models = implode('&', $models);

         unset($condition['fields']['ml']);

         $car = Jelly::select('admin_cars')
         ->where(':primary_key', '=', $car_id)
         ->execute()
         ->current();

         $fields = $condition['fields'] + array(
            'y1' => $car->year_from,
            'y2' => $car->year_to,
            'km' => $car->mileage
         );

         $fields = http_build_query(Arr::get($search, 'fields'))
         .'&'.http_build_query($fields)
         .'&'.$models;

         $options = $this->_remote_options + array(
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_REFERER => $search['url'],
         );

         $response = Remote::factory($search['url'], $options)->execute();
                  
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
         $filtered_by_colors = 0;
         $filtered_by_options = 0;
         $added = 0;
         $cached = 0;

         if ($total > 0)
         {
            $filters = $condition['filters'] + array(
               'mileage' => $car->mileage
            );

            $items = $this->_parse($response, $filters);

            $passed += sizeof($items);

            if (! empty($items))
            {
               foreach ($items AS $item)
               {
                   $cached_item=Jelly::select('cars_cache')->where('url','=',$item['url'])->execute()->current();

                   if ($cached_item->url==null)
                   {
                  $item['source_id'] = $this->_id;
                  $item['target_id'] = $car_id;
                  $item['search_id'] = $search_id;
                  $item['options'] = $this->_get_options($this, $item['url']);
                  $item['picture'] = $this->_picture_exists($item['url'], $item['vincode']);
                   }
                   else
                       $item = Jelly::factory('cars_cache')->get_item_from_cache($cached_item);
                    if (!$this->_is_exist_vincode($item['vincode'],$search_id)){
                          Jelly::factory('cars')
                          ->set($item)
                          ->save();

                           if ($cached_item->url==null)
                           {
                               Jelly::factory('cars_cache')
                                   ->set($item)
                                   ->save();
                               $cached += $this->_cache($item['vincode'], $condition['mark'], $car_id, $condition['cache']);
                           }
                    }
               }
               
               $colors = $this->_get_colors($car_id);

               if (! empty($colors))
               {
                  $filtered_by_colors = Jelly::factory('cars')
                     ->color_filter($search_id, $colors);
               }
               
               $filtered_by_options = $this->_cache_options($car_id, $this->_id);

               unset($items);
            }

            $added = $passed - ($filtered_by_colors + $filtered_by_options);

            /*
            if ($total > ($offset = Arr::get($search, 'offset')))
            {
               $pages = (int) ceil($total/$offset);
            }

            for ($i = 0; $i < $pages; $i++)
            {
               $search['url'] = 'http://www.dealerblock.ca/xamsrunlist/xsearchVehResult.jsp?order=A_VHL_RUNNUM&sortby=A_VHL_RUNNUM&cl=&sr=&km3=&vf=yes&cn3=&queryString=null&notifyme=&offset=';
               $response = Remote::factory($search['url'].($i*$offset), $this->_remote_options)->execute();
            }
            */
         }

         Jelly::factory('statuses')
            ->set(array(
               'target_id' => $car_id,
               'source_id' => $this->_id,
               'items_added' => $added,
            ))
            ->save();

         $this->_log($car->name, array(
            'found: '.$total,
            'pass primary filters: '.$passed,
            'did not pass the filter by colors: '.$filtered_by_colors,
            'did not pass the filter by options: '.$filtered_by_options,
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

            $matches = array(
               'vincode' => $vincode,
               'mileage' => $mileage,
               'color' => $exterior,
               'series' => '',
            );

            if (Filter::factory($filters, $matches)->validate())
            {
               $output[] = array(
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

   protected function _picture_exists($url, $vincode)
   {
      $content = Remote::factory($url, $this->_remote_options)->execute();

      $picture_exists = ! preg_match('/Image Not Available/', $content);

      if (strpos($url, $this->_config['domain']) === FALSE)
      {
         $picture_exists = ! preg_match('#img/NoImage.gif#', $content);
      }

      if ($picture_exists)
      {
         Filecache::factory()
            ->set_url($url)
            ->set_path(strtoupper($vincode).'-ADESA')
            ->import($content);
      }

      return $picture_exists;
   }

   protected function _login()
   {
      if ($this->_is_logged())
         return TRUE;

      $login = Arr::get($this->_config, 'login');

      $options = $this->_remote_options + array(
         CURLOPT_POST => TRUE,
         CURLOPT_FOLLOWLOCATION => TRUE,
         CURLOPT_POSTFIELDS => http_build_query(Arr::get($login, 'post_fields')),
      );

      $response = Remote::factory(Arr::get($login, 'url'), $options)->execute();
      
      // $response = Remote::factory(Arr::get($login, 'redirect_url'), $this->_remote_options)->execute();
      
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