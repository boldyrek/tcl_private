<?php

defined('SYSPATH') or die('No direct script access.');

class Filter {

   protected $filters;
   protected $matches;

   public function __construct(array $filters = NULL, array $matches = NULL)
   {
      $this->filters = $filters;
      $this->matches = $matches;
   }

   public static function factory($filters = NULL, $matches = NULL)
   {
      return new Filter($filters, $matches);
   }

   public function validate()
   {
      // считаем количество элементов из сравниваемого массива
      $matches_count = count($this->matches);

      $filters_count = 0;

      // для каждого фильтра
      foreach ($this->filters AS $filter => $value)
      {
         // проверяем наличие метода
         if (method_exists($this, $filter))
         {
            // если нет сравниваемого значения
            if (!array_key_exists($filter, $this->matches))
            {
               // минусуем резульат
               $filters_count--;
            }

            // увеличиваем результат на "1"
            $filters_count += $this->$filter($value, $this->matches[$filter]);
         }
      }

      // фильтруем по аукциону
      if ($auction = Arr::get($this->matches, 'auction'))
      {
         $filters_count += $this->auction($auction);
      }

      // return array($filters_count, $matches_count, ($filters_count === $matches_count));
      // сравниваем результаты
      return ($filters_count === $matches_count);
   }

   /* ------------------- Filters -------------------- */

   /**
    * Vincode filter.
    *
    * @param (string) $vincode - vincode pattern
    * @param (string) $match - matches value
    * @return (int)
    */
   public function vincode($vincode, $match = NULL)
   {
      return preg_match($vincode, $match);
   }

   /**
    * Millage filter.
    *
    * @param (int) $millage - millage
    * @param (int) $match - matches value
    * @return (int)
    */
   public function millage($millage, $match = NULL)
   {
      return (int) ((int) $match >= $millage);
   }

   /**
    * Auction filter.
    *
    * @param (string) $auction
    * @return (int)
    */
   public function auction($auction)
   {
      $config = Zend_Registry::get('config');
      $auctions = $config->search->auctions->toArray();

      return (int) array_key_exists($auction, $auctions);
   }

   /**
    * Color filter.
    *
    * @param (string) $color
    * @param (string) $match
    * @return (int)
    */
   public function color($color, $match = NULL)
   {
      if ($color === FALSE)
         return 1;

      return preg_match($color, $match);
   }

   /**
    * Series filter.
    *
    * @param (string) $series
    * @param (string) $match
    * @return (int)
    */
   public function series($series, $match = NULL)
   {
      if ($series === FALSE)
         return 1;

      return preg_match($series, $match);
   }

}