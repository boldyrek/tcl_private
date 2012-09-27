<?
error_reporting(0);
set_time_limit(0);	// unlimited timelimit
//require('/home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/inc/baseconf.php');

	$db_host='localhost';
	$db_base='boldyrek_db1';
	$db_user='root';
	$db_pass='ieSeTiengae7Sh';

$dbc = mysql_connect($db_host, $db_user, $db_pass);
if (!$dbc){
	echo 'Cant connect to db. Halting...';
	exit;
}
mysql_select_db($db_base);


// Initiating some Constants
define('VIN_REQUESTS', 24);	// Number of request to Get Vin-code data on script run

// Initiating some vars
$referer = '';
$cars = array();	// Global cars array
$ch = 0;

// Simple Curl request
function curlit($link, $postdata = '', $referer = '') {
	//Initializing Curl
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL,$link);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HEADER, 1);
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiefile");
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookiefile");
	curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
	//curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	if ($postdata != ''){
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
	}
	if ($referer != '') curl_setopt($ch, CURLOPT_REFERER, $referer);

	$result = curl_exec($ch);
	curl_close($ch);
	return $result;
}

// Gets Cars List
function GetCarsList(){
	global $referer;
	// Entering site
	$url = 'http://www.cars.com/for-sale/advancedsearch.action';
	curlit($url);

	// Advanced Searching
	$url = 'http://www.cars.com/for-sale/searchresults.action';
	$ref = 'http://www.cars.com/for-sale/advancedsearch.action';
	$postdata = 'dlId=&dgId=&AmbMkNm='.'Lexus'.'&AmbMdNm='.'GX+470'.'&AmbMkId='.'20070'.'&AmbMdId='.'21213'.
		'&searchSource=ADVANCED_SEARCH&rd=100000&zc=12345&uncpo=2&cpo=&stkTyp=U&VType=&mkId=20070&mdId=21213&alMdId=21213&alMkId=20070&prMn='.
		'&prMx='.'22000'.'&clrId=&yrMn='.'2005'.'&yrMx='.'2006'.'&drvTrnId=&mlgMn=&mlgMx=&transTypeId=&kw=&kwm=ANY&ldId=&rpp=50&slrTypeId=';
	$referer = 'http://www.cars.com/for-sale/searchresults.action?'.$postdata;
	$rawlist = curlit($url,$postdata,$ref);
	return $rawlist;
}

// Parse raw cars list and get links list
function ParseRawList($raw){
	$list = array();
	$base = 'http://www.cars.com';
	$pattern = '/<div class="YmmHeader".*?<a.*?href="(\/go\/search.*?)".*?<\/div>/i';
	preg_match_all($pattern, $raw, $matches);
	foreach ($matches[1] as $url){
		$turl = str_replace('&amp;','&',$url);
		$list[] = $base . $turl;
	}
	return $list;
}

// Parse Vin Code from car details page
function GetDetails($url){
	$data = curlit($url);
	// Parsing : [2005] [Lexus GX 470], [$22,995] -Cars.com
	$pattern = '/<title>([0-9]+) (.*?), (\$[0-9,\.]+) .*?<\/title>/is';
	preg_match($pattern, $data, $matches);
	$year = isset($matches[1]) ? $matches[1] : '' ;
	$name = isset($matches[2]) ? $matches[2] : '' ;
	$price = isset($matches[3]) ? $matches[3] : '' ;
	// Parsing VIN
	$pattern = '/<span class="label">VIN:.*?<span class="data">([A-Z0-9]*?)<\/span>/is';
	preg_match($pattern, $data, $matches);
	$vin = isset($matches[1]) ? $matches[1] : '' ;
	// Parsing Seller's comment
	$pattern = '/<span class="label">Seller\'s Notes:<\/span>(.*?)<\/div>/is';
	preg_match($pattern, $data, $matches);
	$comment = isset($matches[1]) ? $matches[1] : '' ;
	// Parsing Mileage
	//$pattern = '/<span class="label">Mileage:<\/span>(.*?)<\/div>/is';
	$pattern = '/<span class="label">Mileage:.*?<span class="data">(.*?)<\/span>/is';
	preg_match($pattern, $data, $matches);
	$mileage = isset($matches[1]) ? $matches[1] : '' ;
	$mileage = str_replace(',', '', $mileage);

	//echo "<br>\n".$vin.' : '.$name.' : '.$year.' : '.$price."<br>\n";
	return array( 'name' => $name, 'year' => $year, 'price' => $price, 'vin' => $vin, 'comment' => $comment, 'mileage' => $mileage );
}

