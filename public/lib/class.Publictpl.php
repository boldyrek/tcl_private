<?php

class PublicTpl extends Proto {

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
         $this->errorHandler('Пустая страница', 0);
      }
      
      $this->errorsPublisher();
      $this->publish();
   }

   private function _moduleContent()
   {
      if ($this->exists($_GET['sw']))
      {
         switch ($_GET['sw'])
         {
            case 'view':
               $this->_view();
               break;
            case 'form':
               $this->_form();
               break;
            case 'save':
               $this->_save();
               break;
            case 'remove':
               $this->_remove();
               break;
            default:
               $this->_grid();
               break;
         }
      }
      else
      {
         $this->_grid();
      }
   }

   private function _grid()
   {
      $query = $this->mysqlQuery('SELECT * FROM `ccl_user_tpl` WHERE `user_id` = '.$_SESSION['user_id']);
      $rows = mysql_num_rows($query);

      $this->page .= '
         <div class="location">'.$this->translate->_('Шаблоны').' | <a href="/public/?mod=tpl&amp;sw=form">'.$this->translate->_('Добавить').'</a></div>
         <table width="970" border="0" cellspacing="0" cellpadding="0" class="list">
            <thead>
               <tr class="title">
                  <td>'.$this->translate->_('Название шаблона').'</td>
                  <td>'.$this->translate->_('Содержание шаблона').'</td>
               </tr>
            </thead>
      ';

      if ($rows)
      {
         $this->page .= '<tbody>';

         $class = 'rowA rowB';

         while ($row = mysql_fetch_object($query))
         {
            $this->page .= '
               <tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
                  <td><a href="/public/?mod=tpl&amp;sw=form&amp;id='.$row->id.'">'.$row->name.'</a></td>
                  <td>'.$row->content.'</td>
               </tr>
            ';
         }

         $this->page .= '</tbody>';
      }

      $this->page .= '</table>';
   }

   private function _view()
   {
      if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest')
      {
         $table = ($_GET['access'] == 'user') ? 'ccl_user_tpl' : 'ccl_tpl';
         $field = ($_GET['access'] == 'user') ? 'content' : 'txt AS content';
         
         $query = $this->mysqlQuery('SELECT '.$field.' FROM `'.$table.'` WHERE `id` = '.intval($_GET['id']).' LIMIT 1');

         if (mysql_num_rows($query))
         {
            $result = mysql_fetch_object($query);
            
            $this->templates['footer'] = '';
            $this->page = $result->content;
         }
      }
   }

   private function _form()
   {
      $form = array(
         'id' => 0,
         'name' => '',
         'content' => ''
      );

      if ($this->exists($_GET['id']))
      {
         $query = $this->mysqlQuery('SELECT * FROM `ccl_user_tpl` WHERE `id` = '.intval($_GET['id']).' LIMIT 1');

         if (mysql_num_rows($query))
         {
            $form = mysql_fetch_array($query);
         }
         else
         {
            $this->errorHandler('Шаблон не найден', 0);
         }
      }

      $this->page .= '
         <div class="location"><a href="/public/?mod=tpl">'.$this->translate->_('Шаблоны').'</a></div>
         <div class="services">
            <h2>'.$this->translate->_('Добавление/редактирование шаблона').'</h2>
            '.$this->_formTemplate($form).'
         </div>
      ';
   }

   private function _save()
   {
      if ($_POST)
      {
         $id = intval($_POST['id']);
         $name = trim(htmlspecialchars($_POST['name']));
         $content = trim(htmlspecialchars($_POST['content']));

         if ($id)
         {
            $sql = "
               UPDATE `ccl_user_tpl`
               SET `name` = '".$name."',
                   `content` = '".$content."'
               WHERE `id` = ".$id;
         }
         else
         {
            $sql = "INSERT INTO `ccl_user_tpl` VALUES('', '".$_SESSION['user_id']."', '".$name."', '".$content."')";
         }

         $query = $this->mysqlQuery($sql);

         if ($query)
         {
            $this->redirect('/public/?mod=tpl');
         }
         else
         {
            $this->errorHandler('Ошибка при сохранении шаблона', 0);
         }
      }
      else
      {
         $this->redirect('/public/?mod=tpl');
      }
   }

   private function _remove()
   {
      if (! $this->mysqlQuery("DELETE FROM `ccl_user_tpl` WHERE `id` = ".intval($_GET['id'])))
      {
         $this->errorHandler('Ошибка при удалении шаблона', 0);
      }
      else
      {
         $this->redirect('/public/?mod=tpl');
      }
   }

   private function _formTemplate($form)
   {
      $id = $form['id'];

      return '
         <form action="/public/?mod=tpl&amp;sw=save" method="post" id="tpl-form">
            <input type="hidden" name="id" value="'.$id.'" />
            <table width="100%" border="0" cellspacing="0" cellpadding="4">
               <tr>
                  <td width="25%" align="right"><label for="name">'.$this->translate->_('Название шаблона').'</label>:</td>
                  <td><input type="text" name="name" id="name" value="'.$form['name'].'" /></td>
               </tr>
               <tr>
                  <td valign="top" align="right"><label for="content">'.$this->translate->_('Содержание шаблона').'</label>:</td>
                  <td><textarea name="content" id="content" style="height:100px">'.$form['content'].'</textarea></td>
               </tr>
               <tr>
                  <td>'.($id == 0 ? '&nbsp;' : '<a href="/public/?mod=tpl&amp;sw=remove&amp;id='.$id.'" onclick="javascript:return confirm(\''.$this->translate->_('Вы уверены?').'\');">'.$this->translate->_('Удалить').'</a>').'</td>
                  <td><input type="submit" value="'.$this->translate->_('Сохранить').'" class="button" style="width:100px" /></td>
               </tr>
            </table>
         </form>
         <script type="text/javascript" src="/js/jquery.js"></script>
         <script type="text/javascript">
            $("#tpl-form").submit(function()
            {
               var name = $("input#name").val();
               var content = $("textarea#content").val();

               if ($.trim(name) == "" || $.trim(content) == "")
               {
                  alert("Все поля должны быть заполнены");
                  return false;
               }
            });
         </script>
      ';
   }

}
