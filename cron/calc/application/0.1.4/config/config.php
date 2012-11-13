<?php defined('SYSPATH') or die('No direct script access.');

return array
(
   'curl' => array
   (
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_SSL_VERIFYHOST => FALSE,
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_CONNECTTIMEOUT => 0,
      CURLOPT_TIMEOUT => 300,
      CURLOPT_FOLLOWLOCATION => TRUE,
      CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 6.1; rv:2.0.1) Gecko/20100101 Firefox/4.0.1',
      CURLOPT_REFERER => 'http://www.autotrader.ca',
   ),

   'domain' => 'http://www.autotrader.ca/',

   'query' => array
   (
      // 'lloc' => 'Toronto, ON',
      'cty' => 'Toronto',
      'prv' => 'Ontario',
      'ctr' => 'Canada',
      'prx' => 100,
      'rprv' => 'true'
   ),
   'items' => array
   (
      1  => array
      (
         'name' => 'Mercedes-Benz M-Class',
         'year' => array(2006, 2007, 2008),
         'uri' => 'Mercedes-Benz/M-Class/',
      ),
      2  => array
      (
         'name' => 'Infiniti FX',
         'year' => array(2009),
         'uri' => 'Infiniti/FX/',
      ),
      3  => array
      (
         'name' => 'Honda Accord Crosstour',
         'year' => array(2010),
         'uri' => 'Honda/Accord/',
      ),
      4  => array
      (
         'name' => 'Toyota Venza',
         'year' => array(2009, 2010),
         'uri' => 'Toyota/Venza/',
      ),
      5  => array
      (
         'name' => 'BMW X5',
         'year' => array(2007, 2008),
         'uri' => 'BMW/X5/',
      ),
      6  => array
      (
         'name' => 'Subaru Forester',
         'year' => array(2009, 2010),
         'uri' => 'Subaru/Forester/',
      ),
      7  => array
      (
         'name' => 'Toyota Highlander',
         'year' => array(2008, 2009),
         'uri' => 'Toyota/Highlander/',
      ),
      8  => array
      (
         'name' => 'Toyota Sienna',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Toyota/Sienna/',
      ),
      9  => array
      (
         'name' => 'Acura MDX',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Acura/MDX/',
      ),
      10  => array
      (
         'name' => 'Acura RDX',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Acura/RDX/',
      ),
      11  => array
      (
         'name' => 'Acura TL',
         'year' => array(2006, 2007, 2008, 2009),
         'uri' => 'Acura/TL/',
      ),
      12  => array
      (
         'name' => 'Acura TSX',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Acura/TSX/',
      ),
      13  => array
      (
         'name' => 'Honda Odyssey',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Honda/Odyssey/',
      ),
      14  => array
      (
         'name' => 'Honda CR-V',
         'year' => array(2007, 2008, 2009, 2010, 2011),
         'uri' => 'Honda/CR-V/',
      ),
      15  => array
      (
         'name' => 'Honda Pilot',
         'year' => array(2007, 2008, 2009, 2010, 2011),
         'uri' => 'Honda/Pilot/',
      ),
      16  => array
      (
         'name' => 'Mitsubishi Outlander',
         'year' => array(2011),
         'uri' => 'Mitsubishi/Outlander/',
      ),
      17  => array
      (
         'name' => 'Nissan Murano',
         'year' => array(2009, 2010),
         'uri' => 'Nissan/Murano/',
      ),
      18 => array
      (
         'name' => 'Nissan Maxima',
         'year' => array(2009, 2010),
         'uri' => 'Nissan/Maxima/',
      ),
      19 => array
      (
         'name' => 'Nissan Pathfinder',
         'year' =>array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Nissan/Pathfinder/',
      ),
      20 => array
      (
         'name' => 'Honda Accord',
         'year' => array(2008, 2009, 2010),
         'uri' => 'Honda/Accord/',
      ),
      21 => array
      (
         'name' => 'Infiniti G',
         'year' => array(2007, 2008, 2009, 2010),
         'uri' => 'Infiniti/G/',
      ),
      22 => array
      (
         'name' => 'Subaru Impreza WRX',
         'year' => array(2007, 2008, 2009, 2010),
         'uri' => 'Subaru/Impreza/',
      ),
      23 => array
      (
         'name' => 'Subaru Outback',
         'year' => array(2007, 2008, 2009, 2010),
         'uri' => 'Subaru/Outback/',
      ),
      24 => array
      (
         'name' => 'Subaru Tribeca',
         'year' => array(2007, 2008, 2009, 2010),
         'uri' => 'Subaru/Tribeca/',
      ),
      25 => array
      (
         'name' => 'Lexus RX 350',
         'year' => array(2007, 2008, 2009, 2010),
         'uri' => 'Lexus/RX/',
      ),
      26 => array
      (
         'name' => 'Lexus GS 350',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Lexus/GS/',
      ),
      27 => array
      (
         'name' => 'Lexus ES 350',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Lexus/ES/',
      ),
      28 => array
      (
         'name' => 'Audi A8',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Audi/A8/',
      ),
      29 => array
      (
         'name' => 'Audi A6',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Audi/A6/',
      ),
      30 => array
      (
         'name' => 'Audi A4',
         'year' => array(2007, 2008, 2009, 2010),
         'uri' => 'Audi/A4/',
      ),
      31 => array
      (
         'name' => 'Audi Q5',
         'year' => array(2009, 2010),
         'uri' => 'Audi/Q5/',
      ),
      32 => array
      (
         'name' => 'Audi Q7',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Audi/Q7/',
      ),
      33  => array
      (
         'name' => 'Mercedes-Benz E-Class',
         'year' => array(2006, 2007, 2008, 2009),
         'uri' => 'Mercedes-Benz/E-Class/',
      ),
      34  => array
      (
         'name' => 'Mercedes-Benz GLK-Class',
         'year' => array(2010),
         'uri' => 'Mercedes-Benz/GLK-Class/',
      ),
      35  => array
      (
         'name' => 'Mercedes-Benz C-Class',
         'year' => array(2008, 2009),
         'uri' => 'Mercedes-Benz/C-Class/',
      ),
      36  => array
      (
         'name' => 'Mercedes-Benz S-Class',
         'year' => array(2007),
         'uri' => 'Mercedes-Benz/S-Class/',
      ),
      37 => array
      (
         'name' => 'Lexus IS 350',
         'year' => array(2006, 2007, 2008, 2009),
         'uri' => 'Lexus/IS/',
      ),
      38 => array
      (
         'name' => 'Lexus IS 350',
         'year' => array(2006, 2007, 2008, 2009),
         'uri' => 'Lexus/IS/',
      ),
      39 => array
      (
         'name' => 'BMW 1-Series',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'BMW/1+Series/',
      ),
      40 => array
      (
         'name' => 'BMW 3-Series',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'BMW/3+Series/',
      ),
      41 => array
      (
         'name' => 'BMW 5-Series',
         'year' => array(2006, 2007, 2008, 2009),
         'uri' => 'BMW/5+Series/',
      ),
      42  => array
      (
         'name' => 'BMW X3',
         'year' => array(2006, 2007, 2008, 2009),
         'uri' => 'BMW/X3/',
      ),
   ),
);
