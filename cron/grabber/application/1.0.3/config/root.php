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
   ),

   'targets' => array
   (
      1 => array
      (
         'name' => 'Lexus RX 300', // название типа
         'cond' => array // опции для спика типов
         (
            'год выпуска' => '2001-2003',
            'пробег' => 'больше 120 000 миль',
            'VIN-код' => 'четвертым символом должна быть буква H',
         ),
      ),
      2 => array
      (
         'name' => 'Lexus RX 330',
         'cond' => array
         (
            'год выпуска' => '2004-2006',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'четвертым символом должна быть буква H',
         )
      ),
      3 => array
      (
         'name' => 'Lexus RX 400H',
         'cond' => array
         (
            'год выпуска' => '2006-2007',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'четвертым символом должна быть буква H',
         )
      ),
      4 => array
      (
         'name' => 'Lexus GX 470',
         'cond' => array
         (
            'год выпуска' => '2003-2004',
            'пробег' => 'больше 95 000 миль'
         )
      ),
      5 => array
      (
         'name' => 'Lexus GX 470',
         'cond' => array
         (
            'год выпуска' => '2005-2006',
            'пробег' => 'больше 75 000 миль'
         )
      ),
      6 => array
      (
         'name' => 'Lexus LX 470',
         'cond' => array
         (
            'год выпуска' => '2003-2004',
            'пробег' => 'больше 75 000 миль'
         )
      ),
      7 => array
      (
         'name' => 'Lexus LX 470',
         'cond' => array
         (
            'год выпуска' => '2000-2002',
            'пробег' => 'больше 100 000 миль'
         )
      ),
      8 => array
      (
         'name' => 'Toyota Land Cruiser',
         'cond' => array
         (
            'год выпуска' => '2003-2004',
            'пробег' => 'больше 75 000 миль'
         )
      ),
      9 => array
      (
         'name' => 'Honda CR-V',
         'cond' => array
         (
            'год выпуска' => '2002-2003',
            'пробег' => 'больше 110 000 миль',
            'VIN-код' => 'начинается с JHLRD788, SHSRD788',
            'цвет кузова' => 'Silver'
         )
      ),
      10 => array
      (
         'name' => 'Honda CR-V',
         'cond' => array
         (
            'год выпуска' => '2007-2008',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'начинается с JHLRE487, 5J6RE487, JHLRE485, 5J6RE485',
         )
      ),
      11 => array
      (
         'name' => 'Mitsubishi Montero 4WD Limited',
         'cond' => array
         (
            'год выпуска' => '2001-2003',
            'пробег' => 'больше 110 000 миль',
            'VIN-код' => 'встречается W5',
         )
      ),
      12 => array
      (
         'name' => 'Toyota 4RUNNER 4WD Limited',
         'cond' => array
         (
            'год выпуска' => '2003-2006',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'начинаются с JTEBU17R',
         )
      ),
      13 => array
      (
         'name' => 'Toyota RAV 4',
         'cond' => array
         (
            'год выпуска' => '2001-2003',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'четвертым символом должна быть буква H',
         )
      ),
      14 => array
      (
         'name' => 'Toyota Highlander 4WD',
         'cond' => array
         (
            'год выпуска' => '2001-2003',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'начинается с JTEHD',
         )
      ),
      15 => array
      (
         'name' => 'Subaru Forester',
         'cond' => array
         (
            'год выпуска' => '2009-2010',
            'пробег' => 'больше 80 000 миль',
            'VIN-код' => 'одинадцатым символом должна быть буква H',
         )
      ),
      /*
      16 => array
      (
         'name' => 'Nissan Rogue',
         'cond' => array
         (
            'год выпуска' => '2008-2009',
            'пробег' => 'больше 80 000 миль',
            'VIN-код' => 'начинается с JN8AS58V',
         )
      ),
      */
      17 => array
      (
         'name' => 'Toyota Highlander',
         'cond' => array
         (
            'год выпуска' => '2001-2003',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'четвертым символом должна быть буква H',
         )
      ),
      18 => array
      (
         'name' => 'Acura MDX',
         'cond' => array
         (
            'год выпуска' => '2001-2003',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'начинается с 2HNYD189',
         )
      ),
      19 => array
      (
         'name' => 'Lexus RX 350',
         'cond' => array
         (
            'год выпуска' => '2007-2008',
            'пробег' => 'больше 75 000 миль',
            'VIN-код' => 'четвертым символом должна быть буква H',
         ),
      ),
      20 => array
      (
         'name' => 'Toyota 4RUNNER',
         'cond' => array
         (
            'год выпуска' => '2003-2007',
            'пробег' => 'больше 100 000 миль',
            'VIN-код' => 'начинаются с JTEBU17',
         )
      ),
   )
);