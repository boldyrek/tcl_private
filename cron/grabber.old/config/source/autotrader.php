<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'source' => array
   (
      'autotrader' => array
      (
         'domain' => 'http://www.autotrader.com',

         'captcha' => array
         (
            'url' => 'http://www.autotrader.com/ac-servlets/kaptcha?',
            'killer' => 'http://www.autotrader.com/no_cache/vs/click_view_VIN_captcha.asis?cache_killer=',
            'validator' => 'http://www.autotrader.com/ac-servlets/validateKaptcha?kaptchaText=',
            'image' => SYSPATH.'/cache/autotrader/recaptcha.jpg',
            'apikey' => '54aacb5b14e8bdc710b9fbb017027f77',
         ),

         'login' => array
         (
            'url' => 'https://www.autotrader.com/myatc/index.xhtml',
            'bump' => 'http://www.autotrader.com/myatc/bump.xhtml?conversationId=',
            'ident' => 'xcopy',
            'post_fields' => array
            (
               'login' => 'login',
               'login:username' => 'xcopy@list.ru',
               'login:password' => 'cbcntvf',
               'login:j_id97' => 'on',
               'javax.faces.ViewState' => 'j_id1',
               'login:j_id99' => 'login:j_id99',
            ),
         ),

         'remote' => array
         (
            CURLOPT_COOKIEFILE => SYSPATH.'/cache/autotrader/cookie.dat',
            CURLOPT_COOKIEJAR => SYSPATH.'/cache/autotrader/cookie.dat',
            // CURLOPT_REFERER => 'http://www.autotrader.com',
         ),

         'search' => array
         (
            'types' => array(57,58,59,60,61,62,63,64,65,67,/*68,*/69,70),
            // 'types' => array(69,70),
            'url' => 'http://www.autotrader.com/fyc/searchresults.jsp',
            'offset' => 100,
            'fields' => array
            (
               'search_type' => 'used',
               'distance' => '0',
               'address' => '12345',
               'make2' => '',
               'seller_type' => 'd',
               'transmission' => '',
               'engine' => '',
               'drive' => '',
               'doors' => '',
               'fuel' => '',
               'max_mileage' => '',
               'color' => '',
               'keywordsrep' => '',
               'keywordsfyc' => '',
               'keywords_display' => '',
               'certified' => '',
               'advanced' => 'y',
               'num_records' => 100,
            ),
         )
      )
   )
);