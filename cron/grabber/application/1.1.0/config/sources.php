<?php defined('SYSPATH') or die('No direct script access.');

return array
(
   // exporttarder
   1 => array
   (
      'cookie_file' => Kohana::$cache_dir.'/exporttrader/cookie.dat',

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

      'auctions' => array
      (
         'manove' => 'Manheim OVExchange',
         'mansim' => 'Manheim Simulcast',
         'pipeln' => 'Pipeline',
      ),

      'remote_options' => array
      (
         CURLOPT_COOKIEFILE => Kohana::$cache_dir.'/exporttrader/cookie.dat',
         CURLOPT_COOKIEJAR => Kohana::$cache_dir.'/exporttrader/cookie.dat',
         CURLOPT_REFERER => 'http://dealer.exporttrader.com/section/dealer',
      ),

      'search' => array
      (
         'items' => array(1,2,3,4,5,6,7,8,9,10,21,22,23,27,99,/*106,*/129,130),
         // 'items' => array(106),
         'url' => 'http://dealer.exporttrader.com/section/dealer/dealerpage/fullsearch/',
         'offset' => 20,
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

      'captcha' => array
      (
         'url' => 'http://dealer.exporttrader.com/lib/captcha/captcha.php3',
         'image_file' => Kohana::$cache_dir.'/exporttrader/captcha.jpg',
         'apikey' => '54aacb5b14e8bdc710b9fbb017027f77',
      )
   ),

   // adesa
   2 => array
   (
      'domain' => 'http://www.dealerblock.ca',

      'cookie_file' => Kohana::$cache_dir.'/adesa/cookie.dat',

      'runlist_url' => 'http://www.dealerblock.ca/xamsrunlist/searchamsRunlist.jsp',

      'login' => array
      (
         'url' => 'http://www.dealerblock.ca/servlet/login/?null&redirect=',
         'redirect_url' => 'http://www.dealerblock.ca/home/index.do',
         'ident' => 'DmitriiBoldyrev',
         'post_fields' => array
         (
            'language' => 'en_US',
            'hostapplication' => 'adesa',
            'hostlocale' => 'en_US',
            'username' => 'sobex',
            'password' => 'sobex123',
         ),
      ),

      'remote_options' => array
      (
         CURLOPT_COOKIEFILE => Kohana::$cache_dir.'/adesa/cookie.dat',
         CURLOPT_COOKIEJAR => Kohana::$cache_dir.'/adesa/cookie.dat',
         CURLOPT_REFERER => 'http://www.adesa.ca/home',
      ),

      'search' => array
      (
         'items' => array(11,12,13,14,15,16,17,18,19,20,24,25,26,27,/*100,107,*/131,/*132,*/145,146),
         'url' => 'http://www.dealerblock.ca/servlet/AmsRunlist?action=complete',
         'offset' => 250,
         'fields' => array
         (
            'optSearchCountry' => 'all',
            'vf' => 'yes',
            'search' => 'Search Now',
            'lc' => '',
            'sd' => '',
            'cnType' => '',
            'vpp' => 250
         ),
      ),
      'second_search' => array
      (
         'url' => 'http://www.dealerblock.ca/xamsrunlist/xsearchVehResult.jsp',
         'fields' => array
         (
            'order' => 'A_VHL_RUNNUM',
            'sortby' => 'A_VHL_RUNNUM',
            'cl' => '',
            'sr' => '',
            'km3' => '',
            'vf' => 'yes',
            'cn3' => '',
            'queryString' => 'null',
            'notifyme' => ''
         ) 
      )
   ),

   // ebay
   3 => array
   (
      'remote_options' => array
      (
         CURLOPT_REFERER => 'http://motors.shop.ebay.com/Cars-Trucks-/6001/i.html',
      ),

      'search' => array
      (
         'items' => array(29,30,31,32,33,34,35,36,37,38,39,40,41,42,101,/*108,*/133,134),
         // 'items' => array(108),
         'offset' => 50,
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
   ),

   // openlane
   4 => array
   (
      'domain' => 'https://www.openlane.com',
      
      'cookie_file' => Kohana::$cache_dir.'/openlane/cookie.dat',

      'login' => array
      (
         'home_url' => 'https://www.openlane.com/openauction/home.html',
         'home_post_fields' => array
         (
            'e_code' => 'AUTH_SUCCESSFUL',
            'accountName' => 'dmitrii',
         ),

         'login_auth_url' => 'https://login.openlane.com/ssoserver/auth',
         'login_post_fields' => array
         (
            'accountName' => 'dmitrii',
            'password' => 'newcar12345',
            'redirect' => 'https://www.openlane.com/openauction/home.html',
         ),

         'login_slogin_url' => 'https://login.openlane.com/ssoserver/slogin',

         'ident' => 'Dmitrii Boldyrev',
      ),

      'remote_options' => array
      (
         CURLOPT_SSL_VERIFYPEER => FALSE,
         CURLOPT_SSL_VERIFYHOST => FALSE,
         CURLOPT_COOKIEFILE => Kohana::$cache_dir.'/openlane/cookie.dat',
         CURLOPT_COOKIEJAR => Kohana::$cache_dir.'/openlane/cookie.dat',
      ),

      'search' => array
      (
         'items' => array(43,44,45,46,47,48,49,50,51,52,53,54,55,56,102,/*109,*/135,136),
         // 'items' => array(109),
         'url' => 'https://www.openlane.com/openauction/savedsearches_search.html?savedSearchId=',
      )
   ),

   // autotrader
   5 => array
   (
      'domain' => 'http://www.autotrader.com',

      'captcha' => array
      (
         'url' => 'http://www.autotrader.com/ac-servlets/kaptcha?',
         'killer' => 'http://www.autotrader.com/no_vs/click_view_VIN_captcha.asis?cache_killer=',
         'validator' => 'http://www.autotrader.com/ac-servlets/validateKaptcha?kaptchaText=',
         'image_file' => Kohana::$cache_dir.'/autotrader/captcha.jpg',
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
            'login:btn_signin' => 'login:btn_signin',
            // 'login:j_id99' => 'login:j_id99',
         ),
      ),

      'remote_options' => array
      (
         CURLOPT_COOKIEFILE => Kohana::$cache_dir.'/autotrader/cookie.dat',
         CURLOPT_COOKIEJAR => Kohana::$cache_dir.'/autotrader/cookie.dat',
      ),

      'search' => array
      (
         'items' => array(57,58,59,60,61,62,63,64,65,66,67,68,69,70,103,/*110,*/137,138),
         // 'items' => array(110),
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
   ),

   // cars
   6 => array
   (
      'remote_options' => array(),

      'detail_url' => 'http://www.cars.com/go/search/detail.jsp?listingId=',

      'search' => array
      (
         'items' => array(71,72,73,74,75,76,77,78,79,80,81,82,83,84,104,/*111,*/139,140),
         // 'items' => array(111),
         'url' => 'http://www.cars.com/for-sale/searchresults.action',
         'offset' => 50,
         'fields' => array
         (
            'dlId' => '',
            'dgId' => '',
            'searchSource' => 'ADVANCED_SEARCH',
            'rd' => 100000,
            'zc' => 12345,
            'uncpo' => 2,
            'cpo' => '',
            'stkTyp' => 'U',
            'VType' => '',
            'clrId' => '',
            'drvTrnId' => '',
            'mlgMx' => '',
            'transTypeId' => '',
            'kw' => '',
            'kwm' => 'ANY',
            'ldId' => '',
            'rpp' => 50,
            'slrTypeId' => '',
         ),
      )
   ),

   // gsmotors
   7 => array
   (
      'remote_options' => array
      (
         CURLOPT_REFERER => 'http://www.gsmotors.us/manheim/index.php',
      ),

      'url' => 'http://www.gsmotors.us/manheim/',

      'options' => array('камера заднего вида', 'навигация'),

      'search' => array
      (
         'items' => array(85,86,87,88,89,90,91,92,93,94,95,96,97,98,141,147,148),
         'url' => 'http://www.gsmotors.us/manheim/index.php?mode=searchresults&sid=Zz',
         'offset_url' => 'http://www.gsmotors.us/manheim/index.php?mode=searchresults',
         'offset' => 25,
         'fields' => array
         (
            'saveRecentSearch' => 'true',
            'submittedQstr' => '',
            'newSort' => 'false',
            'searchOperation' => 'Search',
            'vehicleTypes' => '4294967293+4294967292+4294967291+4294967290',
            'resultsPerPage' => 25,
         ),
         'offset_fields' => array
         (
            'searchOperation' => 'Page',
            'searchTab' => 'tabAll',
            'srpSortKeys' => 'Year|1',
            'wbSortKeys' => 'Year|1',
            'sortKeys' => 'Year|1',
            'wbResultsPerPage' => '',
            'workbookOffset' => '',

            'srpResultsPerPage' => 25,
            'searchResultsOffset' => 0,

            'previousSortKeys' => '',
            'zipCode' => 11223,
            'sellerCompany' => '',
            'newSort' => 'false',
            'previousSortKeys' => '',
            'sortIndicator' => 'desc',
            'submittedFilters' => '',
            'vehicleUniqueId' => '',
            'detailPageUrl' => '',
            'vin' => '',
            'channel' => '',
            'displayDistance' => '',
            'distanceUnits' => 'MILES',
            'distance' => 0,
            'zipCode' => 11223,
         )
      )
   ),

   // avtobest
   8 => array
   (
      'remote_options' => array(),
      'options' => array('камера заднего вида', 'навигация'),
      'domain' => 'http://avtobest.com',
      'search' => array
      (
         'items' => array(113,114,115,116,117,118,119,120,121,122,123,124,125,126,127,128,143,144),
         'url' => 'http://avtobest.com/search/Manheim/',
         'offset' => 25,
      )
   ),
   
   // megaavto
   9 => array(
      'remote_options' => array(
         CURLOPT_REFERER => 'http://a.mega-avto.com/usa/index.php',
      ),

      'url' => 'http://a.mega-avto.com/usa/',

      'options' => array('камера заднего вида', 'навигация'),

      'search' => array(
         //'items' => array(149,150,151,152,153,154,155,156,157,158,159,160,161,162,163,164,165),
          'items' => array(252,247,249,185,186,187,188,189,190,191,192,193,194,195,196,251,197,198,241,248,250),
         'url' => 'http://a.mega-avto.com/usa/index.php?mode=searchresults&sid=Zz',
         'offset_url' => 'http://a.mega-avto.com/usa/index.php?mode=searchresults',
         'offset' => 25,
         'fields' => array(
            'saveRecentSearch' => 'true',
            'submittedQstr' => '',
            'newSort' => 'false',
            'searchOperation' => 'Search',
            'vehicleTypes' => '4294967293+4294967292+4294967291+4294967290',
            'resultsPerPage' => 25,
         ),
         'offset_fields' => array(
            'searchOperation' => 'Page',
            'searchTab' => 'tabAll',
            'srpSortKeys' => 'Year|1',
            'wbSortKeys' => 'Year|1',
            'sortKeys' => 'Year|1',
            'wbResultsPerPage' => '',
            'workbookOffset' => '',

            'srpResultsPerPage' => 25,
            'searchResultsOffset' => 0,

            'previousSortKeys' => '',
            'zipCode' => 11223,
            'sellerCompany' => '',
            'newSort' => 'false',
            'sortIndicator' => 'desc',
            'submittedFilters' => '',
            'vehicleUniqueId' => '',
            'detailPageUrl' => '',
            'vin' => '',
            'channel' => '',
            'displayDistance' => '',
            'distanceUnits' => 'MILES',
            'distance' => 0,
         )
      )
   ),
   
   //avantag
    10 => array
    (
        'remote_options' => array
        (
            CURLOPT_REFERER => 'http://www.avantag.net/manheim/index.php',
        ),

        'url' => 'http://www.avantag.net/manheim/',

        'options' => array('камера заднего вида', 'навигация'),

        'search' => array
        (
            'items' => array(185,186,187,188,189,190,191,192,193,194,195,196,197,198,241,247,248),
            'url' => 'http://www.avantag.net/manheim/index.php',
            'offset_url' => 'http://www.avantag.net/manheim/index.php',
            'offset' => 25,
            'fields' => array
            (
                'action' => 'list',
                'searchOperation' => 'Search',
                'saveRecentSearch' => 'true',
                'searchTab' => 'tabAll',
                'submittedQstr' => '',
                'recordOffset' => 0,
                'sortKeys' => '',
                'previousSortKeys' => '',
                'sortIndicator' => '',
                'newSort' => 'false',
                'distanceInMiles' => '',
                'vehicleUniqueId' => '',
                'detailPageUrl' => '',
                'vehicleTypes' => '4294967293+4294967292+4294967291+4294967290',
                'inventoryChannels' => '',
                'distance' => 0,
                'zipCode' => 07001,
                'resultsPerPage' => 25,
            ),
            'offset_fields' => array
            (
                'list' => 1,
                'vehicleTypes' => '-1',

                'fromOdometer' => 0,
                'toOdometer' => 'ALL',
                'mmrRanges' => 'ALL',
                'regions' => 'on',

                'searchOperation' => 'Paging',
                'srpSortKeys' => '',
                'wbSortKeys' => '',
                'wbResultsPerPage' => 25,
                'srpResultsPerPage' => 25,
                'wtTracker' => '(wtSearchType,PowerSearch Other)(wtRefLinkPrefix,ps_srp_)(wtSavedSearchRefLink,)(wtSavedSearchTypeLink,)',

                'sellerCompany' => '',
                'vin' => '',
                'channel' => '',
                'workbookOffset' => 0,
                'sortKeys' => 'Year|1',
                'previousSortKeys' => '',
                'zipCode' => 07001,
                'newSort' => 'false',
                'sortIndicator' => 'desc',
                'submittedFilters' => '',
                'vehicleUniqueId' => '',
                'detailPageUrl' => '',
                'displayDistance' => '',
                'distanceInMiles' =>'',
            )
        )
    ),
    
    //198.154.195.13
    11 => array(
    
     'remote_options' => array
        (
            CURLOPT_REFERER => 'http://198.154.195.133/auction/index.php',
        ),

        'url' => 'http://198.154.195.133/auction/',
        'options' => array('камера заднего вида', 'навигация'),
        
         'search' => array
        (
            'items' => array(11002, 11003, 11004, 11005, 11006, 11007, 11008, 11009, 11010, 11011, 11012, 11013, 11014, 11017, 11019, 11020, 11022, 11023, 11024, 11025),
            'url' => 'http://198.154.195.133/auction/index.php?mode=searchresults&sid=Zz',
            'offset_url' => 'http://198.154.195.133/auction/index.php?mode=searchresults&sid=Zz',
            'offset' => 500,
            'fields' => array
            (
                'mmrRanges' => 'ALL',
                'tabAll' => 'searchTab',
                'zipCode' => 11223,
                'newSort' => false,
                'srpResultsPerPage' => 500,
                'saleDate'=>'Дата продажи',
                'saveRecentSearch'=>false,
                'searchOperation'=>'Search',
                'submittedQstr'=>rand(1, 1000000),
                'vehicleTypes'=>'-1'
                
            ),
            'offset_fields' => array()
        )
        
         

    ),

   // Export Of Cars
   12 => array
   (
      'domain' => 'http://www.exportofcars.com/carsearch/',

      'cookie_file' => Kohana::$cache_dir.'/export_of_cars/cookie.dat',

      'remote_options' => array
      (
         CURLOPT_COOKIEFILE => Kohana::$cache_dir.'/export_of_cars/cookie.dat',
         CURLOPT_COOKIEJAR => Kohana::$cache_dir.'/export_of_cars/cookie.dat',
         CURLOPT_REFERER => 'http://www.exportofcars.com/carsearch/',
      ),

      'search' => array
      (
         'items' => array(/*12100, 12101,*/ 12002, 12003, 12004, 12005, 12006, 12007, 12008, 12009, 12010, 12011, 12012, 12013, 12014, 12017, 12019, 12020, 12022, 12023, 12024, 12025),
         'url' => 'http://www.exporttrader.com/connect.php3?rvshowbuynow=1&conntype=js&lang=en&connectcharset=utf-8&nocloru=',
         'offset' => 200,
         'fields' => array
         (
             'se_search_year_1' => '',
             'se_search_year_2' => '',
             'se_search_make' => '',
             'se_search_model' => '',
             'se_search_unit_code' => '',
             'se_search_color_exterior' => '',
             'se_search_color_interior' => '',
             'se_search_vin' => '',
             'se_search_odometer_km_1' => '',
             'se_search_odometer_km_2' => '',
             'se_search_odometer_km_switch' => 1,
             'se_search_buy_now_price_sort_1' => '',
             'se_search_buy_now_price_sort_2' => '',
             'se_search_buy_now_price_only' => '',
             'full_text_search_string' => '',
             'flag_basic_view' => 1,
             'flag_search_submit' => 'Submit'
         )
      )
   ),

   // Oodle
   13 => array
   (
      'domain' => 'http://cars.oodle.com/',
       
      'cookie_file' => Kohana::$cache_dir.'/oodle/cookie.dat',

      'remote_options' => array
      (
         CURLOPT_COOKIEFILE => Kohana::$cache_dir.'/oodle/cookie.dat',
         CURLOPT_COOKIEJAR => Kohana::$cache_dir.'/oodle/cookie.dat',
         CURLOPT_REFERER => 'http://developer.oodle.com/listings',
      ),
       
      'search' => array
      (
         'items' => array(13024,13002, 13003, 13004, 13005, 13006, 13007, 13008, 13009, 13010, 13011, 13012, 13013, 13014, 13017, 13019, 13020, 13022, 13023, 13025,13026,13027),
         //'items' => array(13026,13027),
         'url' => 'http://api.oodle.com/api/v2/listings',

         'start' => 1,
         'offset' => 50,
         
         'fields' => array
         (
             'key' => '712580A5D01E',
             'region' => 'usa',
             'category' => 'vehicle/car',
             'attributes' => 'condition_used',
             'format' => 'php_serial'
         )
      )
   ),

);
