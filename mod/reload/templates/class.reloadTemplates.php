<?php
class ReloadTemplates{
	var $root_path;
	var $fieldList;
	var $translate;
	var $mass= array(0,'');

	function ReloadTemplates($root_path)
	{
		$this->root_path=$root_path;
		$this->translate = Zend_Registry::get('translation');
		$this->setFieldList();
	}
	function setFieldList()
	{
		$this->fieldList = array($this->translate->_('Название порта перегруза'));

	}
	
	function top_ports_link($id=0)
	{
		$text = '<div class="location">
		<table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td><a href="'.$this->root_path.'?mod=containers">'.$this->translate->_('Список контейнеров').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="'.$this->root_path.'?mod=reload">'.$this->translate->_('Список портов перегруза').'</a> |
			<a href="'.$this->root_path.'?mod=reload&sw=form">'.$this->translate->_('Добавить порт перегруза').'</a></td>
		<td width="400" align="right"></td></tr></table>
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
		return '<h2>'.$this->translate->_('Добавление/редактирование').' '.$this->translate->_('ПОРТА ПЕРЕГРУЗА').'</h2>';
	}
	function getError($text)
	{
		return "<div class='mes'>$text</div>";
	}
	function getForm()
	{
		$text=
		"
		<form name='form1' method='post' action='{$this->root_path}?mod=reload&sw=save'>
		<input type='hidden' name='id' value='".intval($_REQUEST['id'])."'>
		<table width='100%' border='0' cellspacing='0' cellpadding='4'>
		  <tr>
		    <td width=30%>{$this->fieldList[0]}</td>
		    <td><input type='text' name='name' value=\"{$this->mass[1]}\"></td>
		  </tr>
		  <tr>
		    <td><a href='{$this->root_path}?mod=reload&sw=delete&id=".intval($_REQUEST['id'])."'>".$this->translate->_('Удалить')."</a></td>
		    <td align='right'><input type='submit' name='Submit' value='".$this->translate->_('Сохранить')."' class='button' style='width:100px;'></td>
				</tr>
				</table>
				</form>
		";
		return  $text;

	}

}


?>