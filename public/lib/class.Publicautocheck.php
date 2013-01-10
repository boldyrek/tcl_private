<?php

class PublicAutocheck extends Proto {

   const INVALID_VINCODE = 'Invalid VIN';
   const ACCESS_DENIED = 'Access denied';
   const ATTEMPTS = 5;
   const PRODUCTION = false;

   public function makePage()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         if ($_SESSION['autocheck_allowed'])
         {
            $this->page .= $this->makeTopMenu();
            $this->_moduleContent();
         }
         else
         {
            Proto::redirect('/');
         }
      }
      else
      {
         Proto::redirect('/');
      }

      $this->page .= $this->templates['footer'];

      if ($this->page == '')
      {
         $this->errorHandler('Пустая страница!', 0);
      }

      $this->errorsPublisher();
      $this->publish();
   }

   private function _moduleContent()
   {
      $switch = (isset($_GET['sw']) ? $_GET['sw'] : '');

      if ($this->exists($switch))
      {
         switch ($switch)
         {
            case 'get':
               $this->_get();
               break;
            default:
               $this->_from();
               break;
         }
      }
      else
      {
         $this->_form();
      }
   }

   private function _requestsCount()
   {
      $day_start_ts = strtotime(date('Y-m-d 00:00:00'));
      $day_end_ts = strtotime(date('Y-m-d 23:59:59'));

      $query = $this->mysqlQuery("
         SELECT COUNT(*) AS `requests_count`
         FROM `ccl_user_autocheck_log`
         WHERE `user_id` = '".$_SESSION['user_id']."'
         AND UNIX_TIMESTAMP(`check_date`) BETWEEN ".$day_start_ts." AND ".$day_end_ts
      );

      $user = mysql_fetch_object($query);

      return (int) $user->requests_count;
   }

   private function _form()
   {
      $this->page .= '
         <div class="location">Autocheck</div>
         <div class="autocheck">
            <div class="input">
               <label for="vincode">'.$this->translate->_('Введите ВИН-код').':</label>
               <input type="text" id="vincode" value="" maxlength="17" />
               <span>'.$this->translate->_('проверить').'</span>
               <em>('.$this->translate->_('запросов осталось').': <b id="requests-count">'.(self::ATTEMPTS-$this->_requestsCount()).'</b> '.$this->translate->_('из').' '.self::ATTEMPTS.')</em>
            </div>
            <div class="response"></div>
         </div>
         <script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>
         <script type="text/javascript">
            input = $("input#vincode");
            response = $(".autocheck .response");

            $(".autocheck .input span").click(function()
            {
               response.fadeOut();
               
               if (input.val() == "")
               {
                  alert("Empty value");
                  input.focus();
                  return false;
               }
               else
               {
                  input.addClass("input-ajax-loader");

                  $.getJSON("/public/", {mod:"autocheck", sw:"get", vincode:input.val()}, function(json)
                  {
                     input.removeClass("input-ajax-loader");

                     if (json.response == "'.self::INVALID_VINCODE.'"
                      || json.response == "'.self::ACCESS_DENIED.'"
                      || json.preCount == '.self::ATTEMPTS.')
                     {
                        response.addClass("invalid");
                        input.focus();
                     }
                     else
                     {
                        response.removeClass("invalid");
                     }

                     var userAgent = $.browser;
                     
                     if (! userAgent.msie || (userAgent.msie && userAgent.version == "9.0"))
                     {
                        response.fadeIn().html(json.response);
                     }
                     else
                     {
                        window.open("http://'.$_SERVER['SERVER_NAME'].'/public/autocheck/"+json.vincode+".html");
                     }

                     $("#requests-count").text(json.requestsCount);
                  });
               }
            });
         </script>
      ';
   }

   private function _get()
   {
      $vincode = strtoupper($_GET['vincode']);

      $response = '';

      $preCount = 0;

      // only AJAX request allowed
      if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
      {
         $filename = $_SERVER['DOCUMENT_ROOT'].'/public/autocheck/'.$vincode.'.html';

         if (($cached = file_exists($filename)))
         {
            // body from cache
            $response = file_get_contents($filename);
         }
         else
         {
            if (!(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2')||($preCount = $this->_requestsCount()) < self::ATTEMPTS)
            {
               // get body from remote resource
                //$url = 'http://reports.avtostat.com/?'.http_build_query(array(
                $url = 'http://reports.avtostat.com/?'.http_build_query(array(
                    'login' => 'makmalauto',
                    'pass' => '123',
                    'type' => 'autocheck',
                    'vin' => $vincode
                ));

               $curl = curl_init($url);

               $curl_params = array(
                  CURLOPT_HEADER => false,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_CONNECTTIMEOUT => 300,
                  CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.2.13) Gecko/20101203 MRA 5.6 (build 03278) Firefox/3.6.13 FirePHP/0.5',
                  CURLOPT_FOLLOWLOCATION => true,
               );

               if (! self::PRODUCTION)
               {
                  $curl_params = $curl_params + array(
                     CURLOPT_HTTPPROXYTUNNEL => true,
                     CURLOPT_PROXY => '88.214.207.23:8182',
                     CURLOPT_PROXYUSERPWD => 'proxy0:Noos6bee',
                  );
               }

               curl_setopt_array($curl, $curl_params);

               $response = trim(curl_exec($curl));

               if ($response != self::ACCESS_DENIED)
               {
                  if ($response != self::INVALID_VINCODE)
                  {
                     preg_match('#</head>(.*)</html>#sU', $response, $matches);

                     $body = preg_replace(array('#class="box"#'), array('class="boxx"'), $matches[1]);

                     if ($body != '')
                     {
                        $style = file_get_contents($_SERVER['DOCUMENT_ROOT'].'/css/autocheck.css');

                        $response = '
                        <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
                        <html xmlns="http://www.w3.org/1999/xhtml">
                           <head>
                              <style type="text/css">'.$style.'</style>
                           </head>
                           '.$body.'
                        </html>
                        ';
                     }

                     // write into cache
                     file_put_contents($filename, $response);
                  } // aka $response = 'Invalid vincode'

                  // write into log
                  $this->mysqlQuery("INSERT INTO `ccl_user_autocheck_log` VALUES(NULL, '".$_SESSION['user_id']."', NOW())");
               } // aka $response = 'Access denied'
            }
            else
            {
               $response = $this->translate->_('Ваш лимит запросов исчерпан');
            }
         }
      }

      echo json_encode(array(
         'cached' => $cached,
         'preCount' => $preCount,
         'requestsCount' => (self::ATTEMPTS-$this->_requestsCount()),
         'response' => $response,
         'vincode' => $vincode,
      ));
      
      exit;
   }
}