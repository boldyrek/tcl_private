<?php defined('SYSPATH') or die('No direct script access.');

abstract class Cron_Abstarct {
   
   protected $config; // base config
   protected $logger;
   
   protected $car;
   protected $vin;
   protected $status;
   protected $cache;


   protected function __construct()
   {
      $this->config = Zend_Registry::get('config');
      $this->logger = Zend_Registry::get('logger');
      
      $this->car = new Model_Car;
      $this->vin = new Model_Vin;
      $this->status = new Model_Status;
      $this->cache = new Model_Cache;
   }
   
   public static function instanse()
   {
      static $instanse;
      
      empty($instanse) AND $instanse = new Cron;
      
      return $instanse;
   }
   
   public static function factory($source)
   {
      $class = 'Cron_Source_'.ucfirst($source);
      
      return new $class;
   }

   protected function _clean($content)
   {
      return preg_replace(array('/(\n|\r)+/', '/>\s+</', '/\s+/'), array('', '><', ' '), $content);
   }
}