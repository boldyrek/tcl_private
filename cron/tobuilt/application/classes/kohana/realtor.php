<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Realtor {

   public function parse()
   {
      $config = Kohana::$config->load('realtor');

      $url = $config->main_url.URL::query(array('xml' => $config->request_xml));

      $json = Content::get($url);

      Zend_Json::$useBuiltinEncoderDecoder = TRUE;

      $result = Zend_Json::decode($json);

      if ($total = Arr::get($result, 'NumberSearchResults'))
      {
         $added = 0;
         
         foreach ($result['MapSearchResults'] AS $item)
         {
            $apartment = ORM::factory('apartment')
               ->where('property_id', '=', $item['PropertyID'])
               ->where('pid_key', '=', $item['PidKey'])
               ->find();

            if (! $apartment->loaded())
            {
               if ($item['Address'] != '')
               {
                  if (strpos($item['Address'], '-') !== FALSE)
                  {
                     list($number, $address) = explode(' - ', $item['Address']);
                  }
                  else
                  {
                     $address = $item['Address'];
                  }

                  $url = $config->detalis_url.URL::query(array(
                     'propertyId' => $item['PropertyID'],
                     'PidKey' => $item['PidKey']
                  ));

                  // get sum. of each room square
                  $square = $this->get_square(Content::get($url));

                  // clean up price "$219,000" to "219000"
                  $price = str_replace(array('$', ','), '', $item['Price']);

                  if ($square)
                  {
                     $house = ORM::factory('house')
                        ->where('address', 'LIKE', "%$address%")
                        ->find();

                     ORM::factory('apartment')
                     ->values(array(
                        'house_id' => ($house->loaded() ? $house->id : NULL),
                        'full_address' => $item['Address'],
                        'address' => $address,
                        'property_id' => $item['PropertyID'],
                        'pid_key' => $item['PidKey'],
                        'square' => $square,
                        'price' => $price,
                        // 'price_sm' => ceil($price/$square),
                     ))
                     ->save();

                     $added++;
                  }
               } // if
            } // if .. loaded()
         } // foreach
      }

      Kohana::$log->add(Log::INFO, ':total items founded, :added items added',
         array(':total' => $total, ':added' => $added));
   }

   protected function get_square($content)
   {
      $sq = 0;

      preg_match('@<table  align="center" cellspacing="1">(.+)</table>@sU', $content, $matches);

      $pattern  = '@<tr>.*';
      $pattern .= '<td class="PropDetailsSpecValue">(?P<type>.+)</td>.*';
      $pattern .= '<td class="PropDetailsSpecValue">(?P<level>.+)</td>.*';
      $pattern .= '<td class="PropDetailsSpecValue"><span isMeasurement=\'True\' title=".*">(?P<square>.+)</span></td>.*';
      $pattern .= '<tr />@sU';

      if (preg_match_all($pattern, Arr::get($matches, 1), $rooms, PREG_SET_ORDER))
      {
         $result = array();

         foreach ($rooms AS $room)
         {
            unset($room[0]);

            $room = array_map('trim', $room);
            
            if (preg_match_all('/[-+]?[0-9]*\.?[0-9]+/', Arr::get($room, 'square'), $square))
            {
               $result[strtolower($room['type'])] = $square[0][0]*$square[0][1];
            }
         }

         if (isset($result['living room']) && isset($result['dining room']))
         {
            if ($result['living room'] == $result['dining room'])
            {
               unset($result['living room']);
            }

            $sq = array_sum($result);
         }
      }

      return $sq;
   }

}