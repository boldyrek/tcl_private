<?php defined('SYSPATH') or die('No direct script access.');

class Captcha {

   const RECOGNIZER_URL = 'http://antigate.com/';

   public static function recognize($filename, $apikey, $rtimeout = 5, $mtimeout = 120, $is_phrase = 0, $is_regsense = 0, $is_numeric = TRUE, $min_len = 0, $max_len = 0)
   {
      if (! file_exists($filename))
         throw new Kohana_Exception('File '.$filename.' not found');

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

      if (FALSE !== strpos($result, 'ERROR'))
      {
         Kohana::$log->add(Log::INFO, $result);
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
            Kohana::$log->add(Log::INFO, $result);
            return FALSE;
         }

         if ($result == 'CAPCHA_NOT_READY')
         {
            // captcha is not ready yet
            $waittime += $rtimeout;

            if ($waittime > $mtimeout)
               break;

            // waiting for $rtimeout seconds
            sleep($rtimeout);
         }
         else
         {
            $ex = explode('|', $result);

            if (trim(Arr::get($ex, 0)) == 'OK')
               return (string) trim(Arr::get($ex, 1));
         }
      }

      return FALSE;
   }

}