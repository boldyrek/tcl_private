<?php
set_time_limit(0);	
define('COOKIE_FILE', 'cookie.txt'); // Путь к файлу с кукисами (кстати, удалять его не надо после каждого прогона)

// парсим форму
function parse_form($s)
{
	$matches = array();
	$action  = '';
	$fields  = array();
	
	if(empty($s)) {die('Empty string'); }
	
	// Узнаем action формы
	$pattern = '/<form.*action="(.*)"/U';
	preg_match_all($pattern, $s, $matches);
	
	$action = $matches[1][0];
	$fields['action']['name']="action";
	$fields['action']['value']=$action;
	
//	echo "Action: {$action}\n\n";

	// Узнаем скрытые поля
	$pattern = '/<input.*type="hidden".*name="(?P<name>.*)".*value="(?P<value>.*)"/Us';
	preg_match_all($pattern, $s, $matches);
	
	if(!empty($matches['name']) && !empty($matches['value'])) {
		$i = 0;
		foreach($matches['name'] as $k => $v) {
			$fields[$i]['name']  = $v;
			$fields[$i]['value'] = $matches['value'][$k];
			$i++;
		}
	}
	
//	echo "\nHiddens: ".count($fields)."\n";

	// Находим поле Title
	$pattern = '/<input.+class="req".+type="text".+name="(?P<name>.*)"/Us';
	preg_match_all($pattern, $s, $matches);
	$title = $matches['name'][0];
	
	$fields['title']['name']  = $title;
	$fields['title']['value'] = '';

	// Находим поле Price
	$pattern = '/\$<input.+type="text".+name="(?P<name>.*)"/Us';
	preg_match_all($pattern, $s, $matches);
	$price = $matches['name'][0];
	
	$fields['price']['name']  = $price;
	$fields['price']['value'] = '';
	
	// Находим поле Phone
	$result1=strstr($s,'Specific Location:');
	$tt=explode('"',$result1);
	$fields['phone']['name']  = $tt[7];
	$fields['phone']['value'] = '';

	// Находим имя радио-кнопки
	$pattern = '/<input.+type="radio".+name="(?P<name>.*)"/Us';
	preg_match_all($pattern, $s, $matches);
	$radio = $matches['name'][0];
	
	$fields['radio']['name']  = $radio;
	$fields['radio']['value'] = 'C';
	
	// Находим имя текстарии
	$pattern = '/<textarea.*name="(?P<name>.*)"/Us';
	preg_match_all($pattern, $s, $matches);
	$textarea = $matches['name'][0];
	
	$fields['desc']['name']  = $textarea;
	$fields['desc']['value'] = '';

	// Находим имя кнопки
	$pattern = '/<button.+type="submit".+name="(?P<name>.*)"/Us';
	preg_match_all($pattern, $s, $matches);
	$button = $matches['name'][0];
	
	$fields['submit']['name']  = $button;
	$fields['submit']['value'] = 'Continue';
	
	return $fields;
}

