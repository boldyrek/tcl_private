<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'source' => array
   (
      'exporttrader' => array
      (
         // ------------------ Base -------------------
         'cookie' => SYSPATH.'/cache/exporttrader/cookie.dat',
         
         'login' => array
         (
            'url' => 'http://dealer.exporttrader.com/section/dealer',
            'ident' => 'Boris Pasko',
            'post_fields' => array
            (
               'section' => 'dealer',
               'dealermail' => 'pasko.boris@gmail.com',
               'dealerpass' => '1291252199',
               'Submit' => ' OK ',
            ),
         ),
         
         'remote' => array
         (
            CURLOPT_COOKIEFILE => SYSPATH.'/cache/exporttrader/cookie.dat',
            CURLOPT_COOKIEJAR => SYSPATH.'/cache/exporttrader/cookie.dat',
            CURLOPT_REFERER => 'http://dealer.exporttrader.com/section/dealer',
         ),
         
         'search' => array
         (
            'types' => array(1,2,3,4,5,6,7,8,9,10,21,22,23,27),
            'url' => 'http://dealer.exporttrader.com/section/dealer/dealerpage/fullsearch/',
            'per_page' => 20,
            'fields' => array
            (
               'section' => 'dealer',
               'dealerpage' => 'fullsearch',
               'unty' => 1,
               'findcar' => 'go',
               'salvage' => '',
               'vin' => '',
               'pricefrom' => '',
               'priceto' => '',
               'kodaukc' => '',
               'partname' => '',
               'lotnumber' => '',
               'auctlocation' => '',
               'seller' => '',
               'notifyme' => '',
            ),
         ),
         
         // ------------------ Additional -------------------
         'captcha' => array
         (
            'url' => 'http://dealer.exporttrader.com/lib/captcha/captcha.php3',
            'image' => SYSPATH.'/cache/exporttrader/recaptcha.jpg',
            'apikey' => '54aacb5b14e8bdc710b9fbb017027f77',
         )
      )
   )
);
