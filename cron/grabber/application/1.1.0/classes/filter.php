<?php defined('SYSPATH') or die('No direct script access.');

class Filter {

   protected $_filters;
   protected $_matches;

   public function __construct($filters = NULL, $matches = NULL)
   {
      $this->_filters = $filters;
      $this->_matches = $matches;
   }

   public static function factory($filters = NULL, $matches = NULL)
   {
      return new Filter($filters, $matches);
   }

   public function validate()
   {
      // считаем количество элементов из сравниваемого массива
      $matches_count = count($this->_matches);

      $filters_count = 0;

      // для каждого фильтра
      foreach ($this->_filters AS $filter => $value)
      {
         // проверяем наличие метода
         if (method_exists($this, $filter))
         {
            // если нет сравниваемого значения
            if (! array_key_exists($filter, $this->_matches))
            {
               // минусуем резульат
               $filters_count--;
            }

            // увеличиваем результат на "1"
            $filters_count += $this->$filter($value, $this->_matches[$filter]);
         }
      }

      // сравниваем результаты
      return ($filters_count === $matches_count);
   }

   public function vincode($vincode, $match = NULL)
   {
      return preg_match($vincode.'i', $match);
   }

   public function mileage($mileage, $match = NULL)
   {
      return (int) ((int) $match >= $mileage);
   }

   public function color($color, $match = NULL)
   {
      if ($color === FALSE)
         return 1;

      return preg_match($color, $match);
   }

   public function series($series, $match = NULL)
   {
      if ($series === FALSE)
         return 1;

      return preg_match($series, $match);
   }

   public function price($price, $match = NULL)
   {
      if ($price === FALSE) return 1;
	        
	  $match = intval(trim($match));
	  if ($price == 0) return 1;
	  
	  if (!is_array($price)) {
	      $price = array($price, $price);
	  }
	  
	  if (!isset($price[0]) || !isset($price[1])) return 0;
	  
      return ($match >= $price[0] && $match <= $price[1]) ? 1 : 0;
   }

}