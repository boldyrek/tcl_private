<?php

define('DOCROOT', $_SERVER['DOCUMENT_ROOT']);
define('SYSPATH', DOCROOT.'/mod/mds');
define('VERSION', '0.1.3');

set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
ini_set('display_erorrs', 'On');

require DOCROOT.'/Zend/Config.php';
require SYSPATH.'/class/controller.php';
require SYSPATH.'/class/view.php';

Zend_Registry::set('config', new Zend_Config(require DOCROOT.'/cron/calc/application/'.VERSION.'/config/config.php'));

if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 11)
   header('Location: /public');

$action = (empty($_GET['action'])) ? 'list' : $_GET['action'];

$controller = new Controller;

switch ($action)
{
   case 'list':
   default:
      $controller->drawList();
      break;
   case 'grid':
      $controller->drawGrid();
      break;
   case 'stat':
      $controller->drawStat();
      break;
   case 'remove':
      $controller->removeSearch();
      break;
   case 'stop':
      $controller->setEndDate();
      break;
   case 'start':
      $controller->setStartDate();
      break;
}
