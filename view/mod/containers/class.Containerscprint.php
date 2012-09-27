<?

class ContainersEdit extends Proto {
	
	var $num;
	
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
		if(isset($_GET['step2'])) {
			if(count($_POST['check'])=='0') $this->redirect($this->root_path.'?mod=containers&sw=cprint&nothing');
	
			$this->page .= '
			<script>
			var myDateFormat = new Array("yyyy-mm-dd");
			 function switchCheck(id) {
					var nowID = id;
					document.getElementById(nowID).checked=\'checked\';
				}
			var curID;
			var startValue;
			var tmpID;
			function listener(id, first) {
			 tmpID = id;
			 startValue = first;
				if(startValue == document.getElementById(tmpID).value) {
					setTimeout(\'listener(tmpID,startValue)\',250);
				}
				else {
				curID = "chk" + tmpID;
					switchCheck(curID);
				}
				
			 }
			 var check = 0;
			 function checkForm() {
			 	if(document.getElementById(\'chksent\').checked==true) check = 1;
			 	if(document.getElementById(\'chkchina\').checked==true) check = 1;
			 	if(document.getElementById(\'chkrail\').checked==true) check = 1;
			 	if(document.getElementById(\'chkdostuck\').checked==true) check = 1;
			 	if(document.getElementById(\'chkbishkek\').checked==true) check = 1;
			 	if(document.getElementById(\'chkarrived\').checked==true) check = 1;
			 	if(check == 1) {
				 	var forma = document.forms.saveContainers
				 	forma.action = \'/?mod=containers&sw=cprint&save\';
				 	forma.target = \'_self\';
				 	forma.submit();
			 	}
			 	else alert(\'Не выбран ни один параметр!\');
			 }
			 function send2print() {
			 	var forma = document.forms.saveContainers;
			 	forma.action = \'/?mod=containers&sw=print\';
			 	forma.target =\'_blank\';
			 	forma.submit();
			 }
			 </script>
			 <script src="'.$this->root_path.'js/datepicker.js"></script><center>
			<form action="'.$this->root_path.'?mod=containers&sw=cprint&save" name="saveContainers" method="post" class="myForm">';
			$content = $this->buildSelectedList();
			$this->num = mysql_num_rows($content);
			$this->page .= $this->drawLines($content, '2').
			'<div style="width:480px; background-color:#fff;font-size:12px;text-align:right; padding:3px;">
			<a onclick="send2print();" style="text-decoration:underline; cursor:pointer;">распечатать выбранные контейнеры</a></div>
			<table width="410" class="list">
			<tr class="rowB">
				<td colspan="3">Можно выставить одну или несколько дат.<br>Даты выставляются <u>всем выбранным контейнерам</u>.</td>
			</tr>
			<tr class="rowA">
				<td>Отправка</td>
				<td><input type="text" name="sent" id="sent" onchange="switchCheck(\'chksent\')" style="width:90px;">
				<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'sent\', \'\', myDateFormat);listener(\'sent\', document.getElementById(\'sent\').value);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
				<td><input type="checkbox" name="chk[sent]" id="chksent" style="border:0px; width:20px;"></td>
			</tr>
			<tr class="rowB">
				<td>Китай</td>
				<td><input type="text" name="china" id="china" onchange="switchCheck(\'chkchina\')" style="width:90px;">
				<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'china\', \'\', myDateFormat);listener(\'china\', document.getElementById(\'china\').value);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
				<td><input type="checkbox" name="chk[china]" id="chkchina" style="border:0px; width:20px;"></td>
			</tr>
			<tr class="rowA">
				<td>Ж/Д</td>
				<td><input type="text" name="rail" id="rail" onchange="switchCheck(\'chkrail\')" style="width:90px;">
				<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'rail\', \'\', myDateFormat);listener(\'rail\', document.getElementById(\'rail\').value);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
				<td><input type="checkbox" name="chk[rail]" id="chkrail" style="border:0px; width:20px;"></td>
			</tr>
			<tr class="rowB">
				<td>Достук</td>
				<td><input type="text" name="dostuck" id="dostuck" onchange="switchCheck(\'chkdostuck\')" style="width:90px;">
				<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'dostuck\', \'\', myDateFormat);listener(\'dostuck\', document.getElementById(\'dostuck\').value);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
				<td><input type="checkbox" name="chk[dostuck]" id="chkdostuck" style="border:0px; width:20px;"></td>
			</tr>
			<tr class="rowA">
				<td>Бишкек</td>
				<td><input type="text" name="bishkek" id="bishkek" onchange="switchCheck(\'chkbishkek\')" style="width:90px;">
				<img src="'.$this->root_path.'img/ccl/cal.gif" border=0 onclick="show_calendar(\'bishkek\', \'\', myDateFormat);listener(\'bishkek\', document.getElementById(\'bishkek\').value);" class="datePicker" style="margin:0px;margin-bottom:-3px;"></td>
				<td><input type="checkbox" name="chk[bishkek]" id="chkbishkek" style="border:0px; width:20px;"></td>
			</tr>
			<tr class="rowB">
				<td>Доставлен</td>
				<td><input type="checkbox" name="arrived" onchange="switchCheck(\'chkarrived\')" style="border:0px; width:20px;"></td>
				<td><input type="checkbox" name="chk[arrived]" id="chkarrived" style="border:0px; width:20px;"></td>
			</tr>
			<tr>
				<td colspan="3"><input type="button" id="save" value="СОХРАНИТЬ ИЗМЕНЕНИЯ" onclick="checkForm();"></td>
			</tr>
	 		</table>
	 		
			</form></center>';
		}
		elseif(isset($_GET['save'])) {
		
		require_once($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');	
			$set_arrived = '0';
			foreach ($_POST['chk'] as $k => $v) {
				if($k!='arrived') {
					if(preg_match('/^[0-9]{4}-[0-9]{2}-[0-9]{2}$/',$_POST[$k])) {
							$query .= "`".$k."` = '".$_POST[$k]."', ";
					}
				}
				elseif($k=='arrived') {
					if($_POST[$k]=='on') $query .= "`arrived` = '1', ";
					else $query .= "`arrived` = '0', ";
					$set_arrived = '1';
				}
			}
			$conts_balance = array();
			foreach ($_POST['hiddenID'] as $k => $v) {
				$where .= "`id` = '".$k."' OR ";
				if($set_arrived == '1') array_push($conts_balance, $k);
			}
			//обновляем баланс клиентов, автомобили которых находятся в данных контейнерах
			if(count($conts_balance)>0) $this->updateContainersByIds($conts_balance);
			
			$request = "
			UPDATE ccl_".ACCOUNT_SUFFIX."containers 
			SET ".rtrim($query, ', ')." 
			WHERE ".rtrim($where,' OR ');
			$this->mysqlQuery($request);
			$this->page .= '<center>
			<table class="list rowA" width="510" cellspacing="0">
			<tr>
			<td><h4 class="report">Изменения сохранены!</h4>
			<br>
			Для контейнеров: '.$this->listContainers().'<br>
			установлены следующие значения:<br>
			<br>
			'.$this->showResult().'<br></td></tr>
			</table></center>';
			
		}
		else $this->redirect($this->root_path);
	}
	
	function getMainContent($condition) {
		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."containers.*, ccl_".ACCOUNT_SUFFIX."expeditors.name as exp_name, ccl_".ACCOUNT_SUFFIX."ports.name as port_name
		FROM `ccl_".ACCOUNT_SUFFIX."containers`
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."expeditors` ON (ccl_".ACCOUNT_SUFFIX."expeditors.id = ccl_".ACCOUNT_SUFFIX."containers.expeditor)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."ports` ON (ccl_".ACCOUNT_SUFFIX."ports.id = ccl_".ACCOUNT_SUFFIX."containers.port) 
		WHERE ".$condition." 
		ORDER BY ccl_".ACCOUNT_SUFFIX."containers.arrived ASC, ccl_".ACCOUNT_SUFFIX."containers.expeditor ASC, ccl_".ACCOUNT_SUFFIX."containers.number DESC";
		
		return $this->mysqlQuery($request);
	}
	
	function drawLines($content, $step) {

		$out = '<table class="list" width="490">';
		 
		if(isset($_GET['nothing'])) $out .= '<tr><td colspan="3">
		 <div class="warn">не выбран ни один контейнер!</div>
		 </td></tr>';
	
		$out .= '<tr class="title">
		 <td width="80">контейнер</td>
		 <td width="170">экспедитор</td>
		 <td width="40">порт</td>
		 <td width="30">&nbsp</td>
		 </tr>';
	
		 $i = 0;
		 $class = 'rowA';
		 while($i<$this->num) {
			$line = mysql_fetch_array($content);
			if($step=='1') {
				$chkswitch = ' onclick="javascript:switchCheck(\'chk'.$line['id'].'\')"';
				$checkbox = '<input type="checkbox" name="check['.$line['id'].']" id="chk'.$line['id'].'" style="border:0px;">';
			}
			else {
				$chkswitch = '';
				$checkbox = '&nbsp;';
			}
			if($line['arrived']=='1') $class = 'greenTR';
			$out .= '
			<tr class="'.$class.'" onmouseover="this.className=\'hovered\'" onmouseout="this.className=\''.$class.'\'">
			<td width="90"'.$chkswitch.'><b>'.$line['number'].'</b></td>
			<td'.$chkswitch.'>'.$line['exp_name'].'</td>
			<td width="40"'.$chkswitch.'>'.$line['port_name'].'</td>
			<td align="right" width="30">'.$checkbox.'
			<input type="hidden" name="hiddenID['.$line['id'].']" value="'.$line['number'].'"></td>
			</tr>';
			if($class=="rowA") $class="rowA rowB";
			else $class="rowA";
			$i++;
		 }
		 $out .= '</table>';
		 return $out;
	}
	
	function buildSelectedList() {
		$condition = '';
		foreach($_POST['check'] as $k => $v) {
			if($v == 'on') {
				$condition .= " ccl_".ACCOUNT_SUFFIX."containers.id = '".$k."' OR ";
			}
		}
		return $this->getMainContent(rtrim($condition, 'OR '));
	
	}
	
	function listContainers() {
		$out = '';
		foreach ($_POST['hiddenID'] as $k => $v) {
			$out .= '<a href="'.$this->root_path.'?mod=containers&sw=form&cont_id='.$k.'">'.$v.'</a>, ';
		}
		return rtrim($out, ', ');
	}
	
	function showResult() {
		$out = '<table class="list rowA" width="300">
			';
		if($_POST['sent']!='' and $_POST['chk']['sent']=='on')
		$out.='
			<tr>
			<td align="right">Отправка</td>
			<td>'.$_POST['sent'].'</td>
			</tr>';
		if($_POST['china']!='' and $_POST['chk']['china']=='on')
			$out.='<tr>
			<td align="right">Китай</td>
			<td>'.$_POST['china'].'</td>
			</tr>';
		if($_POST['rail']!='' and $_POST['chk']['rail']=='on')
			$out.='<tr>
			<td align="right">Ж/Д</td>
			<td>'.$_POST['rail'].'</td>
			</tr>';
		if($_POST['dostuck']!='' and $_POST['chk']['dostuck']=='on')
			$out.='<tr>
			<td align="right">Достук</td>
			<td>'.$_POST['dostuck'].'</td>
			</tr>';
		if($_POST['bishkek']!='' and $_POST['chk']['bishkek']=='on')
			$out.='<tr>
			<td align="right">Бишкек</td>
			<td>'.$_POST['bishkek'].'</td>
			</tr>';
		if($_POST['arrived']=='on' and $_POST['chk']['arrived']=='on')
			$out.='<tr>
			<td align="right">Доставлен</td>
			<td>ДА</td>
			</tr>';
			$out .= '</table>';
		return $out;
	}
	
	function updateContainersByIds($containers) {
		foreach ($containers as $k => $v) {
			$conts .= "ccl_".ACCOUNT_SUFFIX."containers.id = '".$v."' OR ";
		}
			$cars = $this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."cars.id as car 
			FROM `ccl_".ACCOUNT_SUFFIX."containers` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars` 
			ON (ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."containers.slot1 
			OR ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."containers.slot2
			OR ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."containers.slot3
			OR ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."containers.slot4) 
			WHERE ".rtrim($conts, ' OR '));
			
			$num = mysql_num_rows($cars);
			if($num>0) {
				$car = array();
				$i = 0;
				while($i<$num) {
					$line = mysql_fetch_array($cars);
					array_push($car, $line['car']);
					$i++;
				}
				if(count($car)>0) {
					$this->deliverCars($car);
					updateContainerBalance($car);
				}
			}
	}
	
	function deliverCars($cars) {
		$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."cars` 
		SET `delivered` = '1' WHERE ";
		foreach ($cars as $k => $v) {
			$request .= "`id` = '".$v."' OR ";
		}
		$this->mysqlQuery(rtrim($request, ' OR '));
	}

}
?>