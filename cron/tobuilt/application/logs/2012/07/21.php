<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-07-21 20:00:01 --- INFO: --- start: 21-07-2012 20:00:01
2012-07-21 20:00:02 --- ERROR: ErrorException [ 8 ]: Undefined index: MapSearchResults ~ APPPATH/classes/kohana/realtor.php [ 21 ]
2012-07-21 20:00:02 --- STRACE: ErrorException [ 8 ]: Undefined index: MapSearchResults ~ APPPATH/classes/kohana/realtor.php [ 21 ]
--
#0 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/realtor.php(21): Kohana_Core::error_handler(8, 'Undefined index...', '/home/clients/b...', 21, Array)
#1 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/controller/home.php(40): Kohana_Realtor->parse()
#2 [internal function]: Controller_Home->action_realtor()
#3 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Home))
#4 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/index.php(109): Kohana_Request->execute()
#7 {main}