// Parse curl responce with headers
function parse_response($this_response)
{
	// Split response into header and body sections
	list($response_headers, $response_body) = explode("\r\n\r\n", $this_response, 2);
	$response_header_lines = explode("\r\n", $response_headers);

	// First line of headers is the HTTP response code
	$http_response_line = array_shift($response_header_lines);
	if(preg_match('@^HTTP/[0-9]\.[0-9] ([0-9]{3})@',$http_response_line, $matches)) { $response_code = $matches[1]; }

	// put the rest of the headers in an array
	$response_header_array = array();
	foreach($response_header_lines as $header_line)
	{
		list($header,$value) = explode(': ', $header_line, 2);
		$response_header_array[$header] .= $value."\n";
	}

	return array("code" => $response_code, "header" => $response_header_array, "body" => $response_body);
}

// Get Vin Code Data (ext. and int. colors)
function GetVinData($vin_parameter){
	$base_url = 'http://www.japancats.ru/';
	$model = 'Lexus';
	$vin = preg_replace('![^A-Za-z0-9]!', '', $vin_parameter);	// Cleaning Vin string
	$post_values = '__EVENTTARGET=&__EVENTARGUMENT=&__LASTFOCUS=&__VIEWSTATE=%2FwEPDwUJODM1NjIxNjg1ZBgBBR5fX0NvbnRyb2xzUmVxdWlyZVBvc3RCYWNrS2V5X18WAgUhY3RsMDAkY3BoTWFzdGVyUGFnZSRjaGJTYXZlQ29va2llBSJjdGwwMCRjcGhNYXN0ZXJQYWdlJGNoYkZpbmRCeUZyYW1le75lwFhTFBgj%2F8xmymjv25JOtd8%3D&ctl00%24cphMasterPage%24txbVIN='.$vin.'&ctl00%24cphMasterPage%24btnFindByVIN=%D0%9F%D0%BE%D0%B8%D1%81%D0%BA&ctl00%24cphMasterPage%24txbFrame1=&ctl00%24cphMasterPage%24txbFrame2=&ctl00%24cphMasterPage%24rblCountry=E';
	$url = $base_url . $model . '/Default.aspx';
	$raw_data = curlit($url,$post_values);	// Sending car detail request

	$response = parse_response($raw_data);
	$location = $response['header']['Location'];
	$url2 = rtrim($base_url,'/') . $location;
	$vin_raw_data = curlit($url2,'',$url);	// Resending Location redirect
	preg_match_all('!<table id="ctl00_cphMasterPage_tblComplectation".*>(.*)</table>!sU', $vin_raw_data, $matches);
	//file_put_contents('car-vin-data.htm', $raw_data);

	if (!isset($matches[1][0])){	// If request failed show error notice
		echo "\n<span style='color:red;'>Vin code request failed!</span><br>\n";
	}

	$matches = explode('</tr>', $matches[1][0]);

	$headers = array();
	$values = array();
	foreach ($matches as $cell){	// Extracting Titles and Values from Vin-Data-Table
		preg_match('/<td>(.*)<\/td>.*<td><b>(.*)<\/b><\/td>/isU', $cell, $headpart);
		if (isset($headpart)){
			array_shift($headpart);
			$headers[] = $headpart[0];
			$values[] = $headpart[1];
		}
	}

	return array_combine($headers, $values);
}

// Checks if VIN code already exists in a database
function vin_exists($vin){
//	$vin = mysql_real_escape_string($vin);
//	$q = mysql_fetch_assoc(mysql_query("SELECT * FROM ccl_carscom_vins WHERE `vin` = '$vin'"));
//	$n = $q['cardata'];
	$n = mysql_num_rows(mysql_query("SELECT * FROM ccl_carscom_vins WHERE `vin` = '$vin'"));
	return $n;
}

// Checks if car satisfies search criteria
function car_is_proper(&$vindata){
	if ($vindata['extcolor'] == '202' or $vindata['extcolor'] == '1F0' or $vindata['extcolor'] == '070'){
		if ($vindata['intcolor'] == 'LK10' or $vindata['intcolor'] == 'LH10')
			return TRUE;
		else
			return FALSE;
	}
	else
		return FALSE;
}