// Exec cURL
function c_exec($url, $post='', &$info = NULL, $options = NULL) {

	$ch = curl_init();

    $c = COOKIE_FILE;

    $opts = array();
    $opts['referer'] = empty($options['referer']) ? $url : $options['referer'];


	curl_setopt($ch, CURLOPT_URL,            $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	curl_setopt($ch, CURLOPT_TIMEOUT,        60);
//	curl_setopt($ch, CURLOPT_VERBOSE,        0);
	curl_setopt($ch, CURLOPT_HEADER,         1);
	curl_setopt($ch, CURLOPT_NOBODY,         0);
	curl_setopt($ch, CURLOPT_REFERER,        $opts['referer']);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_USERAGENT,      "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; bgft)");
//	curl_setopt($ch, CURLOPT_COOKIE,         true);
	curl_setopt($ch, CURLOPT_COOKIEJAR,      $c);
	curl_setopt($ch, CURLOPT_COOKIEFILE,     $c);

//	echo "#$url - ";
	if(is_array($post)){
//		echo " - 2#<br>";
		
	    //$post1 = http_build_query($post);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_POST,       1);

		//********************************************
		// Вот в этом вся жопень то и заключалась :)
		//********************************************
			//echo "\n\nSending additional header\n\n";
			//curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	
		//********************************************

	}
	else
	if($post)
	{
//		echo " - 1#<br>";
		curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
		curl_setopt($ch, CURLOPT_POST,       1);

		//********************************************
		// Вот в этом вся жопень то и заключалась :)
		//********************************************

		$length = strlen($post);

		if($length > 1020) {
			echo "\n\nSending additional header\n\n";
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
		}
	}	
//	echo "<br>";

    $return = curl_exec($ch);
    $info   = curl_getinfo($ch);
    curl_close($ch);

	if (strpos($return,"1.1 302 Found")>2)
	// значит переадресация идет
	{
		$return=strstr($return,'Location:');
		$tt=explode(' ',$return);
		$tt=explode(chr(10),$tt[1]);
		
		if ($tt[0][0]=='/')
		{
			$tt1=explode('/',$url);
			$tt[0]=$tt1[0].'//'.$tt1[2].$tt[0];
		}
//		echo "tt0=".$tt[0];
//		exit();
	
		$return = c_exec(trim($tt[0]));	
	}

	return $return;
}

// функция для работы с изображениями - нужная для создания тумб из картинок
function LoadJpeg($imgname) 
{
   $im = @imagecreatefromjpeg($imgname); /* Attempt to open */
   if (!$im) 
   { /* See if it failed */
       $im  = imagecreate(150, 30); /* Create a blank image */
       $bgc = imagecolorallocate($im, 255, 255, 255);
       $tc  = imagecolorallocate($im, 0, 0, 0);
       imagefilledrectangle($im, 0, 0, 150, 30, $bgc);
       /* Output an errmsg */
       imagestring($im, 1, 5, 5, "Error loading $imgname", $tc);
   }
   return $im;
}

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////  БЛОК РАСПОЗНАВАНИЯ КАПЧИ
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
function preg($i){
 global $result;
 preg_match ('|'.$i.'|is' , $result, $r);
 return $r[1];
}

