<?php defined('SYSPATH') or die('No direct script access.');

require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
   require APPPATH.'classes/kohana'.EXT;
}
else
{
   require SYSPATH.'classes/kohana'.EXT;
}

setlocale(LC_ALL, 'en_US.utf-8');
spl_autoload_register(array('Kohana', 'auto_load'));
ini_set('unserialize_callback_func', 'spl_autoload_call');
I18n::lang('en-us');

Kohana::init(array(
   'base_url' => '/cron/grabber/',
));

Kohana::$log->attach(new Log_File(APPPATH.'logs'));
Log::$timestamp = 'd-m-Y H:i:s';
Kohana::$config->attach(new Config_File);

Kohana::modules(array(
   'database' => MODPATH.'database',
   'jelly'    => MODPATH.'jelly',
   'cache'    => MODPATH.'cache',
));

Route::set('default', '(<controller>(/<action>(/<id>)))')
->defaults(array(
   'controller' => 'home',
   'action' => 'index',
));

Kohana::$environment = Kohana::DEVELOPMENT;

if (Kohana::$environment == Kohana::PRODUCTION)
{
   Database::$default = 'production';
   date_default_timezone_set('America/Chicago');
}
else
{
   date_default_timezone_set('Asia/Bishkek');
   error_reporting(E_ALL);
   ini_set('display_errors', 'On');
}


set_time_limit(0);

ini_set('mysql.connect_timeout', 3600);
ini_set('default_socket_timeout', 3600);