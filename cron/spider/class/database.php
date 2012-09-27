<?php defined('SYSPATH') or die('No direct script access.');

class Database {

   const SELECT = 1;
   const INSERT = 2;
   const UPDATE = 3;
   const DELETE = 4;

   protected $_connection;
   protected $_last_query;
   protected $_insert_id;
   protected $_affected_rows;

   protected function _connect()
   {
      $config = Zend_Registry::get('config')->database->{APPENV};

      try
      {
         $this->_connection = mysql_connect($config->hostname, $config->username, $config->password, TRUE);
      }
      catch (ErrorException $e)
      {
         throw $e;
      }
      catch (Exception $e)
      {
         throw $e;
      }

      $this->_select_db($config->database);

      $this->_set_charset();
   }

   protected function _disconnect()
   {
      try
      {
         $status = TRUE;

         if (is_resource($this->_connection))
         {
            if ($status = mysql_close($this->_connection))
            {
               $this->_connection = NULL;
            }
         }
      }
      catch (Exception $e)
      {
         $status = ! is_resource($this->_connection);
      }

      return $status;
   }

   protected function _select_db($database)
   {
      if (! mysql_select_db($database, $this->_connection))
         throw new Exception(mysql_error($this->_connection));
   }

   protected function _set_charset($charset = 'utf8')
   {
      $status = function_exists('mysql_set_charset') ? mysql_set_charset($charset, $this->_connection) : $this->query(NULL, 'SET NAMES '.Database::quoteIdentifier($charset));

      if ($status === FALSE)
         throw new Exception(mysql_error($this->_connection));
   }

   public function query($type, $sql)
   {
      try
      {
         $this->_connect();

         if (($result = mysql_query($sql, $this->_connection)) === FALSE)
            throw new Exception(mysql_error($this->_connection).' ['.$sql.']');

         $this->_last_query = $sql;

         if ($type == Database::SELECT)
         {
            $output = array();

            if (mysql_num_rows($result))
            {
               while ($row = mysql_fetch_array($result, MYSQL_ASSOC))
               {
                  $output[] = $row;
               }

               return $output;
            }

            return NULL;
         }

         $this->_insert_id = mysql_insert_id($this->_connection);
         $this->_affected_rows = mysql_affected_rows($this->_connection);

         return $this->metadata();
      }
      catch (ErrorException $e)
      {
         throw $e;
      }
      catch (Exception $e)
      {
         throw $e;
      }
   }

   public function metadata()
   {
      return array
      (
         'last_query' => $this->_last_query,
         'insert_id' => $this->_insert_id,
         'affected_rows' => $this->_affected_rows,
      );
   }

   public static function instanse()
   {
      static $instanse;

      if (empty($instanse))
      {
         $instanse = new Database;
      }

      return $instanse;
   }

   /**
    * Quote identifier.
    * Database::quoteIdentifier('foo') will return "`foo`"
    * 
    * @param (mixed) $value
    * @return (string)
    */
   public static function quoteIdentifier($value)
   {
      if (is_array($value))
      {
         foreach ($value AS $val)
         {
            $cols[] = "`{$val}`";
         }

         return implode(', ', $cols);
      }

      return "`{$value}`";
   }

   /**
    * Quote values.
    * Database::quoteValues(array('foo' => 'bar')) will return "'bar'"
    * Database::quoteValues(array('foo' => 'bar'), TRUE) will return "`foo` = 'bar'"
    *
    * @param (array) $binds
    * @param (boolean) $update
    * @return (string)
    */
   public static function quoteValues(array $binds, $update = FALSE)
   {
      if ($update)
      {
         foreach ($binds AS $key => $value)
         {
            $cols[] = self::quoteIdentifier($key) . ' = ' . self::quote($value);
         }
      }
      else
      {
         foreach ($binds AS $value)
         {
            $cols[] = self::quote($value);
         }
      }

      return implode(', ', $cols);
   }

   /**
    * Quote.
    * Database::quote('foo') will return "'foo'"
    *
    * @param (mixed) $value
    * @return (string)
    */
   public static function quote($value)
   {
      if (is_float($value))
         $value = sprintf('%F', $value);

      return "'" . addcslashes($value, "\000\n\r\\'\"\032") . "'";
   }

   final public function  __destruct()
   {
      // $this->_disconnect();
   }

}