<?php defined('SYSPATH') or die('No direct script access.');

class Filecache {

   protected $_remote_options = array();

   protected $_url;
   
   protected $_path;

   public function __construct()
   {
   }

   public static function factory()
   {
      return new Filecache;
   }

   public function set_url($url)
   {
      $this->_url = $url;

      return $this;
   }

   public function set_path($path)
   {
      $path = DOCROOT.'../../gcache/'.trim($path, '/');

      if (! is_dir($path))
      {
         @mkdir($path, 0755, TRUE);

         @chmod($path, 0755);
      }
      else
      {
         $path = FALSE;
      }

      $this->_path = $path;

      return $this;
   }

   public function set_remote_options(array $remote_options)
   {
      $this->_remote_options = $remote_options;

      return $this;
   }

   public function import($html = FALSE)
   {
      // cache already exists
      if (FALSE === $this->_path)
         return TRUE;

      if (! $html)
      {
         $html = Remote::factory($this->_url, $this->_remote_options)->execute();
      }

      // JUST OVERWRITE FILES

      // get all "<img|script src=''>" paths
      preg_match_all('/src=(["\'])(.*?)\1/', $html, $src);

      // get all "<link href=''>" paths
      preg_match_all('/<link.*href=["\'](.*)["\'].*>/sU', $html, $href);

      // merge paths
      $matches = array_merge($src[2], $href[1]);

      // for all images, scripts & styles
      foreach ($matches AS $path)
      {
         if (trim($path) != '')
         {
            // external source as "http(s)://"
            if (preg_match('#http?(s)?://#', $path))
            {
               // parse url
               $parsed_url = parse_url($path);

               // http://www.auctionpipeline.com hack
               $path = str_replace(';', '?', $parsed_url['path']);

               // source without query string
               $source = $parsed_url['scheme'].'://'.$parsed_url['host'].parse_url($path, PHP_URL_PATH);
            }
            else // local source
            {
               if (strpos($path, '/') === 0) // absolute path as "/"
               {
                  $parsed_url = parse_url($this->_url);
                  
                  $source = $parsed_url['scheme'].'://'.$parsed_url['host'].$path;
               }
               else // relative path such as "../" or empty path
               {
                  // find full path to source file (without quesry string)
                  $source = rtrim(dirname($this->_url), '/').'/'.parse_url($path, PHP_URL_PATH);
               }
            }

            // find desctination path
            $dest = $this->_path.'/'.pathinfo($source, PATHINFO_BASENAME);

            // copy source file into local storage if file is not exists
            if (! file_exists($dest))
            {
               @copy($source, $dest);
            }
         }
      }

      unset($matches);

      // replace script & images src.
      $html = preg_replace('#src=[\'"].*([-_a-zA-Z0-9.]+.(\w+))[\'"]#U', 'src="$1"', $html);

      // replace style & favicon href.
      $html = preg_replace('#<link(.+)href=[\'"].*([-_a-zA-Z0-9.]+.(\w+))[\'"](.+)>#sU', '<link$1href="$2"$4>', $html);

      // write into file
      $filename = $this->_path.'/index.html';

      @file_put_contents($filename, $html);

      unset($html);

      // write/update changelog time
      $h = fopen($this->_path.'/changelog.txt', 'w+');

      fwrite($h, (string) time());
      
      fclose($h);

      return TRUE;
   }

}