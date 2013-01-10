<?php defined('SYSPATH') or die('No direct script access.');

class Vincode_Epcdata extends Vincode {

   protected $_urls = array
   (
      'toyota' => 'http://toyota-usa.epc-data.com/search_frame/?frame_no=',
      'lexus' => 'http://lexus-usa.epc-data.com/search_frame/?frame_no='
   );

   public function get($vincode, $mark = NULL)
   {
      $response = Remote::factory(Arr::get($this->_urls, strtolower($mark)).$vincode, $this->_remote_options)->execute(TRUE);

      $response = HTML::cleanup($response);

      preg_match('#<td valign="middle">Color code: (.*)</td>#sU', $response, $exterior);
      preg_match('#<td valign="middle">Trim code: (.*)</td>#sU', $response, $interior);
      preg_match('#<td valign="middle">Manufacture date: (.*)</td>#sU', $response, $date);

      return array
      (
         'date_made' => Arr::get($date, 1),
         'interior_code' => Arr::get($interior, 1),
         'exterior_code' => Arr::get($exterior, 1)
      );
   }

}