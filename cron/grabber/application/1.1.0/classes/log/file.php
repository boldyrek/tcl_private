<?php defined('SYSPATH') or die('No direct script access.');

class Log_File extends Kohana_Log_File {

   const EXT = '.log';
   
   public function write(array $messages)
   {
      $directory = $this->_directory;

      if (! is_dir($directory))
      {
         mkdir($directory, 02777);
         chmod($directory, 02777);
      }

      $filename = $directory.date('d-m-Y').self::EXT;

      if (! file_exists($filename))
      {
         @chmod($filename, 0666);
      }

      foreach ($messages AS $message)
      {
         file_put_contents($filename,PHP_EOL.Arr::get($message, 'time').' --- '.Arr::get($this->_log_levels, Arr::get($message, 'level')).': '.Arr::get($message, 'body'), FILE_APPEND);
      }
   }

}