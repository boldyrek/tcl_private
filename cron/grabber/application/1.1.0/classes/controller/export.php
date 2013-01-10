<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Export extends Controller {

   protected $_body;

   public function action_index()
   {
      $this->_body = Jelly::select('cars')
         ->order_by('vincode_date_added', 'DESC')
         ->limit(10)
         ->execute()
         ->as_array();
   }
   
   public function action_date()
   {
      $data = Vincode::factory('ip')->get($this->request->query('vincode'));
      $data['date_made'] = preg_replace('/(\d{4})(?:\.)?(\d{2})/', '$2.$1', $data['date_made']);
      
      $this->_body = $data;
   }

   public function after()
   {
      $this->response->headers('Content-Type', 'text/javascript;charset=utf-8');
      $this->response->body(json_encode($this->_body));
   }

}