<?php

defined('SYSPATH') or die('No direct script access.');

// error_reporting(E_ALL^E_NOTICE);

class Curl {

   private $instanse;
   private $response;

   public function __construct($url, array $options = NULL)
   {
      $this->instanse = curl_init($url);

      if ($options !== NULL)
      {
         if (!curl_setopt_array($this->instanse, $options))
            throw new Exception('Failed to set CURL options');
      }
   }

   public static function factory($url, $options = NULL)
   {
      return new Curl($url, $options);
   }

   public function execute($follow_location = FALSE)
   {
      if (($errno = curl_errno($this->instanse)) !== 0)
         throw new Exception('Error fetching remote: '.curl_error($this->instanse).' ('.$errno.')');

      // return self::_curl_exec($this->instanse);

      if ($follow_location == TRUE)
      {
         // return self::_curl_exec($this->instanse);
      }

      return curl_exec($this->instanse);
   }

   public function info($key = NULL)
   {
      if ($key !== NULL)
         return curl_getinfo($this->instanse, $key);

      return curl_getinfo($this->instanse);
   }

   public function __toString()
   {
      return (string) $this->execute();
   }

   public function __destruct()
   {
      curl_close($this->instanse);
   }

   protected static function _curl_exec($ch)
   {
      static $curl_loops = 0;
      static $curl_max_loops = 20;

      if ($curl_loops++ >= $curl_max_loops)
      {
         $curl_loops = 0;
         return FALSE;
      }

      curl_setopt($ch, CURLOPT_HEADER, TRUE);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
      $data = curl_exec($ch);

      // Core::debug($data);
      // list($header, $data) = explode("\n\n", $data, 2);
      $ex = explode("\n\n", $data, 2);
      $header = Arr::get($ex, 0);
      $data = Arr::get($ex, 1, '');

      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      if ($http_code == 301 OR $http_code == 302)
      {
         $matches = array();

         preg_match('/Location:(.*?)\n/', $header, $matches);

         $url = parse_url(trim(array_pop($matches)));

         if (!$url)
         {
            $curl_loops = 0;
            return $data;
         }

         $last_url = parse_url(curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));

         if (!Arr::get($url, 'scheme'))
         {
            $url['scheme'] = Arr::get($last_url, 'scheme');
         }

         if (!Arr::get($url, 'host'))
         {
            $url['host'] = Arr::get($last_url, 'host');
         }

         if (!Arr::get($url, 'path'))
         {
            $url['path'] = Arr::get($last_url, 'path');
         }

         $new_url = Arr::get($url, 'scheme').'://'.Arr::get($url, 'host').Arr::get($url, 'path').Arr::get($url, 'query', '');

         curl_setopt($ch, CURLOPT_URL, $new_url);

         return self::_curl_exec($ch);
      }
      else
      {
         $curl_loops = 0;
         return $data;
      }
   }

}
