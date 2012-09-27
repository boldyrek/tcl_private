<?php
class BackupTemplates{
	var $root_path;
	var $translate;
		
	var $backupRetCount;
	var $backupRetSize;
	var $backupRestoreCnt;
	var $backupRestoreSize;

	function BackupTemplates($root_path,$lang)
	{
		$this->root_path=$root_path;
		$this->translate=Zend_Registry::get('translation');
	}
	
	function BackupDBtplList($backupname) {
		if(!$backupname) 
			return "<tr><th style='padding:20px;text-align:center;'>".$this->translate->_('Резервных копий нет');
			
		$class="rowA rowB";
		$out='<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list vlines"><tr class="title">';
		foreach($backupname as $k=>$v){
			$onclick_location = 'document.location=\''.$this->root_path.'?mod=backup&sw=create&id='.$k.'\'';
			$out.='<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"><td onclick="'.$onclick_location.'">'.date("H:i, d-m-Y",$k).($v[1]==true?" <span style='color:green'>[".$this->translate->_('автосохранение')."]</span> ":"").'<td onclick="'.$onclick_location.'">'.$v[0].'<td><a href="'.$this->root_path.'backup/?'.$k.'" target="_blank">'.$this->translate->_('скачать').'</a><td><a href="'.$this->root_path.'?mod=backup&sw=create&erasefile&id='.$k.'" onclick="return confirm (\''.$this->translate->_('удалить').'?\')">'.$this->translate->_('удалить').'</a>';
			$class=($class=="rowA"?"rowA rowB":"rowA");
		}
		$out.='</table>';
		return $out;
	}

	function BackupDBtplListContent($dumpDBlist){
		return '<div class="location" style="width:970px"><table width="100%;" border=0 cellspacing="0" cellpadding="0" class="location_tab">
		<tr><td width="100%">'.$this->translate->_('Резервное копирование').' <a href="'.$this->root_path.'?mod=backup&sw=create">'.$this->translate->_('Создать').'</a>  <a href="'.$this->root_path.'?mod=backup&sw=load"><!--'.$this->translate->_('Загрузить (и Восстановить)').'--></a></td>
		</table>
		</div>
		<div class="cont" style="width:970px">
		<h3>'.$this->translate->_('Резервное копирование').'</h3>
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="list">
		'.$dumpDBlist.'
		</table>
		</div>';
	}
	
