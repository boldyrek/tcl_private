<?php defined('SYSPATH') or die('No direct access allowed.');

class Kohana_Tobuilt {
   
   public function __construct()
   {
   }

   public function parse()
   {
      $config = Kohana::$config->load('tobuilt');

      $content = Content::get($config->main_url);

      $cache = Cache::instance();

      // cache first page
      $cache->set('firstpage', $content);

      // find total pages
      preg_match_all('#<option value="\d+">(\d+)</option>#', $content, $totals);

      $total_pages = Arr::get($totals[1], count($totals[1])-1, 0);

      unset($content);

      $total = 0;

      for ($page = 1; $page <= $total_pages; $page++)
      {
         $page_content = ($page == 1)
            ? $cache->get('firstpage') // get cached content
            : Content::get($config->main_url.URL::query(array('page' => $page)));

         // find each item id & image name
         preg_match_all('#fd3=(?P<id>\d+)["].+src="Buildingimagessmall/(?P<thumb>.+)"#U', $page_content, $matches);

         unset($page_content);

         $count = 0;

         foreach (Arr::map('intval', $matches['id']) AS $key => $house_id)
         {
            $item = ORM::factory('house')
               ->where('house_id', '=', $house_id)
               ->find();

            // if house not exists in db table
            if (! $item->loaded())
            {
               $url = $config->more_url.URL::query(array('search_fd3' => $house_id));
               
               $values = $this->parse_content(Content::get($url));

               // remember original name before replace
               $picture = $values['picture'];
               $pics_count = $values['pics_count'];

               // unset "pics_count" key
               unset($values['pics_count']);

               $values = array(
                  'house_id' => $house_id,
                  'url' => $url,
                  'new' => TRUE
               ) + $values;

               // insert new record
               $saved = ORM::factory('house')
                  ->values($values)
                  ->save();

               if ($saved)
               {
                  // download thumb.
                  $dir = $config->cache_path.$saved->pk();

                  if (@mkdir($dir, 0755, TRUE))
                  {
                     $shell_command = 'convert "%s" -resize '.$config->thumb_width.' "%s"';

                     @chmod($dir, 0755);

                     // first image "HouseAddress1"
                     $dest = $dir.'/'.$picture;

                     // download
                     if (@copy($config->images_url.$picture, $dest))
                     {
                        // make thumb.
                        shell_exec(sprintf($shell_command, $dest, $dir.'/thumb-'.$picture));
                     }

                     // any additional images?
                     if ($pics_count > 0)
                     {
                        $filename = pathinfo($picture, PATHINFO_FILENAME);

                        // get base name "HouseAddress1" > "HouseAddress"
                        $filename = substr($filename, 0, strlen($filename)-1);

                        for ($num = 1; $num <= $pics_count; $num++)
                        {
                           // "HouseAddress2.jpg", "HouseAddress3.jpg"
                           $image = $filename.($num+1).'.jpg';

                           $dest = $dir.'/'.$image;
                           
                           // download
                           if (@copy($config->images_url.$image, $dest))
                           {
                              // make thumb.
                              shell_exec(sprintf($shell_command, $dest, $dir.'/thumb-'.$image));
                           }
                        }
                     }
                  }
               }
               
               $count++;
            }
            else
            {
               // set is as "old"
               $house = ORM::factory('house')
                  ->where('house_id', '=', $house_id)
                  ->find();

               $house->new = FALSE;
               $house->save();
            }
         }

         unset($matches);

         $total += $count;
      }

      Kohana::$log->add(Log::INFO, ':total items added', array(':total' => $total));

      $cache->delete('firstpage');
   }

   /**
    * Parse detailed info content.
    *
    * @param string $content
    * @return array
    */
   protected function parse_content($content)
   {
      $pattern  = '#<Table.*id="masterDataTable".*>';
      $pattern .= '.*<td class="TrOdd".*><img src="./Buildingimages/(?P<picture>.+)".*></td>';
      $pattern .= '.*<td class="ThRows">Additional Images</td><td class="TrOdd".*>.*<b>(?P<pics_count>\d+)</b>.*</td>'; // pictutes count
      $pattern .= '.*<td class="ThRows">Name & Location</td><td class="TrOdd".*><font size = 4><b>(?P<name>.+)</font><br>(?P<address>.+)<br>(?P<location>.+)</b></td>'; // name, address, location
      $pattern .= '.*<td class="ThRows">Other Identification</td><td class="TrOdd".*>(?P<identification>.+)</td>'; // identification
      $pattern .= '.*<td class="ThRows">Notes</td><td class="TrOdd".*>(?P<notes>.*)</td>'; // notes
      $pattern .= '.*<td class="ThRows">Quote</td><td class="TrOdd".*>(?P<quote>.*)</td>'; // quote
      $pattern .= '.*<td class="ThRows">Status</td><td class="TrOdd".*>(?P<status>.+)</td>'; // status
      $pattern .= '.*<td class="ThRows">Year Completed</td><td class="TrOdd".*>(?P<year_completed>.+)</td>'; // year completed
      $pattern .= '.*<td class="ThRows">Companies</td><td class="TrOdd".*>(?P<companies>.*)</td>'; // companies
      $pattern .= '.*<td class="ThRows">Click for Google Map</td><td class="TrOdd".*>(?P<google_map>.+)</td>'; // google map
      $pattern .= '.*<td class="ThRows"><font size = 3>Height and Building Data</font></td><td class="TrOdd".*>(?P<building_data>.*)</td>'; // building data
      $pattern .= '.*<td class="ThRows">Floors</td><td class="TrOdd".*>(?P<floors>.+)</td>'; // floors
      $pattern .= '.*<td class="ThRows">Height \(m\)</td><td class="TrOdd".*>(?P<height>.+)</td>'; // height
      $pattern .= '.*<td class="ThRows">Building type</td><td class="TrOdd".*>(?P<type>.+)</td>'; // type
      $pattern .= '.*<td class="ThRows">General use</td><td class="TrOdd".*>(?P<general_use>.*)</td>'; // general use
      $pattern .= '.*<td class="ThRows">Specific Use</td><td class="TrOdd".*>(?P<specific_use>.*)</td>'; // specific use
      $pattern .= '.*<td class="ThRows">Former Use</td><td class="TrOdd".*>(?P<former_use>.*)</td>'; // former use
      $pattern .= '.*<td class="ThRows">Heritage</td><td class="TrOdd".*>(?P<heritage>.+)</td>'; // heritage
      $pattern .= '.*<td class="ThRows">Main Style</td><td class="TrOdd".*>(?P<main_style>.+)</td>.*'; // main style
      $pattern .= '</Table>#sU';

      $content = iconv('ISO-8859-1', 'UTF-8', $content);

      preg_match($pattern, $content, $matches);

      // get db table keys
      $keys = array_keys(ORM::factory('house')->table_columns());

      // extract values from matches
      $values = Arr::extract($matches, $keys);

      // append main image name & count for download them
      // later will be unset
      $values['pics_count'] = Arr::get($matches, 'pics_count', 0);

      // remove white spaces
      return Arr::map('trim', $values);
   }

}
