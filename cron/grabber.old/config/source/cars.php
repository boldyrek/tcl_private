<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'source' => array
   (
      'cars' => array
      (
         'remote' => array
         (
            // CURLOPT_REFERER => 'http://www.cars.com/for-sale/searchresults.action',
         ),

         'search' => array
         (
            'types' => array(71,72,73,74,75,76,77,78,/*79,80,*/81,/*82,*/83,84),
            // 'types' => array(81,82,83,84),
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
      )
   )
);