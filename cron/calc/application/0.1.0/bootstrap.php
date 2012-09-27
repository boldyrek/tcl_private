<?php defined('SYSPATH') or die('No direct script access.');

require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
   // Application extends the core
   require APPPATH.'classes/kohana'.EXT;
}
else
{
   // Load empty core extension
   require SYSPATH.'classes/kohana'.EXT;
}

Kohana::$environment = Kohana::PRODUCTION;

date_default_timezone_set((Kohana::$environment == Kohana::DEVELOPMENT) ? 'Asia/Bishkek' : 'America/Toronto');

setlocale(LC_ALL, 'en_US.utf-8');

spl_autoload_register(array('Kohana', 'auto_load'));

ini_set('unserialize_callback_func', 'spl_autoload_call');

I18n::lang('en-us');

Kohana::init(array(
   'base_url' => '/cron/calc/',
   'index_file' => FALSE,
));

Kohana::$log->attach(new Log_File(APPPATH.'logs'));

Kohana::$config->attach(new Config_File);

Kohana::modules(array(
   'database' => MODPATH.'database',
   'jelly'    => MODPATH.'jelly-0.9.6.2',
   'cache'    => MODPATH.'cache'
));

if (Kohana::$environment == Kohana::PRODUCTION)
{
   Database::$default = 'production';
}
else
{
   error_reporting(E_ALL);
   ini_set('display_errors', 'On');
}

set_time_limit(0);

// import
Route::set('import', '<controller>/import/<id>(/<year>)', array(
   'controller' => 'mds|ptm',
   'id' => '\d+',
   'year' => '\d{4}',
))
->defaults(array(
   'action' => 'import',
));

Route::set('ptm-export-chart', 'ptm/export/chart.json')
->defaults(array(
   'directory' => 'ptm',
   'controller' => 'export',
   'action' => 'chart'
));

// grid export
Route::set('export-grid', '<directory>/export/grid.json', array(
   'directory' => 'mds|ptm',
))
->defaults(array(
   'controller' => 'export',
   'action' => 'grid'
));

Route::set('mds-export', 'mds/export/<action>.json', array(
   'action' => 'view|stat|add'
))
->defaults(array(
   'directory' => 'mds',
   'controller' => 'export',
   'action' => 'view'
));

Route::set('default', '(<controller>(/<action>(/<id>)))')
->defaults(array(
   'controller' => 'welcome',
   'action' => 'index',
));
