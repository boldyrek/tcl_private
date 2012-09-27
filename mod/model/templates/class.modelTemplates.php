<?php
class ModelTemplates{
	var $root_path;
	var $fieldList;
	var $translate;
	var $mass= array(0,'');

	function ModelTemplates($root_path)
	{
		$this->root_path=$root_path;
		$this->translate = Zend_Registry::get('translation');
		$this->setFieldList();
	}
	function setFieldList()
	{
		$this->fieldList = array(
			$this->translate->_('Марка'),
			$this->translate->_('Название модели')
		);

	}
	
	function top_model_link($id=0)
	{
		$text = '<div class="location">
		<table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td><a href="'.$this->root_path.'?mod=cars&sw=">'.$this->translate->_('Список автомобилей').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="'.$this->root_path.'?mod=stuff">'.$this->translate->_('Список товаров').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="'.$this->root_path.'?mod=marka">'.$this->translate->_('Список марок').'</a> | <a href="'.$this->root_path.'?mod=marka&sw=form">'.$this->translate->_('Добавить').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="'.$this->root_path.'?mod=model">'.$this->translate->_('Список моделей').'</a> | <a href="'.$this->root_path.'?mod=model&sw=form">'.$this->translate->_('Добавить').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="'.$this->root_path.'?mod=places">'.$this->translate->_('Список мест').'</a> | <a href="'.$this->root_path.'?mod=places&sw=form">'.$this->translate->_('Добавить').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<a href="'.$this->root_path.'?mod=ports">'.$this->translate->_('Список портов').'</a> | <a href="'.$this->root_path.'?mod=ports&sw=form">'.$this->translate->_('Добавить').'</a></td>
		</tr></table>
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
		return '<h2>'.$this->translate->_('Добавление/редактирование').' '.$this->translate->_('модели').'</h2>';
	}
	function getError($text)
	{
		return "<div class='mes'>$text</div>";
	}
	function getForm($select)
	{
		$text=
		"
		<form name='form1' method='post' action='{$this->root_path}?mod=model&sw=save'>
		<input type='hidden' name='id' value='".intval($_REQUEST['id'])."'>
		<table width='100%' border='0' cellspacing='0' cellpadding='4'>
		  <tr>
		    <td width=30%>{$this->fieldList[0]}</td>
		    <td>$select</td>
		  </tr>
		<tr>
		    <td width=30%>{$this->fieldList[1]}</td>
		    <td><input type='text' name='name' value=\"{$this->mass[1]}\" style='width:97%;'></td>
		  </tr>
		  <tr>
		    <td><a href='{$this->root_path}?mod=model&sw=delete&id=".intval($_REQUEST['id'])."'>".$this->translate->_('Удалить')."</a></td>
		    <td align='right'><input type='submit' name='Submit' value='".$this->translate->_('Сохранить')."' class='button' style='width:100px;'></td>
				</tr>
				</table>
				</form>
		";
		return  $text;

	}

}


?>