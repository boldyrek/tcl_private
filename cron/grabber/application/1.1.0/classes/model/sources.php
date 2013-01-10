<?php defined('SYSPATH') or die('No direct script access.');

class Model_Sources extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('sources')
      ->fields(array(
         'id' => new Field_Primary,
         'name' => new Field_String,
         'active' => new Field_Boolean,
      ));
   }

}