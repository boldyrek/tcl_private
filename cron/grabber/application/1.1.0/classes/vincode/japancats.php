<?php defined('SYSPATH') or die('No direct script access.');

class Vincode_Japancats extends Vincode {

   protected $_values = array
   (
      /*
      'Mercedes' => array
      (
         'url' => 'http://www.elcats.ru/mercedes/CheckVin.aspx?VIN=:VINCODE',
      ),
      */

      'Lexus' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJODM1NjIxNjg1ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAgUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llBSJjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYkZpbmRCeUZyYW1le75lwFhTFBgj%2F8xmymjv25JOtd8%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=E',
      ),
      'Honda' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTEwNDA2NjUyOThkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWV66RMtO5JHvZq7nb8BzRx1mnK1TA%3D%3D&__EVENTVALIDATION=%2FwEWBALX5YOaBgKPmLHqDgLn0cGUCAKEwYK2BB0Vt7FUl3Mj%2FnJw9Xa0Lv1x3ZjS&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA',
      ),
      'Mazda' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__VIEWSTATE=%2FwEPDwULLTEwNDA2NjUyOThkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWViBfOS3b1vt0ep%2Ba%2Fts9jKPvRiPg%3D%3D&__EVENTVALIDATION=%2FwEWBAL48orDAQKPmLHqDgLn0cGUCAKEwYK2BAtDq1Wr%2FaUi0AGSEIxOP0I2hCJv&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA',
      ),
      'Mitsubishi' => array // north america
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUKLTE2NjAxMjE2MmQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgIFIWN0bDAwJGNwaE1hc3RlclBhZ2UkY2hiU2F2ZUNvb2tpZQUmY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llRnJhbWWOelnG0HLGvYzgNFqGlCY9kC5Lhw%3D%3D&ctl00%24cphMasterPage%24rblRegionForVin=us&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=us',
      ),
      'Nissan' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJMjEyMjUyNzg0ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAQUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llI3FiW1MXh7RpxF0olzwAX6cNHIA%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=el',
      ),
      'Infiniti' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJMjEyMjUyNzg0ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAQUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llI3FiW1MXh7RpxF0olzwAX6cNHIA%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=el',
      ),
      'Infiniti' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwULLTExMTYxMTU0MDZkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWUIk45hLzmAlwXJbhA4grmPftVPXw%3D%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=el',
      ),
      'Suzuki' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUKLTQ3OTQ1Nzg5MQ9kFgJmD2QWAgIDD2QWAgIDD2QWBAICDw8WCB4JQmFja0NvbG9yCcyZMwAeC0JvcmRlckNvbG9yCqQBHglGb3JlQ29sb3IKpAEeBF8hU0ICHGRkAgUPEGRkFgECBWQYAQUeX19Db250cm9sc1JlcXVpcmVQb3N0QmFja0tleV9fFgEFIWN0bDAwJGNwaE1hc3RlclBhZ2UkY2hiU2F2ZUNvb2tpZVNlJfGjaBMKP%2F837Yeot8QTRG7B&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblRegions=EU',
      ),
      'Subaru' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwULLTE2MzY4Mzk1NzlkGAEFHl9fQ29udHJvbHNSZXF1aXJlUG9zdEJhY2tLZXlfXxYBBSFjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYlNhdmVDb29raWX%2BRQhX1Bjv67eApZHjcRmDgE65jg%3D%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24rblCountry=e',
      ),
      'Toyota' => array
      (
         'values' => '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJODM1NjIxNjg1ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAgUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llBSJjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYkZpbmRCeUZyYW1lB%2BDoWrCtCGjgqfjxk0GpMwsIc3o%3D&ctl00%24cphMasterPage%24txbVIN=:VINCODE&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=E',
      ),
   );

   protected $_made_identifiers = array
   (
      'Дата выпуска',
      'ДАТА ИЗГОТОВЛЕНИЯ'
   );

   protected $_exterior_identifiers = array
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

   const URL = 'http://www.japancats.ru/';

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
      if ($mark != 'Mercedes')
      {
         $values = strtr(Arr::get($this->_values[$mark], 'values'), array(':VINCODE' => $vincode));

         $options = $this->_prepare_request($values, $mark.'/');

         $result = Remote::factory(self::URL.$mark.'/Default.aspx', $options)->execute();

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
            $options = $this->_prepare_request(NULL, self::URL.$mark.'/');

            $result = Remote::factory(self::URL.ltrim($location, '/'), $options)->execute();

            $response = $this->_parse_response($result);

            // TODO
            if ($mark == 'Suzuki')
            {
               // Suzuki
               return FALSE;
            }
            else
            {
               preg_match('#<table id=".*cphMasterPage_tblComplectation"[^>]*>(.*)</table>#sU', Arr::get($response, 'body'), $matches);

               if (! ($content = Arr::get($matches, 1)))
               {
                  return FALSE;
               }

               $content = HTML::cleanup($content);

               preg_match('#<td>(?:'.implode('|', $this->_made_identifiers).')</td><td[^>]*></td><td><b>(.*)</b></td>#U', $content, $date);
               preg_match('#<td>(?:'.implode('|', $this->_exterior_identifiers).')</td><td[^>]*></td><td><b>(.*)</b></td>#U', $content, $exterior);
               preg_match('#<td>(?:'.implode('|', $this->_interior_identifiers).')</td><td[^>]*></td><td><b>(.*)</b></td>#U', $content, $interior);

               return array
               (
                  'date_made' => Arr::get($date, 1),
                  'interior_code' => Arr::get($interior, 1),
                  'exterior_code' => Arr::get($exterior, 1)
               );
            }
         }

         return FALSE;
      }
   }
   
}