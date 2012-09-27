<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'source' => array
   (
      'openlane' => array
      (
         // ------------------ Base -------------------
         'cookie' => SYSPATH.'/cache/openlane/cookie.dat',

         'login' => array
         (
            'url1' => 'https://login.openlane.com/ssoserver/auth',
            'referer1' => 'https://www.openlane.com/openauction/home.html',
            'post_fields1' => array
            (
               'accountName' => 'dmitrii',
               'password' => 'newcar12345',
               'redirect' => 'https://www.openlane.com/openauction/home.html',
            ),

            'url2' => 'https://www.openlane.com/openauction/home.html',
            'referer2' => 'https://login.openlane.com/ssoserver/auth',
            'post_fields2' => array
            (
               'e_code' => 'AUTH_SUCCESSFUL',
               'accountName' => 'dmitrii',
            ),

            'url3' => 'https://login.openlane.com/ssoserver/slogin',
            'referer3' => 'https://login.openlane.com/ssoserver/auth',

            'url4' => 'https://www.openlane.com/openauction/home.html',
            'referer4' => 'https://login.openlane.com/ssoserver/auth',

            'ident' => 'Dmitrii Boldyrev',
         ),

         'remote' => array
         (
            CURLOPT_SSL_VERIFYPEER => FALSE,
            CURLOPT_SSL_VERIFYHOST => FALSE,
            CURLOPT_COOKIEFILE => SYSPATH.'/cache/openlane/cookie.dat',
            CURLOPT_COOKIEJAR => SYSPATH.'/cache/openlane/cookie.dat',
         ),

         'search' => array
         (
            'types' => array(43,44,45,46,47,48,49,50,51,52,53,54,55,56),
            'url' => 'https://www.openlane.com/openauction/savedsearches_search.html?savedSearchId=',
         )
      )
   )
);