<?php

class PublicDateByVin extends Proto {

   public function makePage()
   {
      $this->page .= $this->templates['header'];

      if ($this->checkAuth())
      {
         $this->page .= $this->makeTopMenu();
         $this->_moduleContent();
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
      $this->page .= '
         <div class="location">Проверка даты выпуска по VIN</div>
         <div class="autocheck">
            <div class="input">
               <label for="vincode">'.$this->translate->_('Введите ВИН-код').':</label>
               <input type="text" id="vincode" value="" maxlength="17" />
               <span id="check-date">'.$this->translate->_('проверить').'</span>
            </div>
            <div id="response" style="display:none; margin:10px;font-size:18px"></div>
         </div>
         <script type="text/javascript" src="/js/jquery-1.4.4.min.js"></script>
         <script type="text/javascript">
            $("#check-date").live("click", function(){
               $("#response")
                  .show()
                  .html("проверка...")
                  .css({color:"black", fontWeight:"normal"});
                  
               $.getJSON("/cron/grabber/export/date", {vincode:$("#vincode").val()}, function(json){
                  $("#response")
                     .html(json.date_made)
                     .css({color:"green", fontWeight:"bold"})
                     .click(function(){
                        $(this).hide();
                     });
               });
            });
         </script>
      ';
   }
   
}