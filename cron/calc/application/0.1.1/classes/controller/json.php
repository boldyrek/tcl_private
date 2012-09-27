<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_JSON extends Controller {

   const DATE_FORMAT = 'd.m.Y';

   protected $_body = array();

   protected $_id;
   protected $_year;
   protected $_search;

   public function before()
   {
      $this->_id = $this->request->query('id');
      $this->_year = $this->request->query('year');
      $this->_search = $this->request->query('search');
   }

   public function after()
   {
      if (! $this->request->query('debug'))
      {
         $this->response->headers('Content-Type', 'text/javascript;charset=utf-8');
         $this->response->body(json_encode($this->_body));
      }
      else
      {
         Debug::vars($this->_body);
      }
   }

}