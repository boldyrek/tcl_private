<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'source' => array
   (
      'adesa' => array
      (
         // ------------------ Base -------------------
         'cookie' => SYSPATH.'/cache/adesa/cookie.dat',
         
         'login' => array
         (
            'url' => 'http://www.dealerblock.ca/servlet/login/?null&redirect=',
            'redirect' => 'http://www.dealerblock.ca/home/index.do',
            'ident' => 'Dmitrii Boldyrev',
            'post_fields' => array
            (
               'language' => 'en_US',
               'loginAttempts' => '',
               'hostapplication' => 'adesa',
               'hostlocale' => 'en_US',
               'username' => 'sobex',
               'password' => 'sobex123',
            ),
         ),
         
         'remote' => array
         (
            CURLOPT_COOKIEFILE => SYSPATH.'/cache/adesa/cookie.dat',
            CURLOPT_COOKIEJAR => SYSPATH.'/cache/adesa/cookie.dat',
            CURLOPT_REFERER => 'http://www.adesa.ca/home?locale=en_CA',
         ),
         
         'search' => array
         (
            'types' => array(11,12,13,14,15,16,17,18,19,20,24,25,26,28),
            // 'types' => array(11),
            'url' => 'http://www.dealerblock.ca/servlet/AmsRunlist?action=complete',
            'fields' => array
            (
               'optSearchCountry' => 'all',
               'lc' => '',
               'sd' => '',
               'cnType' => '',
               'vpp' => 250,
            ),
         )
      )
   )
);
