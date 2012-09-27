<?php
class tplTemplates{
	var $root_path;
	var $fieldList;
        var $translate;
	var $mass= array(0,'');

	function tplTemplates($root_path)
	{
		$this->root_path=$root_path;
                $this->translate = Zend_Registry::get('translation');
		$this->setFieldList();
	}
	function setFieldList()
	{
		$this->fieldList = array(
                    $this->translate->_('Название шаблона'),
                    $this->translate->_('Текст шаблона')
                );

	}
	
	function top_tpl_link($id=0)
	{
		$type=htmlspecialchars($_GET['type']);
		$text = '<div class="location">
		<table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td><a href="'.$this->root_path.'?mod=cars">'.$this->translate->_('Список машин').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="'.$this->root_path.'?mod=stuff">'.$this->translate->_('Список товаров').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    <a href="'.$this->root_path.'?mod=tpl&type='.$type.'">'.$this->translate->_('Список шаблонов').'</a> |
                    <a href="'.$this->root_path.'?mod=tpl&sw=form&type='.$type.'">'.$this->translate->_('Добавить').'</a></td>
		<td width="300" align="right"></td></tr></table>
		</div>';
		return $text;
	}
	
	function getTop()
	{
		return "<div class='services'>";
	}
	function getBottom()
	{
		return "</div>";
	}
	function getTitle()
	{
		return '<h2>'.$this->translate->_('Добавление/редактирование').' '.$this->translate->_('шаблона').'</h2>';
	}
	function getError($text)
	{
		return "<div class='mes'>$text</div>";
	}
	function getForm()
	{
		$type=htmlentities($_REQUEST['type']);
		$text=
		"
		<form name='form1' method='post' action='{$this->root_path}?mod=tpl&sw=save'>
		<input type='hidden' name='id' value='".intval($_REQUEST['id'])."'>
		<input type='hidden' name='type' value='".$type."'>
		
		<table width='100%' border='0' cellspacing='0' cellpadding='4'>
		
		  <tr>
		    <td colspan=2><br></td>
		  </tr>

		<tr>
		    <td width=30% align=right>{$this->fieldList[0]}:</td>
		    <td><input type='text' name='name' value=\"{$this->mass[1]}\"></td>
		  </tr>
		  <tr>
		    <td width=30% valign=top align=right>{$this->fieldList[1]}:</td>
		    <td><textarea name='txt' rows=10>{$this->mass[2]}</textarea></td>
		  </tr>
		  <tr>
		    <td><a href='{$this->root_path}?mod=tpl&sw=delete&id=".intval($_REQUEST['id'])."&type={$type}'>".$this->translate->_('Удалить')."</a></td>
		    <td align='right'><input type='submit' name='Submit' value='".$this->translate->_('Сохранить')."' class='button' style='width:100px;'></td>
				</tr>
				</table>
				</form>
		";
		return  $text;

	}

}


?>