<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'sources' => array
   (
      // 1 => 'Exporttrader',
      2 => 'Adesa',
      // 3 => 'Ebay',
      // 4 => 'Openlane',
      // 5 => 'Autotrader',
      // 6 => 'Cars',
      7 => 'Gsmotors',
      // 8 => 'Avtobest',
      9 => 'Megaavto',
      10 => 'Avantag',
      11 => 'Auction',
      12 => 'ExpOfCars',
      
   ),

   'options' => array
   (
      'nav',
      'navigation',
      'gps',
      'dvd',
      'camera',
      'entertainment',
      'abs',
   ),

   'remote_options' => array
   (
      CURLOPT_RETURNTRANSFER => TRUE,
      CURLOPT_SSL_VERIFYHOST => FALSE,
      CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_CONNECTTIMEOUT => 0,
      CURLOPT_TIMEOUT => 300,
      CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 MRA 5.6 (build 03278) Firefox/3.6.13 FirePHP/0.5',
   )
);