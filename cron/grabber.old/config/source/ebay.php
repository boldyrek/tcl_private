<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'source' => array
   (
      'ebay' => array
      (
         'remote' => array
         (
            CURLOPT_REFERER => 'http://motors.shop.ebay.com/Cars-Trucks-/6001/i.html',
         ),
         
         'search' => array
         (
            'types' => array(29,30,31,32,33,34,35,36,37,38,39,40,41,42),
	    // 'types' => array(29,30,31,32),
            'per_page' => 50,
            'url' => 'http://motors.shop.ebay.com/Cars-Trucks-/6001/i.html',
            'fields' => array
            (
               'LH_ItemCondition' => '2|0',
               '_dmpt' => 'US_Cars_Trucks',
               '_ipg' => '200',
               '_mqf' => '0',
               '_qfkw' => '1',
               '_sop' => '7',
               '_trksid' => 'p4506.c0.m273',
               '_fpos' => '12345',
            ),
         )
      )
   )
);