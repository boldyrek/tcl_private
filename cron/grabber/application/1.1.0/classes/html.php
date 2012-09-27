<?php defined('SYSPATH') or die('No direct script access.');

class HTML extends Kohana_HTML {

   public static function cleanup($str)
   {
      return preg_replace(array('/(\n|\r)+/', '/>\s+</', '/\s+/'), array('', '><', ' '), $str);
   }

}