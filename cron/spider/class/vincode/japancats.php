<?php defined('SYSPATH') or die('No direct script access.');

class Vincode_Japancats extends Vincode {

   protected $_made_identifiers = array
   (
      'Дата выпуска',
      'ДАТА ИЗГОТОВЛЕНИЯ'
   );

   protected $_body_identifiers = array
   (
      'КОД ЦВЕТА',
      'Код цвета кузова',
      'Код внешней отделки',
      'Цвет',
      'Код цвета'
   );

   protected $_interior_identifiers = array
   (
      'КОД ЦВЕТА ОБИВКИ',
      'Код отделки',
      'Код внутренней отделки'
   );

   public function  __construct()
   {
      parent::__construct();
   }

   protected function _prepare_request($values = NULL, $referer = NULL)
   {
      $options = array();

      if (NULL !== $values)
      {
         $options[CURLOPT_POST] = TRUE;
         $options[CURLOPT_POSTFIELDS] = $values;
      }

      if (NULL !== $referer)
         $options[CURLOPT_REFERER] = $referer;

      return ($options + $this->_remote_options);
   }

   public function get($vincode, $mark = NULL)
   {
      $logger = Zend_Registry::get('logger');

      $config = Zend_Registry::get('config')->vincode->toArray();

      $mark = Arr::get($config['marks'][$mark], 'mark', $mark);

      $url = $config['url'];

      if ($mark != 'Mercedes')
      {
         $values = strtr($config['marks'][$mark]['values'], array(':VINCODE' => $vincode));

         $options = $this->_prepare_request($values, $mark.'/');

         $result = Remote::factory($url.$mark.'/Default.aspx', $options)->execute();

         if (! $result)
         {
            return FALSE;
         }

         $response = $this->_parse_response($result);

         if (! isset($response['headers']))
         {
            return FALSE;
         }

         $location = Arr::get($response['headers'], 'Location');

         if ($location)
         {
            $options = $this->_prepare_request(NULL, $url.$mark.'/');

            $result = Remote::factory($url.ltrim($location, '/'), $options)->execute();

            $response = $this->_parse_response($result);

            // TODO
            if ($mark == 'Suzuki')
            {
               /*
               $match = preg_match_all('/<table id="ctl00_cphMasterPage_[^>]*>(.*)<\/table>/sU', $response['body'], $matches);

               if (! (bool) $match)
                  return FALSE;

               preg_match('/<td>([A-Z0-9]{3})(?:\s+.*)<\/td>/sU', $matches[1][0], $outside);
               preg_match('/<td>([A-Z0-9]{3})<\/td>/sU', $matches[1][1], $inside);

               return array
               (
                  'date_made' => '',
                  'color_inside' => Arr::get($inside, 1),
                  'color_outside' => Arr::get($outside, 1)
               );
               */

               return FALSE;
            }
            else
            {
               preg_match('#<table id="cphMasterPage_tblComplectation"[^>]*>(.*)</table>#sU', $response['body'], $matches);

               if (Arr::get($matches, 1) === NULL)
               {
                  return FALSE;
               }

               $content = preg_replace('/(\n|\r)+/', '', $matches[1]);
               $content = preg_replace('/>\s+</', '><', $content);

               preg_match('#<td>(?:'.implode('|', $this->_made_identifiers).')</td><td[^>]*></td><td><b>(.*)</b></td>#U', $content, $date);
               preg_match('#<td>(?:'.implode('|', $this->_body_identifiers).')</td><td[^>]*></td><td><b>(.*)</b></td>#U', $content, $body);
               preg_match('#<td>(?:'.implode('|', $this->_interior_identifiers).')</td><td[^>]*></td><td><b>(.*)</b></td>#U', $content, $interior);

               $data = array
               (
                  'date_made' => Arr::get($date, 1),
                  'color_inside' => Arr::get($interior, 1),
                  'color_outside' => Arr::get($body, 1)
               );

               return $data;
            }
         }

         return FALSE;
      }
      elseif ($mark == 'Mercedes')
      {
         $url = strtr($config['marks'][$mark]['url'], array(':VINCODE' => $vincode));
         $values = NULL;

         $options = $this->_prepare_request($values);

         $result = Remote::factory($url, $options)->execute();

         $response = $this->_parse_response($result);

         if (! isset($response['headers']))
         {
            return FALSE;
         }

         $location = Arr::get($response['headers'], 'Location');

         if ($location)
         {
            $options = $this->_prepare_request(NULL, $mark.'/');

            $url = parse_url($url);
            $url = $url['scheme'].'://'.$url['host'].$location;

            $result = Remote::factory($url, $options)->execute();

            $response = $this->_parse_response($result);

            preg_match('/<table id="ctl00_cphMasterPage_tblComplectation"[^>]*>(.*)<\/table>/sU', $response['body'], $matches);

            if (($content = Arr::get($matches, 1)) === NULL)
               return FALSE;

            preg_match_all('/<td align="center">(\d{2}\/\d{2}\/\d{4}|[A-Z0-9]{0,4})<\/td>/sU', $matches[1], $match);

            return array
            (
               'date_made' => Arr::get($match[1], 0),
               'color_inside' => Arr::get($match[1], 2),
               'color_outside' => Arr::get($match[1], 1)
            );
         }

         return FALSE;
      }

      return FALSE;
   }
}