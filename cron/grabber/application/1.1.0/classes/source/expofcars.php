<?php defined('SYSPATH') or die('No direct script access.');

class Source_ExpOfCars extends Source implements Kohana_Source {

   public function execute()
   {           
      $search = $this->_config['search'];
           
      foreach ($this->_get_active_items($search['items']) AS $search_id)
      {          
         $condition = Kohana::config('search.'.$search_id);

         $car_id = $condition['parent'];

         $car = Jelly::select('admin_cars')
         ->where(':primary_key', '=', $car_id)
         ->execute()
         ->current();

         $fields = $condition['fields'] + array(
            'se_search_year_1' => $car->year_from,
            'se_search_year_2' => $car->year_to,
            'se_search_odometer_km_1' => $car->mileage
         );         

         $second_cnt = -1;
         $offset_cnt = 0;

         while (true) {
             $fields['flag_basic_view'] = 1;
             $fields_url = http_build_query(array_merge(Arr::get($search, 'fields'), $fields)).'&offset='.($offset_cnt*$search['offset']).'&limit='.$search['offset'];

             $options = $this->_remote_options;
             $options[CURLOPT_POST] = FALSE;
             $options[CURLOPT_REFERER] = $options[CURLOPT_REFERER].'?'.$fields_url;

             $request_url     = $search['url'].'&'.$fields_url;
             $request_options = $options;
         
             $response = Remote::factory($request_url, $request_options)->execute();

             if ($response === FALSE)
             {
                Kohana::$log->add(Log::ERROR, '500 Internal Server Error');
                return;
             }

             $response_basic = $this->_clear_from_js($response);
             
             $fields['flag_basic_view'] = 0;
             $fields_url = http_build_query(array_merge(Arr::get($search, 'fields'), $fields)).'&offset='.($offset_cnt*$search['offset']).'&limit='.$search['offset'];
             $options[CURLOPT_REFERER] = $options[CURLOPT_REFERER].'?'.$fields_url;
             $request_url     = $search['url'].'&'.$fields_url;
             $request_options = $options;
         
             $response = Remote::factory($request_url, $request_options)->execute();

             if ($response === FALSE)
             {
                Kohana::$log->add(Log::ERROR, '500 Internal Server Error');
                return;
             }

             $response_detail = $this->_clear_from_js($response);
             
             $total = 0;

             if (! (bool) preg_match('/No records found!/i', $response_basic))
             {
                if ((bool) preg_match('#<span>Fetched: <b>(\d+)</b> of <b>(\d+)</b></span>#i', $response_basic, $matches)) {
                    $total = (int) Arr::get($matches, 2);                    
                }
             }

             if ($total > 0 && $second_cnt == -1) {
                 $second_cnt = intval($total/$search['offset']);
                 if ($second_cnt*$search['offset'] < $total) {
                     $second_cnt++;
                 }
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

                $items = $this->_parse($response_basic, $response_detail, $filters);
                
                $passed += sizeof($items);

                if (! empty($items))
                {
                   foreach ($items AS $item)
                   {
                        $cached_item = Jelly::select('cars_cache')->where('url','=',$item['url'])->execute()->current();

                        if ($cached_item->url == null) {
                           $item['source_id'] = $this->_id;
                           $item['target_id'] = $car_id;
                           $item['search_id'] = $search_id;
                           $item['options']   = $this->_get_options($this, $item['url']);
                           $item['picture']   = $this->_picture_exists($item['url'], $item['vincode']);
                        } else {
                           $item = Jelly::factory('cars_cache')->get_item_from_cache($cached_item);
                        }
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
                   
                   $filtered_by_options = 0;
                   $filtered_by_colors  = 0;

                   unset($items);
                }

                $added = $passed - ($filtered_by_colors + $filtered_by_options);
             }
             
             $second_cnt--;
             $offset_cnt++;
             if ($second_cnt <= 0) break;
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
      return Arr::get($qs, 'se_search_id');
   }

   protected function _clear_from_js($value) {
      $lines = explode("\n", $value);
      for ($i=0; $i<count($lines); $i++) {
          $lines[$i] = mb_substr($lines[$i], 16, -5, 'UTF-8');
      }
      
      return implode('', $lines);
   }
   
   protected function _get_car_price_by_VIN(&$full_list, $indx, $VIN, $car_name) {
       //if (!isset($full_list[$indx])) return '';
       
       foreach ($full_list as $key => $full_rec) {
           $full_rec = array_map(array($this, '_clearup'), $full_rec);
           if ($full_rec[2] == mb_substr($VIN, 0, mb_strlen($full_rec[2])) && $full_rec[1] == $car_name) {
               return floatval(str_replace(',', '', $full_rec[3]));
           }
       }
       
       return '';
   }

   protected function _get_auction_date_by_VIN(&$full_list, $indx, $VIN, $car_name) {
       //if (!isset($full_list[$indx])) return '';
       
       foreach ($full_list as $key => $full_rec) {
           $full_rec = array_map(array($this, '_clearup'), $full_rec);
           if ($full_rec[2] == mb_substr($VIN, 0, mb_strlen($full_rec[2])) && $full_rec[1] == $car_name) {
               return date('Y-m-d', strtotime($full_rec[4]));
           }
       }
       
       return '';
   }

   protected function _parse(&$content_brief, &$content_full, array $filters = NULL)
   {     
      $output = array();
      
      try
      {
         $pattern  = '#<tr class="search_vehicle_basic_row search_name_cell">.*';
         $pattern .= '<a href="(.+)".*>(.+)</a>.*([A-Z0-9]{17}).*</td>.*';
         $pattern .= '<td[^>]*>(.*)(<br />|<br>|<br/>)(.*)</td>.*';
         $pattern .= '<td[^>]*>.*<br />.*([0-9]+)mi.*</td>.*';
         $pattern .= '.*</tr>';
         $pattern .= '#isU';

         $scores_brief = array();
         preg_match_all($pattern, $content_brief, $scores_brief, PREG_SET_ORDER);

         //$pattern  = '#<td>([A-Z0-9]{11})xxxxxx</td>';
         $pattern  = '#<td><a[^>]*>(.*)</a></td>';
         $pattern .= '.*<td>([A-Z0-9]{11})xxxxxx</td>';
         $pattern .= '[^x]*<b>Buy Now Price:</b>.*[$]([0-9,]{1,9}.[0-9]{2}) USD';
         $pattern .= '.*<b>Listing Ends</b>.*<td>([0-9]{2}/[0-9]{2}/[0-9]{4} [0-9]{1,2}:[0-9]{1,2} (am|pm))</td>';
         $pattern .= '#isU';
         
         $scores_full = array();
         preg_match_all($pattern, $content_full, $scores_full, PREG_SET_ORDER);

         foreach ($scores_brief AS $indx => $score)
         {
            $score = array_map(array($this, '_clearup'), $score);

            $url = Arr::get($score, 1);

            if (strpos($url, 'http://') === FALSE)
            {
               $url = $this->_config['domain'].$url;
            }

            $car_name = Arr::get($score, 2);
            
            $mileage = (int)str_replace(',', '', Arr::get($score, 7));
            $vincode = strtoupper(Arr::get($score, 3));
            $exterior = Arr::get($score, 4);
            $price = $this->_get_car_price_by_VIN($scores_full, $indx, $vincode, $car_name);

            $matches = array(
               'vincode' => $vincode,
               'mileage' => $mileage,
               'color' => $exterior,
               'series' => '',
            );

            if (Filter::factory($filters, $matches)->validate())
            {
               $output[] = array(
                  'date_auction' => $this->_get_auction_date_by_VIN($scores_full, $indx, $vincode, $car_name),
                  'name' => Arr::get($score, 2),
                  'vincode' => $vincode,
                  'mileage' => $mileage,
                  'exterior' => $exterior,
                  'interior' => Arr::get($score, 6),
                  'price' => $price,
                  'url' => $url,
               );
            }
         }

         unset($scores_brief);
         unset($scores_full);

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
      //$content = Remote::factory($url, $this->_remote_options)->execute();

      $parts = explode('?', $url);
      $options = $this->_remote_options;
      $url = $this->_config['search']['url'].'&'.$parts[1];
      $options[CURLOPT_REFERER] = $url;

      $response = Remote::factory($url, $options)->execute();

      if ($response === FALSE)
      {
         $picture_exists = false;
         $content = '';
      } else {
          $content = $this->_clear_from_js($response);
          $picture_exists = preg_match('/<div style="float: left; padding: 0px;">.*<img[^>]*>.*<\/div>/', $content);
      }
     
      if ($picture_exists)
      {
         Filecache::factory()
            ->set_url($url)
            ->set_path(strtoupper($vincode).'-EXPOFCARS')
            ->import($content);
      }
      
      return $picture_exists;
   }

}