function recognize($filename, $apikey, $is_verbose = true, $rtimeout = 5, $mtimeout = 120, $is_phrase = 0, $is_regsense = 0, $is_numeric = 0, $min_len = 0, $max_len = 0)
{
	if (!file_exists($filename))
	{
		if ($is_verbose) echo "file $filename not found\n";
		return false;
	}
    $postdata = array(
        'method'    => 'post', 
        'key'       => $apikey, 
        'file'      => '@'.$filename, //полный путь к файлу
        'phrase'	=> $is_phrase,
        'regsense'	=> $is_regsense,
        'numeric'	=> $is_numeric,
        'min_len'	=> $min_len,
        'max_len'	=> $max_len,
        
    );
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,             'http://www.ac-service.info/in.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER,     1);
    curl_setopt($ch, CURLOPT_TIMEOUT,             60);
    curl_setopt($ch, CURLOPT_POST,                 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,         $postdata);
    $result = curl_exec($ch);
    if (curl_errno($ch)) 
    {
    	if ($is_verbose) echo "CURL returned error: ".curl_error($ch)."\n";
        return false;
    }
    curl_close($ch);
    if (strpos($result, "ERROR")!==false)
    {
    	if ($is_verbose) echo "server returned error: $result\n";
        return false;
    }
    else
    {
        $ex = explode("|", $result);
        $captcha_id = $ex[1];
    	if ($is_verbose) echo "captcha sent, got captcha ID $captcha_id\n";
        $waittime = 0;
        if ($is_verbose) echo "waiting for $rtimeout seconds\n";
        sleep($rtimeout);
        while(true)
        {
            $result = file_get_contents('http://ac-service.info/res.php?key='.$apikey.'&action=get&id='.$captcha_id);
            if (strpos($result, 'ERROR')!==false)
            {
            	if ($is_verbose) echo "server returned error: $result\n";
                return false;
            }
            if ($result=="CAPCHA_NOT_READY")
            {
            	if ($is_verbose) echo "captcha is not ready yet\n";
            	$waittime += $rtimeout;
            	if ($waittime>$mtimeout) 
            	{
            		if ($is_verbose) echo "timelimit ($mtimeout) hit\n";
            		break;
            	}
        		if ($is_verbose) echo "waiting for $rtimeout seconds\n";
            	sleep($rtimeout);
            }
            else
            {
            	$ex = explode('|', $result);
            	if (trim($ex[0])=='OK') return trim($ex[1]);
            }
        }
        
        return false;
    }
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////  КОНЕЦ БЛОКА РАСПОЗНАВАНИЯ КАПЧИ
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Считали ID машины из строки ввода браузера
$car_id=$_POST["car_id"];
if ($car_id<1) $car_id=$_GET["car_id"];
// проверили ID на валидность
if ($car_id<1 or strlen($car_id)<1) 
{
	echo "INCORRECT car_id";
	exit();
}

$main_url=$_POST["main_url"];

$login=$_POST["login"];
$password=$_POST["password"];
$my_phone=$_POST["phone"];
$email=$_POST["email"];
//echo "main_url=$main_url<br>";
//exit();

unlink(COOKIE_FILE);

// подключились к базе данных и считали строку данных для нашего ID
$link = mysql_connect("localhost", "root", "ieSeTiengae7Sh")
    or die("Could not connect : " . mysql_error());
mysql_query( 'SET NAMES cp1251' );	
mysql_select_db("boldyrek_db1") or die("Could not select database");
$query = 'SELECT car,price,comment FROM ccl_forsale WHERE id='.$car_id;
$result = mysql_query($query);// or echo "Query failed : " . mysql_error();
if ($result===FALSE) 
	echo "Query failed : " . mysql_error()."<br>";
$i=0;
while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
{
	$list_cars[$i]["id"]=$row[0];// считали ID для ccl_cars
	$list_cars[$i]["price"]=$row[1];// считали цену
	$list_cars[$i]["comment"]=$row[2];// считали описание
	$i++;
   }
mysql_free_result($result);			
mysql_close($link);


$i=0;// здесь раньше был оператор цикла, теперь обрабатывает только один первый элемент - оставили для совместимости (мало-ли потом понадобится)
{
	// проверили, существует ли введенный ID в таблице
	if (strlen($list_cars[$i]["id"])<1 or $list_cars[$i]["id"]<1)
	{
		echo "ID:$car_id не существует в таблице ccl_forsale";
		exit();
	}
	
	// считали все данные по введенному ID из базы
	$link = mysql_connect("localhost", "root", "ieSeTiengae7Sh")
		or die("Could not connect : " . mysql_error());
	mysql_select_db("boldyrek_db1") or die("Could not select database");

	$query = 'SELECT model,engine,milage,top_photo,frame FROM ccl_cars WHERE id='.$list_cars[$i]["id"];
	$result = mysql_query($query);// or echo "Query failed : " . mysql_error();
	if ($result===FALSE) 
		echo "Query failed : " . mysql_error()."<br>";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
	{
		$field_model=$row[0];
		$main_photo=$row[3];
		$vin=$row[4];
		//echo "top_photo=$main_photo | ";
		break;
   }
	mysql_free_result($result);			

	if ($main_photo==0)
		$query = 'SELECT file FROM ccl_cars_photos WHERE car='.$list_cars[$i]["id"];
	else	
		$query = 'SELECT file FROM ccl_cars_photos WHERE id='.$main_photo;
	$result = mysql_query($query);// or echo "Query failed : " . mysql_error();
	if ($result===FALSE) 
		echo "Query failed : " . mysql_error()."<br>";
	while ($row = mysql_fetch_array($result, MYSQL_NUM)) 
	{
		$main_photo=$row[0];
		break;
   }
	mysql_free_result($result);	
	mysql_close($link);	

	//echo "main_photo=$main_photo<br>";

	// если поле с названием марки машины пустое, то выходим
	if (strlen($field_model)<2) 
	{
		echo "Model name is EMPTY";
		exit();
	}

	////////////////// ДАННЫЕ, ВЗЯТЫЕ ИЗ БАЗЫ
	$field_price=$list_cars[$i]["price"];// цена
	//$field_desc=$list_cars[$i]["comment"];// описание
	// добавляем в конец описания домен
	//if ((strlen($field_desc)+strlen(" www.makmalauto.com"))<=150) $field_desc.=" www.makmalauto.com";
	
	/////////////////// ВВЕСТИ КОНТАКТНЫЕ ДАННЫЕ
	//$my_email="dmitrii@makmalauto.com";//Ваш e-mail
	//$my_email="alexfeat@gmail.com";//Ваш e-mail
	//$my_phone="416-738-9586";// телефон
	////////////////// 
	
	// БЕРЕМ ОПИСАНИЕ ПО ВИН КОДУ
	$url="https://www.agedstock.com/main/vin.php";
	$fields="var[vin]=$vin&submit=Go";
	$result = c_exec($url, $fields, $info);

	// грабим описалово
/*	СТАРЫЙ ФОРМАТ
	$result1=strstr($result,'Options');
	$tt=explode("valign='top'>",$result1);
	$result1=$tt[1];		
	$tt=explode("</td>",$result1);
	$options=str_replace("<br />",", ",$tt[0]);		
	$options=str_replace("\r","",$options);		
	$options=str_replace("\n","",$options);	*/
	// НОВЫЙ ФОРМАТ
	$options="";
/*	while (TRUE)
	{
		$result=strstr(substr($result,2),"<th");
		if (strlen($result)<10) break;
		$tt=explode('>',$result);
		$tt=explode("<",$tt[1]);
		$options.="".$tt[0].":".chr(10);
		
		$f1=strpos($result,"<th",2);
		$result1=substr($result,0,$f1);
		
		while (TRUE)	
		{	
			$result1=strstr(substr($result1,2),'<td class="VinField">');
			if (strlen($result1)<10) break;
			$tt=explode('>',$result1);
			$tt=explode("<",$tt[1]);
			$options.=$tt[0].": ";		
			$result1=strstr(substr($result1,2),'<td class="VinValue">');
			if (strlen($result1)<10) break;
			$tt=explode('>',$result1);
			$tt=explode("<",$tt[1]);
			$options.=$tt[0].", ";		
		}
		$options.=chr(10).chr(10);
	}
	$options=rtrim($options,", ");
	
	if (strlen($options)<10)
	{
		echo "https://www.agedstock.com/main/vin.php for $vin is EMPTY";
		exit();
	}
	*/
	
	$options='For detail please call to 6474355876';
	
	
	// считываем данные по городам из крэйглиста
	$result = c_exec("http://www.craigslist.org/about/sites");	
	
	$result=strstr($result,"</table>");
	

	//echo "options=$options";
	//exit();

	
	unlink($c);// удаляем кукисы
	
	//$login="alexfeat@gmail.com";
	//$password="smcalex";

	// логинимся	
	//$url="https://accounts.craigslist.org/login";
	//$fields="loginType=L&step=confirmation&originalURI=/login&rt=&rp=&inputEmailHandle=$login&inputPassword=$password";
	//$result = c_exec($url, $fields, $info);
	
	//echo $fields."<br>$result";
	//exit();
	// проверяем валидность логина
	/*if (strpos($result,"log out")<10)
	{
		echo "Incorrect Login or Password";
		exit();
	}*/
	
	// открываем страницу с формой ввода
	$url=$main_url;
	$result = c_exec($url, '', $info);
	//echo $result;
	//exit();
	
	/////////////////////////////////////////////////////////////////////////
	// прикрепляем изображения
	/////////////////////////////////////////////////////////////////////////

	// считали все файлы картинок из папки для нашей тачки
	$handle = opendir('../photos/'.$list_cars[$i]["id"]);
	//echo '../photos/'.$list_cars[$i]["id"]."<br>";
	$field_photos=array();
	$field_photos[0]=$main_photo;// первая картинка будет та, которую мы выбрали
	while (false !== ($file = readdir($handle)))
	{
		// нам нужны толька картинки и не нужны с файлы перфиксами
		if (strpos(strtolower($file),".jp")>1 and substr_count(strtolower($file),"adoc_")<1 and substr_count(strtolower($file),"insp_")<1 and substr_count(strtolower($file),"achk_")<1 and substr_count(strtolower($file),"cert_")<1 and substr_count(strtolower($file),"carf_")<1 and $file!==$main_photo)
		{
			// заносим имена файлов в массив
			$field_photos[] = $file;
			//echo $file."<br>";
		}
	}
	closedir($handle);	

	// максимально можно добавить 4 фотки
	if (count($field_photos)>4) 
		$max_photos=4; 
	else
		$max_photos=count($field_photos)-1;
		 
	$postdata = array();

	// постим объявление
	// парсим форму на скрытые поля
	$fields=parse_form($result);
	$url='https://post.craigslist.org'.$fields['action']['value'];
	
	$fields['title']['value'] = $field_model;
	$fields['price']['value'] = $field_price;
	$fields['phone']['value']  = $my_phone;
	$fields['desc']['value']  = $options;
	
	$fields['email1']['name']  = "FromEMail";
	$fields['email2']['name']  = "ConfirmEMail";
	$fields['email1']['value']  = $email;
	$fields['email2']['value']  = $email;
	
	
	foreach($fields as $field) {
		if ($field['name']!=="action")
			$postdata[$field['name']] = $field['value'];
//		$post.= $field['name'].'='.urlencode($field['value']).'&';
	}

	// перебираем все изображения
	for ($ii=1;$ii<=$max_photos;$ii++)
	{
		// временно копируем картинку в папку со скриптом
		copy('../photos/'.$list_cars[$i]["id"].'/'.$field_photos[$ii],$field_photos[$ii]);
		
		// постим картинку
		$postdata["imgfile$ii"] = "@".$field_photos[$ii];
	}		


	$post = '';
	reset($postdata);
	while (list($key, $val) = each($postdata)) {
		$post.="$key=$val&";
	}
	$post=rtrim($post,"&");
	
//	$post = rtrim($post, '&');
	$opts = array('referer' => $main_url);

   // $post = http_build_query($postdata);
	
//	$post.=$post_images;
	//echo "$post<br>";
	echo "\n<pre>\n";
	//print_r($postdata);
	echo '</pre>';
	
	//$result = c_exec($url, $postdata, $info, $opts);
	while (TRUE)
	{
		$ch = curl_init();
	
		$c = COOKIE_FILE;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,             $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,     1);
		curl_setopt($ch, CURLOPT_TIMEOUT,             60);
		curl_setopt($ch, CURLOPT_POST,                 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS,         $postdata);
		
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	//	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 60);
	//	curl_setopt($ch, CURLOPT_TIMEOUT,        60);
		curl_setopt($ch, CURLOPT_VERBOSE,        0);
		curl_setopt($ch, CURLOPT_HEADER,         1);
	//	curl_setopt($ch, CURLOPT_NOBODY,         0);
	//	curl_setopt($ch, CURLOPT_REFERER,        $url);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT,      "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; bgft)");
	//	curl_setopt($ch, CURLOPT_COOKIE,         true);
		curl_setopt($ch, CURLOPT_COOKIEJAR,      $c);
		curl_setopt($ch, CURLOPT_COOKIEFILE,     $c);
		
		//echo "\n\nSending additional header\n\n";
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));	
		
		$result = curl_exec($ch);
		$info   = curl_getinfo($ch);
		curl_close($ch);	
		
		if (strpos($result,"1.1 302 Found")>2)
		// значит переадресация идет
		{
			$result=strstr($result,'Location:');
			$tt=explode(' ',$result);
			$tt=explode(chr(10),$tt[1]);
	//		echo "tt0=".$tt[0];
	//		exit();
		
			$url = trim($tt[0]);	
		}
		else
			break;

	}

	echo "\n<pre>\n";
	//print_r($info);
	echo '</pre>';
	
	//echo $result;
	//exit();
	

	$f1=1;
	$add_hiddens="";
	while (TRUE)
	{
		$f1=strpos($result,'<input type="hidden"',$f1+1);
		if ($f1<10) break;
		$f2=strpos($result,'>',$f1+1);
		$inp=substr($result,$f1,$f2-$f1+1);
		//echo "$inp<br>";
		
		$tt=explode('name="',$inp);
		//echo $tt[1]."<br>";
		$tt=explode('"',$tt[1]);
		$hidden1_t=$tt[0];				

		$tt=explode('value="',$inp);
		$tt=explode('"',$tt[1]);
		$hidden2_t=$tt[0];				
		
		if ($hidden2_t!=="Edit")
			$add_hiddens.=urlencode($hidden1_t)."=".urlencode($hidden2_t)."&";
	}
	
	$result1=strstr($result,'type="submit"');
	$tt=explode('"',$result1);
	$hidden1=$tt[5];	
	
	$result1=strstr($result,'action=');
	$tt=explode('"',$result1);
	$hidden_action=$tt[1];		

	$fields=$add_hiddens.urlencode($hidden1)."=Continue";
	$fields=str_replace("%3A","",$fields);
	//echo "$fields<br>";
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields); 	
	curl_setopt($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $c);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $c);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 160);
	curl_setopt($ch, CURLOPT_TIMEOUT, 160);					
	curl_setopt($ch, CURLOPT_REFERER, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Widows NT)"); 

	if(strlen($fields) > 1020) {
		//echo "\n\nSending additional header\n\n";
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	}

	$result = curl_exec ($ch);
	curl_close ($ch); 
	
	$f1=1;
	$add_hiddens="";
	while (TRUE)
	{
		$f1=strpos($result,'<input type="hidden"',$f1+1);
		if ($f1<10) break;
		$f2=strpos($result,'>',$f1+1);
		$inp=substr($result,$f1,$f2-$f1+1);
		//echo "$inp<br>";
		
		$tt=explode('name="',$inp);
		//echo $tt[1]."<br>";
		$tt=explode('"',$tt[1]);
		$hidden1_t=$tt[0];				

		$tt=explode('value="',$inp);
		$tt=explode('"',$tt[1]);
		$hidden2_t=$tt[0];				
		
		if (substr_count($hidden2_t,"DECLINE")<1)
			$add_hiddens.=urlencode($hidden1_t)."=".urlencode($hidden2_t)."&";
	}
	
	$result1=strstr($result,'type="submit"');
	$tt=explode('"',$result1);
	$hidden1=$tt[3];	
	
	$result1=strstr($result,'action=');
	$tt=explode('"',$result1);
	$hidden_action=$tt[1];		

	$fields=$add_hiddens.urlencode($hidden1)."=".urlencode("ACCEPT the terms of use");
	$fields=str_replace("%3A","",$fields);
	//echo "$fields<br>";
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields); 	
	curl_setopt($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $c);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $c);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 160);
	curl_setopt($ch, CURLOPT_TIMEOUT, 160);					
	curl_setopt($ch, CURLOPT_REFERER, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Widows NT)"); 

	if(strlen($fields) > 1020) {
		//echo "\n\nSending additional header\n\n";
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	}

	$result = curl_exec ($ch);
	curl_close ($ch); 	
	
	//echo $result;
	//exit();
	
	
	$result1=strstr($result,'challenge?k=');
	$tt=explode('"',$result1);
	$hidden1=substr($tt[0],12);
