<?php defined('SYSPATH') or die('No direct script access.');

abstract class Vincode {

   protected $_remote_options;

   protected function __construct()
   {
      $this->_remote_options = Kohana::config('root.remote_options') + array(CURLOPT_HEADER => TRUE);
   }

   public static function factory($driver)
   {
      if (! in_array(strtolower($driver), Kohana::config('vincode.drivers')))
      {
         throw new Kohana_Exception('Vincode driver ":driver" not found',
            array(':driver' => $driver));
      }

      $class = 'Vincode_'.ucfirst($driver);

      return new $class;
   }

   protected function _parse_response($response)
   {
      // для обратной совместимости
      return Remote::parse_response($response);
   }

   abstract public function get($vincode, $mark = NULL);
   
   public static function option_exists($vincode, $option = FALSE)
   {
      $content = Remote::factory(Vincode_Ip::URL.'?'.http_build_query(array(
         'vin' => $vincode,
         'option' => (string) $option)), Kohana::config('root.remote_options') + array(CURLOPT_HEADER => TRUE));
      
      $response = Remote::parse_response($content);
      
      if (Arr::get($response, 'body'))
      {
         return (bool) preg_match('/TRUE/i', $response['body']);
      }
      
      return FALSE;
   }

}