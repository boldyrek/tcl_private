<?php defined('SYSPATH') or die('No direct access allowed.');

return array
(
   'core' => array
   (
      'sources' => array
      (
         1 => 'Exporttrader',
         2 => 'Adesa',
	 4 => 'Openlane',
         3 => 'Ebay',
         6 => 'Cars',
         5 => 'Autotrader',
      ),

      // ID источников кторые будут запускаться отдельно
      'alones' => array(),

      'remote' => array
      (
         CURLOPT_RETURNTRANSFER => TRUE,
         CURLOPT_SSL_VERIFYHOST => FALSE,
         CURLOPT_SSL_VERIFYPEER => FALSE,
         CURLOPT_CONNECTTIMEOUT => 0,
         CURLOPT_TIMEOUT => 300,
         CURLOPT_USERAGENT => $_SERVER['HTTP_USER_AGENT'],
      ),
      
      'log_sql' => FALSE,
      
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
                  // 'VIN-код' => 'начинается с JHLRD788, SHSRD788',
                  'цвет кузова' => 'Silver'
               )
            ),
            10 => array
            (
               'name' => 'Honda Pilot',
               'cond' => array
               (
                  'год выпуска' => '2003-2005',
                  'пробег' => 'больше 100 000 миль',
                  'VIN-код' => 'начинается с 5FNYF186, 5FNYF187, 2HKYF186, 2HKYF187',
                  'цвет кузова' => 'Silver, Black'
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
                  'пробег' => 'больше 120 000 миль',
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
         ),
      )
   )
);