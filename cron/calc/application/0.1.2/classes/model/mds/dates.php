<?php defined('SYSPATH') or die('No direct script access.');

class Model_MDS_Dates extends Jelly_Model {

   public static function initialize(Jelly_Meta $meta)
   {
      $meta->db(Database::$default)
      ->table('mds_dates')
      ->fields(array(
         'start_date' => new Field_Timestamp(array(
            'format' => 'Y-m-d'
         )),
         'end_date' => new Field_Timestamp(array(
            'format' => 'Y-m-d'
         )),
      ));
   }

}