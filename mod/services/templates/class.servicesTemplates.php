<?php
class ServicesTemplates{
	var $root_path;
	var $fieldList;
	var $mass= array(0,'','',0,1);
	var $translate;

	function ServicesTemplates($root_path)
	{
		$this->root_path=$root_path;
		$this->translate = Zend_Registry::get('translation');
		$this->setFieldList();
	}
	function setFieldList()
	{
		$this->fieldList = array(
			$this->translate->_('Название услуги'),
			$this->translate->_('Описание услуги'),
			$this->translate->_('Цена услуги по умолчанию'),
			$this->translate->_('Количество услуги по умолчанию')
		);

	}
	function top_services_link($id=0)
	{
		$text = '<div class="location">
		<table width="98%" border="0" cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td><a href="'.$this->root_path.'?mod=invoices&sw=">'.$this->translate->_('Список инвойсов').'</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <a href="'.$this->root_path.'?mod=services">'.$this->translate->_('Список услуг').'</a> | <a href="'.$this->root_path.'?mod=services&sw=form">'.$this->translate->_('Добавить').'</a></td>
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
		return "<h2>".$this->translate->_('Добаление/редактирование услуги')."</h2>";
	}
	function getError($text)
	{
		return "<div class='mes'>$text</div>";
	}
	function getForm()
	{
		$text=
		"
		<form name='form1' method='post' action='{$this->root_path}?mod=services&sw=save'>
		<input type='hidden' name='id' value='".intval($_REQUEST['id'])."'>
		<table width='100%' border='0' cellspacing='0' cellpadding='4'>
		  <tr>
		    <td width=30%>{$this->fieldList[0]}</td>
		    <td><input type='text' name='item' value=\"{$this->mass[1]}\"></td>
		  </tr>
		  <tr>
		    <td style='background-color:#F8E9DA;'>{$this->fieldList[1]}</td>
		    <td style='background-color:#F8E9DA;'><textarea name='description'>{$this->mass[2]}</textarea></td>
		  </tr>
		  <tr>
		    <td>{$this->fieldList[2]}</td>
		    <td><input type='text' name='cost' value='{$this->mass[3]}'></td>
		  </tr>
		  <tr>
		    <td style='background-color:#F8E9DA;'>{$this->fieldList[3]}</td>
		    <td style='background-color:#F8E9DA;'><input type='text' name='quantity' value='{$this->mass[4]}'></td>
		  </tr>
		  <tr>
		    <td><a href='{$this->root_path}?mod=services&sw=delete&id=".intval($_REQUEST['id'])."'>".$this->translate->_('Удалить')."</a></td>
		    <td align='right'><input type='submit' name='Submit' value='".$this->translate->_('Сохранить')."' class='button' style='width:100px;'></td>
				</tr>
				</table>
				</form>
		";
		return  $text;

	}

}


?>