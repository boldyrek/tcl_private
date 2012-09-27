<?

function getContent($request) {
	return mysql_query($request);
}

function printList($content) {
	global $template;
	$num = mysql_num_rows($content);
	if($num>0) {
		$i = 0;
		while ($i<$num)
		{
			$line = mysql_fetch_array($content);
			$cars[$i] = $line;
			$ids[$i] = $line['car'];
			$i++;	
		}
		$photos = getPhotos($ids);
		foreach ($cars as $k => $v) {
			$line = $v;
			$item = $template;
			if($photos[$line['car']]!='') $photo = '<a href="/photos/'.ACCOUNT_SUFFIX.$line['car'].'/'.$photos[$line['car']].'" target="_blank"><img src="/photos/'.ACCOUNT_SUFFIX.$line['car'].'/thumb/'.$photos[$line['car']].'" border="0" align="left"></a>';
			else $photo = '';
			$item = str_replace('[%photo%]', $photo, $item);
			$item = str_replace('[%id%]', $line['id'], $item);
			$item = str_replace('[%model%]', $line['model'], $item);
			$item = str_replace('[%year%]', $line['year'], $item);
			$item = str_replace('[%price%]', $line['price'], $item);
			$item = str_replace('[%info%]', $line['comment'], $item);
			$out .= ' 
			'.$item;
		}
		return $out;
	}
	else return 'nothing to print!';
}

function printItem($id, $mode) {
	global $item_template_max, $item_template_min;
	$request = "SELECT ccl_".ACCOUNT_SUFFIX."forsale.*, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.year, ccl_".ACCOUNT_SUFFIX."cars_cert.file as auc_list 
		FROM `ccl_".ACCOUNT_SUFFIX."forsale` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
		ON (ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."forsale.car)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars_cert`
		ON (ccl_".ACCOUNT_SUFFIX."cars_cert.car = ccl_".ACCOUNT_SUFFIX."forsale.car)
		WHERE ccl_".ACCOUNT_SUFFIX."forsale.id = '".mysql_real_escape_string(intval($id))."'";
	
	$content = mysql_fetch_array(getContent($request));
	if($mode == 'max') {
	$out = $item_template_max;
	$out = str_replace('[%model%]', $content['model'], $out);
	$out = str_replace('[%year%]', $content['year'], $out);
	$out = str_replace('[%price%]', $content['price'], $out);
	$out = str_replace('[%info%]', $content['info'], $out);
	$out = str_replace('[%auc_list%]', $content['auc_list'], $out);
	}
	elseif($mode=='mini') {
		$out = $item_template_min;
		$out = str_replace('[%id%]', $content['id'], $out);
		$out = str_replace('[%model%]', $content['model'], $out);
		$out = str_replace('[%year%]', $content['year'], $out);
		$out = str_replace('[%price%]', $content['price'], $out);
	}
	else $out = '';
	return $out;
}
function getPhotos($target) {
	
	foreach ($target as $k => $v) {
		$where .= " or `car` = '".intval($v)."'";
	}
	
	$photos = mysql_query("SELECT car,file FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE ".ltrim($where, ' or'));
	$num = mysql_num_rows($photos);
	if($num>0) {
		$i = 0;
		while($i<$num) {
			$line = mysql_fetch_array($photos);
			if($out[$line['car']]=='') $out[$line['car']] = $line['file'];
			$i++;
		}
	}
	return $out;
}

?>