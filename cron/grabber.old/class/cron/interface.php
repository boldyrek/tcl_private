<?php defined('SYSPATH') or die('No direct access allowed.');

interface Cron_Interface {
   
   /**
    * Выполняет крон.
    *
    * @access public
    * @return void
    */
   public function execute($sourse_id);
   
   /**
    * Парсит html ответа.
    *
    * @access protected
    * @param (string) $content
    * @param (array) $filters
    * @return (array)
    */
   public function parse($content, array $filters = NULL);
   
   /**
    * Проходит авторизацию.
    * 
    * @access protected
    * @return (boolean)
    */
   public function login();
   
   /**
    * Проверяет, авторизирован ли запрос.
    *
    * @param (string) $response
    * @return (boolen)
    */
   public function is_logged($response = NULL);
}