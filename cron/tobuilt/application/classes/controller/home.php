<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller {

   protected $_token;

   public function before()
   {
      $this->_token = Profiler::start('controller', 'cron');

      Kohana::$log->add(Log::INFO, '--- start: :date',
         array(':date' => date('d-m-Y H:i:s', (int) microtime(TRUE))));
   }

   public function after()
   {
      Profiler::stop($this->_token);

      list($time, $memory) = Profiler::total($this->_token);

      Kohana::$log->add(Log::INFO, '--- finish: :date, elapsed time: ~:time sec., memory usage: ~:memory_usage Mb.',
         array(
            ':date' => date('d-m-Y H:i:s',(int) microtime(TRUE)),
            ':time' => round($time, 2),
            ':memory_usage' => round(($memory/(1024*1024)), 2)
         ));
   }

   public function action_tobuilt()
   {
      $tobuilt = new Tobuilt;

      $tobuilt->parse();
   }

   public function action_realtor()
   {
      $realtor = new Realtor;

      $realtor->parse();
   }

}
