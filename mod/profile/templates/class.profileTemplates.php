<?php
class ProfileTemplates{
	var $root_path;
	var $fieldList;
	var $translate;

	var $place;
	var $tplace;
	var $purposes;

	var $mass= array(0,'');

	function ProfileTemplates($root_path)
	{
		$this->root_path=$root_path;
		$this->translate = Zend_Registry::get('translation');
		$this->place = Zend_Registry::get('car_location');
		$this->tplace = Zend_Registry::get('title_location');
		$this->purposes = Zend_Registry::get('purposes');
		$this->setFieldList();
	}
	function setFieldList()
	{
		$this->fieldList = array($this->translate->_('Название места'));

	}
	
	function top_additional_link($id=0)
	{
		$text = '
		<div class="location">&nbsp;</div>
		<div class="location" style="width:120px;">
		<table width="98%" border="0" cellspacing="2" cellpadding="2" class="location_tab">
			<tr><td><a href="'.$this->root_path.'?mod=cars&sw=">'.$this->translate->_('Список автомобилей').'</a></td></tr>
			<tr><td><a href="'.$this->root_path.'?mod=marka">'.$this->translate->_('Список марок').'</a> | <a href="'.$this->root_path.'?mod=marka&sw=form">'.$this->translate->_('Добавить').'</a></td></tr>
			<tr><td><a href="'.$this->root_path.'?mod=model">'.$this->translate->_('Список моделей').'</a> | <a href="'.$this->root_path.'?mod=model&sw=form">'.$this->translate->_('Добавить').'</a></td></tr>
			<tr><td><a href="'.$this->root_path.'?mod=places">'.$this->translate->_('Список мест').'</a> | <a href="'.$this->root_path.'?mod=places&sw=form">'.$this->translate->_('Добавить').'</a></td></tr>
			<tr><td><a href="'.$this->root_path.'?mod=ports">'.$this->translate->_('Список портов').'</a> | <a href="'.$this->root_path.'?mod=ports&sw=form">'.$this->translate->_('Добавить').'</a></td></tr>
		</table>
		</div>';
		return $text;
	}
	
	function getTop()
	{
		return "<div class='services' style='width:960px'>";
	}
	function getBottom()
	{
		return "</div>";
	}
	function getTitle()
	{
		return '<h2>'.$this->translate->_('Профайл').'</h2>';
	}
	function getError($text)
	{
		return "<div class='mes'>$text</div>";
	}
	function getForm()
	{
		$purposes_list='';
		foreach ($this->purposes as $id => $pl) { $purposes_list.=$id.':'.$pl."\n"; }
		$car_places='';
		foreach ($this->place as $id => $pl) { $car_places.=$id.':'.$pl."\n"; }
		$docs_places='';
		foreach ($this->tplace as $id => $pl) { $docs_places.=$id.':'.$pl."\n"; }
		$text='
		<form name="form1" method="post" action="'.$this->root_path.'?mod=profile&sw=save">
		<input type="hidden" name="id" value="'.intval($_REQUEST['id']).'">
		<table width="100%" border="0" cellspacing="0" cellpadding="4">
		<tr>
			<td width=100>'.$this->translate->_('Назначения платежей').'</td>
			<td><textarea rows=16 cols=25 name="purposes">'.$purposes_list.'</textarea></td>
			<td width="250">&nbsp;</td>
		</tr>
		<tr>
			<td width=100>'.$this->translate->_('Местонахождения автомобиля').'</td>
			<td><textarea rows=8 cols=25 name="carlocation">'.$car_places.'</textarea></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td width=100>'.$this->translate->_('Местонахождения сопроводительных документов').'</td>
			<td><textarea rows=8 cols=25 name="titlelocation">'.$docs_places.'</textarea></td>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td></td>
			<td><input type="submit" value="'.$this->translate->_('Сохранить').'"></td>
			<td>&nbsp;</td>
		</tr>
		</table>
		</form>
		';
		return  $text;

	}

}


?>