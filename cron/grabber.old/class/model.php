<?php

abstract class Model {

   protected $_db;
   protected $_table_name;
   protected $_primary_key = 'id';

   public function __construct()
   {
      $this->_db = Database::instanse();
      // $this->_db = Zend_Registry::get('db');
   }

   public function truncate()
   {
      $sql = 'TRUNCATE TABLE '.Database::quoteIdentifier($this->_table_name);
      return $this->_db->query(NULL, $sql);
   }

   public function find($value)
   {
      $sql = 'SELECT * FROM '
      .Database::quoteIdentifier($this->_table_name)
      .' WHERE '
      .Database::quoteValues(array($this->_primary_key => $value), TRUE).
      ' LIMIT 1';

      $result = $this->_db->query(Database::SELECT, $sql);

      return Arr::get($result, 0);

      // $stmt = $this->_db->query($sql);
      // return $stmt->fetch(PDO::FETCH_ASSOC);
   }

   /**
    *
    * @param array $binds
    * @return array
    */
   public function get(array $binds, $row = FALSE)
   {
      $cols = array();

      $sql = 'SELECT * FROM '
      .Database::quoteIdentifier($this->_table_name)
      .' WHERE ';

      foreach ($binds AS $key => $value)
      {
         $cols[] = Database::quoteIdentifier($key).' = '.Database::quote($value);
      }

      $sql .= implode(' AND ', $cols);

      $result = $this->_db->query(Database::SELECT, $sql);

      if ($row == TRUE)
      {
         return Arr::get($result, 0);
      }
      
      return $result;
   }

   public function insert(array $binds)
   {
      $sql = 'INSERT INTO '
      .Database::quoteIdentifier($this->_table_name)
      .' ('.Database::quoteIdentifier(array_keys($binds)).') '
      .' VALUES '
      .' ('.Database::quoteValues(array_values($binds)).')';

      return $this->_db->query(Database::INSERT, $sql);

      // $this->_db->query($sql);
      // return $this->_db->lastInsertId();
   }

   public function update(array $binds, $where = '')
   {
      $sql = 'UPDATE '
      .Database::quoteIdentifier($this->_table_name)
      .' SET '.Database::quoteValues($binds, TRUE)
      .($where ? ' WHERE '.$where : '');

      return $this->_db->query(Database::UPDATE, $sql);

      // $stmt = $this->_db->query($sql);
      // return $stmt->rowCount();
   }

   public function delete($where = '')
   {
      $sql = 'DELETE FROM '
      .Database::quoteIdentifier($this->_table_name)
      .($where ? ' WHERE '.$where : '');

      return $this->_db->query(Database::DELETE, $sql);

      // $stmt = $this->_db->query($sql);
      // return $stmt->rowCount();
   }
}