<?php defined('SYSPATH') or die('No direct script access.');
/**
 * simple controller
 */
class Controller {

   public function index()
   {
      $this->execute();
   }

   public function execute()
   {
      Cron::instanse()->run();
   }

   public function autotrader()
   {
      Cron::instanse()->run(5);
   }

   public function phpinfo()
   {
      phpinfo();
   }

   public function test()
   {
      $data = Vincode::factory('lexuspartsnow')->get('JTJHF10UX30295782');
      Core::debug($data);
   }

}