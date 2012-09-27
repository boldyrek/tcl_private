<?php defined('SYSPATH') or die('No direct script access.'); ?>

2012-06-20 20:00:01 --- INFO: --- start: 20-06-2012 20:00:01
2012-06-20 20:00:02 --- ERROR: Zend_Json_Exception [ 0 ]: Illegal Token ~ APPPATH/vendor/Zend/Json/Decoder.php [ 457 ]
2012-06-20 20:00:02 --- STRACE: Zend_Json_Exception [ 0 ]: Illegal Token ~ APPPATH/vendor/Zend/Json/Decoder.php [ 457 ]
--
#0 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/vendor/Zend/Json/Decoder.php(113): Zend_Json_Decoder->_getNextToken()
#1 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/vendor/Zend/Json/Decoder.php(154): Zend_Json_Decoder->__construct('<html><head><ti...', 1)
#2 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/vendor/Zend/Json.php(103): Zend_Json_Decoder::decode('<html><head><ti...', 1)
#3 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/kohana/realtor.php(15): Zend_Json::decode('<html><head><ti...')
#4 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/application/classes/controller/home.php(40): Kohana_Realtor->parse()
#5 [internal function]: Controller_Home->action_realtor()
#6 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client/internal.php(118): ReflectionMethod->invoke(Object(Controller_Home))
#7 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request/client.php(64): Kohana_Request_Client_Internal->execute_request(Object(Request))
#8 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/system/classes/kohana/request.php(1138): Kohana_Request_Client->execute(Object(Request))
#9 /home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/cron/tobuilt/index.php(109): Kohana_Request->execute()
#10 {main}