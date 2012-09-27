<?php

defined('SYSPATH') or die('No direct script access.');

class Debug extends Kohana_Debug {

   public static function vars()
   {
      if (func_num_args() === 0)
         return;

      $variables = func_get_args();

      $output = array();
      foreach ($variables as $var)
      {
         $output[] = str_replace('&nbsp;&hellip;', '', Debug::_dump($var, NULL));
      }

      echo '<pre class="debug">'.implode("\n", $output).'</pre>';
   }

}
