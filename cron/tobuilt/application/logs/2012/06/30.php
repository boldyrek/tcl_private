<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-06-30 20:00:01 --- INFO: --- start: 30-06-2012 20:00:01
2012-06-30 20:00:02 --- ERROR: RuntimeException [ 0 ]: SplFileInfo::openFile(/home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/cache/ee/ee6a39d50d489755e011d7dd92040c3d31281f1b.cache): failed to open stream: Permission denied ~ MODPATH/cache/classes/kohana/cache/file.php [ 234 ]
2012-06-30 20:00:02 --- STRACE: RuntimeException [ 0 ]: SplFileInfo::openFile(/home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/cache/ee/ee6a39d50d489755e011d7dd92040c3d31281f1b.cache): failed to open stream: Permission denied ~ MODPATH/cache/classes/kohana/cache/file.php [ 234 ]
--
#0 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/modules/cache/classes/kohana/cache/file.php(234): SplFileInfo->openFile('w')
#1 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/tobuilt.php(18): Kohana_Cache_File->set('firstpage', '<HTML>?<HEAD>?<...')
#2 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/controller/home.php(33): Kohana_Tobuilt->parse()
#3 [internal function]: Controller_Home->action_tobuilt()
#4 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Home))
#5 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#6 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#7 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/index.php(109): Kohana_Request->execute()
#8 {main}
2012-06-30 20:00:01 --- INFO: --- start: 30-06-2012 20:00:01
2012-06-30 20:00:03 --- ERROR: ErrorException [ 8 ]: Undefined index: MapSearchResults ~ APPPATH/classes/kohana/realtor.php [ 21 ]
2012-06-30 20:00:03 --- STRACE: ErrorException [ 8 ]: Undefined index: MapSearchResults ~ APPPATH/classes/kohana/realtor.php [ 21 ]
--
#0 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/realtor.php(21): Kohana_Core::error_handler(8, 'Undefined index...', '/home/clients/b...', 21, Array)
#1 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/controller/home.php(40): Kohana_Realtor->parse()
#2 [internal function]: Controller_Home->action_realtor()
#3 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Home))
#4 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#5 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#6 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/index.php(109): Kohana_Request->execute()
#7 {main}