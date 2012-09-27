<?php

defined('SYSPATH') or die('No direct script access.');

class Cron_Source_Openlane extends Cron implements Cron_Interface {

   protected $_config;
   protected $_remote_options;

   public function __construct()
   {
      parent::__construct();

      $this->_config = $this->config->source->openlane;
      $this->_remote_options = $this->config->core->remote->toArray() + $this->_config->remote->toArray();
   }

   public function execute($source_id)
   {
      if (! $this->login())
      {
         $message = get_class($this).'Запрос не авторизован';
         $this->logger->log($message, Zend_Log::INFO);
         echo $message;
         return;
      }

      foreach ($this->_config->search->types->toArray() AS $id)
      {
         $types = $this->config->search->types->toArray();
         $type = Arr::get($types, $id);

         $locked = $this->status->insert(array('search_type' => $type['parent'], 'source' => $source_id, 'locked' => 1));

         $url = $this->_config->search->url.Arr::get($type, 'search_id');

         $response = Remote::factory($url, $this->_remote_options)->execute();

         $total = 0;

         if ((bool) preg_match('#<span style="font-weight:bold;">(.+)&nbsp; Results Found</span>#isU', $response, $matches))
         {
            $total = (int) Arr::get($matches, 1);
         }

         $passed = 0;
         $filtered = 0;
         $added = 0;
         $new_vins = 0;

         if ($total > 0)
         {
            $items = $this->parse($response, Arr::get($type, 'filters'));

            $passed += sizeof($items);
            
            foreach ($items AS $item)
            {
               $item['source'] = $source_id;
               $item['search_type'] = $type['parent'];
               $item['search_id'] = $id;

               $this->car->insert($item);

               $new_vins += $this->_cache(Arr::get($item, 'vin'), Arr::get($type, 'mark'), $type['parent'], Arr::get($type, 'cache'));
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

      $pattern  = '#<tr class="searchResultTitle">.*';
      $pattern .= '<span class="ymms">.*<a\s+.*href="(.+)">(.+)</a>.*</span>'; // link, name
      $pattern .= '.*</tr>';

      $pattern .= '.*<tr class="searchResultTitle subtitle">.*<td colspan="9">.*';
      $pattern .= '([A-Z0-9]{17}).*Ext: (.+)&nbsp;.*Int: (.+)&nbsp;'; // VIN & colors
      $pattern .= '.*</td>.*</tr>';

      $pattern .= '.*<tr class="search_records">';
      $pattern .= '.*<td valign="top">.*';
      $pattern .= '(\d+,\d+) mi'; // milage
      $pattern .= '.*</td>';


      $pattern .= '.*<td valign="top">\s+';
      $pattern .= '\$(.+)'; // price
      $pattern .= '<br/>.*&nbsp;\s+</td>';

      $pattern .= '.*<td class="lastcol" valign="top">.*';
      $pattern .= '(\d+h \d+m)'; // time left
      $pattern .= '.*</td>.*';

      $pattern .= '.*</tr>';
      $pattern .= '#isU';

      try {
         preg_match_all($pattern, $content, $matches, PREG_SET_ORDER);

         foreach ($matches AS $item)
         {
            $name = Arr::get($item, 2, 'unknown');
            $url = 'https://www.openlane.com'.Arr::get($item, 1);
            $vincode = strtoupper(Arr::get($item, 3));
            $millage = (int) str_replace(',', '', Arr::get($item, 6));
            $price = (int) str_replace(',', '', Arr::get($item, 7));
            $outside = trim(Arr::get($item, 4, ''));
            $inside = trim(Arr::get($item, 5, ''));

            $date_auction = preg_replace('/(\d+)h (\d+)m/', '$1 hours $2 minutes', Arr::get($item, 8));
            $date_auction = date('Y-m-d H:i', strtotime($date_auction));

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
               $output[] = array
               (
                  'date_added' => date('Y-m-d H:i:s'),
                  'date_auction' => $date_auction,
                  'name' => $name,
                  'vin' => $vincode,
                  'millage' => $millage,
                  'price' => $price,
                  'color_outside_origin' => $outside,
                  'color_inside_origin' => $inside,
                  'url' => $url
               );
            }
         }
      } catch (Exception $e) {
         throw $e;
      }

      return $output;
   }

   public function login()
   {
      if ($this->is_logged())
         return TRUE;

      $login = $this->_config->login->toArray();

      $options1 = array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => http_build_query($login['post_fields1']),
         CURLOPT_REFERER => $login['referer1'],
      );

      $options1 = $this->_remote_options + $options1;
      Remote::factory($login['url1'], $options1)->execute(TRUE);

      $options2 = array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => http_build_query($login['post_fields2']),
         CURLOPT_REFERER => $login['referer2'],
         // CURLOPT_FOLLOWLOCATION => TRUE,
      );

      $options2 = $this->_remote_options + $options2;
      Remote::factory($login['url2'], $options2)->execute(TRUE);

      $options3 = array
      (
         CURLOPT_REFERER => $login['referer3'],
         // CURLOPT_FOLLOWLOCATION => TRUE,
      );

      $options3 = $this->_remote_options + $options3;
      Remote::factory($login['url3'], $options3)->execute(TRUE);

      $options4 = array
      (
         CURLOPT_REFERER => $login['referer4'],
      );

      $options4 = $this->_remote_options + $options4;
      $response = Remote::factory($login['url4'], $options4)->execute(TRUE);

      return $this->is_logged($response);
   }

   public function is_logged($response = NULL)
   {
      if ($response === NULL)
      {
         $response = Remote::factory($this->_config->login->redirect, $this->_remote_options)->execute();
      }

      if ((bool) preg_match('/' . $this->_config->login->ident . '/', $response))
      {
         $this->logger->log('Запрос авторизован', Zend_Log::INFO);
         return TRUE;
      }

      return FALSE;
   }

}