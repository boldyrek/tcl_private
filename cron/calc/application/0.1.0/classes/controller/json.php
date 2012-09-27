<?php defined('SYSPATH') or die('No direct script access.');

abstract class Controller_JSON extends Controller {

   protected $_body = array();

   const DATE_FORMAT = 'd.m.Y';

   public function before()
   {
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