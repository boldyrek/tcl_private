<?php defined('SYSPATH') or die('No direct script access.');

class Model_Options extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('options')
      ->fields(array(
         'vincode' => new Field_String,
         'option' => new Field_String,
         'exists' => new Field_Boolean
      ));
   }

}