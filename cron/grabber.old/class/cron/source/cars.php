<?php defined('SYSPATH') or die('No direct script access.');

class Cron_Source_Cars extends Cron implements Cron_Interface {

   protected $_config;
   protected $_remote_options;

   public function __construct()
   {
      parent::__construct();

      $this->_config = $this->config->source->cars;
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

         $response = Remote::factory($url, $this->_remote_options)->execute();

         $total = 0;
         $pages = 1;

         if ($response == '')
         {
            $message = 'Запрос вернул пустой результат';
            $this->logger->log($message, Zend_Log::INFO);
            echo $message;
            return;
         }

         if ((bool) preg_match('#<span\s+class="headerDataPoint">(\d+)</span>#sU', $response, $matches))
         {
            $total = (int) Arr::get($matches, 1);
         }

         $filtered = 0;
         $added = 0;
         $passed = 0;
         $new_vins = 0;

         if ($total > 0)
         {
            if ($total > ($offset=$this->_config->search->offset))
            {
               $pages = (int) ceil($total/$offset);
            }

            for ($i = 0; $i < $pages; $i++)
            {
               $response = Remote::factory($url.'&rn='.$i*$offset, $this->_remote_options)->execute();

               $urls = $this->get_urls($response);

               foreach ($urls AS $item_url)
               {
                  parse_str($item_url, $parsed_url);
                  $item_url = 'http://www.cars.com/go/search/detail.jsp?listingId='.$parsed_url['amp;listingId'];

                  $response = Remote::factory($item_url, $this->_remote_options)->execute();

                  $item = $this->parse($response, Arr::get($type, 'filters'));

                  if (! empty($item))
                  {
                     $parent = Arr::get($type, 'parent');

                     $item['url'] = $item_url;
                     $item['source'] = $source_id;
                     $item['search_type'] = $parent;
                     $item['search_id'] = $id;

                     $this->car->insert($item);

                     $passed++;

                     $new_vins += $this->_cache(Arr::get($item, 'vin'), Arr::get($type, 'mark'), $parent, Arr::get($type, 'cache'));
                  }
               }
            }

            if (($color = Arr::get($type, 'colors')) !== NULL)
            {
               $colors = $this->config->colors->toArray();
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

   public function parse($content, array $filters = NULL)
   {
      $content = $this->_clean($content);

      $output = array();

      preg_match('#<div class="basicInfo"><h1>(.+)</h1>.*</div>#sU', $content, $title);
      preg_match('#<span class="vehiclePrice">\$(.+)</span>#sU', $content, $price);
      preg_match('#<div class="dataPoint" id="Mileage"><span[^>]+>Mileage:</span><span[^>]+>(.+)</span></div>#sU', $content, $mileage);
      preg_match('#<div class="dataPoint" id="ExteriorColor"><span[^>]+>Exterior Color:</span><span[^>]+>(.+)</span></div>#sU', $content, $outside);
      preg_match('#<div class="dataPoint" id="InteriorColor"><span[^>]+>Interior Color:</span><span[^>]+>(.+)</span></div>#sU', $content, $inside);
      preg_match('#<div class="dataPoint" id="VIN"><span[^>]+>VIN:</span><span[^>]+>(.+)</span></div>#sU', $content, $vincode);

      $name = trim(strip_tags(Arr::get($title, 1)));
      $vincode = trim(strtoupper(Arr::get($vincode, 1)));
      $price = (int) str_replace(',', '', Arr::get($price, 1));
      $mileage = (int) str_replace(',', '', Arr::get($mileage, 1));
      $outside = Arr::get($outside, 1);
      $inside = Arr::get($inside, 1);

      try
      {
         $matches = array
         (
            'vincode' => $vincode,
            'millage' => $mileage,
            'color'   => $outside,
            'series' => $name,
         );

         if (Filter::factory($filters, $matches)->validate())
         // if ($matches)
         {
            $output = array
            (
               'date_added' => date('Y-m-d H:i:s'),
               'date_auction' => NULL,
               'name' => $name,
               'vin' => $vincode,
               'millage' => $mileage,
               'price' => $price,
               'color_inside_origin' => $inside,
               'color_outside_origin' => $outside,
            );
         }
      }
      catch (Exception $e)
      {
         throw $e;
      }

      return $output;
   }

   protected function get_urls($content)
   {
      try {
         preg_match_all('#<div class="YmmHeader"><a.*href="(.*)">.*</a>.*</div>#sU', $content, $matches);
      } catch (Exception $e) {
         throw $e;
      }

      return Arr::get($matches, 1);
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