<?php defined('SYSPATH') or die('No direct access allowed.');

return array(
   'default' => array
   (
      'type' => 'mysql',
      'connection' => array
      (
         'hostname' => 'localhost',
         'database' => 'boldyrek_db0',
         'username' => 'root',
         'password' => '',
         'persistent' => FALSE,
      ),
      'table_prefix' => 'ccl_grabber_',
      'charset' => 'utf8',
      'caching' => FALSE,
      'profiling' => TRUE,
   ),
   'production' => array
   (
      'type' => 'mysql',
      'connection' => array
      (
         'hostname' => 'localhost',
         'database' => 'boldyrek_db1',
         'username' => 'root',
         'password' => 'ieSeTiengae7Sh',
         'persistent' => FALSE,
      ),
      'table_prefix' => 'ccl_grabber_',
      'charset' => 'utf8',
      'caching' => FALSE,
      'profiling' => TRUE,
   ),
);