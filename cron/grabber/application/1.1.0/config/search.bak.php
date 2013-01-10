<?php

return array
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
         'mileage' => 120000, // пробег больше 120000
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 95000,
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
         'mileage' => 75000,
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
         'mileage' => 75000,
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
         'mileage' => 100000,
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
         'mileage' => 75000,
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
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 110000,
         'color' => '/Silver\/[A-Z]*/i',
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
         'carmodel' => 'CR-V',
         'yearfrom' => 2007,
         'yearto' => 2008,
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
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
         'km' => 120000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('RX300', 'RX 300', 'RX 300 2WD', 'RX 300 4WD', 'RX 300 AWD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 120000,
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
         'km' => 100000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('RX330', 'RX 330', 'RX 330 2WD', 'RX 330 4WD', 'RX 330 AWD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
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
         'km' => 100000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('RX400H', 'RX 400H'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
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
         'km' => 90000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('GX470', 'GX 470', 'GX 470 4WD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 95000,
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
         'km' => 70000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('GX470', 'GX 470', 'GX 470 4WD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
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
         'km' => 70000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('LX470', 'LX 470', 'LX 470 4WD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
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
         'km' => 100000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('LX470', 'LX 470', 'LX 470 4WD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 100000,
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
         'km' => 70000,
         'km2' => '',
         'mk' => 'TOYOTA',
         'ml' => array('LAND CRUISER', 'LANDCRUISER S/W', 'LANDCRUISER S/W BA'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
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
         'km' => 110000,
         'km2' => '',
         'mk' => 'HONDA',
         'ml' => array('CR-V', 'CR-V 2WD', 'CR-V 4WD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 110000,
         'color' => '/Silver/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   20 => array
   (
      'parent' => 10,
      'cache' => FALSE,
      'fields' => array
      (
         'y1' => 2007,
         'y2' => 2008,
         'km' => 100000,
         'km2' => '',
         'mk' => 'HONDA',
         'ml' => array('CR-V', 'CR-V 2WD', 'CR-V 4WD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
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
         'mileage' => 110000,
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
         'mileage' => 60000,
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
         'mileage' => 100000,
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
         'km' => 110000,
         'km2' => '',
         'mk' => 'MITSUBISHI',
         'ml' => array('MONTERO', 'MONTERO SPORT', 'MONTEROSPORT', 'MONTERO SPORT 2WD', 'MONTERO SPORT 4WD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
         'mileage' => 110000,
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
         'km' => 100000,
         'km2' => '',
         'mk' => 'TOYOTA',
         'ml' => array('4 RUNNER', '4RUNNER', '4 RUNNER BAS', '4 RUNNER LIMITED', '4 RUNNER SPT', '4 RUNNER SR5', '4RUNNER LTD', '4RUNNER SR5', '4RUNNER SR5/SPORT', '4RUNNER 4WD', '4RUNNER 4WD V6', '4RUNNER 4WD V8'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
         'mileage' => 60000,
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
         'km' => 120000,
         'km2' => '',
         'mk' => 'TOYOTA',
         'ml' => array('RAV4', 'RAV4 2WD', 'RAV4 2WD I-4', 'RAV4 4WD', 'RAV4 4WD I-4', 'RAV4 4WD V6'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
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
         'mileage' => 100000,
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
         'km' => 100000,
         'km2' => '',
         'mk' => 'TOYOTA',
         'ml' => array('HIGHLANDER', 'HIGHLANDER 4WD', 'HIGHLANDER 4WD V6', 'HIGHLANDER B'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/JTEHD[A-Z0-9]{12}/',
         'mileage' => 100000,
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
         'mileage' => 120000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 95000,
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
         'mileage' => 75000,
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
         'mileage' => 75000,
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
         'mileage' => 100000,
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
         'mileage' => 75000,
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
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 110000,
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
         '_myi' => '2007-08',
         'Make' => 'Honda',
         'Model' => 'CR%2DV',
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
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
         'mileage' => 110000,
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
         'mileage' => 60000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 95000,
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
         'mileage' => 75000,
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
         'mileage' => 75000,
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
         'mileage' => 100000,
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
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 8
   ),

   51 => array
   (
      'parent' => 9,
      'mark' => 'Honda',
      'cache' => FALSE,
      'search_id' => 54500,
      'filters' => array
      (
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/Silver/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   52 => array
   (
      'parent' => 10,
      'mark' => 'Honda',
      'cache' => FALSE,
      'search_id' => 60949,
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
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
         'mileage' => 110000,
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
         'mileage' => 60000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 120000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 95000,
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
         'mileage' => 75000,
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
         'mileage' => 75000,
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
         'mileage' => 100000,
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
         'mileage' => 75000,
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
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 110000,
         'color' => '/Silver/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   66 => array
   (
      'parent' => 10,
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 'HONDA',
         'model' => 'CRV',
         'start_year' => 2007,
         'end_year' => 2008,
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

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
         'mileage' => 110000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 120000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
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
         'mileage' => 95000,
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
         'mileage' => 75000,
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
         'mileage' => 75000,
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
         'mileage' => 100000,
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
         'mileage' => 75000,
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
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 110000,
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
         'yrMn' => 2007,
         'yrMx' => 2008,
         'mkId' => 20017,
         'mdId' => 20762,
         'mlgMn' => 100000,
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
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
         'mileage' => 110000,
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
         'mileage' => 60000,
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
         'mileage' => 100000,
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
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 12
   ),

   85 => array
   (
      'parent' => 1,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961021,
         'fromYear' => 2001,
         'toYear' => 2003,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961021&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2001+2003',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 120000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 1
   ),

   86 => array
   (
      'parent' => 2,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961017,
         'fromYear' => 2004,
         'toYear' => 2006,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961017&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2004+2006',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 2
   ),

   87 => array
   (
      'parent' => 3,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961334,
         'fromYear' => 2006,
         'toYear' => 2007,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961334&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2006+2007',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 3
   ),

   88 => array
   (
      'parent' => 4,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961020,
         'fromYear' => 2003,
         'toYear' => 2004,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961020&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2003+2004',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 95000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 4
   ),

   89 => array
   (
      'parent' => 5,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961020,
         'fromYear' => 2005,
         'toYear' => 2006,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961020&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2005+2006',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 5
   ),

   90 => array
   (
      'parent' => 6,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961000,
         'fromYear' => 2003,
         'toYear' => 2004,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961000&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2003+2004',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 6
   ),

   91 => array
   (
      'parent' => 7,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961000,
         'fromYear' => 2000,
         'toYear' => 2002,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961000&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2000+2002',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 7
   ),

   92 => array
   (
      'parent' => 8,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960953,
         'model' => 4294961743,
         'fromYear' => 2003,
         'toYear' => 2004,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960953+4294961743&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2003+2004',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 8
   ),

   93 => array
   (
      'parent' => 9,
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 4294960968,
         'model' => 4294961135,
         'fromYear' => 2002,
         'toYear' => 2003,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960968+4294961135&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2002+2003',
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 110000,
         'color' => '/silver|серебро|серебристый/iu',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   94 => array
   (
      'parent' => 10,
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 4294960968,
         'model' => 4294961135,
         'fromYear' => 2007,
         'toYear' => 2008,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960968+4294961135&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2007+2008',
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   95 => array
   (
      'parent' => 11,
      'mark' => 'Mitsubishi',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960935,
         'model' => 4294961205,
         'fromYear' => 2001,
         'toYear' => 2003,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960935+4294961205&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2001+2003',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
         'mileage' => 110000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 10
   ),

   96 => array
   (
      'parent' => 12,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960953,
         'model' => 4294961038,
         'fromYear' => 2003,
         'toYear' => 2006,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960953+4294961038&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2003+2006',
      ),
      'filters' => array
      (
         'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
         'mileage' => 60000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 9
   ),

   97 => array
   (
      'parent' => 13,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960953,
         'model' => 4294961473,
         'fromYear' => 2001,
         'toYear' => 2003,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960953+4294961473&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2001+2003',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 11
   ),

   98 => array
   (
      'parent' => 14,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960953,
         'model' => 4294961207,
         'fromYear' => 2001,
         'toYear' => 2003,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960953+4294961207&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2001+2003',
      ),
      'filters' => array
      (
         'vincode' => '/JTEHD[A-Z0-9]{12}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 12
   ),

   99 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'fields' => array
      (
         'carmark' => 'SUBARU',
         'carmodel' => 'FORESTER',
         'yearfrom' => 2009,
         'yearto' => 2010,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   100 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'fields' => array
      (
         'y1' => 2009,
         'y2' => 2010,
         'km' => 80000,
         'km2' => '',
         'mk' => 'SUBARU',
         'ml' => array('FORESTER', 'FORESTER L'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   101 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'fields' => array
      (
         '_myi' => '2009-10',
         'Make' => 'Subaru',
         'Model' => 'Forester',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL,
   ),

   102 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'search_id' => 61844,
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   103 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 'SUB',
         'model' => 'FOREST',
         'start_year' => 2009,
         'end_year' => 2010,
         // 'min_price' => 5000,
         // 'max_price' => 8000,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   104 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'fields' => array
      (
         'yrMn' => 2009,
         'yrMx' => 2010,
         'mkId' => 20041,
         'mdId' => 21165,
         'mlgMn' => 80000,
         // 'prMn' => 5000,
         // 'prMx' => 8000,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   105 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 4294960938,
         'model' => 4294961459,
         'fromYear' => 2009,
         'toYear' => 2010,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960938+4294961459&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2009+2010',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),
   /*
   106 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'fields' => array
      (
         'carmark' => 'NISSAN',
         'carmodel' => 'ROGUE',
         'yearfrom' => 2008,
         'yearto' => 2009,
      ),
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   107 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'fields' => array
      (
         'y1' => 2008,
         'y2' => 2009,
         'km' => 80000,
         'km2' => '',
         'mk' => 'NISSAN',
         'ml' => array('ROGUE', 'ROGUE AWD'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   108 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'fields' => array
      (
         '_myi' => '2008-09',
         'Make' => 'Nissan',
         'Model' => 'Rogue',
      ),
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL,
   ),

   109 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'search_id' => 54503,
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   110 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 'NISSAN',
         'model' => 'ROGUE',
         'start_year' => 2008,
         'end_year' => 2009,
         // 'min_price' => 5000,
         // 'max_price' => 8000,
      ),
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   111 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'fields' => array
      (
         'yrMn' => 2008,
         'yrMx' => 2009,
         'mkId' => 20077,
         'mdId' => 21894,
         'mlgMn' => 80000,
         // 'prMn' => 5000,
         // 'prMx' => 8000,
      ),
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   112 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 4294960941,
         'model' => 4294937917,
         'fromYear' => 2008,
         'toYear' => 2009,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960941+4294937917&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2008+2009',
      ),
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),
   */
   113 => array
   (
      'parent' => 1,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'LEXUS',
         'model' => 'RX300',
         'year1' => 2001,
         'year2' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 120000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 1
   ),

   114 => array
   (
      'parent' => 2,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'LEXUS',
         'model' => 'RX330',
         'year1' => 2004,
         'year2' => 2006,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 2
   ),

   115 => array
   (
      'parent' => 3,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'LEXUS',
         'model' => 'RX400',
         'year1' => 2006,
         'year2' => 2007,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 3
   ),

   116 => array
   (
      'parent' => 4,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'LEXUS',
         'model' => 'GX470',
         'year1' => 2003,
         'year2' => 2004,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 95000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 4
   ),

   117 => array
   (
      'parent' => 5,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'LEXUS',
         'model' => 'GX470',
         'year1' => 2005,
         'year2' => 2006,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 5
   ),

   118 => array
   (
      'parent' => 6,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'LEXUS',
         'model' => 'LX470',
         'year1' => 2003,
         'year2' => 2004,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 6
   ),

   119 => array
   (
      'parent' => 7,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'LEXUS',
         'model' => 'LX470',
         'year1' => 2000,
         'year2' => 2002,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 7
   ),

   120 => array
   (
      'parent' => 8,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'TOYOTA',
         'model' => 'LAND CRUISER',
         'year1' => 2003,
         'year2' => 2004,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{0,17}/',
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 8
   ),

   121 => array
   (
      'parent' => 9,
      'cache' => FALSE,
      'fields' => array
      (
         'mark' => 'HONDA',
         'model' => 'CR-V',
         'year1' => 2002,
         'year2' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRD788|SHSRD788)[A-Z0-9]{9}/',
         'mileage' => 110000,
         'color' => '/Silver/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   122 => array
   (
      'parent' => 10,
      'cache' => FALSE,
      'fields' => array
      (
         'mark' => 'HONDA',
         'model' => 'CR-V',
         'year1' => 2007,
         'year2' => 2008,
      ),
      'filters' => array
      (
         'vincode' => '/(JHLRE487|5J6RE487|JHLRE485|5J6RE485)[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   123 => array
   (
      'parent' => 11,
      'mark' => 'Mitsubishi',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'MITSUBISHI',
         'model' => 'MONTERO',
         'year1' => 2001,
         'year2' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{4}W5[A-Z0-9]{11}/',
         'mileage' => 110000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 10
   ),

   124 => array
   (
      'parent' => 12,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'TOYOTA',
         'model' => '4RUNNER',
         'year1' => 2003,
         'year2' => 2006,
      ),
      'filters' => array
      (
         'vincode' => '/JTEBU14R[A-Z0-9]{9}/',
         'mileage' => 60000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 9
   ),

   125 => array
   (
      'parent' => 13,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'TOYOTA',
         'model' => 'RAV4',
         'year1' => 2001,
         'year2' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 11
   ),

   126 => array
   (
      'parent' => 14,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'TOYOTA',
         'model' => 'HIGHLANDER',
         'year1' => 2001,
         'year2' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/JTEHD[A-Z0-9]{12}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 12
   ),

   127 => array
   (
      'parent' => 15,
      'mark' => 'Subaru',
      'cache' => FALSE,
      'fields' => array
      (
         'mark' => 'SUBARU',
         'model' => 'FORESTER',
         'year1' => 2009,
         'year2' => 2010,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{10}H[A-Z0-9]{6}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   128 => array
   (
      'parent' => 16,
      'mark' => 'Nissan',
      'cache' => FALSE,
      'fields' => array
      (
         'mark' => 'NISSAN',
         'model' => 'ROGUE',
         'year1' => 2008,
         'year2' => 2009,
      ),
      'filters' => array
      (
         'vincode' => '/JN8AS58V[A-Z0-9]{9}/',
         'mileage' => 80000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   129 => array
   (
      'parent' => 17,
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
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   130 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => FALSE,
      'fields' => array
      (
         'carmark' => 'ACURA',
         'carmodel' => 'MDX',
         'yearfrom' => 2001,
         'yearto' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '#Silver|Gray/[A-Z]*#i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   131 => array
   (
      'parent' => 17,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'y1' => 2001,
         'y2' => 2003,
         'km' => 100000,
         'km2' => '',
         'mk' => 'TOYOTA',
         'ml' => array('HIGHLANDER', 'HIGHLANDER 4WD', 'HIGHLANDER 4WD V6', 'HIGHLANDER B'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   132 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => FALSE,
      'fields' => array
      (
         'y1' => 2001,
         'y2' => 2003,
         'km' => 100000,
         'km2' => '',
         'mk' => 'ACURA',
         'ml' => array('MDX'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/Silver|Gray/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   133 => array
   (
      'parent' => 17,
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
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   134 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => FALSE,
      'fields' => array
      (
         '_myi' => '2001-03',
         'Make' => 'Acura',
         'Model' => 'MDX',
      ),
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/Silver|Gray/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   135 => array
   (
      'parent' => 17,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'search_id' => 66159,
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   136 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => FALSE,
      'search_id' => 66158,
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/Silver|Gray/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   137 => array
   (
      'parent' => 17,
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
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   138 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 'ACURA',
         'model' => 'MDX',
         'start_year' => 2001,
         'end_year' => 2003,
         'min_price' => 5000,
         'max_price' => 8000,
      ),
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/Silver|Gray/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   139 => array
   (
      'parent' => 17,
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
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   140 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => FALSE,
      'fields' => array
      (
         'yrMn' => 2001,
         'yrMx' => 2003,
         'mkId' => 20001,
         'mdId' => 21422,
         'mlgMn' => 100000,
         'prMn' => 5000,
         'prMx' => 8000,
      ),
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/Silver|Gray/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   141 => array
   (
      'parent' => 17,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960953,
         'model' => 4294961207,
         'fromYear' => 2001,
         'toYear' => 2003,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960953+4294961207&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2001+2003',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   142 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => FALSE,
      'fields' => array
      (
         'make' => 4294960945,
         'model' => 4294961239,
         'fromYear' => 2001,
         'toYear' => 2003,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960945+4294961239&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2001+2003',
      ),
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/silver|gray|серебро|серебристый/iu',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   143 => array
   (
      'parent' => 17,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'TOYOTA',
         'model' => 'HIGHLANDER',
         'year1' => 2001,
         'year2' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 13
   ),

   144 => array
   (
      'parent' => 18,
      'mark' => 'Acura',
      'cache' => TRUE,
      'fields' => array
      (
         'mark' => 'ACURA',
         'model' => 'MDX',
         'year1' => 2001,
         'year2' => 2003,
      ),
      'filters' => array
      (
         'vincode' => '/2HNYD189[A-Z0-9]{9}/',
         'mileage' => 100000,
         'color' => '/Silver|Gray/i',
         'series' => FALSE,
      ),
      'colors' => NULL
   ),

   145 => array
   (
      'parent' => 19,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'y1' => 2007,
         'y2' => 2008,
         'km' => 70000,
         'km2' => '',
         'mk' => 'LEXUS',
         'ml' => array('RX350', 'RX 350'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 75000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 2
   ),

   146 => array
   (
      'parent' => 20,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'y1' => 2003,
         'y2' => 2007,
         'km' => 100000,
         'km2' => '',
         'mk' => 'TOYOTA',
         'ml' => array('4 RUNNER', '4RUNNER', '4 RUNNER BAS', '4 RUNNER LIMITED', '4 RUNNER SPT', '4 RUNNER SR5', '4RUNNER LTD', '4RUNNER SR5', '4RUNNER SR5/SPORT', '4RUNNER 4WD', '4RUNNER 4WD V6', '4RUNNER 4WD V8'),
         'search' => 'Search Now'
      ),
      'filters' => array
      (
         'vincode' => '/JTEBU17[A-Z0-9]{10}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 9
   ),

   147 => array
   (
      'parent' => 19,
      'mark' => 'Lexus',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960951,
         'model' => 4294961324,
         'fromYear' => 2007,
         'toYear' => 2008,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960951+4294961324&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2007+2008',
      ),
      'filters' => array
      (
         'vincode' => '/[A-Z0-9]{3}H[A-Z0-9]{13}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 2
   ),

   148 => array
   (
      'parent' => 20,
      'mark' => 'Toyota',
      'cache' => TRUE,
      'fields' => array
      (
         'make' => 4294960953,
         'model' => 4294961038,
         'fromYear' => 2003,
         'toYear' => 2007,
      ),
      'offset_fields' => array
      (
         'submittedQstr' => 'N=4294967293+4294967292+4294967291+4294967290+4294960953+4294961038&Ne=4294967294+4294960998+4294961936&Nf=Year|BTWN 2003+2007',
      ),
      'filters' => array
      (
         'vincode' => '/JTEBU17[A-Z0-9]{10}/',
         'mileage' => 100000,
         'color' => FALSE,
         'series' => FALSE,
      ),
      'colors' => 9
   ),

);
