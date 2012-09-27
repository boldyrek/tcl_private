<?php defined('SYSPATH') or die('No direct script access.');

interface Kohana_Source {

   public function execute();

   public function get_ident($url);

}