//	echo "$hidden1<br>";

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "http://api.recaptcha.net/challenge?k=$hidden1"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $c);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $c);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 160);
	curl_setopt($ch, CURLOPT_TIMEOUT, 160);					
	curl_setopt($ch, CURLOPT_REFERER, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Widows NT)"); 
	$result1 = curl_exec ($ch);
	curl_close ($ch); 	
	
	$result1=strstr($result1,'challenge :');
	$tt=explode("'",$result1);
	$hidden1=$tt[1];

	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "http://api.recaptcha.net/image?c=$hidden1"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $c);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $c);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 160);
	curl_setopt($ch, CURLOPT_TIMEOUT, 160);					
	curl_setopt($ch, CURLOPT_REFERER, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Widows NT)"); 
	$result1 = curl_exec ($ch);
	curl_close ($ch); 	

 	//echo $result;
	//exit();
 
	 $fp_p=fopen("recapcha.jpg",'w');
	 fwrite($fp_p,$result1);
	 fclose($fp_p);	

	$cap=recognize("recapcha.jpg","cb4d7981ccd88a3594799c898cd1e331",false);		
	
	$f1=1;
	$add_hiddens="";
	while (TRUE)
	{
		$f1=strpos($result,'<input type="hidden"',$f1+1);
		if ($f1<10) break;
		$f2=strpos($result,'>',$f1+1);
		$inp=substr($result,$f1,$f2-$f1+1);
		//echo "$inp<br>";
		
		$tt=explode('name="',$inp);
		//echo $tt[1]."<br>";
		$tt=explode('"',$tt[1]);
		$hidden1_t=$tt[0];				

		$tt=explode('value="',$inp);
		$tt=explode('"',$tt[1]);
		$hidden2_t=$tt[0];				
		
		if ($hidden2_t!=="Edit")
			$add_hiddens.=urlencode($hidden1_t)."=".urlencode($hidden2_t)."&";
	}
	
	$result1=strstr($result,'action=');
	$tt=explode('"',$result1);
	$hidden_action=$tt[1];		

	$fields=$add_hiddens."recaptcha_challenge_field=".urlencode($hidden1)."&recaptcha_response_field=".urlencode($cap);
	$fields=str_replace("%3A","",$fields);
	//echo "$fields<br>";
	
	$ch = curl_init(); 
	curl_setopt($ch, CURLOPT_URL, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, 1); 
	curl_setopt($ch, CURLOPT_POSTFIELDS,$fields); 	
	curl_setopt($ch, CURLOPT_HEADER, 1); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  2); 
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); 
	curl_setopt($ch, CURLOPT_COOKIEFILE, $c);
	curl_setopt($ch, CURLOPT_COOKIEJAR, $c);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 160);
	curl_setopt($ch, CURLOPT_TIMEOUT, 160);					
	curl_setopt($ch, CURLOPT_REFERER, "https://post.craigslist.org$hidden_action"); 
	curl_setopt($ch, CURLOPT_USERAGENT, "User-Agent: Mozilla/4.0 (compatible; MSIE 5.01; Widows NT)"); 

	if(strlen($fields) > 1020) {
		//echo "\n\nSending additional header\n\n";
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Expect:'));
	}

	$result = curl_exec ($ch);
	curl_close ($ch); 
	
	// удаляем фотки из папки
	for ($ii=1;$ii<=$max_photos;$ii++)
	{
		// временно копируем картинку в папку со скриптом
		unlink($field_photos[$ii]);
	}		
	

	// проверяем выходные данные
	// проверяем если выдало что такой пост уже был
	if (strpos($result,"Similar Posting Already Submitted")>10)
	{
		$result=strstr($result,'<p><font size="4"><b>Similar Posting Already Submitted');
		$tt=explode("</div>",$result);
		echo $tt[0]."</div>";
		echo "<br><br>Post:<br>$field_model - $field_price - $my_phone<br><br>$options";
		//echo "Sorry. You may post to one category and in one city, no more often than about every 48 hours.";
		
		exit();
	}

	if (strpos($result,"You have not entered the verification word correctly")>10)
	// неверно распозналась капча
	{
		echo "Sorry, but not entered the verification word correctly. Press F5 (Refresh) please.";
		exit();
	}

	if (strpos($result,"You will receive an email shortly")>10)
	{
		echo "You will receive an email shortly, containing a link that will allow you to: <br>
publish your ad <br>
edit your ad <br>
delete your ad <br>
KEEP THIS EMAIL -- you will need it to delete your ad when you are done with it.
";
		exit();
	}
	
	echo "<h1>UNKNOWN ERROR</h1><br>";$result;
	exit();
}
?>