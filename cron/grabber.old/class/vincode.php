<?php defined('SYSPATH') or die('No direct script access.');

abstract class Vincode {

   protected static $_drivers = array
   (
      'japancats',
      'lexuspartsnow',
   );

   protected $_remote_options;

   protected function  __construct()
   {
      $this->_remote_options = array
      (
         CURLOPT_HEADER => TRUE,
         CURLOPT_RETURNTRANSFER => TRUE,
         CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
         CURLOPT_TIMEOUT => 60
      );
   }

   public static function factory($driver)
   {
      if (in_array(strtolower($driver), self::$_drivers))
      {
         $class = 'Vincode_'.ucfirst($driver);
         return new $class;
      }
      else
         throw new Exception('Vincode driver "'.$driver.'" not found');
   }

   protected function _parse_response($response)
   {
      try {
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

      } catch (ErrorException $e) {
      } catch (Exception $e) {
      }

      return array();
   }

   abstract public function get($vincode, $mark = NULL);

}