<?php defined('SYSPATH') or die('No direct script access.');

class Jelly_Model extends Jelly_Model_Core {

   const DETALIS = 'detalis';

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
   }

   /**
    * Поиск по условиюю
    *
    * @param array $search
    * @param Jelly_Builder $select
    * @param string $append
    * @return Database_MySQL_Result object
    */
   public function search($search, Jelly_Builder $select)
   {
      if (! is_array($search))
      {
         throw new Kohana_Exception('Condition must be an array. :type given',
            array(':type' => gettype($search)));
      }

      try
      {
         $sql  = (string) $select;
         $sql .= ' HAVING (';
         $sql .= '('.self::_build_like($search['condition']).')';

         if (Arr::get($search, 'exception'))
         {
            $sql .= ' AND ('.self::_build_like($search['exception'], FALSE).')';
         }

         $sql .= ')';

         return DB::query(Database::SELECT, $sql)->execute();
      }
      catch (Kohana_Database_Exception $e)
      {
         return FALSE;
      }
   }

   /**
    * Возвращает строку условий отбора.
    *
    * на входе:
    * foo|(bar,baz)
    *
    * на выходе:
    * `detalis` LIKE|NOT LIKE '%foo%'
    * OR (`detalis` LIKE '%bar%' AND `detalis` LIKE '%baz%')
    *
    * @param string $condition
    * @return string
    */
   protected static function _build_like($condition, $like = TRUE)
   {
      $pattern = array(
         '/([a-z0-9-\s]+)/i',
         '[,]',
         '[\|]',
         "['%]",
      );

      $replace = array(
         "'%$1%'",
         ' AND ',
         ' OR ',
         "`".self::DETALIS."` ".(! $like ? 'NOT' : '')." LIKE '%",
      );

      return preg_replace($pattern, $replace, $condition);
   }

}