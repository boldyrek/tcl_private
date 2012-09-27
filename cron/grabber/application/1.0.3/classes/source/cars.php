<?php defined('SYSPATH') or die('No direct script access.');

class Source_Cars extends Source implements Kohana_Source {

   public function execute()
   {
      $search = Arr::get($this->_config, 'search');

      foreach (Arr::get($search, 'items') AS $search_id)
      {
         $condition = Kohana::config('search.'.$search_id);

         $target_id = Arr::get($condition, 'parent');

         $url = Arr::get($search, 'url')
         .'?'.http_build_query(Arr::get($search, 'fields'))
         .'&'.http_build_query(Arr::get($condition, 'fields'));

         $response = Remote::factory($url, $this->_remote_options)->execute();

         $total = 0;
         $pages = 1;

         if ($response == '')
         {
            Kohana::$log->add(Log::ERROR, 'Request returns empty result');
            return;
         }

         if ((bool) preg_match('#<span\s+class="headerDataPoint">(\d+)</span>#sU', $response, $matches))
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
               $response = Remote::factory($url.'&rn='.$i*$offset, $this->_remote_options)->execute();

               $urls = $this->_get_urls($response);

               foreach ($urls AS $item_url)
               {
                  parse_str($item_url, $parsed_url);
                  
                  $item_url = Arr::get($this->_config, 'detail_url').Arr::get($parsed_url, 'amp;listingId');

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
      return Arr::get($qs, 'listingId');
   }

   protected function _parse($content, array $filters = NULL)
   {
      $content = HTML::cleanup($content);

      preg_match('#<div class="basicInfo"><h1>(.+)</h1>.*</div>#sU', $content, $title);
      preg_match('#<span class="vehiclePrice">\$(.+)</span>#sU', $content, $price);
      preg_match('#<div class="dataPoint" id="Mileage"><span[^>]+>Mileage:</span><span[^>]+>(.+)</span></div>#sU', $content, $mileage);
      preg_match('#<div class="dataPoint" id="ExteriorColor"><span[^>]+>Exterior Color:</span><span[^>]+>(.+)</span></div>#sU', $content, $exterior);
      preg_match('#<div class="dataPoint" id="InteriorColor"><span[^>]+>Interior Color:</span><span[^>]+>(.+)</span></div>#sU', $content, $interior);
      preg_match('#<div class="dataPoint" id="VIN"><span[^>]+>VIN:</span><span[^>]+>(.+)</span></div>#sU', $content, $vincode);

      $name = trim(strip_tags(Arr::get($title, 1)));
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
               'date_auction' => NULL,
               'name' => $name,
               'vincode' => $vincode,
               'mileage' => $mileage,
               'price' => Arr::get($price, 1),
               'interior' => Arr::get($interior, 1),
               'exterior' => $exterior,
               'picture' => (bool) preg_match('/<div class="thumbnail"[^>]+>/', $content)
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
         preg_match_all('#<div class="YmmHeader"><a.*href="(.*)">.*</a>.*</div>#sU', $content, $matches);
         return Arr::get($matches, 1);
      } catch (Kohana_Exception $e) {
         throw $e;
      }
   }

}