	function BackupCreateContent(){		
		if(isset($_GET['store'])) {
			$tmps="<tr><td style='padding:5px;'>
			".$this->translate->_('Сохранено строк данных')." : {$this->backupRetCount}<br>
			".$this->translate->_('Суммарный объем данных')." : {$this->backupRetSize}";
		} elseif(isset($_GET['restore'])){
			$tmps="<tr><td style='padding:5px;'>
				".$this->translate->_('Автосохранение')." :<br>
				<li>".$this->translate->_('Сохранено строк данных')." : {$this->backupRetCount}<br>
				<li>".$this->translate->_('Суммарный объем данных')." : {$this->backupRetSize}<br>
				".$this->translate->_('Востановленно из резервной копии')." :<br>
				<li> ".$this->translate->_('Строк данных')." : {$this->backupRestoreCnt}<br>
				<li> ".$this->translate->_('Объем данных')." : {$this->backupRestoreSize}<br>";
		} elseif(isset($_GET['restorefile'])){
			$tmps="<tr><td style='padding:5px;'>
				".$this->translate->_('Автосохранение')." :<br>
				<li>".$this->translate->_('Сохранено строк данных')." : {$this->backupRetCount}<br>
				<li>".$this->translate->_('Суммарный объем данных')." : {$this->backupRetSize}<br>
				".$this->translate->_('Востановленно из загруженной резервной копии')." :<br>
				<li> ".$this->translate->_('Строк данных')." : {$this->backupRestoreCnt}<br>
				<li> ".$this->translate->_('Объем данных')." : {$this->backupRestoreSize}<br>";
		} else {
			if(isset($_GET["id"])) {
				$tmps="<tr><th style='padding:5px;text-align:center;'>
				".$this->translate->_('Восстановить данные из резервной копии за')." &laquo;".date("H:i, d-m-Y",$_GET["id"])."&raquo;?<br><br><small style='color:red;'>".$this->translate->_('все данные после это даты будут утеряны, <br>но их можно будет восстановить из резервной копии которая будет создана сейчас.')."</small><br><br>
				<tr><td style='padding:5px;text-align:right;'>
				<form class='myForm' action='".$this->root_path."?mod=backup&sw=create&restore' method='post'>
				<input type='hidden' name='id' value='".$_GET["id"]."'>
				<input type='submit' name='Submit' value='".$this->translate->_('Восстановить')."' id='save' tabindex='0' style='width:150px;'/>
				</form>";
			} else {
				$tmps="<tr><th style='padding:5px;text-align:center;'>
				".$this->translate->_('Создать новую резервную копию?')."<br><br>
				<tr><td style='padding:5px;text-align:right;'>
				<form class='myForm' action='{$this->root_path}?mod=backup&sw=create&store' method='post'>
				<input type='hidden' name='times' value='".time()."'>
				<input type='submit' name='Submit' value='".$this->translate->_('Создать')."' id='save' tabindex='0' style='width:150px;'/>
				</form>";
			}
		}
		
		$tmps="<div class='location' style='width:960px;'><table width='100%' border=0 cellspacing=0 cellpadding=0 class='location_tab'>
		<tr><td width='100%'>".$this->translate->_('Резервное копирование')." | <a href='{$this->root_path}?mod=backup&sw=create'>".$this->translate->_('Создать')."</a> | <a href='{$this->root_path}?mod=backup&sw=load'><!--".$this->translate->_('Загрузить (и Восстановить)')."--></a></td>
		</table>
		</div>
		<div class='cont' style='width:962px;'>
		<h3>".$this->translate->_('Резервное копирование')."</h3>
		<table width='100%' border=0 cellpadding=0 cellspacing=0 class='list'>
		{$tmps}
		</table>
		</div>";
		
		return $tmps;
	}
	
	function BackupLoadContent(){
		$tmps="<tr><th style='padding:5px;text-align:center;'>
		".$this->translate->_('Восстановить данные из файла резервной копии?')."<br><br><small style='color:red;'>".$this->translate->_('все данные внесенные после создания загружаемой копии будут утеряны, <br>но их можно будет восстановить из резервной копии которая будет создана сейчас.')."</small><br><br>
		<tr><td style='padding:5px;text-align:center;'>
			<form class='myForm' action='{$this->root_path}?mod=backup&sw=create&restorefile' method='post' enctype='multipart/form-data'>
			<input type='file' name='restorefile' style='width:150px;'/>
		<tr><td style='padding:5px;text-align:right;'>
			<input type='submit' name='Submit' value='<!--".$this->translate->_('Загрузить (и Восстановить)')."-->' id='save' tabindex='0' style='width:190px;'/>
		</form>";

		$tmps="<div class='location' style='width:960px;'><table width='100%' border=0 cellspacing=0 cellpadding=0 class='location_tab'>
		<tr><td width='100%'>".$this->translate->_('Резервное копирование')." <a href='{$this->root_path}?mod=backup&sw=create'>".$this->translate->_('Создать')."</a><a href='{$this->root_path}?mod=backup&sw=load'><!--".$this->translate->_('Загрузить (и Восстановить)')."--></a></td>
		</table>
		</div>
		<div class='cont' style='width:962px;'>
		<h3>".$this->translate->_('Резервное копирование')."</h3>
		<table width='100%' border=0 cellpadding=0 cellspacing=0 class='list'>
		{$tmps}
		</table>
		</div>";
		
		return $tmps;
	}
}
?>