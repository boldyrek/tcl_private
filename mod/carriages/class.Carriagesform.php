<?
class CarriagesForm extends Proto {

    var $content;
    var $id;
    var $slots;
    var $expeditors;
    var $ports;
    var $orphants;
    var $loaded;
    var $myport;
    var $files;
    var $stuff;
    var $stuff_onboard;

    public function drawContent() {
        $this->page .= $this->templates['header'];
        if($this->checkAuth()) {
            $this->page .= $this->makeTopMenu();
            $this->getContent();
            $this->page .= $this->module_content;
        }
        $this->page .= $this->templates['footer'];

        $this->errorsPublisher();
        $this->publish();
    }

    function getContent() {
        // готовим данные для формы контейнера
        require ($_SERVER['DOCUMENT_ROOT'].$this->root_path.'mod/carriages/templates/carriages.form.php');

        if(isset($_GET['cont_id'])) {
            $this->id = intval($_GET['cont_id']);
            $this->content = mysql_fetch_array($this->mysqlQuery("
			SELECT * FROM `ccl_".ACCOUNT_SUFFIX."carriage`
			WHERE `id` = '".$this->id."'"));

            if($this->content['id']=='') {
                $this->errorHandler('<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Вагон с такими параметрами в базе не обнаружен').'</div>', 1);
            }
            $form_link = '?mod=carriages&sw=save&id='.intval($_GET['cont_id']);
        }
        elseif(isset($_GET['add'])) {
            $form_link = '?mod=carriages&sw=add';
        }


        $this->page .= containerForm($form_link, $this->content);
    }



}
?>