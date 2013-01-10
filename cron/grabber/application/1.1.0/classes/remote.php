<?php defined('SYSPATH') or die('No direct script access.');

class Remote {

   protected $_instance;

   public function __construct($url, array $options = NULL)
   {
      $this->_instance = curl_init($url);

      if ($options !== NULL)
      {
         if (! curl_setopt_array($this->_instance, $options))
            throw new Kohana_Exception('Failed to set CURL options');
      }
   }

   public static function factory($url, $options = NULL)
   {
   	return new Remote($url, $options);
   }

   public function execute($follow_location = FALSE)
   {
      if (($errno = curl_errno($this->_instance)) !== 0)
         throw new Kohana_Exception('Error fetching remote: '.curl_error($this->_instance).' ('.$errno.')');

      // if FOLLOW_LOCATION option turned off
      if ($follow_location == TRUE)
      {
         return $this->_execute($this->_instance);
      }

      return curl_exec($this->_instance);
   }

   public function info($key = NULL)
   {
      if ($key !== NULL)
         return curl_getinfo($this->_instance, $key);

      return curl_getinfo($this->_instance);
   }

   public function __toString()
   {
      return (string) $this->execute();
   }

   public function __destruct()
   {
      curl_close($this->_instance);
   }

   protected function _execute($ch)
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

      $ex = explode("\n\n", $data, 2);

      $header = Arr::get($ex, 0);
      $data = Arr::get($ex, 1, '');

      $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

      if ($http_code == 301 OR $http_code == 302)
      {
         $matches = array();

         preg_match('/Location:(.*?)\n/', $header, $matches);

         $url = parse_url(trim(array_pop($matches)));

         if (! $url)
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

         $new_url = Arr::get($url, 'scheme').'://'.Arr::get($url, 'host').Arr::get($url, 'path').'?'.Arr::get($url, 'query', '');

         curl_setopt($ch, CURLOPT_URL, $new_url);

         return $this->_execute($ch);
      }
      else
      {
         $curl_loops = 0;
         return $data;
      }
   }
   
   public static function parse_response($response)
   {
      try
      {
         list($response_headers, $response_body) = explode("\r\n\r\n", $response, 2);

         $response_header_lines = explode("\r\n", $response_headers);

         $http_response_line = array_shift($response_header_lines);

         if (preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@', $http_response_line, $matches))
         {
            $response_code = Arr::get($matches, 1);
         }

         $response_header_array = array();

         foreach ($response_header_lines AS $header_line)
         {
            list($key, $value)= explode(': ', $header_line);
            $response_header_array[$key] = $value;
         }

         return array
         (
            'code' => $response_code,
            'headers' => $response_header_array,
            'body' => $response_body
         );

      }
      catch (Kohana_Exception $e)
      {
         throw $e;
      }
   }
   

}
