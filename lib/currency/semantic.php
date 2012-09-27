<?php

class Currency_Semantic {

   private $_sum = 0;
   private $_sign = true;

   public function __construct($value)
   {
      $this->_sum = abs($value);

      if ($value < 0)
      {
         $this->_sign = false;
      }
   }

   public static function factory($value, $lang)
   {
      $class = 'Currency_Semantic_'.ucfirst($lang);
      return new $class($value);
   }

   protected function _semantic($i, &$words, &$many, $f)
   {
      $words = '';
      $fl = 0;

      if ($i >= 100)
      {
         $jkl = intval($i / 100);
         $words .= $this->_nums_hundred_fold[$jkl];
         $i %= 100;
      }

      if ($i >= 20)
      {
         $jkl = intval($i / 10);
         $words .= $this->_nums_ten_fold[$jkl];
         $i %= 10;
         $fl = 1;
      }

      switch ($i)
      {
         case 1: $many = 1; break;
         case 2:
         case 3:
         case 4: $many = 2; break;
         default: $many = 3; break;
      }

      if ($i)
      {
         if ($i < 3 && $f == 1)
            $words.=$this->_nums_1_2[$i];
         else
            $words.=$this->_nums_1_19[$i];
      }
   }

   /**
    * @return string
    * @see http://library.mnwhost.ru/webdev/php/sum.php
    */
   public function toString()
   {
      $output = '';
      $sum = '';
      $cents = intval(($this->_sum*100-intval($this->_sum)*100));

      $L = (int) $this->_sum;

      if ($L >= 1000000000)
      {
         $many = 0;
         $this->_semantic(intval($L/1000000000), $sum, $many, 3);
         $output .= $sum.$this->_names_billion[$many];
         $L %= 1000000000;

         if ($L == 0)
         {
            $output .= $this->_currency_names[3];
         }
      }

      if ($L >= 1000000)
      {
         $many = 0;
         $this->_semantic(intval($L/1000000), $sum, $many, 2);
         $output .= $sum.$this->_names_million[$many];
         $L %= 1000000;

         if ($L == 0)
         {
            $output .= $this->_currency_names[3];
         }
      }

      if ($L >= 1000)
      {
         $many = 0;
         $this->_semantic(intval($L/1000), $sum, $many, 1);
         $output .= $sum.$this->_names_thousand[$many];
         $L %= 1000;

         if ($L == 0)
         {
            $output .= $this->_currency_names[3];
         }
      }

      if ($L != 0)
      {
         $many = 0;
         $this->_semantic($L, $sum, $many, 0);
         $output .= $sum.$this->_currency_names[$many];
      }

      if ($cents > 0)
      {
         $many = 0;
         $this->_semantic($cents, $sum, $many, 1);
         $output = trim($output).', '.$cents.' '.$this->_cents[$many];
      }

      return (! $this->_sign ? '- ' : '').trim($output);
   }

   public function __toString()
   {
      return $this->toString();
   }

}
