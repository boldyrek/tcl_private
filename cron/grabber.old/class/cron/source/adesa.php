<?php

defined('SYSPATH') or die('No direct script access.');

class Cron_Source_Adesa extends Cron implements Cron_Interface {

   protected $_config;
   protected $_remote_options;

   public function __construct()
   {
      parent::__construct();

      $this->_config = $this->config->source->adesa;
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

      $output = array();

      ob_start();
      echo Remote::factory('http://www.dealerblock.ca/xamsrunlist/searchamsRunlist.jsp', $this->_remote_options);
      ob_end_clean();

      foreach ($this->_config->search->types->toArray() AS $id)
      {
         $types = $this->config->search->types->toArray();
         $type = Arr::get($types, $id);

         $locked = $this->status->insert(array('search_type' => $type['parent'], 'source' => $source_id, 'locked' => 1));

         $url = $this->_config->search->url;

         $models = array();
         foreach ($type['fields']['ml'] AS $model)
         {
            $models[] = 'ml=' . urlencode($model);
         }

         $models = implode('&', $models);

         unset($type['fields']['ml']);

         $fields = http_build_query($this->_config->search->fields->toArray())
                 . '&' . http_build_query(Arr::get($type, 'fields'))
                 . '&' . $models;

         $options = array
         (
            CURLOPT_POST => TRUE,
            CURLOPT_POSTFIELDS => $fields,
            CURLOPT_REFERER => $url,
         );

         $options = $options + $this->_remote_options;

         $response = Remote::factory($url, $options)->execute();

         if (strpos($response, 'Error 500') !== FALSE)
            throw new Exception('Внутренняя ошибка сервера');

         $total = 0;

         if (!(bool) preg_match('/No Records Found/i', $response))
         {
            // определяем количество записей
            if ((bool) preg_match('/<b><font(?:[^>]+)>(\d+)<\/font>&nbsp;Total Vehicles<\/b>/i', $response, $matches))
               $total = (int) Arr::get($matches, 1);
         }

         $passed = 0;
         $filtered = 0;
         $added = 0;
         $new_vins = 0;

         if ($total > 0)
         {
            $items = $this->parse($response, Arr::get($type, 'filters'));

            // Core::debug($items); exit;
            // $output[$id]['items'] = $items;
            $passed += sizeof($items);

            foreach ($items AS $item)
            {
               $item['source'] = $source_id;
               $item['search_type'] = $type['parent'];
               $item['search_id'] = $id;

               $this->car->insert($item);

               // кэшируем VIN-код
               $new_vins += $this->_cache(Arr::get($item, 'vin'), Arr::get($type, 'mark'), $type['parent'], Arr::get($type, 'cache'));
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
            'найдено: ' . $total,
            'прошли первичные фильтры: ' . $passed,
            'не прошли фильтр по цветам: ' . $filtered,
            'добавлено: ' . $added,
            'новых вин-кодов: ' . $new_vins
         );

         $this->logger->log('Тип ' . $id . ' - ' . implode(', ', $output[$id]['totals']), Zend_Log::INFO);

         $where = Database::quoteValues(array('id' => $locked['insert_id']), TRUE);
         $this->status->update(array
         (
            'date_last_updated' => date('Y-m-d H:i:s', (int) microtime(TRUE)),
            'items_added' => $added,
            'locked' => 0,
         ), $where);

         echo $this->_message($source_id, $type['parent'], $output[$id]['totals']);
      }

      // Core::debug($output);
   }

   public function parse($content, array $filters = NULL)
   {
      $content = preg_replace(array('/(\n|\r)+/', '/>\s+</'), array('', '><'), $content);

      $output = array();

      $pattern = '#';
      $pattern .= '<tr(?:\s{2})bgcolor="(?:ffffff|f0f0f0)">';
      $pattern .= '<td align="center" nowrap>.*</td>';
      $pattern .= '<td align="center" >(.*)</td>'; // year
      $pattern .= '<td align="center" >(.*)</td>'; // mark
      $pattern .= '<td align="left">(.*)</td>'; // model
      $pattern .= '<td align="left">(.*)</td>'; // model extras
      $pattern .= '<td align="center">.*</td>';
      $pattern .= '<td align="left">.*</td>';
      $pattern .= '<td align="right">(.*)</td>'; // millage
      $pattern .= '<td align="center">(.*)</td>'; // outside color
      $pattern .= '<td align="center">(.*)</td>'; // VIN
      $pattern .= '<td align="center">(.*)</td>'; // auction date
      $pattern .= '<td align="center">.*</td>';
      $pattern .= '<td nowrap align="center">.*</td>';
      $pattern .= '<td align="center">(.*)</td>'; // url
      $pattern .= '.*';
      $pattern .= '</tr>';
      $pattern .= '#sU';

      try
      {
         preg_match_all($pattern, $content, $scores, PREG_SET_ORDER);

         foreach ($scores AS $score)
         {
            $score = array_map(array($this, '_clearup'), $score);
            
            if (preg_match('#<a\s+.*href=[\'"]([^\'"]+)[\'"].*</a>#sU', Arr::get($score, 9), $url)) // temp. hack
            {
               $url = Arr::get($url, 1);
               $url = (strpos($url, 'http://') === FALSE ? 'http://www.dealerblock.ca'.$url : $url);

               $millage = Arr::get($score, 5);
               $millage = (int) (strpos($millage, 'K') !== FALSE
               ? floor((int) $millage / 1.609344) // km. into ml.
               : (int) $millage);                 // ml. into ml.

               $vincode = Arr::get($score, 7);
               $color = Arr::get($score, 6);

               $matches = array
               (
                  'vincode' => $vincode,
                  'millage' => $millage,
                  'color' => $color,
                  'series' => '',
               );

               if (Filter::factory($filters, $matches)->validate())
               // if ($matches)
               {
                  $output[] = array
                  (
                     'date_added' => date('Y-m-d H:i:s'),
                     'date_auction' => date('Y-m-d', strtotime(Arr::get($score, 8))),
                     'name' => Arr::get($score, 1).' '.Arr::get($score, 2).' ' .Arr::get($score, 3).' '.Arr::get($score, 4),
                     'vin' => $vincode,
                     'millage' => $millage,
                     'color_outside_origin' => $color,
                     'url' => $url
                  );
               }
            }
         }
      }
      catch (Exception $e)
      {
         throw $e;
      }

      return $output;
   }

   protected function _clearup($str)
   {
      return trim(str_replace(array('&nbsp;'), ' ', $str));
      // return trim($str);
   }

   public function login()
   {
      if ($this->is_logged())
         return TRUE;

      $login = $this->_config->login->toArray();

      $options = array
         (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => http_build_query($login['post_fields']),
      );

      $options = $this->_remote_options + $options;

      Remote::factory($login['url'], $options)->execute();

      $this->logger->log('Авторизация: запрос №1 прошел', Zend_Log::INFO);

      $response = Remote::factory($login['redirect'], $this->_remote_options)->execute();

      $this->logger->log('Авторизация: запрос №2 прошел', Zend_Log::INFO);

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