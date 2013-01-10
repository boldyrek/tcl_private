<?php defined('SYSPATH') or die('No direct script access.');

class Source_Avtobest extends Source implements Kohana_Source {

   public function execute()
   {
      $search = Arr::get($this->_config, 'search');

      foreach (Arr::get($search, 'items') AS $search_id)
      {
         $condition = Kohana::config('search.'.$search_id);

         $target_id = Arr::get($condition, 'parent');

         $url = Arr::get($search, 'url').'?'.http_build_query(Arr::get($condition, 'fields'));

         $response = Remote::factory($url, $this->_remote_options)->execute();

         $total = 0;
         $pages = 1;

         if ($response == '')
         {
            Kohana::$log->add(Log::ERROR, 'Request returns empty result');
            return;
         }

         if ((bool) preg_match('/Просмотр.*из\s+(\d+)/', $response, $matches))
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
               $response = Remote::factory($url.'&page='.$i, $this->_remote_options)->execute();

               $urls = $this->_get_urls($response);

               foreach ($urls AS $item_url)
               {
                  parse_str($item_url, $parsed_url);
                  
                  $item_url = $url.'&lot='.Arr::get($parsed_url, 'amp;lot');

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
      return Arr::get($qs, 'lot');
   }

   public function parse_options($content)
   {
      $options = $this->_parse_options($content);

      if (preg_match_all('/('.implode('|', $this->_config['options']).')/iu', $content, $matches))
      {
         $options .= ', '.implode(', ', array_unique(Arr::get($matches, 1)));
      }

      return $options;
   }

   protected function _parse($content, array $filters = NULL)
   {
      $content = HTML::cleanup($content);

      preg_match('#<h2 align="center">(.+)</h2>#sU', $content, $name);
      preg_match('#<tr class="(?:light1|white)"><td>Дата продажи</td><td>(\d{2}/\d{2}/\d{4}).*</td></tr>#sU', $content, $date);
      preg_match('#<tr class="(?:light1|white)"><td>Прогнозируемая цена продажи</td><td>(.*)</td></tr>#sU', $content, $price);
      preg_match('#<tr class="(?:light1|white)"><td>Пробег</td><td>(.*)(?:\s+mi)?</td></tr>#sU', $content, $mileage);
      preg_match('#<tr class="(?:light1|white)"><td>Внешний цвет</td><td>(.*)</td></tr>#U', $content, $exterior);
      preg_match('#<tr class="(?:light1|white)"><td>Цвет интерьер</td><td>(.*)</td></tr>#U', $content, $interior);
      preg_match('#<tr class="(?:light1|white)"><td>VIN номер</td><td>(.*)</td></tr>#U', $content, $vincode);

      $name = Arr::get($name, 1);
      $date_auction = (Arr::get($date, 1) ? date('Y-m-d', strtotime($date[1])) : NULL);
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
               'date_auction' => $date_auction,
               'name' => $name,
               'vincode' => $vincode,
               'mileage' => $mileage,
               'price' => Arr::get($price, 1),
               'interior' => Arr::get($interior, 1),
               'exterior' => $exterior,
               'picture' => preg_match('/<img.*id="imgLarge"[^>]+>/', $content)
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
         preg_match_all('#<a href="(.+)" title="Детально" target="_blank"><img src="/.templates/img/details.gif" style=\'cursor:pointer\' alt=""></a>#U', $content, $matches);
         return Arr::get($matches, 1);
      } catch (Kohana_Exception $e) {
         throw $e;
      }
   }

}