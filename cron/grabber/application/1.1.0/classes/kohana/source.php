<?php defined('SYSPATH') or die('No direct script access.');

interface Kohana_Source {

   public function execute();

   //возвращает уникальный id машины для поиска по кешу
   public function get_ident($url);

}