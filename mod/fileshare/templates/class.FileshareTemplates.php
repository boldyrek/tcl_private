<?php
class FileshareTemplates{
	var $root_path;
	var $translate;

	function FileshareTemplates($root_path)
	{
		$this->root_path=$root_path;
		$this->translate = Zend_Registry::get('translation');
	}

	function FileshareListTPL($R) {
		if(!$R) 
			return "<tr><th style='padding:20px;text-align:center;'>".$this->translate->_('файлов на обмен нет');
			
		$class="rowA rowB";
		$out='<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list vlines"><tr class="title">';
		foreach($R as $v){
			$tmpD=($_SESSION['user_type']==$v["access2delete"]||$_SESSION['user_type']==1||$_SESSION['user_type']==7?'<a href="?mod=fileshare&delete='.$v["id"].'" onclick="return confirm (\''.$this->translate->_('удалить файл').' - '.$v["title"].'?\')">'.$this->translate->_('удалить').'</a>':'&nbsp;');
			$out.='<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
				<td style="width:140px;">'.date("H:i, d-m-Y",$v["dt_up"]).'
				<td style="width:60px;">'.$v["sizefile"].'
				<td>'.$v["title"].'
				<td>'.$v["text"].'
				<td style="width:100px;text-align:center;"><a href="?mod=fileshare&id='.$v["id"].'">'.$this->translate->_('скачать').'</a>
				<td style="width:100px;text-align:center;">'.$tmpD;
			$class=($class=="rowA"?"rowA rowB":"rowA");
		}
		$out.='</table>';
		return $out;
	}

	function FileshareListContent($list,$fsPages){
		return '<div class="location" style="width:970px"><table width="100%;" border=0 cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td width="100%">'.$this->translate->_('Обмен файлами').' / <a href="?mod=fileshare&sw=add">'.$this->translate->_('загрузить').'</a></td>
		</table>
		</div>
		<div class="cont_customer" style="width:970px">
		<h3>'.$this->translate->_('Обмен файлами').'</h3>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
		'.$list.$fsPages['print'].'
		</table>
		</div>';	
	}
	
	function FileshareAddContent(){
/*
<tr>
	<td align="right" class="title">доступен для
	<td class="rowA title">'.$this->fsAccessList().'
*/
		return '
		<div class="location" style="width:970px"><table width="100%;" border=0 cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td width="100%">'.$this->translate->_('Обмен файлами').' / <a href="?mod=fileshare">'.$this->translate->_('список').'</a></td>
		</table>
		</div>
		<form action="/?mod=fileshare&sw=add" method="post" enctype="multipart/form-data" class="myForm">
		<div style="width:970px" class="cont_customer">
		<h3>'.$this->translate->_('Обмен файлами').'</h3>
		<table width="792" border="0" cellpadding="0" cellspacing="0" class="list">
		<tr>
			<td width=160 align="right" class="title">'.$this->translate->_('название').'
			<td class="rowA title"><input type="text" name="title" value="" />
			
		<tr>
			<td align="right" class="title rowB">'.$this->translate->_('описание').'
			<td class="rowA rowB title"><textarea name="text"></textarea>
		<tr>
			<td align="right" class="title">'.$this->translate->_('загружаемый файл').'
			<td class="rowA title"><input type="file" name="uploadfile">
			
		</table>
		</div>
		<table width="802" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
		  	<td>&nbsp;
			<td width=200 align="right" class="title"><input type="submit" name="Submit" value="'.$this->translate->_('Загрузить').'" id="save" />
			<td width="8" align="right" class="title"><br /><br />
		</table>
 		
		</form>';	
	}

	function FileshareStubContent(){
		return '<div class="location" style="width:970px"><table width="100%;" border=0 cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td width="100%">Обмен файлами
		</table>
		</div>
		<div class="cont_customer" style="width:970px">
		<h3>Обмен файлами</h3>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
		<tr><th style="padding:20px;text-align:center;">обмен файлами не доступен
		</table>
		</div>';	
	}
	
	private function fsAccessList(){
		$TXT="";
		foreach($this->fsAccessName as $k=>$v)
			$TXT.="<input type='checkbox' name='access[{$k}]' value='on' style='width:24px;'> {$v}<br/>";
		return $TXT;
	}
}
?>