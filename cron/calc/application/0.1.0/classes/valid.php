<?php defined('SYSPATH') or die('No direct script access.');

class Valid extends Kohana_Valid {

   public static function name($value, $expression)
   {
      if (! $expression)
         return TRUE;
      
      return parent::regex($value, $expression);
   }

   public static function description($value, $expression)
   {
      return ! parent::regex($value, $expression);
   }

}
