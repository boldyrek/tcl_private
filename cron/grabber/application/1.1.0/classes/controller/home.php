<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Home extends Controller {

   private $_token;

   public function before()
   {
      $this->_token = Profiler::start('controller', 'cron');
      
      Kohana::$log->add(Log::INFO, 'start: '.date('d-m-Y H:i:s', (int) microtime(TRUE)));
   }

   public function after()
   {
      Profiler::stop($this->_token);

      list($time, $memory) = Profiler::total($this->_token);

      Kohana::$log->add(Log::INFO, 'finish: '.date('d-m-Y H:i:s', (int) microtime(TRUE)));

      Kohana::$log->add(Log::INFO, 'elapsed time: ~'.round($time, 2).' sec., memory usage: ~'.round(($memory/(1024*1024)), 2).' Mb.');
   }

   public function action_index()
   {
   }

   public function action_execute()
   {
      ///cron/grabber/home/execute/7
       $id = (int) $this->request->param('id');

      Source::instance($id)
      ->reset()
      ->run();

      if ($this->request->is_ajax())
      {
         echo 'Request for <b>'.Kohana::config('root.sources.'.$id).'</b> completed (browse logs for more information)';
      }
   }

}
