<?php defined('SYSPATH') or die('No direct script access.');

class Cron_Source_Ebay extends Cron implements Cron_Interface {
   
   protected $_config;
   protected $_remote_options;
   
   public function __construct()
   {
      parent::__construct();
      
      $this->_config = $this->config->source->ebay;
      $this->_remote_options = $this->config->core->remote->toArray() + $this->_config->remote->toArray();
   }
   
   public function execute($source_id)
   {
      $output = array();

      foreach ($this->_config->search->types->toArray() AS $id)
      { 
         $types = $this->config->search->types->toArray(); 
         $type = Arr::get($types, $id);
         
         $locked = $this->status->insert(array('search_type' => $type['parent'], 'source' => $source_id, 'locked' => 1));
         
         $url = $this->_config->search->url
         .'?'.http_build_query($this->_config->search->fields->toArray())
         .'&'.http_build_query(Arr::get($type, 'fields'));

         // Core::debug($url); exit;

         $response = Remote::factory($url, $this->_remote_options)->execute();
         
         if ($response == '')
            throw new Exception('Запрос вернул пустой результат');
            
         $total = 0;
         $pages = 1;
         
         if (! (bool) preg_match('#Your search returned <b>0 items</b>#i', $response))
         {
            if ((bool) preg_match("#<span class='countClass'>(\d+)</span>#i", $response, $matches))
            {
               $total = (int) Arr::get($matches, 1);
            }
         }

         $filtered = 0;
         $added = 0;
         $passed = 0;
         $new_vins = 0;
         
         if ($total > 0)
         {
            if ($total > $this->_config->search->per_page)
            {
               $pages = (int) ceil($total/$this->_config->search->per_page);
            }

            for ($i = 1; $i <= $pages; $i++)
            {
               $response = Remote::factory($url.'&_pgn='.$i, $this->_remote_options)->execute();
               
               $urls = $this->get_urls($response);

               foreach ($urls AS $url)
               {
                  $response = Remote::factory($url, $this->_remote_options)->execute();
                  
                  $item = $this->parse($response, Arr::get($type, 'filters'));

                  if (! empty($item))
                  {
                     $item['url'] = $url;
                     $item['source'] = $source_id;
                     $item['search_type'] = $type['parent'];
                     $item['search_id'] = $id;

                     $this->car->insert($item);

                     $passed++;

                     $new_vins += $this->_cache(Arr::get($item, 'vin'), Arr::get($type, 'mark'), $type['parent'], Arr::get($type, 'cache'));
                  }
               }
            }

            if (($color = Arr::get($type, 'colors')) !== NULL)
            {
               $colors = $this->config->colors->toArray();
               // $filtered = $this->car->color_filter($type['parent'], Arr::get($colors, $color));
               $filtered = $this->car->color_filter($id, Arr::get($colors, $color));
            }
            
            $added = $passed - $filtered;
         }
         
         $output[$id]['totals'] = array
         (
            'найдено: '.$total,
            'прошли первичные фильтры: '.$passed,
            'не прошли фильтр по цветам: '.$filtered,
            'добавлено: '.$added,
            'новых вин-кодов: '.$new_vins
         );
         
         $this->logger->log('Тип '.$id.' - '.implode(', ', $output[$id]['totals']), Zend_Log::INFO);
         
         $where = Database::quoteValues(array('id' => $locked['insert_id']), TRUE);
         $this->status->update(array
         (
            'date_last_updated' => date('Y-m-d H:i:s',(int) microtime(TRUE)),
            'items_added' => $added,
            'locked' => 0,
         ), $where);
         
         echo $this->_message($source_id, $type['parent'], $output[$id]['totals']);
      }
   }
   
   protected function get_urls($content)
   {
      $content = preg_replace('/(\n|\r)+/', '', $content);
      $output = array();
      $pattern  = '#<div class="ttl">(?:.*)<a href="(.+)"[^>]+>(?:.+)</a></div>#sU';
      
      try {
         preg_match_all($pattern, $content, $scores, PREG_SET_ORDER);
         
         foreach ($scores AS $score)
         {
            $output[] = $score[1];
         }
      } catch (Exception $e) {
         throw $e;
      }
      
      return $output;
   }

   public function parse($content, array $filters = NULL)
   {
      $content = preg_replace('/(\n|\r)+/', '', $content);
      
      $output = array();
      
      try {
         preg_match('#<h1 class="vi-it-itHd">(.+)</h1>#isU', $content, $title);
         preg_match('#<h2 class="vi-it-itSbHd">(.+)</h2>#isU', $content, $description);
         preg_match('#<span class="vi-is1-prcp"><span [^>]+>US \$(.+)</span></span>#isU', $content, $price);
         preg_match('#<span>\((.+)</span><span class="vi-is1-t">.+\)</span>#isU', $content, $date);
         preg_match('#<span id="vhrisl"> ([A-Z0-9]{0,17}) \|.+</span>#isU', $content, $vincode);
         preg_match('#<th [^>]+>Mileage: </th><td [^>]+>(.+) miles</td>#isU', $content, $millage);
         preg_match('#<th [^>]+>Exterior color: </th><td [^>]+>(.+)</td>#isU', $content, $outside);
         preg_match('#<th [^>]+>Interior color: </th><td [^>]+>(.+)</td>#isU', $content, $inside);

         $title = strip_tags(Arr::get($title, 1));
         $description = preg_replace('#<.+>(.+)</.+>#', '', Arr::get($description, 1));
         $date = (Arr::get($date, 1) !== NULL) ? date('Y-m-d', strtotime($date[1])) : NULL;
         $vincode = strtoupper(Arr::get($vincode, 1));
         $millage = (int) str_replace(',', '', Arr::get($millage, 1));
         $price = (int) str_replace(',', '', Arr::get($price, 1));
         $outside = Arr::get($outside, 1);
         $inside = Arr::get($inside, 1);

         $name = $title.': '.$description;

         // return array($name, $date, $vincode, $millage, $outside, $inside); exit;
         
         $matches = array
         (
            'vincode' => $vincode,
            'millage' => $millage,
            'color'   => $outside,
            'series' => $name,
         );

         if (Filter::factory($filters, $matches)->validate())
         // if ($matches)
         {
            $output = array
            (
               'date_added' => date('Y-m-d H:i:s'),
               'date_auction' => $date,
               'name' => $name,
               'vin' => $vincode,
               'millage' => $millage,
               'price' => $price,
               'color_inside_origin' => $inside,
               'color_outside_origin' => $outside,
            );
         }
      } catch (Exception $e) {
         throw $e;
      }
      
      return $output;
   }

   public function login()
   {
      return TRUE;
   }
   
   public function is_logged($response = NULL)
   {
      return TRUE;      
   }
}