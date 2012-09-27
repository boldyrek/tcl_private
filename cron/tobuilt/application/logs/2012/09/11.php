<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-09-11 20:00:01 --- INFO: --- start: 11-09-2012 20:00:01
2012-09-11 20:00:57 --- ERROR: Request_Exception [ 0 ]: Error fetching remote /cron/tobuilt/index.php/handlers/MapSearchHandler.ashx [ status 0 ] Couldn't resolve host 'www.realtor.ca' ~ SYSPATH/classes/kohana/request/client/curl.php [ 100 ]
2012-09-11 20:00:57 --- STRACE: Request_Exception [ 0 ]: Error fetching remote /cron/tobuilt/index.php/handlers/MapSearchHandler.ashx [ status 0 ] Couldn't resolve host 'www.realtor.ca' ~ SYSPATH/classes/kohana/request/client/curl.php [ 100 ]
--
#0 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client/external.php(137): Kohana_Request_Client_Curl->_send_message(Object(Request))
#1 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client.php(64): Kohana_Request_Client_External->execute_request(Object(Request))
#2 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/content.php(27): Kohana_Request_Client->execute(Object(Request))
#3 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/realtor.php(11): Kohana_Content::get('http://www.real...')
#4 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/controller/home.php(40): Kohana_Realtor->parse()
#5 [internal function]: Controller_Home->action_realtor()
#6 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Home))
#7 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#8 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#9 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/index.php(109): Kohana_Request->execute()
#10 {main}