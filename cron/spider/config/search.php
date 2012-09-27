<?php

return array
(
   'search' => array
   (
      'auctions' => array
      (
         'manove' => 'Manheim OVExchange',
         'mansim' => 'Manheim Simulcast',
         'pipeln' => 'Pipeline',
      ),
      
      'types' => array
      (
         1 => array
         (
            'parent' => 1, // отношение к типу посика (берется из config/core.php)
            'mark' => 'Lexus', // марка для japancats.ru
            'cache' => TRUE, // кэшировать вин-код
            'fields' => array // поля для поиска
            (
               'carmark' => 'LEXUS',
               'carmodel' => 'RX300',
               'yearfrom' => 2001,
               'yearto' => 2003,
            ),
            'filters' => array // для фильтра в парсинге HTML (отдается в $cron->parse())
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/', // 4 буква в VIN-коде "H"
               'millage' => 120000, // пробег больше 120000
               'color' => FALSE, // не учитывать цвет
               'series' => FALSE, // серия модели
            ),
            'colors' => 1 // id фильтров по цвету (берутся из config/colors.php)
         ),
         
         2 => array
         (
            'parent' => 2,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'LEXUS',
               'carmodel' => 'RX330',
               'yearfrom' => 2004,
               'yearto' => 2006,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 2
         ),
         
         3 => array
         (
            'parent' => 3,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'LEXUS',
               'carmodel' => 'RX400H',
               'yearfrom' => 2006,
               'yearto' => 2007,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 3
         ),
         
         4 => array
         (
            'parent' => 4,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'LEXUS',
               'carmodel' => 'GX470',
               'yearfrom' => 2003,
               'yearto' => 2004,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 95000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 4
         ),
         
         5 => array
         (
            'parent' => 5,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'LEXUS',
               'carmodel' => 'GX470',
               'yearfrom' => 2005,
               'yearto' => 2006,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 5
         ),
         
         6 => array
         (
            'parent' => 6,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'LEXUS',
               'carmodel' => 'LX470',
               'yearfrom' => 2003,
               'yearto' => 2004,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 6
         ),
         
         7 => array
         (
            'parent' => 7,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'LEXUS',
               'carmodel' => 'LX470',
               'yearfrom' => 2000,
               'yearto' => 2002,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 7
         ),
         
         8 => array
         (
            'parent' => 8,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'TOYOTA',
               'carmodel' => 'LAND CRUISER',
               'yearfrom' => 2003,
               'yearto' => 2004,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 8
         ),
         
         9 => array
         (
            'parent' => 9,
            'cache' => FALSE,
            'fields' => array
            (
               'carmark' => 'HONDA',
               'carmodel' => 'CR-V',
               'yearfrom' => 2002,
               'yearto' => 2003,
            ),
            'filters' => array
            (
               // 'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 110000,
               'color' => '/(Silver)\/([A-Z]*)/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),
         
         10 => array
         (
            'parent' => 10,
            'cache' => FALSE,
            'fields' => array
            (
               'carmark' => 'HONDA',
               'carmodel' => 'PILOT',
               'yearfrom' => 2003,
               'yearto' => 2005,
            ),
            'filters' => array
            (
               'vincode' => '/(5FNYF186|5FNYF187|2HKYF186|2HKYF187)[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => '/(Silver|Black)\/([A-Z]*)/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),
         
         11 => array
         (
            'parent' => 1,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2001,
               'y2' => 2003,
               'km' => 12,
               'km2' => '',
               'mk' => 'LEXUS',
               'ml' => array('RX300', 'RX 300', 'RX 300 2WD', 'RX 300 4WD', 'RX 300 AWD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 1
         ),
         
         12 => array
         (
            'parent' => 2,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2004,
               'y2' => 2006,
               'km' => 10,
               'km2' => '',
               'mk' => 'LEXUS',
               'ml' => array('RX330', 'RX 330', 'RX 330 2WD', 'RX 330 4WD', 'RX 330 AWD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 2
         ),
         
         13 => array
         (
            'parent' => 3,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2006,
               'y2' => 2007,
               'km' => 10,
               'km2' => '',
               'mk' => 'LEXUS',
               'ml' => array('RX400H', 'RX 400H'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 3
         ),
         
         14 => array
         (
            'parent' => 4,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2003,
               'y2' => 2004,
               'km' => 9,
               'km2' => '',
               'mk' => 'LEXUS',
               'ml' => array('GX470', 'GX 470', 'GX 470 4WD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 95000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 4
         ),
         
         15 => array
         (
            'parent' => 5,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2005,
               'y2' => 2006,
               'km' => 7,
               'km2' => '',
               'mk' => 'LEXUS',
               'ml' => array('GX470', 'GX 470', 'GX 470 4WD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 5
         ),
         
         16 => array
         (
            'parent' => 6,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2003,
               'y2' => 2004,
               'km' => 7,
               'km2' => '',
               'mk' => 'LEXUS',
               'ml' => array('LX470', 'LX 470', 'LX 470 4WD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 6
         ),
         
         17 => array
         (
            'parent' => 7,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2000,
               'y2' => 2002,
               'km' => 10,
               'km2' => '',
               'mk' => 'LEXUS',
               'ml' => array('LX470', 'LX 470', 'LX 470 4WD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 7
         ),
         
         18 => array
         (
            'parent' => 8,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2003,
               'y2' => 2004,
               'km' => 7,
               'km2' => '',
               'mk' => 'TOYOTA',
               'ml' => array('LAND CRUISER', 'LANDCRUISER S/W', 'LANDCRUISER S/W BA'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 8
         ),
         
         19 => array
         (
            'parent' => 9,
            'mark' => 'Honda',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2002,
               'y2' => 2003,
               'km' => 11,
               'km2' => '',
               'mk' => 'HONDA',
               'ml' => array('CR-V', 'CR-V 2WD', 'CR-V 4WD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
               'millage' => 110000,
               'color' => '/Silver/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),
         
         20 => array
         (
            'parent' => 10,
            'mark' => 'Honda',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2003,
               'y2' => 2005,
               'km' => 10,
               'km2' => '',
               'mk' => 'HONDA',
               'ml' => array('PILOT', 'PILOT 2WD', 'PILOT 4WD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/(5FNYF186|5FNYF187|2HKYF186|2HKYF187)[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => '/(Silver|Black)/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),
         
         21 => array
         (
            'parent' => 11,
            'mark' => 'Mitsubishi',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'MITSUBISHI',
               'carmodel' => 'MONTERO',
               'yearfrom' => 2001,
               'yearto' => 2003,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
               'millage' => 110000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 10
         ),
         
         22 => array
         (
            'parent' => 12,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'TOYOTA',
               'carmodel' => '4 RUNNER',
               'yearfrom' => 2003,
               'yearto' => 2006,
            ),
            'filters' => array
            (
               'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
               'millage' => 60000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 9
         ),
         
         23 => array
         (
            'parent' => 13,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'TOYOTA',
               'carmodel' => 'RAV-4',
               'yearfrom' => 2001,
               'yearto' => 2003,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 11
         ),
         
         24 => array
         (
            'parent' => 11,
            'mark' => 'Mitsubishi',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2001,
               'y2' => 2003,
               'km' => 11,
               'km2' => '',
               'mk' => 'MITSUBISHI',
               'ml' => array('MONTERO', 'MONTERO SPORT', 'MONTEROSPORT', 'MONTERO SPORT 2WD', 'MONTERO SPORT 4WD'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
               'millage' => 110000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 10
         ),
         
         25 => array
         (
            'parent' => 12,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2003,
               'y2' => 2006,
               'km' => 10,
               'km2' => '',
               'mk' => 'TOYOTA',
               'ml' => array('4 RUNNER', '4RUNNER', '4RUNNER 4WD', '4RUNNER 4WD V6', '4RUNNER 4WD V8'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
               'millage' => 60000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 9
         ),
         
         26 => array
         (
            'parent' => 13,
            'mark' => 'Toyota',
            'cache' => FALSE,
            'fields' => array
            (
               'y1' => 2001,
               'y2' => 2003,
               'km' => 12,
               'km2' => '',
               'mk' => 'TOYOTA',
               'ml' => array('RAV4', 'RAV4 2WD', 'RAV4 2WD I-4', 'RAV4 4WD', 'RAV4 4WD I-4', 'RAV4 4WD V6'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => '/Silver/i',
               'series' => FALSE,
            ),
            'colors' => 11
         ),

         27 => array
         (
            'parent' => 14,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'carmark' => 'TOYOTA',
               'carmodel' => 'HIGHLANDER',
               'yearfrom' => 2001,
               'yearto' => 2003,
            ),
            'filters' => array
            (
               'vincode' => '/JTEHD[A-Z0-9]{12}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 12
         ),

         28 => array
         (
            'parent' => 14,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'y1' => 2001,
               'y2' => 2003,
               'km' => 10,
               'km2' => '',
               'mk' => 'TOYOTA',
               'ml' => array('HIGHLANDER', 'HIGHLANDER 4WD', 'HIGHLANDER 4WD V6', 'HIGHLANDER B'),
               'search' => 'Search Now'
            ),
            'filters' => array
            (
               'vincode' => '/JTEHD[A-Z0-9]{12}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 12
         ),
         
         29 => array
         (
            'parent' => 1,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2001-03',
               'Make' => 'Lexus',
               'Model' => 'RX',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => '/RX\s*300/i',
            ),
            'colors' => 1,
         ),
         
         30 => array
         (
            'parent' => 2,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2004-06',
               'Make' => 'Lexus',
               'Model' => 'RX',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => '/RX\s*330/i',
            ),
            'colors' => 2,
         ),

         31 => array
         (
            'parent' => 3,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2006-07',
               'Make' => 'Lexus',
               'Model' => 'RX',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => '/RX\s*400H/i',
            ),
            'colors' => 3,
         ),

         32 => array
         (
            'parent' => 4,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2003-04',
               'Make' => 'Lexus',
               'Model' => 'GX',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 95000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 4,
         ),

         33 => array
         (
            'parent' => 5,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2005-06',
               'Make' => 'Lexus',
               'Model' => 'GX',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 5,
         ),

         34 => array
         (
            'parent' => 6,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2003-04',
               'Make' => 'Lexus',
               'Model' => 'LX',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 6,
         ),

         35 => array
         (
            'parent' => 7,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2000-02',
               'Make' => 'Lexus',
               'Model' => 'LX',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 7,
         ),

         36 => array
         (
            'parent' => 8,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2003-04',
               'Make' => 'Toyota',
               'Model' => 'Land%20Cruiser',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 8
         ),

         37 => array
         (
            'parent' => 9,
            'cache' => FALSE,
            'fields' => array
            (
               '_myi' => '2002-03',
               'Make' => 'Honda',
               'Model' => 'CR%2DV',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 110000,
               'color' => '/Silver/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),

         38 => array
         (
            'parent' => 10,
            'cache' => FALSE,
            'fields' => array
            (
               '_myi' => '2003-05',
               'Make' => 'Honda',
               'Model' => 'Pilot',
            ),
            'filters' => array
            (
               'vincode' => '/(5FNYF186|5FNYF187|2HKYF186|2HKYF187)[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => '/(Silver|Black)/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),

         39 => array
         (
            'parent' => 11,
            'mark' => 'Mitsubishi',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2003-05',
               'Make' => 'Mitsubishi',
               'Model' => 'Montero',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
               'millage' => 110000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 10
         ),

         40 => array
         (
            'parent' => 12,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2003-06',
               'Make' => 'Toyota',
               'Model' => '4Runner',
            ),
            'filters' => array
            (
               'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
               'millage' => 60000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 9
         ),

         41 => array
         (
            'parent' => 13,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2001-03',
               'Make' => 'Toyota',
               'Model' => 'RAV4',
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 11
         ),

         42 => array
         (
            'parent' => 14,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               '_myi' => '2001-03',
               'Make' => 'Toyota',
               'Model' => 'Highlander',
            ),
            'filters' => array
            (
               'vincode' => '/JTEHD[A-Z0-9]{12}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 12
         ),

         43 => array
         (
            'parent' => 1,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'search_id' => 54492,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 1
         ),

         44 => array
         (
            'parent' => 2,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'search_id' => 54493,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => '/RX\s*330/i',
            ),
            'colors' => 2
         ),

         45 => array
         (
            'parent' => 3,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'search_id' => 54494,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 3
         ),

         46 => array
         (
            'parent' => 4,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'search_id' => 54495,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 95000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 4
         ),

         47 => array
         (
            'parent' => 5,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'search_id' => 54496,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 5
         ),

         48 => array
         (
            'parent' => 6,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'search_id' => 54497,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 6
         ),

         49 => array
         (
            'parent' => 7,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'search_id' => 54498,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 7
         ),

         50 => array
         (
            'parent' => 8,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'search_id' => 54501,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 8
         ),

         51 => array
         (
            'parent' => 9,
            'cache' => FALSE,
            'search_id' => 54500,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 100000,
               'color' => '/Silver/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),

         52 => array
         (
            'parent' => 10,
            'cache' => FALSE,
            'search_id' => 54502,
            'filters' => array
            (
               'vincode' => '/(5FNYF186|5FNYF187|2HKYF186|2HKYF187)[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => '/(Silver|Black)/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),

         53 => array
         (
            'parent' => 11,
            'mark' => 'Mitsubishi',
            'cache' => TRUE,
            'search_id' => 54503,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
               'millage' => 110000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 10
         ),

         54 => array
         (
            'parent' => 12,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'search_id' => 54504,
            'filters' => array
            (
               'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
               'millage' => 60000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 9
         ),

         55 => array
         (
            'parent' => 13,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'search_id' => 54505,
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 11
         ),

         56 => array
         (
            'parent' => 14,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'search_id' => 54505,
            'filters' => array
            (
               'vincode' => '/JTEHD[A-Z0-9]{12}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 12
         ),

         57 => array
         (
            'parent' => 1,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'LEXUS',
               'model' => 'RX300',
               'start_year' => 2001,
               'end_year' => 2003,
               'min_price' => 5000,
               'max_price' => 8000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 1
         ),

         58 => array
         (
            'parent' => 2,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'LEXUS',
               'model' => 'RX330',
               'start_year' => 2004,
               'end_year' => 2006,
               'min_price' => 10000,
               'max_price' => 16000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 2
         ),

         59 => array
         (
            'parent' => 3,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'LEXUS',
               'model' => 'RX400H',
               'start_year' => 2006,
               'end_year' => 2007,
               'min_price' => 13000,
               'max_price' => 17000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 3
         ),

         60 => array
         (
            'parent' => 4,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'LEXUS',
               'model' => 'GX470',
               'start_year' => 2003,
               'end_year' => 2004,
               'min_price' => 12000,
               'max_price' => 17000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 95000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 4
         ),

         61 => array
         (
            'parent' => 5,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'LEXUS',
               'model' => 'GX470',
               'start_year' => 2005,
               'end_year' => 2006,
               'min_price' => 17000,
               'max_price' => 21000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 5
         ),

         62 => array
         (
            'parent' => 6,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'LEXUS',
               'model' => 'LX470',
               'start_year' => 2003,
               'end_year' => 2004,
               'min_price' => 18000,
               'max_price' => 23000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 6
         ),

         63 => array
         (
            'parent' => 7,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'LEXUS',
               'model' => 'LX470',
               'start_year' => 2000,
               'end_year' => 2002,
               'min_price' => 9000,
               'max_price' => 12000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 7
         ),

         64 => array
         (
            'parent' => 8,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'TOYOTA',
               'model' => 'LC',
               'start_year' => 2003,
               'end_year' => 2004,
               'min_price' => 15000,
               'max_price' => 19000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 8
         ),

         65 => array
         (
            'parent' => 9,
            'cache' => FALSE,
            'fields' => array
            (
               'make' => 'HONDA',
               'model' => 'CRV',
               'start_year' => 2003,
               'end_year' => 2004,
               'min_price' => 6000,
               'max_price' => 8000,
               'color2' => 'SILVER',
            ),
            'filters' => array
            (
               // 'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 110000,
               'color' => '/Silver/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),

         /*
         66 => array
         (
            'parent' => 10,
            'cache' => FALSE,
            'fields' => array
            (
               'make' => 'HONDA',
               'model' => 'PILOT',
               'start_year' => 2003,
               'end_year' => 2004,
            ),
            'filters' => array
            (
               'vincode' => '/(5FNYF186|5FNYF187|2HKYF186|2HKYF187)[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => '/(Silver|Black)\/([A-Z]*)/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),
         */

         67 => array
         (
            'parent' => 11,
            'mark' => 'Mitsubishi',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'MIT',
               'model' => 'MONT',
               'start_year' => 2001,
               'end_year' => 2003,
               'min_price' => 4000,
               'max_price' => 8000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
               'millage' => 110000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 10
         ),

         68 => array
         (
            'parent' => 12,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'TOYOTA',
               'model' => '4RUN',
               'start_year' => 2003,
               'end_year' => 2006,
               'min_price' => 10000,
               'max_price' => 15000,
            ),
            'filters' => array
            (
               'vincode' => '/JTEBU17R[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 9
         ),

         69 => array
         (
            'parent' => 13,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'TOYOTA',
               'model' => 'RAV4',
               'start_year' => 2001,
               'end_year' => 2003,
               'min_price' => 4000,
               'max_price' => 7000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 11
         ),

         70 => array
         (
            'parent' => 14,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'make' => 'TOYOTA',
               'model' => 'HIGHLANDER',
               'start_year' => 2001,
               'end_year' => 2003,
               'min_price' => 5000,
               'max_price' => 8000,
            ),
            'filters' => array
            (
               'vincode' => '/JTEHD[A-Z0-9]{12}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 12
         ),

         71 => array
         (
            'parent' => 1,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2001,
               'yrMx' => 2003,
               'mkId' => 20070,
               'mdId' => 21785,
               'mlgMn' => 100000,
               'prMn' => 5000,
               'prMx' => 8000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 1
         ),

         72 => array
         (
            'parent' => 2,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2004,
               'yrMx' => 2006,
               'mkId' => 20070,
               'mdId' => 21839,
               'mlgMn' => 100000,
               'prMn' => 10000,
               'prMx' => 16000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 2
         ),

         73 => array
         (
            'parent' => 3,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2006,
               'yrMx' => 2007,
               'mkId' => 20070,
               'mdId' => 21841,
               'mlgMn' => 100000,
               'prMn' => 13000,
               'prMx' => 17000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 2
         ),

         74 => array
         (
            'parent' => 4,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2003,
               'yrMx' => 2004,
               'mkId' => 20070,
               'mdId' => 21213,
               'mlgMn' => 90000,
               'prMn' => 12000,
               'prMx' => 17000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 95000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 4
         ),

         75 => array
         (
            'parent' => 5,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2005,
               'yrMx' => 2006,
               'mkId' => 20070,
               'mdId' => 21213,
               'mlgMn' => 70000,
               'prMn' => 17000,
               'prMx' => 21000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 5
         ),

         76 => array
         (
            'parent' => 6,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2003,
               'yrMx' => 2004,
               'mkId' => 20070,
               'mdId' => 21353,
               'mlgMn' => 70000,
               'prMn' => 18000,
               'prMx' => 23000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 6
         ),

         77 => array
         (
            'parent' => 7,
            'mark' => 'Lexus',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2000,
               'yrMx' => 2002,
               'mkId' => 20070,
               'mdId' => 21353,
               'mlgMn' => 100000,
               'prMn' => 9000,
               'prMx' => 12000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 7
         ),

         78 => array
         (
            'parent' => 8,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2003,
               'yrMx' => 2004,
               'mkId' => 20088,
               'mdId' => 21381,
               'mlgMn' => 70000,
               'prMn' => 15000,
               'prMx' => 19000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 75000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 8
         ),

         79 => array
         (
            'parent' => 9,
            'cache' => FALSE,
            'fields' => array
            (
               'yrMn' => 2002,
               'yrMx' => 2003,
               'mkId' => 20017,
               'mdId' => 20762,
               'mlgMn' => 100000,
               'prMn' => 6000,
               'prMx' => 8000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{0,17}/',
               'millage' => 110000,
               'color' => '/Silver/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),

         80 => array
         (
            'parent' => 10,
            'cache' => FALSE,
            'fields' => array
            (
               'yrMn' => 2003,
               'yrMx' => 2005,
               'mkId' => 20017,
               'mdId' => 21729,
               'mlgMn' => 100000,
            ),
            'filters' => array
            (
               'vincode' => '/(5FNYF186|5FNYF187|2HKYF186|2HKYF187)[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => '/(Silver|Black)/i',
               'series' => FALSE,
            ),
            'colors' => NULL
         ),

         81 => array
         (
            'parent' => 11,
            'mark' => 'Mitsubishi',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2001,
               'yrMx' => 2003,
               'mkId' => 20030,
               'mdId' => 21676,
               'mlgMn' => 100000,
               'prMn' => 4000,
               'prMx' => 8000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
               'millage' => 110000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 10
         ),

         82 => array
         (
            'parent' => 12,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2003,
               'yrMx' => 2006,
               'mkId' => 20088,
               'mdId' => 20482,
               'mlgMn' => 60000,
               'prMn' => 10000,
               'prMx' => 15000,
            ),
            'filters' => array
            (
               'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 9
         ),

         83 => array
         (
            'parent' => 13,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2001,
               'yrMx' => 2003,
               'mkId' => 20088,
               'mdId' => 21780,
               'mlgMn' => 100000,
               'prMn' => 4000,
               'prMx' => 7000,
            ),
            'filters' => array
            (
               'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
               'millage' => 120000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 11
         ),

         84 => array
         (
            'parent' => 14,
            'mark' => 'Toyota',
            'cache' => TRUE,
            'fields' => array
            (
               'yrMn' => 2001,
               'yrMx' => 2003,
               'mkId' => 20088,
               'mdId' => 21260,
               'mlgMn' => 100000,
               'prMn' => 5000,
               'prMx' => 8000,
            ),
            'filters' => array
            (
               'vincode' => '/JTEHD[A-Z0-9]{12}/',
               'millage' => 100000,
               'color' => FALSE,
               'series' => FALSE,
            ),
            'colors' => 12
         ),
         
      ) // types
   ) // search
);
