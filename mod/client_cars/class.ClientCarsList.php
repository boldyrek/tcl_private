<?

class ClientCarsList extends Proto {
	
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
		if(isset($_GET['client'])) {
			$clients = $this->mysqlQuery("SELECT id,name 
			FROM `ccl_".ACCOUNT_SUFFIX."customers` 
			WHERE 1 ORDER BY name ASC");
			$num = mysql_num_rows($clients);
			$i=1;
			if($num>0) {
				$clist = '<select name="buyer" onchange="javascript:getNewList();" id="buyer" style="width:300px;">
				<option value="0"';
				if(intval($_GET['client'])==0) $out .= ' selected="selected"';
				$clist .= '> - - - </option>';
				while($i<=$num) {
					$line = mysql_fetch_array($clients);
					$clist .= '
					<option value="'.$line['id'].'"';
					if(intval($_GET['client'])==$line['id']) $clist .= ' selected="selected"';
					$clist .= '>'.stripslashes($line['name']).'</option>';
					$i++;
				}
				$clist .= '</select>';
			}
			
			$cols[1] = array('name' => 'buy_date', 		'caption' => 'дата покупки','width' => '100');
			$cols[2] = array('name' => 'model', 		'caption' => 'название', 	'width' => '');
			$cols[3] = array('name' => 'frame', 		'caption' => 'вин код', 	'width' => '180');
			$cols[4] = array('name' => 'total', 		'caption' => 'цена всего', 	'width' => '90');
			$cols[5] = array('name' => 'paid', 			'caption' => 'оплачено', 	'width' => '90');
			$cols[6] = array('name' => 'balance', 		'caption' => 'баланс', 		'width' => '90');
			
			if($_SESSION['show_arrived_client_cars']=='') $_SESSION['show_arrived_client_cars'] = '0';
			elseif(intval($_GET['arrived'])=='1') $_SESSION['show_arrived_client_cars'] = '0';
			elseif(isset($_GET['arrived']) and intval($_GET['arrived'])=='0') $_SESSION['show_arrived_client_cars'] = '1';
			if($_SESSION['show_arrived_client_cars']=='0') $arrived_filter = "AND `delivered` = '0'";
			else $arrived_filter = "";
			if(intval($_GET['client'])!='0' and intval($_GET['client'])!='') $cars_list = 
			$this->localBuildList("
			SELECT id,model,frame,buy_date,total,paid,delivered, (total-paid) as balance
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			WHERE (buyer = '".intval($_GET['client'])."' 
			OR dealer = '".intval($_GET['client'])."') ".$arrived_filter,$cols,'client_cars','cars','car_id','');
			else $this->redirect($this->root_path);
			
			
			$this->page .= '
			<script>
			
			function getNewList() {
				var buyer_id = document.getElementById(\'buyer\').value;
				document.location=\'/?mod=client_cars&client=\'+buyer_id;
			}
			</script>';
			$this->page .= '<div class="location">Автомобили клиента: '.$clist.' &nbsp;&nbsp; <input type="checkbox" name="show_arrived" id="arrived"'.($_SESSION['show_arrived_client_cars']=='1'?' checked="checked"':'').' onclick="document.location=\''.$this->root_path.'?mod=client_cars&client='.intval($_GET['client']).'&arrived='.$_SESSION['show_arrived_client_cars'].'\'" style="border:0px;"><label for="arrived" style="cursor:hand; cursor:pointer;">показывать прибывшие</label></div>
			'.$cars_list;
		}
		else $this->redirect($this->root_path);
		
		}
		function localBuildList($request,$cols,$list,$module,$item,$limit)
		{
			//сортировка
			$order_list = $this->defineSort('sort_'.$list, '`'.$cols[1]['name'].'` DESC'); //добавляем сортировку в запрос
			$this->sortDeco('sort_'.$list); //выводим указатель того, что сейчас сортируется и направление сортировки
			$request = $request." ORDER BY ".$order_list.$limit;
		
			$content = $this->mysqlQuery($request);
				
			$num = mysql_num_rows($content);
			$i=1; 
			$class="rowA rowB";
			$out.='
			<table width="970" border="0" cellspacing="0" cellpadding="0" class="list vlines">
				<tr class="title sortButtons">';
			while($i<=count($cols))
			{
				$out.='
				<td width="'.$cols[$i]['width'].'" onclick="document.location=\''.$this->root_path.'?mod=client_cars&sort='.$cols[$i]['name'].'&sdir='.$this->sortNow['resort'][$cols[$i]['name']].'&client='.intval($_GET['client']).'\'" onMouseOver="this.className=\'sortButtonsHover\'" onMouseOut="this.className=\'\'">'.$cols[$i]['caption'].' '.$this->sortNow['image'][$cols[$i]['name']].'</td>';
				 
				$i++;
			}
			$out.='</tr>';
			$i=1; 
			while ($i<=$num)
			{
				$line = mysql_fetch_array($content);
				if($line['delivered']=='1') $class='greenTR';
			$out .= '
			<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" onclick="document.location=\''.$this->root_path.'?mod='.$module.'&sw=form&'.$item.'='.$line['id'].'\'">';
				$j=1;
				while($j<=count($cols))
				{
					$out.='
					<td>'.cleanContent($line[$cols[$j]['name']]).'&nbsp;</td>';
					$j++;
				}
				$out .= '</tr>';
				$i++;
				if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
			}
			$out .= '
			</table>';
			
			return $out;
		}		

}

?>