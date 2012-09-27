<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Content {

   /**
    * Executes curl request & returns html-content.
    *
    * @param string $url
    * @return string
    */
   public static function get($url, $curl_options = NULL)
   {
      // init external request
      $request = Request::factory($url);

      $options = Kohana::$config->load('config.curl_options');

      if (is_array($curl_options))
      {
         $options += $curl_options;
      }

      // execute request
      $response = $request
         ->client()
         ->options($options)
         ->execute($request);

      return (string) $response->body();
   }

}