// Storing VIN code into database
function StoreVIN($vin){
	$date = date("Y-m-d H:i:s");
	$n = mysql_num_rows(mysql_query("SELECT * FROM ccl_carscom_vins WHERE `vin` = '$vin'"));
	if ($n<1 and (strlen($vin) > 10)){
		$q = mysql_query("INSERT IGNORE INTO ccl_carscom_vins (vin, cardata, date) VALUES ('$vin', 0, '$date')");
	}
	if (mysql_errno ()) echo '<span style="color:red">'.mysql_error ()."</span><br>\n";
}

// Store car data to cardata table
// $card - car data (name, year, price, comment for gps)
// $vind - car vincode data (interior color, exterior color)
function StoreCarData($card, $vind){
	$date = date("Y-m-d H:i:s");
	$query = "INSERT INTO ccl_carscom_cars (name, year, price, intcolor, extcolor, gps, date, link, mileage, prodate) VALUES ('{$card['name']}', '{$card['year']}', '{$card['price']}', '{$vind['intcolor']}', '{$vind['extcolor']}','{$card['gps']}', '$date', '".$card['link']."', '{$card['mileage']}', '{$vind['prodate']}')";
	echo '<span color:blue;>Insert query string: </span>' . $query;
	$q = mysql_query($query);
	if(mysql_errno())
		echo "<br>\n<span style='color:orange;'>FAILED to INSERT new car: </span>".mysql_error ();
	else{
		echo "<br>\n<span style='color:green;'>New matched car found and stored into the base!</span><br>\n";
		$last_id = mysql_insert_id();
		$qq = mysql_query("UPDATE ccl_carscom_vins SET `cardata` = '$last_id' WHERE `vin` = '{$card['vin']}'");
		if(mysql_errno())
			echo "<br>\n<span style='color:orange;'>FAILED to UPDATE a link to newly inserted car: </span>".mysql_error ();
	}
}

// Script running time counter
function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}

// === - === - === - === - === - === - === - ===
// Main block
// === - === - === - === - === - === - === - ===

$time_start = microtime_float();

// Removing cookies
file_put_contents('cookiefile', '');

// Getting List
$rawlist = GetCarsList();
$links_list = ParseRawList($rawlist);

echo '<br><b>Links count: '.count($links_list)."</b><br><br>";
//echo '<div style="width:800px;height:120px;overflow:auto;"><pre style="font-size:12px;">';
//print_r($links_list);
//echo '</pre></div><br><br>';

// Getting cars datas in cycle
for($i=0;$i<VIN_REQUESTS;$i++){
	$link = $links_list[$i];
	$details = GetDetails($link);
	if(vin_exists($details['vin']) < 1){	// If VIN doesn't exist in the base
		StoreVIN($details['vin']);
		$cardata = GetVinData($details['vin']);
		if(!$cardata) continue;				// Continue if last request failed
		$clear_car_data = array('intcolor'=>$cardata['Код отделки'], 'extcolor'=>$cardata['Код цвета кузова'], 'prodate'=>$cardata['Дата выпуска']);
		if(preg_match('/[gps|navigat]+/is', $details['comment'])==1)	// Checking for gps/navigation presence
			$details['gps'] = '1';
		else
			$details['gps'] = '0';
		if(car_is_proper($clear_car_data)){
			$details['link'] = $link;
			file_put_contents('carscom_links.txt', $link."\n", FILE_APPEND);
			$r = StoreCarData($details, $clear_car_data);
		}
	}
	else echo "<span style='color:orange;'>Vin exists, skipping.</span><br>\n";
	echo sprintf("%02d",$i).' - '.$details['name'].' - '.$details['year'].' - '.$details['price'].' - '.$details['mileage'].' - '.$details['vin'].' - '.$cardata['Код цвета кузова'].' : '.$cardata['Код отделки']."<br>\n<hr style='width:50%;' align=left>";
}

// Script running time counter
$time_end = microtime_float();
$time = $time_end - $time_start;

echo "<br><br>\n\n<pre>Runtime: ".sprintf('%01.5f',$time).'</pre>';
//echo "<br><br>".$cardata['Код цвета кузова']."<br><br>";
//echo "<br><br>".$cardata['Код отделки']."<br><br>";

?>
