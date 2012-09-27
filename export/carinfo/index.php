<?
//echo 'Введите 6 (шесть) последних цифр номера кузова<br><form action="" method="post"><input type="text" name="frame_id"><input type="submit" value="get"></form>';
//echo $_POST['frame_id'].'<br>';
if($_SERVER['REMOTE_ADDR']!='127.0.0.1' or $_SERVER['REMOTE_ADDR']=='212.42.102.205') {
	if(isset($_POST['frame_id']) and $_POST['frame_id']!='' and $_POST['frame_id']!=0) {
		if(strlen($_POST['frame_id'])>=6) {
		
		sleep(1); //подождем, чтобы не весело было подбирать номерочки
		
		require($_SERVER['DOCUMENT_ROOT'].'/inc/config.php');
		$conn=mysql_connect($DBvars['host'],$DBvars['user'],$DBvars['pass']) or die ("Нет соединения с базой");
		mysql_query('SET NAMES cp1251');
		mysql_select_db($DBvars['name'],$conn);
		
		echo drawCar();
		
		mysql_close($conn);
		}
		else echo '<br>Неверные данные для поиска!';
	}
	
}
else die('Forbidden!');

function getCarData($id) {
	$cardata = mysql_fetch_array(mysql_query("SELECT ccl_".ACCOUNT_SUFFIX."cars.id,ccl_".ACCOUNT_SUFFIX."cars.model,ccl_".ACCOUNT_SUFFIX."cars.frame,ccl_".ACCOUNT_SUFFIX."cars.reciever,
	ccl_".ACCOUNT_SUFFIX."customers.name,ccl_".ACCOUNT_SUFFIX."cars_cert.file, 
	ccl_".ACCOUNT_SUFFIX."containers.number as container, ccl_".ACCOUNT_SUFFIX."containers.sent, ccl_".ACCOUNT_SUFFIX."containers.china, ccl_".ACCOUNT_SUFFIX."containers.rail, ccl_".ACCOUNT_SUFFIX."containers.dostuck, ccl_".ACCOUNT_SUFFIX."containers.bishkek
	FROM `ccl_".ACCOUNT_SUFFIX."cars` LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers` ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."cars.reciever)
	LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars_cert` ON (ccl_".ACCOUNT_SUFFIX."cars_cert.car = ccl_".ACCOUNT_SUFFIX."cars.id)
	LEFT JOIN `ccl_".ACCOUNT_SUFFIX."containers` ON (ccl_".ACCOUNT_SUFFIX."containers.id = ccl_".ACCOUNT_SUFFIX."cars.container)
	WHERE ccl_".ACCOUNT_SUFFIX."cars.frame LIKE '%".mysql_real_escape_string(addslashes(ltrim(rtrim($id, ' '),' ')))."' LIMIT 1"));
	
	$cardata['photos'] = array();
	$photos = mysql_query("SELECT file FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE `car` = '".mysql_real_escape_string(intval($cardata['id']))."' ORDER BY `id` ASC");
	$num = @mysql_num_rows($photos);
	if($num>0) {
		$i = 0;
		while($i<$num) {
			$line = mysql_fetch_array($photos);
			$cardata['photos'][$i] = $line['file'];
			$i++;
		}
	}
	return $cardata;
}

function drawCar() {
	$info = getCarData($_POST['frame_id']);
	if($info['id']!=0 or $info['id']!='') {
	$out = '
	<hr size="1" noshade color="#bbbbbb">
	<span style="font-size:16px;font-weight:bold;">'.$info['model'].'</span><br>
	<span style="font-size:14px;">'.$info['frame'].'</span><br>
	<span style="font-size:14px;">'.$info['name'].'</span><br>
	<br>';
	if($info['file']!='') $out .= '<a href="/carinfo/?cl_id='.$info['id'].'&file='.$info['file'].'" target="_blank">Аукционный лист</a><br>';
	if($info['container']!='') {
		$out .= '<div style="padding:10px; border:1px solid #bbb; width:250px; margin-top:10px;">Конейнер №: '.$info['container'].'<br>
		<table border="0" cellspacing="1" cellpadding="1">
		<tr><td>Отправлен:</td><td>'.$info['sent'].'</td></tr>
		<tr><td>Китай:</td><td>'.killZeros($info['china']).'</td></tr>
		<tr><td>Ж/Д:</td><td>'.killZeros($info['rail']).'</td></tr>
		<tr><td>Достук:</td><td>'.killZeros($info['dostuck']).'</td></tr>
		<tr><td>Бишкек:</td><td>'.killZeros($info['bishkek']).'</td></tr>
		</table></div>';
	}
	if(count($info['photos'])>0) {
		$out .= '<div style="width:520px;">';
		foreach ($info['photos'] as $k => $v) {
			$out .= '<div style="width:150xp; float:left; padding:10px; border:1px solid #fff;" onMouseOver="this.style.border=\'1px solid #f00\'" onMouseOut="this.style.border=\'1px solid #fff\'">
			<a href="/carinfo/?cl_id='.$info['id'].'&photo='.$v.'" target="_blank">
			<img src="/carinfo/?cl_id='.$info['id'].'&photo='.$v.'&thumb" border="0"></a>
			</div>';
		}
		$out .= '</div>';
	}
	
	return $out;
	}
	return '<h3>По вашему запросу ничего не найдено.</h3>';
}

function killZeros($data) {
	if($data == '0000-00-00') return ' --- ';
	else return $data;
}

?>