<?php defined('SYSPATH') or die('No direct script access.');

class Source_Avantag extends Source implements Kohana_Source {

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
         $fields = $search['fields']+ $condition['fields'] + array(
            'fromYear' => $car->year_from,
            'toYear' => $car->year_to,
         );

         $options = $this->_remote_options + array
         (
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($fields),
         );
         $response = Remote::factory($search['url'], $options)->execute();

         if ($response == '')
         {
            Kohana::$log->add(Log::ERROR, 'Request returns empty result');
         }
          $response = iconv("Windows-1251","UTF-8",$response);
         $total = 0;
         $pages = 1;
         if (! (bool) preg_match('/По вашему запросу ничего не найдено/', $response))
         {
            if ((bool) preg_match('#<strong>Найдено:</strong>(.+)</div>#', $response, $matches))
            {
               $total = (int) Arr::get($matches, 1);
            }
         }
         $filtered_by_colors = 0;
         $filtered_by_options = 0;
         $added = 0;
         $passed = 0;
         $cached = 0;
         if ($total > 0)
         {
            if ($total > ($offset = $search['offset']))
            {
               $pages = (int) ceil($total/$offset);
            }

            for ($i = 0; $i < $pages; $i++)
            {
               $page = $offset*$i;

               $search['offset_fields']['recordOffset'] = $page;
               
               $offset_fields = $search['fields']+ $condition['fields'] + $search['offset_fields'] + $condition['offset_fields'];

               //$offset_fields['submittedQstr'] .= $car->year_from.'+'.$car->year_to;

               $options = $this->_remote_options + array
               (
                  CURLOPT_POST => TRUE,
                  CURLOPT_POSTFIELDS => http_build_query($offset_fields),
               );

               $response = Remote::factory($search['offset_url'].'&searchResultsOffset='.$page, $options)->execute();
                $response = iconv("Windows-1251","UTF-8",$response);
               $urls = $this->_get_urls($response);

               foreach ($urls AS $item_url)
               {
                  $item_url = $this->_config['url'].$item_url;
                  $cached_item=Jelly::select('cars_cache')->where('url','=',$item_url)->execute()->current();

                  if ($cached_item->url==null)
                  {
                  $response = Remote::factory($item_url, $this->_remote_options)->execute();
                  $response = iconv("Windows-1251","UTF-8",$response);
                  $filters = $condition['filters'] + array(
                     'mileage' => $car->mileage
                  );
                  $item = $this->_parse($response, $filters);
                  }
                   else
                   {
                       $item = Jelly::factory('cars_cache')->get_item_from_cache($cached_item);
                   }

                  if (! empty($item) && !$this->_is_exist_vincode($item['vincode'],$search_id))
                  {
                     $item['url'] = $item_url;
                     $item['source_id'] = $this->_id;
                     $item['target_id'] = $car_id;
                     $item['search_id'] = $search_id;
                     $item['options'] = $this->_get_options($this, $item_url);

                     if ($item['picture']&&$cached_item->url==null)
                     {
                        Filecache::factory()
                           ->set_url($item_url)
                           ->set_path(strtoupper($item['vincode']).'-AVANTAG')
                           ->import($response);
                     }

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
                     $passed++;


                  }
               }

               unset($urls);
            }

            $colors = $this->_get_colors($car_id);

            if (! empty($colors))
            {
               $filtered_by_colors = Jelly::factory('cars')
                  ->color_filter($search_id, $colors);
            }
            
            $filtered_by_options = $this->_cache_options($car_id, $this->_id);

            $added = $passed - ($filtered_by_colors + $filtered_by_options);
         }

         Jelly::factory('statuses')
            ->set(array
            (
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
      return Arr::get($qs, 'car');
   }

   public function parse_options($content)
   {
      $options = $this->_parse_options($content);

      // добавляем в основные опции данные с кастомного парсера
      if (preg_match_all('/('.implode('|', $this->_config['options']).')/iu', $content, $matches))
      {
         $options .= ', '.implode(', ', array_unique(Arr::get($matches, 1)));
      }

      return $options;
   }

   protected function _parse($content, array $filters = NULL)
   {
      preg_match('#<td[^>]*>Марка:</td> <td>(.+)</td>#U', $content, $make);
      preg_match('#<td[^>]*>Модель:</td> <td>(.+)</td>#U', $content, $model);
      preg_match('#<td[^>]*>Модификация:</td> <td>(.+)</td>#U', $content, $modification);
      preg_match('#<th>Время продажи:</th><td>(.+)\(#U', $content, $date_auction);
      preg_match('#<td[^>]*>Пробег:</td> <td>(.+)</td>#U', $content, $mileage);
      preg_match('#<td[^>]*>VIN:</td> <td <a[^>]+>(.+)</a> </tr>#U', $content, $vincode);
      preg_match('#<td[^>]*>Цвет кузова:</td> <td>(.+)</td>#U', $content, $exterior);
      preg_match('#<strong>Местоположение:</strong><br />(.+)\-(.+)<br /><strong>#U', $content, $state);
      $pre_price = '';
        $price = 0;
         if (! preg_match('#<th>Ориентировочная цена:</th><td>Не определена</td>#U', $content))
         {
            preg_match('#<th>Ориентировочная цена:</th><td>\$(.*)\(#U', $content, $price);
            $pre_price = 'MMR ';
            $price = (int)Arr::get($price, 1);
         }

      $name = Arr::get($make, 1).' '.Arr::get($model, 1).' '.Arr::get($modification, 1);
      $date_auction = preg_replace('#(\d{2})\.(\d{2})\.(\d{4})\s(?:(\d{2}:\d{2})?).*#', '$3-$2-$1 $4', Arr::get($date_auction, 1));
      $vincode = strtoupper(Arr::get($vincode, 1));
      $mileage = (int) str_replace(',', '', Arr::get($mileage, 1));
      $exterior = Arr::get($exterior, 1);
      $state = Arr::get($state, 1);


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
               'date_auction' => trim($date_auction),
               'name' => $name,
               'vincode' => $vincode,
               'mileage' => $mileage,
               'price' => $pre_price.$price,
               'exterior' => $exterior,
               'state' => $state,
               'picture' => preg_match('/<img.*id="image"[^>]+>/', $content)
            );
         }
      }
      catch (Kohana_Exception $e)
      {
         throw $e;
      }
   }

   protected function _get_urls($content)
   {
      try {
         preg_match_all('#<h3> <a[^>]* href="(.+)"[^>]*>[^>]*</a>[^>]*</h3>#U', $content, $matches);
         return Arr::get($matches, 1);
      } catch (Kohana_Exception $e) {
         throw $e;
      }
   }

}
