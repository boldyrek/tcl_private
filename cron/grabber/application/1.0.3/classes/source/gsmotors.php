<?php defined('SYSPATH') or die('No direct script access.');

class Source_Gsmotors extends Source implements Kohana_Source {

   public function execute()
   {
      $search = Arr::get($this->_config, 'search');

      foreach (Arr::get($search, 'items') AS $search_id)
      {
         $condition = Kohana::config('search.'.$search_id);

         $target_id = Arr::get($condition, 'parent');

         $options = $this->_remote_options + array
         (
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => http_build_query($search['fields'] + $condition['fields']),
         );

         $response = Remote::factory($search['url'], $options)->execute();

         if ($response == '')
         {
            Kohana::$log->add(Log::ERROR, 'Request returns empty result');
            return;
         }

         $total = 0;
         $pages = 1;

         if (! (bool) preg_match('/По вашему запросу ничего не найдено/', $response))
         {
            if ((bool) preg_match('#<b>Всего по вашему запросу найдено:</b>&nbsp;(\d+)&nbsp;авто#i', $response, $matches))
            {
               $total = (int) Arr::get($matches, 1);
            }
         }

         $filtered = 0;
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
               
               $options = $this->_remote_options + array
               (
                  CURLOPT_POST => TRUE,
                  CURLOPT_POSTFIELDS => http_build_query($search['offset_fields'] + $condition['offset_fields']),
               );

               $response = Remote::factory($search['offset_url'].'&searchResultsOffset='.$page, $options)->execute();

               $urls = $this->_get_urls($response);

               foreach ($urls AS $item_url)
               {
                  $item_url = Arr::get($this->_config, 'url').$item_url;

                  $response = Remote::factory($item_url, $this->_remote_options)->execute();

                  $item = $this->_parse($response, Arr::get($condition, 'filters'));

                  if (! empty($item))
                  {
                     $item['url'] = $item_url;
                     $item['source_id'] = $this->_id;
                     $item['target_id'] = $target_id;
                     $item['search_id'] = $search_id;
                     $item['options'] = $this->_get_options($this, $item_url);

                     Jelly::factory('cars')
                     ->set($item)
                     ->save();

                     $passed++;

                     $cached += $this->_cache(Arr::get($item, 'vincode'), Arr::get($condition, 'mark'), $target_id, Arr::get($condition, 'cache'));
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
      preg_match('#<td[^>]+>Марка:</td><td><span>(.+)</span>#U', $content, $make);
      preg_match('#<td[^>]+>Модель:</td><td><span>(.+)</span>#U', $content, $model);
      preg_match('#<td[^>]+>Модификация:</td><td[^>]+><span>(.+)</span>#U', $content, $modification);
      preg_match('#<strong>Дата и время проведения аукциона:</strong><br />(.+)</td>#U', $content, $date_auction);
      preg_match('#<td[^>]+>Пробег:</td><td><span>(.+)(?:\s+mi)?</span>#U', $content, $mileage);
      preg_match('#<td[^>]+>VIN:</td><td><span>(.+)</span>#U', $content, $vincode);
      preg_match('#<td[^>]+>Цвет кузова:</td><td>(.+)</td>#U', $content, $exterior);

      $pre_price = '';

      if (! preg_match('#<td><br />Купить сейчас на OVE.com.*\$(.*)</td>#U', $content, $price))
      {
         if (! preg_match('#<strong>Прогнозируемая цена:</strong><br />Нет данных</td>#U', $content))
         {
            preg_match('#<strong>Прогнозируемая цена:</strong><br />\$(.*)</td>#U', $content, $price);
            $pre_price = 'MMR ';
         }
      }

      $name = Arr::get($make, 1).' '.Arr::get($model, 1).' '.Arr::get($modification, 1);
      $date_auction = preg_replace('#(\d{2})/(\d{2})/(\d{4})\s\|\s(?:(\d{2}:\d{2})?).*#', '$3-$1-$2 $4', Arr::get($date_auction, 1));
      $vincode = strtoupper(Arr::get($vincode, 1));
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
               'date_auction' => trim($date_auction),
               'name' => $name,
               'vincode' => $vincode,
               'mileage' => $mileage,
               'price' => $pre_price.Arr::get($price, 1),
               'exterior' => $exterior,
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
         preg_match_all('#<td><a href="(.+)"><img[^>]+></a></td>#U', $content, $matches);
         return Arr::get($matches, 1);
      } catch (Kohana_Exception $e) {
         throw $e;
      }
   }

}
