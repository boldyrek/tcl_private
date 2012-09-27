<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-09-18 20:00:01 --- INFO: --- start: 18-09-2012 20:00:01
2012-09-18 20:00:43 --- ERROR: ErrorException [ 8 ]: Undefined offset: 1 ~ APPPATH/classes/kohana/realtor.php [ 106 ]
2012-09-18 20:00:43 --- STRACE: ErrorException [ 8 ]: Undefined offset: 1 ~ APPPATH/classes/kohana/realtor.php [ 106 ]
--
#0 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/realtor.php(106): Kohana_Core::error_handler(8, 'Undefined offse...', '/home/clients/b...', 106, Array)
#1 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/realtor.php(47): Kohana_Realtor->get_square('<!DOCTYPE HTML ...')
#2 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/controller/home.php(40): Kohana_Realtor->parse()
#3 [internal function]: Controller_Home->action_realtor()
#4 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Home))
#5 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/index.php(109): Kohana_Request->execute()
#8 {main}