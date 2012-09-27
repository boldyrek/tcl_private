<?php defined('SYSPATH') or die('No direct script access.');

class Jelly_Model extends Jelly_Model_Core {

   public function validate($data = NULL)
   {
      if ($data === NULL)
      {
         $data = $this->_changed;
      }

      if (empty($data))
      {
         return $data;
      }

      return $data;

      /*
      // Create the validation object
      $data = Validation::factory($data);

      // If we are passing a unique key value through, add a filter to ensure it isn't removed
      if ($data->offsetExists(':unique_key'))
      {
         $data->filter(':unique_key', 'trim');
      }

      // Loop through all columns, adding rules where data exists
      foreach ($this->_meta->fields() as $column => $field)
      {
         // Do not add any rules for this field
         if (! $data->offsetExists($column))
         {
            continue;
         }

         $data->label($column, $field->label);
         $data->filters($column, $field->filters);
         $data->rules($column, $field->rules);
         $data->callbacks($column, $field->callbacks);
      }

      if (! $data->check())
      {
         throw new Validation_Exception($data);
      }

      return $data->as_array();
      */
   }

}