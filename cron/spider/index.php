<?php

error_reporting(E_ALL);
set_time_limit(0);
ini_set('display_errors', 'On');

define('MODULE', 'spider');
define('DOCROOT', $_SERVER['DOCUMENT_ROOT']);
define('SYSPATH', DOCROOT.'/cron/'.MODULE);
define('APPENV', 'production');

set_include_path
(
   get_include_path()
   .PATH_SEPARATOR.
   SYSPATH.'/class/'
   .PATH_SEPARATOR.
   DOCROOT.'/'
);

require SYSPATH.'/class/core.php';

// Init core
Core::init();

// Get config
$config = new Zend_Config(require SYSPATH.'/config/core.php', TRUE);

// merge base configs
$config->merge(new Zend_Config(require SYSPATH.'/config/database.php'));
$config->merge(new Zend_Config(require SYSPATH.'/config/search.php'));
$config->merge(new Zend_Config(require SYSPATH.'/config/vincode.php'));
$config->merge(new Zend_Config(require SYSPATH.'/config/colors.php'));

// merge sources
if ($handle = opendir(SYSPATH.'/config/source'))
{
   while (FALSE !== ($file = readdir($handle)))
   {
      if ($file != '.' AND $file != '..' AND $file != '.svn')
      {
         $config->merge(new Zend_Config(require SYSPATH.'/config/source/'.$file));
      }
   }

   closedir($handle);
}

// Init logger
$stream = fopen(SYSPATH.'/logs/'.date('d-m-Y').'.log', 'a+');
$writer = new Zend_Log_Writer_Stream($stream);
$logger = new Zend_Log($writer);

// Register objects
Zend_Registry::set('config', $config);
Zend_Registry::set('logger', $logger);

// Init database
// Database::init();

// Parse request
$uri = trim($_SERVER['REQUEST_URI'], '/');
$uri = explode('/', $uri);
// Find action
$action = Arr::get($uri, 2, 'index');
// Init controller
$class = new ReflectionClass('controller');
$controller = $class->newInstance();
// Execute request
$class->getMethod($action)->invoke($controller);