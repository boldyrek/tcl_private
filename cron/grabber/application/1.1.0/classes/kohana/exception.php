<?php defined('SYSPATH') or die('No direct script access.');

class Kohana_Exception extends Kohana_Kohana_Exception {

   public static function text(Exception $e)
   {
      return sprintf("%s [ %s ]: %s ~ %s [ %d ]\n%s",
         get_class($e), $e->getCode(), strip_tags($e->getMessage()), Debug::path($e->getFile()), $e->getLine(), $e->getTraceAsString());
   }
}
