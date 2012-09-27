<?php defined('SYSPATH') or die('No direct script access.');

class Captcha {

   const RECOGNIZER_URL = 'http://antigate.com/';

   /**
    * Распознает каптчу.
    *
    * @param (string) $filename
    * @param (string) $apikey
    * @param (int) $rtimeout
    * @param (int) $mtimeout
    * @param (int) $is_phrase
    * @param (int) $is_regsense
    * @param (boolean) $is_numeric
    * @param (int) $min_len
    * @param (int) $max_len
    * @return (mixed) string|boolean
    */
   public static function recognize($filename, $apikey, $rtimeout = 5, $mtimeout = 120, $is_phrase = 0, $is_regsense = 0, $is_numeric = TRUE, $min_len = 0, $max_len = 0)
   {
      $logger = Zend_Registry::get('logger');

      if (! file_exists($filename))
         throw new Exception('File '.$filename.' not found');

      $postdata = array
      (
         'method' => 'post',
         'key' => $apikey,
         'file' => '@'.$filename,
         'phrase' => $is_phrase,
         'regsense' => $is_regsense,
         'numeric' => $is_numeric,
         'min_len' => $min_len,
         'max_len' => $max_len,
      );

      $url = self::RECOGNIZER_URL;

      $options = array
      (
         CURLOPT_RETURNTRANSFER => TRUE,
         CURLOPT_TIMEOUT => 300,
         CURLOPT_POST => TRUE,
         CURLOPT_POSTFIELDS => $postdata
      );

      $result = Remote::factory($url.'in.php', $options)->execute();

      $logger->log('Captcha::recognize() result '.$result, Zend_Log::INFO);

      if (FALSE !== strpos($result, 'ERROR'))
      {
         $logger->log('Captcha::recognize() error '.$result, Zend_Log::INFO);
         return FALSE;
      }

      $ex = explode('|', $result);
      $captcha_id = Arr::get($ex, 1);

      // captcha sent, got captcha ID
      $waittime = 0;

      sleep($rtimeout);

      while (TRUE)
      {
         $result = @file_get_contents($url.'res.php?key='.$apikey.'&action=get&id='.$captcha_id);

         if (FALSE !== strpos($result, 'ERROR'))
         {
            $logger->log('Captcha::recognize() error '.$result, Zend_Log::INFO);
            return FALSE;
         }

         if ($result == 'CAPCHA_NOT_READY')
         {
            // captcha is not ready yet
            $waittime += $rtimeout;

            if ($waittime > $mtimeout)
            // timelimit $mtimeout hit
               break;

            // waiting for $rtimeout seconds
            sleep($rtimeout);
         }
         else
         {
            $logger->log('Captcha::recognize() result '.$result, Zend_Log::INFO);

            $ex = explode('|', $result);

            if (trim($ex[0]) == 'OK')
               return (string) trim($ex[1]);
         }
      }

      return FALSE;
   }

}