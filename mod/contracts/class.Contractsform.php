<?

class ContractsForm extends Proto {
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
		if(isset($_GET['contract']) and $_GET['contract']!='0' and $_GET['contract']!='') { 
			$contract = mysql_fetch_array($this->mysqlQuery("
			SELECT ccl_".ACCOUNT_SUFFIX."customers.name, ccl_".ACCOUNT_SUFFIX."contracts.*
			FROM `ccl_".ACCOUNT_SUFFIX."contracts`
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` ON ( ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."contracts.client )
			WHERE ccl_".ACCOUNT_SUFFIX."contracts.id = '".intval($_GET['contract'])."'"));
		
			if($contract['id']==0 or $contract['id']=='') $this->redirect($this->root_path.'?mod=contracts');
		}
		elseif (isset($_GET['client']) and intval($_GET['client'])!='' and intval($_GET['client'])!=0) $contract['client']=intval($_GET['client']); 
		else $contract['client'] = 0;
		
		$max_number = @mysql_fetch_array($this->mysqlQuery("
		SELECT max(number) as number 
		FROM `ccl_".ACCOUNT_SUFFIX."contracts` WHERE 1"));
		
		$clients = $this->mysqlQuery("
		SELECT id,name 
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE 1 
		ORDER BY name ASC");
		$cl_list = $this->buildSelect($clients, 'cust_id', $contract['client'], ' - не выбран - ', 2, 'switchCars(this.value);');
		
		$cars_list = $this->getCarList($contract);
		
		//определяем текущий номер контракта. максимальный из имеющихся + 1
		if($contract['number']=='') {
			if($max_number['number']!='') $contract['number'] = $max_number['number'] + 1;
			else $contract['number'] = '1';
			$info = '* +1 к последнему номеру';
		}
		
		$supply_link = '';
		
		if($contract['id']!=0 or $contract['id']!='') { 
			$del_link = '<a href="'.$this->root_path.'?mod=contracts&sw=delete&id='.$contract['id'].'" class="delete" onclick="return confirm(\'Вы действительно хотите удалить этот контракт?\')">удалить</a>';
			$supply_link = '<a href="'.$this->root_path.'?mod=contracts&sw=show&contract='.$contract['id'].'&type=supply">Договор доставки</a>';
			$open_button = '<input name="open" type="button" value="Открыть файл" style="width:100px;" onclick="document.location=\''.$this->root_path.'?mod=contracts&sw=show&contract='.$contract['id'].'\'">';
			}
		else { 
			$del_link = '';
			$open_button = '';
			}
		
		if($contract['data_source']=='local') {
			$show_list = 'display:none;';
			$show_info = '';
			$l_check = '';
			$i_check = ' checked="checked"';
		}
		elseif($contract['data_source']=='client') {
			$show_info = 'display:none;';
			$show_list = '';
			$l_check = ' checked="checked"';
			$i_check = '';
		}
			
		$this->page .= '
		<script src=../js/jquery.js></script>		
		<script>
				function switchCheck(id) {
					var nowID = id;
					if(document.getElementById(nowID).checked==true) document.getElementById(nowID).checked=\'\';
					else document.getElementById(nowID).checked=\'checked\';
				}
				
				function showMoreFields() {
					document.getElementById(\'client_info\').style.display=\'\';
					document.getElementById(\'client_list\').style.display=\'none\';
				}				
				function showClientList() {
					document.getElementById(\'client_info\').style.display=\'none\';
					document.getElementById(\'client_list\').style.display=\'\';
				}
				  function switchCars(id) {
				    $.get("/?mod=contracts&sw=cars", { client: id },
						 	function(data) {
						 		document.getElementById("carsContainer").innerHTML = data;
						 		
						 	} );
				}
				</script>
				<div class="report">'.$report.'</div>
					<div style="background-color:#fe6; width:620px; border:1px solid #fff;padding:5px;"><div style="float:left;font-size:16px; font-weight:bold; margin-bottom:20px;">Договор</div>
					<div style="float:left;margin-left:30px;" class="title">'.($_GET['sw']!='add'?'<a href="'.$this->root_path.'?mod=contracts&sw=form">добавить</a>':'').'</div>
					<form name="invoice" class="myForm" action="'.$this->root_path.'?mod=contracts&sw=save" method="post">
					<input type="hidden" name="cont_id" value="'.$contract['id'].'">
					<table width="100%" border="0" cellspacing="0" cellpadding="0" class="list" style="clear:both;">
					  <tr>
					    <td width="180" class="rowB title">&nbsp;</td>
					    <td class="rowB title">'.(isset($_GET['nocars'])?'<div class="warn">Не выбрано ни одного автомобиля!</div>':'&nbsp;').'</td>
					    <td width="50" class="rowB title">&nbsp;</td>
					  </tr>
					  <tr>
					    <td width="180" align="right" class="title">номер*</td>
					    <td class="title"><input type="text" name="number" value="'.$contract['number'].'" style="text-transform: uppercase;" /></td>
					    <td class="title">&nbsp;</td>
					  </tr>
					  <tr>
					    <td width="180" align="right" class="rowB title">&nbsp;</td>
					    <td class="rowB title"><span style="color:#999">'.$info.'</span></td>
					    <td class="rowB title">&nbsp;</td>
					  </tr>					  
					  <tr>
					    <td width="180" align="center" colspan="3"><b>Получатель</b></td>
					  </tr>
					  <tr>
					    <td width="180" align="right" class="title"></td>
					    <td class="title" colspan="2"><input type="radio" name="client_type" value="1"'.$l_check.' style="width:20px;" onClick="showClientList();" id="mark1"><label for="mark1">Выбрать из списка</label>
					    <input type="radio" name="client_type" value="2"'.$i_check.' style="width:20px;" onClick="showMoreFields();" id="mark2"><label for="mark2">Указать здесь</label></td>
					  </tr>
					   <tr>
					    <td width="180" align="right" class="title" valign="top"></td>
					    <td class="title">
					    <div style="'.$show_list.'" id="client_list">'.$cl_list.'</div>
					    <div style="'.$show_info.'" id="client_info">
					    ФИО:
					    <input type="text" name="client_info[name]" value="'.stripslashes($contract['name']).'"><br>
					    Паспорт:
					    <input type="text" name="client_info[passport]" value="'.stripslashes($contract['passport']).'"><br>
					    Адрес:
					    <input type="text" name="client_info[address]" value="'.stripslashes($contract['address']).'"><br>
					    Телефоны:
					    <input type="text" name="client_info[contacts]" value="'.stripslashes($contract['contacts']).'"><br>
					    
					    </div>
					    </td>
					    <td class="title">&nbsp;</td>
					  </tr>
					  <tr>
					    <td width="180" align="right" class="rowB title">Автомобили</td>
					    <td class="rowB title" id="carsContainer">'.$cars_list.'</td>
					    <td class="rowB title">&nbsp;</td>
					  </tr>
					  <tr>
					    <td width="180" align="right" class="title">Сумма договора </td>
					    <td class="title"><input type="text" name="sum" value="'.$contract['sum'].'" style="width:100px;" />
					    &nbsp;Оплачено <input type="text" name="paid" value="'.$contract['paid'].'" style="width:100px;" /></td>
					    <td class="title">&nbsp;</td>
					  </tr>
		  			  <tr>
		  			    <td width="180" align="right" class="rowB title">Агентское вознаграждение</td>
		  			    <td class="rowB title"><input type="text" name="agent" value="'.$contract['agent'].'" style="width:100px;" />
		  			    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Долг: '.$contract['dolg'].'
		  			    </td>
		  			    <td class="rowB title">&nbsp;</td>
				      </tr>
		  			  <tr>
		  			    <td width="180" class="title"><div style="float:left">'.$del_link.'</div><div style="float:right; padding-right:5px;">'.$supply_link.'</div></td>
		  			    <td class="title"><input type="submit" name="submit" value="Сохранить" id="save" style="width:100px;float:right;"><div style="float:right;margin-right:50px;">'.($_GET['sw']!='add'?'<a href="'.$this->root_path.'?mod=contracts&sw=act&cont_id='.$contract['id'].'">Акт приемки</a>':'').'</div>
						'.$open_button.'
						</td>
		  			    <td class="title">&nbsp;</td>
					  </tr>
					</table>
		
		</form></div>';
			
	}
	private function compare($id, $list) {
		foreach($list as $k => $v) {
			if($v == $id) return true;
		}
		return false;
	}
	
	function getCarList($contract) {
		if($contract['client']!=0) {
			$cars_in_game = explode(';',rtrim($contract['car'], ';'));
			$cars = $this->mysqlQuery("
			SELECT model,frame,id 
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE `buyer` = '".intval($contract['client'])."' 
			OR `reciever` = '".intval($contract['client'])."' 
			ORDER BY `buy_date` ASC");
				$num = mysql_num_rows($cars);
				$i=1;
				$class="rowA";
				if($num>0) {
					$cars_list = '
					<div style="overflow:auto; width:100%; height:100px;padding:3px; border:1px solid #ccc;">
					<table class="list" cellpadding="0" cellspacing="0" width="98%">';
					while($i<=$num) {
						$line = mysql_fetch_array($cars);
						$cars_list .= '
						<tr class="'.$class.'" style="cursor:pointer;" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'">
						<td width="30"><input type="checkbox" name="chk['.$line['id'].']" id="chk'.$line['id'].'"'.($this->compare($line['id'],$cars_in_game)?' checked="checked"':'').'></td>
						<td width="80" style="font-family:monospace;" onclick="javascript:switchCheck(\'chk'.$line['id'].'\')">'.substr($line['frame'],0,3).'..'.substr($line['frame'],(strlen($line['frame'])-4),4).'</td>
						<td onclick="javascript:switchCheck(\'chk'.$line['id'].'\')">'.$line['model'].'</td>
						</tr>';
						if($class=="rowA") $class="rowA rowB";
						else $class="rowA";
						$i++;
					}
					$cars_list .= '</table></div>';
				}
				else $cars_list = 'не найдено ни одного автомобиля!';
		}
		else $cars_list = 'не выбран клиент!';
		return $cars_list;
	}
}
?>