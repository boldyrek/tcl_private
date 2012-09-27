<?php

defined('SYSPATH') or die('No direct script access.');

class Core {

   public static $charset = 'utf-8';

   public static function init()
   {
      spl_autoload_register(array('Core', 'auto_load'));
      set_exception_handler(array('Core', 'exception_handler'));
   }

   public static function exception_handler(Exception $e)
   {
      $logger = Zend_Registry::get('logger');

      $str = 'Uncaught exception: '.$e->getMessage().' ['.$e->getFile().': '.$e->getLine().']';
      $logger->log($str, Zend_Log::ERR);

      echo $str;

      Core::debug($e->getFile(), $e->getLine(), $e->getTraceAsString());
   }

   public static function auto_load($class)
   {
      if (strtolower(substr($class, 0, 4)) == 'zend')
      {
         $file = str_replace('_', '/', $class); // ZF classes
      }
      else
      {
         $file = str_replace('_', '/', strtolower($class)); // module classes
      }

      if ($path = $file.'.php')
      {
         require $path;
      }

      return FALSE;
   }

   /**
    * Returns an HTML string of debugging information about any number of
    * variables, each wrapped in a "pre" tag:
    *
    *     // Displays the type and value of each variable
    *     Core::debug($foo, $bar, $baz);
    *
    * @author     Kohana Team
    * @copyright  (c) 2008-2009 Kohana Team
    * @param   mixed   variable to debug
    * @param   ...
    * @return  string
    */
   public static function debug()
   {
      if (func_num_args() === 0)
         return;

      // Get all passed variables
      $variables = func_get_args();

      $output = array();
      foreach ($variables as $var)
      {
         $output[] = Core::_dump($var, 1024);
      }

      echo '<pre class="debug">'.implode("\n", $output).'</pre>';
   }

   /**
    * Helper for Core::dump(), handles recursion in arrays and objects.
    *
    * @author     Kohana Team
    * @copyright  (c) 2008-2009 Kohana Team
    * @param   mixed    variable to dump
    * @param   integer  maximum length of strings
    * @param   integer  recursion level (internal)
    * @return  string
    */
   protected static function _dump(& $var, $length = 500, $level = 0)
   {
      if ($var === NULL)
      {
         return '<small>NULL</small>';
      }
      elseif (is_bool($var))
      {
         return '<small>bool</small> '.($var ? 'TRUE' : 'FALSE');
      }
      elseif (is_float($var))
      {
         return '<small>float</small> '.$var;
      }
      elseif (is_resource($var))
      {
         if (($type = get_resource_type($var)) === 'stream' AND $meta = stream_get_meta_data($var))
         {
            $meta = stream_get_meta_data($var);

            if (isset($meta['uri']))
            {
               $file = $meta['uri'];

               /*
                 if (function_exists('stream_is_local'))
                 {
                 // Only exists on PHP >= 5.2.4
                 if (stream_is_local($file))
                 {
                 $file = self::debug_path($file);
                 }
                 }
                */

               return '<small>resource</small><span>('.$type.')</span> '.htmlspecialchars($file, ENT_NOQUOTES, Core::$charset);
            }
         }
         else
         {
            return '<small>resource</small><span>('.$type.')</span>';
         }
      }
      elseif (is_string($var))
      {
         // if (UTF8::strlen($var) > $length)
         if (strlen($var) > $length)
         {
            // Encode the truncated string
            // $str = htmlspecialchars(UTF8::substr($var, 0, $length), ENT_NOQUOTES, Core::$charset).'&nbsp;&hellip;';
            $str = htmlspecialchars(substr($var, 0, $length), ENT_NOQUOTES, Core::$charset).'&nbsp;&hellip;';
         }
         else
         {
            // Encode the string
            $str = htmlspecialchars($var, ENT_NOQUOTES, Core::$charset);
         }

         return '<small>string</small><span>('.strlen($var).')</span> "'.$str.'"';
      }
      elseif (is_array($var))
      {
         $output = array();

         // Indentation for this variable
         $space = str_repeat($s = '    ', $level);

         static $marker;

         if ($marker === NULL)
         {
            // Make a unique marker
            $marker = uniqid("\x00");
         }

         if (empty($var))
         {
            // Do nothing
         }
         elseif (isset($var[$marker]))
         {
            $output[] = "(\n$space$s*RECURSION*\n$space)";
         }
         elseif ($level < 5)
         {
            $output[] = "<span>(";

            $var[$marker] = TRUE;
            foreach ($var as $key => & $val)
            {
               if ($key === $marker)
                  continue;
               if (!is_int($key))
               {
                  $key = '"'.htmlspecialchars($key, ENT_NOQUOTES, Core::$charset).'"';
               }

               $output[] = "$space$s$key => ".Core::_dump($val, $length, $level + 1);
            }
            unset($var[$marker]);

            $output[] = "$space)</span>";
         }
         else
         {
            // Depth too great
            $output[] = "(\n$space$s...\n$space)";
         }

         return '<small>array</small><span>('.count($var).')</span> '.implode("\n", $output);
      }
      elseif (is_object($var))
      {
         // Copy the object as an array
         $array = (array) $var;

         $output = array();

         // Indentation for this variable
         $space = str_repeat($s = '    ', $level);

         $hash = spl_object_hash($var);

         // Objects that are being dumped
         static $objects = array();

         if (empty($var))
         {
            // Do nothing
         }
         elseif (isset($objects[$hash]))
         {
            $output[] = "{\n$space$s*RECURSION*\n$space}";
         }
         elseif ($level < 10)
         {
            $output[] = "<code>{";

            $objects[$hash] = TRUE;
            foreach ($array as $key => & $val)
            {
               if ($key[0] === "\x00")
               {
                  // Determine if the access is protected or protected
                  $access = '<small>'.($key[1] === '*' ? 'protected' : 'private').'</small>';

                  // Remove the access level from the variable name
                  $key = substr($key, strrpos($key, "\x00") + 1);
               }
               else
               {
                  $access = '<small>public</small>';
               }

               $output[] = "$space$s$access $key => ".Core::_dump($val, $length, $level + 1);
            }
            unset($objects[$hash]);

            $output[] = "$space}</code>";
         }
         else
         {
            // Depth too great
            $output[] = "{\n$space$s...\n$space}";
         }

         return '<small>object</small> <span>'.get_class($var).'('.count($array).')</span> '.implode("\n", $output);
      }
      else
      {
         return '<small>'.gettype($var).'</small> '.htmlspecialchars(print_r($var, TRUE), ENT_NOQUOTES, Core::$charset);
      }
   }

}
