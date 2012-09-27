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
         'uri' => 'Mercedes-Benz/M-Class/MCLASS+MERCEDESBENZ',
         // 'query' => array('yRng' => '2006,2008'),
      ),
      2  => array
      (
         'name' => 'Infiniti FX',
         'year' => array(2009),
         'uri' => 'Infiniti/FX/Infiniti+FX',
         // 'query' => array('yRng' => '2009,2009'),
      ),
      3  => array
      (
         'name' => 'Honda Accord Crosstour',
         'year' => array(2010),
         'uri' => 'Honda/Accord/Honda+Accord+Crosstour',
         // 'query' => array('yRng' => '2010,2010'),
      ),
      4  => array
      (
         'name' => 'Toyota Venza',
         'year' => array(2009, 2010),
         'uri' => 'Toyota/Venza/Toyota+Venza',
         // 'query' => array('yRng' => '2009,2010'),
      ),
      5  => array
      (
         'name' => 'BMW X5',
         'year' => array(2007, 2008),
         'uri' => 'BMW/X5/BMW+X5',
         // 'query' => array('yRng' => '2007,2008'),
      ),
      6  => array
      (
         'name' => 'Subaru Forester',
         'year' => array(2009, 2010),
         'uri' => 'Subaru/Forester/Subaru+Forester',
         // 'query' => array('yRng' => '2009,2010'),
      ),
      7  => array
      (
         'name' => 'Toyota Highlander',
         'year' => array(2008, 2009),
         'uri' => 'Toyota/Highlander/Toyota+Highlander',
         // 'query' => array('yRng' => '2008,2009'),
      ),
      8  => array
      (
         'name' => 'Toyota Sienna',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Toyota/Sienna/Toyota+Sienna',
         // 'query' => array('yRng' => '2006,2010'),
      ),
      9  => array
      (
         'name' => 'Acura MDX',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Acura/MDX/Acura+MDX',
         // 'query' => array('yRng' => '2007,2009'),
      ),
      10  => array
      (
         'name' => 'Acura RDX',
         'year' => array(2007, 2008, 2009),
         'uri' => 'Acura/RDX/Acura+RDX',
         // 'query' => array('yRng' => '2007,2009'),
      ),
      11  => array
      (
         'name' => 'Acura TL',
         'year' => array(2006, 2007, 2008, 2009),
         'uri' => 'Acura/TL/Acura+TL',
         // 'query' => array('yRng' => '2006,2009'),
      ),
      12  => array
      (
         'name' => 'Acura TSX',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Acura/TSX/Acura+TSX',
         // 'query' => array('yRng' => '2006,2010'),
      ),
      13  => array
      (
         'name' => 'Honda Odyssey',
         'year' => array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Honda/Odyssey/Honda+Odyssey',
         // 'query' => array('yRng' => '2006,2010'),
      ),
      14  => array
      (
         'name' => 'Honda CR-V',
         'year' => array(2007, 2008, 2009, 2010, 2011),
         'uri' => 'Honda/CR-V/Honda+CRV',
         // 'query' => array('yRng' => '2007,2011'),
      ),
      15  => array
      (
         'name' => 'Honda Pilot',
         'year' => array(2007, 2008, 2009, 2010, 2011),
         'uri' => 'Honda/Pilot/Honda+Pilot',
         // 'query' => array('yRng' => '2007,2011'),
      ),
      16  => array
      (
         'name' => 'Mitsubishi Outlander',
         'year' => array(2011),
         'uri' => 'Mitsubishi/Outlander/Mitsubishi+Outlander',
         // 'query' => array('yRng' => '2011,2011'),
      ),
      17  => array
      (
         'name' => 'Nissan Murano',
         'year' => array(2009, 2010),
         'uri' => 'Nissan/Murano/Nissan+Murano',
         // 'query' => array('yRng' => '2009,2010'),
      ),
      18 => array
      (
         'name' => 'Nissan Maxima',
         'year' => array(2009, 2010),
         'uri' => 'Nissan/Maxima/Nissan+Maxima',
         // 'query' => array('yRng' => '2009,2010'),
      ),
      19 => array
      (
         'name' => 'Nissan Pathfinder',
         'year' =>array(2006, 2007, 2008, 2009, 2010),
         'uri' => 'Nissan/Pathfinder/Nissan+Pathfinder',
         // 'query' => array('yRng' => '2006,2010'),
      ),
      20 => array
      (
         'name' => 'Honda Accord',
         'year' => array(2008, 2009, 2010),
         'uri' => 'Honda/Accord/Honda+Accord',
         // 'query' => array('yRng' => '2008,2010'),
      ),
      21 => array
      (
         'name' => 'Infiniti G',
         'year' => array(2007, 2008, 2009, 2010),
         'uri' => 'Infiniti/G/Infiniti+G',
         // 'query' => array('yRng' => '2007,2010'),
      ),
   ),
);