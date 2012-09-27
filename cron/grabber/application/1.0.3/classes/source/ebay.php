<?php defined('SYSPATH') or die('No direct script access.');

class Source_Ebay extends Source implements Kohana_Source {
   
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
         
         if ($response == '')
         {
            Kohana::$log->add(Log::ERROR, 'Request returns empty result');
            return;
         }
            
         $total = 0;
         $pages = 1;
         
         if (! (bool) preg_match('#Your search returned <b>0 items</b>#i', $response))
         {
            if ((bool) preg_match("#<span class='countClass'>(\d+)</span>#i", $response, $matches))
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

            for ($i = 1; $i <= $pages; $i++)
            {
               $response = Remote::factory($url.'&_pgn='.$i, $this->_remote_options)->execute();
               
               $urls = $this->_get_urls($response);

               foreach ($urls AS $item_url)
               {
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
      $url = parse_url($url);
      $url = trim(Arr::get($url, 'path'), '/');
      $url = explode('/', $url);
      return Arr::get($url, 2);
   }
   
   protected function _get_urls($content)
   {
      try {
         preg_match_all('#<div class="ttl">.*<a href="(.+)"[^>]+>.+</a>.*</div>#sU', HTML::cleanup($content), $matches);
         return Arr::get($matches, 1);
      } catch (Kohaan_Exception $e) {
         throw $e;
      }
   }

   protected function _parse($content, array $filters = NULL)
   {
      try {
         preg_match('#<h1 class="vi-it-itHd">(.+)</h1>#isU', $content, $title);
         preg_match('#<h2 class="vi-it-itSbHd">(.+)</h2>#isU', $content, $description);
         preg_match('#<span.*class="vi-is1-prcp">US \$(.+)</span>#isU', $content, $price);
         preg_match('#<span>\((.+)</span><span class="vi-is1-t">.+\)</span>#isU', $content, $date);
         preg_match('#<span id="vhrisl"> ([A-Z0-9]{0,17}) \|.+</span>#isU', $content, $vincode);
         preg_match('#<th [^>]+>Mileage: </th><td [^>]+>(.+) miles</td>#isU', $content, $mileage);
         preg_match('#<th [^>]+>Exterior color: </th><td [^>]+>(.+)</td>#isU', $content, $exterior);
         preg_match('#<th [^>]+>Interior color: </th><td [^>]+>(.+)</td>#isU', $content, $interior);

         $title = strip_tags(Arr::get($title, 1));
         $description = preg_replace('#<.+>(.+)</.+>#', '', Arr::get($description, 1));
         $vincode = strtoupper(Arr::get($vincode, 1));
         $mileage = (int) str_replace(',', '', Arr::get($mileage, 1));
         $exterior = Arr::get($exterior, 1);

         $name = $title.': '.$description;

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
               'date_auction' => (Arr::get($date, 1) !== NULL) ? date('Y-m-d', strtotime($date[1])) : NULL,
               'name' => $name,
               'vincode' => $vincode,
               'mileage' => $mileage,
               'price' => Arr::get($price, 1),
               'interior' => Arr::get($interior, 1),
               'exterior' => $exterior,
               'picture' => ! preg_match('/<div class="noimg">/', $content)
            );
         }
      } catch (Kohana_Exception $e) {
         throw $e;
      }
   }

}