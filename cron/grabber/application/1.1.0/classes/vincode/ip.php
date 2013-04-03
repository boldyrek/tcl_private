<?php defined('SYSPATH') or die('No direct script access.');

class Vincode_Ip extends Vincode {

   const URL = 'http://198.154.195.133/decode/index_option.php';

   public function get($vincode, $mark = NULL)
   {
      $content = Remote::factory(self::URL.'?'.http_build_query(array('vin' => $vincode, 'option' => '')), $this->_remote_options);
      
      $response = $this->_parse_response($content);
      
      if (Arr::get($response, 'body'))
      {
         if (preg_match('/Color:<\/br>(?P<color>.+)<\/br>Production date:<\/br>(?P<date>.*)<\/br>/sU', $response['body'], $matches))
         {
            $color = Arr::get($matches, 'color');

            return array(
               'date_made' => Arr::get($matches, 'date'),
               'interior_code' => substr($color, -4),
               'exterior_code' => substr($color, 0, -4),
            );
         }
         
         return FALSE;
      }
      
      return FALSE;
   }

}
