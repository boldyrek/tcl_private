<?php defined('SYSPATH') or die('No direct script access.');

class Source_Exporttrader extends Source implements Kohana_Source {

   public function execute()
   {
      if (! $this->_login())
      {
         Kohana::$log->add(Log::ERROR, 'Unauthorized request');
         return;
      }

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

         if (! (bool) preg_match('#Please change you search criteria#i', $response))
         {
            if ((bool) preg_match('#Total units found.*<span[^>]+>(\d+)</span>#i', $response, $matches))
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
            if ($total > ($offset = Arr::get($search, 'offset')))
            {
               $pages = (int) ceil($total/$offset);
            }

            for ($i = 1; $i <= $pages; $i++)
            {
               $response = Remote::factory($url.'&nmbpg='.$i, $this->_remote_options)->execute();

               $items = $this->_parse($response, Arr::get($condition, 'filters'));

               $passed += sizeof($items);

               if (! empty($items))
               {
                  foreach ($items AS $item)
                  {
                     $item['source_id'] = $this->_id;
                     $item['target_id'] = $target_id;
                     $item['search_id'] = $search_id;
                     $item['options'] = $this->_get_options($this, Arr::get($item, 'url'));

                     Jelly::factory('cars')
                     ->set($item)
                     ->save();

                     $cached += $this->_cache(Arr::get($item, 'vincode'), Arr::get($condition, 'mark'), $target_id, Arr::get($condition, 'cache'));
                  }
               }

               unset($items);
            }

            // проводим через фильтр цветов, если цвета заданы
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
      preg_match('#http://.*/carnumber/([A-Z0-9]+)/#U', $url, $match);
      return Arr::get($match, 1);
   }

   protected function _parse($content, array $filters = NULL)
   {
      $content = HTML::cleanup($content);
      
      $output = array();

      $pattern  = '#';
      $pattern .= '<td class=img rowspan=2><a.*><img src=\'(.+)\'.*></a></td>';
      $pattern .= '<td class=bgwt colspan=2 style=\'text-align: left;\'><a href=\'';
      $pattern .= '(.*)'; // url
      $pattern .= '\'[^>]+>';
      $pattern .= '(.*)'; // marl, model, name
      $pattern .= '</a>.*</td>';
      $pattern .= '<td class=bgwt style=\'text-align: right;\'>';
      $pattern .= '(\d+)'; // mileage
      $pattern .= '</td><td class=bglg>';
      $pattern .= '(.+)'; // color
      $pattern .= '</td>';
      $pattern .= '.*<b>(?:Listing Ends:\s|)';
      $pattern .= '(\d{2}/\d{2}/\d{4})'; // auction date
      $pattern .= '</b>.*<td class=bgwt style=\'background: url\("im/auctionslogo/';
      $pattern .= '(?:'.implode('|', array_keys($this->_config['auctions'])).')'; // auction
      $pattern .= '.gif"\) center top no-repeat;\'><img[^>]+></td>';
      $pattern .= '.*<td style=\'text-align: left; padding-left: 3px;\'>';
      $pattern .= '(.*)'; // possible price
      $pattern .= '<span>VIN:&nbsp;';
      $pattern .= '([A-Z0-9]{0,17})'; // VIN
      $pattern .= '</span>.*</td>';
      $pattern .= '#isU';

      try
      {
         preg_match_all($pattern, $content, $scores, PREG_SET_ORDER);

         foreach ($scores AS $score)
         {
            $mileage = (int) Arr::get($score, 4);
            $colors = Arr::get($score, 5);
            $vincode = strtoupper(Arr::get($score, 8));
            $price = 0;
            $url = Arr::get($score, 2);

            if (preg_match('#<div align=right><a [^>]+>Buy&nbsp;now&nbsp;\$(.+)</a>.*</div>#isU', Arr::get($score, 7), $_price))
            {
               $price = Arr::get($_price, 1);
            }

            // количество фильтов в каждом типе должно быть одинаковым!
            $matches = array
            (
               'vincode' => $vincode,
               'mileage' => $mileage,
               'color' => $colors,
               'series' => '',
            );

            if (Filter::factory($filters, $matches)->validate())
            {
               list($exterior, $interior) = explode('/', $colors);

               $output[] = array
               (
                  'date_auction' => date('Y-m-d', strtotime(Arr::get($score, 6))),
                  'name' => Arr::get($score, 3),
                  'vincode' => $vincode,
                  'mileage' => $mileage,
                  'price' => $price,
                  'interior' => $interior,
                  'exterior' => $exterior,
                  'url' => $url,
                  'picture' => ! preg_match('/noimg.jpg/', Arr::get($score, 1))
               );
            }
         }

         unset($scores);

         return $output;
      }
      catch (Kohana_Exception $e)
      {
         throw $e;
      }
   }

   protected function _login()
   {
      if ($this->_is_logged())
         return TRUE;

      $login = Arr::get($this->_config, 'login');

      $login['post_fields']['sendcaptcha'] = $this->_captcha();

      $options = $this->_remote_options + array
      (
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => Arr::get($login, 'post_fields'),
      );

      // request 1
      Remote::factory(Arr::get($login, 'url'), $options)->execute();

      // request 2
      $response = Remote::factory(Arr::get($login, 'url'), $this->_remote_options)->execute();

      return $this->_is_logged($response);
   }

   protected function _is_logged($response = FALSE)
   {
      $login = Arr::get($this->_config, 'login');

      if (! $response)
         $response = Remote::factory(Arr::get($login, 'url'), $this->_remote_options)->execute();

      if ((bool) preg_match('/'.Arr::get($login, 'ident').'/', $response))
      {
         Kohana::$log->add(Log::INFO, 'Authorized request');
         return TRUE;
      }

      return FALSE;
   }
   
   protected function _captcha()
   {
      $captcha = Arr::get($this->_config, 'captcha');

      $response = Remote::factory(Arr::get($captcha, 'url'), $this->_remote_options)->execute();

      $file = Arr::get($captcha, 'image_file');

      $fp = fopen($file, 'w');
      fwrite($fp, $response);
      fclose($fp);

      return Captcha::recognize($file, Arr::get($captcha, 'apikey'));
   }
}