<?php defined('SYSPATH') or die('No direct script access.');

abstract class Database extends Kohana_Database {

   public function quote($value)
   {
      return (is_int($value)) ? $this->escape($value) : parent::quote($value);
   }

}
