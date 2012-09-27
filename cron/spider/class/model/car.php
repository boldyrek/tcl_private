<?php

class Model_Car extends Model {

   protected $_table_name = 'ccl_spider_cars';

   public function color_filter($id, array $colors)
   {
      $sql = ' ( '
      .Database::quoteIdentifier('color_outside').' IS NULL OR '.Database::quoteIdentifier('color_inside').' IS NULL ';

      if (isset($colors['outside']))
      {
         $sql .= ' OR '.Database::quoteIdentifier('color_outside').' NOT IN ('.Database::quoteValues(array_keys($colors['outside'])).') ';
      }

      if (isset($colors['inside']))
      {
         $sql .= ' OR '.Database::quoteIdentifier('color_inside').' NOT IN ('.Database::quoteValues(array_keys($colors['inside'])).') ';
      }

      $sql .= ' ) AND '.Database::quoteValues(array('search_id' => $id), TRUE);

      $result = $this->delete($sql);

      return $result['affected_rows'];

      // $stmt = $this->_db->query($sql);
      // return $stmt->rowCount();
   }
}