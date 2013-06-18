<?php defined('SYSPATH') or die('No direct script access.');

class Source_Oodle extends Source implements Kohana_Source {
   protected $id_urls = array();

   public function execute()
   {           
      $search = $this->_config['search'];
      $this->id_urls = array();

      foreach ($this->_get_active_items($search['items']) AS $search_id)
      {          
         $condition = Kohana::config('search.'.$search_id);

         $car_id = $condition['parent'];

         $car = Jelly::select('admin_cars')
         ->where(':primary_key', '=', $car_id)
         ->execute()
         ->current();

         $max_year = intval(date("Y"));
         $min_year = 1900;
         
         if (trim($car->year_from) != '' && trim($car->year_to) != '') {
             if (trim($car->year_from) == '' || intval($car->year_from) < $min_year) {
                 $car->year_from = '';
             } else {
                 $car->year_from = intval($car->year_from);
             }
             
             if (trim($car->year_to) == '' || intval($car->year_to) > $max_year) {
                 $car->year_to = '';
             } else {
                 $car->year_to = intval($car->year_to);
             }
             
             if ($car->year_to == '' && $car->year_from == '') {
                 $year_str = '';
             } else {
                 if ($car->year_to == '') {
                     $year_str = 'year_'.$car->year_from.'+';
                 } elseif ($car->year_from == '') {
                     $year_str = 'year_'.$car->year_to.'-';
                 } else {
                     if ($car->year_from == $car->year_to) {
                         $year_str = 'year_'.$car->year_from;
                     } else {
                         $year_str = 'year_'.$car->year_from.'_'.$car->year_to;
                     }
                 }
             }
         }
         
         if (trim($car->mileage) == '' || intval($car->mileage) <= 0) {
             $mileage_str = '';
         } else {
             $mileage_str = 'mileage_'.intval($car->mileage).'+';
         }
         
         $attr_str = $search['fields']['attributes'];
         if ($year_str != '') {
             $attr_str = $attr_str.($attr_str != '' ? ',' : '').$year_str;
         }
         if ($mileage_str != '') {
             $attr_str = $attr_str.($attr_str != '' ? ',' : '').$mileage_str;
         }
         foreach ($condition['fields'] as $key => $attr) {
             if ($attr != '') {
                 $attr_str = $attr_str.($attr_str != '' ? ',' : '').$key.'_'.$attr;
             }
         }

         $params_str = '';
         foreach ($search['fields'] as $key => $attr) {
             if ($key == 'attributes') $attr = $attr_str;
             if ($attr != '') {
                 $params_str = $params_str.($params_str != '' ? '&' : '').$key.'='.$attr;
             }
         }
         
         $second_cnt = -1;
         $offset_cnt = 0;

         while (true) {
             $fields_url = $params_str.'&start='.($offset_cnt*$search['offset'] + 1).'&num='.$search['offset'];

             $options = $this->_remote_options;
             $options[CURLOPT_POST] = FALSE;

             $request_url     = $search['url'].'?'.$fields_url;
             $request_options = $options;

             $response = Remote::factory($request_url, $request_options)->execute();

             if ($response === FALSE)
             {
                Kohana::$log->add(Log::ERROR, '500 Internal Server Error');
                return;
             }

             $response_basic = @unserialize($response);

             if ($response_basic === false) {
                 $response_basic = array();
             }

             if (isset($response_basic['stat']) && $response_basic['stat'] != 'ok') {
                 if (isset($response_basic['error'])) {
                     Kohana::$log->add(Log::ERROR, $response_basic['error']);
                 } else {
                     Kohana::$log->add(Log::ERROR, '500 Internal Server Error');
                 }
                 return;
             }

             $total = 0;
             if (isset($response_basic['meta'])) {
                 $total = intval($response_basic['meta']['total']);
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

                $items = $this->_parse($response_basic, $filters);
				
                $passed += sizeof($items);

                if (! empty($items))
                {
                   foreach ($items AS $item)
                   {
                        $this->id_urls[$item['url']] = $item['car_id'];
                        $cached_item = Jelly::select('cars_cache')->where('url','=',$item['url'])->execute()->current();

                        if ($cached_item->url == null) {
                           $item['source_id'] = $this->_id;
                           $item['target_id'] = $car_id;
                           $item['search_id'] = $search_id;
                           $item['options']   = $this->_get_options($this, $item['url']);
                           $item['picture']   = $this->_picture_exists($item);
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
     if (isset($this->id_urls[$url])) {
         $id = $this->id_urls[$url];
         if ($id != '') return $this->id_urls[$url];
     }

     $parts = explode('/', $url);
     if (isset($parts[4])) {
         $parts = explode('-', $parts[4]);
         return $parts[0];
     } else {
         md5($url);
     }
   }

   protected function _parse(&$content_brief, array $filters = NULL)
   {     
      $output = array();
      
      try
      {
         foreach ($content_brief['listings'] AS $indx => $score)
         {
            $url = $this->_clearup($score['url']);

            if (strpos($url, 'http://') === FALSE)
            {
               $url = $this->_config['domain'].$url;
            }

            if (!isset($score['title']) || !isset($score['attributes']['vin'])) continue;

            $car_name = $this->_clearup($score['title']);
            $vincode = $this->_clearup($score['attributes']['vin']);

            if (isset($score['id'])) {
                $car_id = $this->_clearup($score['id']);
            } else {
                $car_id = '';
            }

            if (isset($score['attributes']['color'])) {
                $mileage = $this->_clearup($score['attributes']['mileage']);
            } else {
                $mileage = 0;
            }
            if (isset($score['attributes']['color'])) {
                $exterior = $this->_clearup($score['attributes']['color']);
            } else {
                $exterior = '';
            }
            if (isset($score['attributes']['price']) && isset($score['attributes']['currency']) && strtolower($score['attributes']['currency']) == 'usd') {
                $price = $this->_clearup($score['attributes']['price']);
            } else {
                $price = '';
            }
            if (isset($score['attributes']['has_photo'])) {
                $has_photo = strtolower($this->_clearup($score['attributes']['has_photo'])) != 'no';
            } else {
                $has_photo = false;
            }

            $matches = array(
               'vincode' => $vincode,
               'mileage' => $mileage,
               'color' => $exterior,
			   'price' => $price,
               'series' => '',
            );

            if (Filter::factory($filters, $matches)->validate())
            {
               $output[] = array(
                  'date_auction' => '',
                  'name' => $car_name,
                  'vincode' => $vincode,
                  'mileage' => $mileage,
                  'exterior' => $exterior,
                  'interior' => '',
                  'price' => $price,
                  'url' => $url,
                  'has_photo' => $has_photo,
                  'car_id' => $car_id
               );
            }
         }

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

   protected function _picture_exists($car_rec)
   {
      $url     = $car_rec['url'];
      $vincode = $car_rec['vincode'];

      if ($car_rec['has_photo']) {
          $options = $this->_remote_options;
          $options[CURLOPT_REFERER] = 'http://cars.oodle.com/';//$this->_config['domain'];
          $options[CURLOPT_POST] = FALSE;

          $response = Remote::factory($url, $options)->execute(true);

          if ($response === FALSE)
          {
              $picture_exists = false;
              $response = '';
          } else {
              $picture_exists = true;
          }

          if ($picture_exists)
          {
              Filecache::factory()
                ->set_url($url)
                ->set_path(strtoupper($vincode).'-OODLE')
                ->import($response, true);
          }
          return $picture_exists;
      } else {
          return false;
      }
      

   }

}