<?php

define('MODULE', 'grabber');
define('DOCROOT', $_SERVER['DOCUMENT_ROOT']);
define('SYSPATH', DOCROOT.'/mod/'.MODULE);
define('VERSION', '1.1.0');

set_time_limit(0);
error_reporting(E_ALL^E_NOTICE);
ini_set('display_erorrs', 'On');

require DOCROOT.'/Zend/Config.php';
require SYSPATH.'/'.VERSION.'/core.php';

$config_root = new Zend_Config(require DOCROOT.'/cron/'.MODULE.'/application/'.VERSION.'/config/root.php');
$config_search = new Zend_Config(require DOCROOT.'/cron/'.MODULE.'/application/'.VERSION.'/config/search.php');
$config_colors = new Zend_Config(require DOCROOT.'/cron/'.MODULE.'/application/'.VERSION.'/config/colors.php');

$configs = array
(
   'root' => $config_root->toArray(),
   'search' => $config_search->toArray(),
   'colors' => $config_colors->toArray(),
);

Zend_Registry::set('config', $configs);

if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 11)
   header('Location: /public');

$action = (empty($_GET['action'])) ? 'list' : $_GET['action'];

$core = new Core;

switch ($action)
{
   case 'list':
   default:
      $core->drawList();
      break;
   case 'grid':
      $core->drawGrid();
      break;
   case 'recommend':
      $core->drawUsers();
      break;
   case 'email':
      $core->email();
      break;

   // admin
   case 'add':
      $core->add();
      break;
   case 'edit':
      $core->edit();
      break;
   case 'start':
      $core->start();
      break;
   case 'stop':
      $core->stop();
      break;
}
