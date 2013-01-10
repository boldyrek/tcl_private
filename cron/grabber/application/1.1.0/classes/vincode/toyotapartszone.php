<?php defined('SYSPATH') or die('No direct script access.');

class Vincode_Toyotapartszone extends Vincode {

   const URL = 'http://www.toyotapartszone.com/Page_Product/VinSearch.aspx?vinnum=';

   public function get($vincode, $mark = NULL)
   {
      $content = Remote::factory(self::URL.$vincode, $this->_remote_options);

      $response = $this->_parse_response($content);

      if ($headers = Arr::get($response, 'headers'))
      {
         if ($url = parse_url(Arr::get($headers, 'Location')))
         {
            parse_str(Arr::get($url, 'query'), $query);

            return array
            (
               'date_made' => Arr::get($query, 'productiondate'),
               'interior_code' => Arr::get($query, 'trimcode'),
               'exterior_code' => Arr::get($query, 'paintcode')
            );
         }

         return FALSE;
      }

      return FALSE;
   }

}