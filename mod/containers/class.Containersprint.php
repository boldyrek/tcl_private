<?
class ContainersPrint extends Proto {
	
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
		echo $this->page;
		
	}
		
	private	function Process() {
		//
		//  выводит список контейнеров на печать (id контейнеров приходят в $_POST
		//
		
		if(count($_POST['check'])=='0' and count($_POST['hiddenID'])=='0') $this->redirect($this->root_path.'?mod=containers&sw=cprint&nothing');
		
		$i = 0;
		if(count($_POST['check'])>0) {
			foreach($_POST['check'] as $k => $v) {
				if($v == 'on') {
					$container[$i]['id'] = $k;
					$container[$i]['date'] = date('d-m-Y');
					
					$cars[$i] = $this->getInfo($k);
				}
				$i++;
			}
		}
		elseif(count($_POST['hiddenID'])>0) {
			foreach($_POST['hiddenID'] as $k => $v) {
				$container[$i]['id'] = $k;
				$container[$i]['date'] = date('d-m-Y');
				$cars[$i] = $this->getInfo($k);
				$i++;
			}
		}
		$total_containers = $i;
		$i = 0;
		
		//вытаскиваем инфо об автомобилях в каждом контейнере
		while($i<$total_containers) {
			$j = 0;
			$num = mysql_num_rows($cars[$i]);
			while($j<$num) {
				$line = mysql_fetch_array($cars[$i]);
				$car[$i][$j]['frame'] = $line['frame'];
				$car[$i][$j]['model'] = $line['model'];
				$car[$i][$j]['buyer'] = $line['buyer'];
				$car[$i][$j]['dealer'] = $line['dealer'];
				$car[$i][$j]['year'] = $line['year'];
				$car[$i][$j]['engine'] = $line['engine'];
				$car[$i][$j]['seats'] = $line['seats'];
				$car[$i][$j]['fuel_type'] = $line['fuel_type'];
				$j++;
			}
			$container[$i]['number'] = $line['number'];
			$i++;
		}
		
		$i = 0;
		while($i<$total_containers) {
			$j = 0;
			while ($j < count($car[$i])) {
				if($this->clientInfo($car[$i][$j]['reciever'])!='') $buyer[$i][$j] = $this->clientInfo($car[$i][$j]['reciever']);
				else $buyer[$i][$j] = $this->clientInfo($car[$i][$j]['buyer']);
				if($car[$i][$j]['buyer'] != $car[$i][$j]['dealer'] and $car[$i][$j]['dealer'] != '0') $dealer[$i][$j] = $this->clientInfo($car[$i][$j]['dealer']);
				elseif($car[$i][$j]['dealer']  == '0') {
					$dealer[$i][$j]['name'] = '---';
				}
				else {
					$dealer[$i][$j]['name'] = '<<<';
				}
				$j++;
			}
			$i++;
		}
		
		$i = 0;
		$this->page .= '<center><b>'.$container[$i]['date'].'</b></center><br>';
		while($i<$total_containers)	{
			$this->page .= '<div style="margin-bottom:3px;"><img src="'.$this->root_path.'img/ccl/gbg.gif" style="position:absolute; z-index:-1;" width="667" height="20">
			<table width="100%" style="font-size:15px;background-color:#ccc;" cellpadding="0" cellspacing="0">
				<td colspan="3" style="padding-top:1px;">&nbsp;№ <b style="font-size:16px;background-color:#fff;">&nbsp;'.$container[$i]['number'].'&nbsp;</b></td>
				</tr></table>
			<table cellspacing="0" cellpadding="1" width="100%" style="font-size:13px; border:1px solid #aaa;">';	
			$j = 0;
			while($j<count($car[$i])) {
			$this->page .= '
			<tr>
				<td style="font-size:15px; color:#555;background-color:#fff;" valign="top" width="15">'.($j+1).'</td>
				<td valign="top" style="background-color:#fff;">
				<b><nobr>'.$car[$i][$j]['model'].'</b></nobr> 
				<nobr>'.$car[$i][$j]['frame'].'</nobr></td>
				<td width="90" valign="top"><nobr>&nbsp;('.$car[$i][$j]['year'].'/'.$car[$i][$j]['seats'].'P/'.$car[$i][$j]['engine'].'/'.$car[$i][$j]['fuel_type'].')&nbsp;</nobr></td>
				<td width="350" style="background-color:#fff;" valign="top"><b>'.$buyer[$i][$j]['name'].'</b>'.($buyer[$i][$j]['contacts']!=''?' тел: '.$buyer[$i][$j]['contacts']:'').'</td>
			</tr>
			';
			
			if(($j+1)<count($car[$i])) $this->page .='
			<tr><td colspan="4"><hr size="1" noshade color="#555555" style="margin:0px;"></td></tr>';
			$j++;
			}
			$this->page .= '</table>
			</div>';
			
			if($_POST['pagebrakes']=='on') $this->page .= '<p style="page-break-before: always"></p>';
			
			$i++;
		}
		
	}
		
	function saveChanges($id, $date) {
		$this->mysqlQuery("UPDATE `ccl_".ACCOUNT_SUFFIX."containers` 
		SET `bishkek` = '".$date."', `arrived` = '1' 
		WHERE `id` ='".$id."'");
	}
	
	function getInfo($id) {
		return $this->mysqlQuery("
		SELECT t2.model, t2.frame, t2.buyer, t2.reciever, t2.dealer, t2.year, t2.engine, t2.seats, t2.fuel_type,t1.number 
		FROM `ccl_".ACCOUNT_SUFFIX."containers` AS t1 
		RIGHT JOIN `ccl_".ACCOUNT_SUFFIX."cars` as t2 
		ON (t1.slot1 = t2.id 
		OR t1.slot2 = t2.id 
		OR t1.slot3 = t2.id 
		OR t1.slot4 = t2.id
                OR t1.slot5 = t2.id)
		WHERE t1.id = ".$id);
	}
	
	function clientInfo($id) {
		return mysql_fetch_array($this->mysqlQuery("
		SELECT name,contacts 
		FROM `ccl_".ACCOUNT_SUFFIX."customers` 
		WHERE `id` = '".$id."'"));
	}
}



?>