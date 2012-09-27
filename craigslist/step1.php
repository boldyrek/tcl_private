<?php
set_time_limit(0);	

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
//		echo "tt0=".$tt[0];
//		exit();
	
		$return = c_exec(trim($tt[0]));	
	}


	return $return;
}

// функция выбора страны
function select_country()
{
	global $car_id,$login,$password,$phone,$email;
	// выводим форму с выбором страны и ждем ввода
	echo '<form id="form1" name="form1" method="post" action="step1.php">
	  <label>STEP1 - Select Country:<br />
	  <select name="sel1" size="4" id="sel1">
		<option value="1">us cities</option>
		<option value="2">united states</option>
		<option value="3">canada</option>
		<option value="4">ca cities</option>
	  </select>
	  </label>
	  <input type="hidden" name="car_id" value="'.$car_id.'">
	  <input type="hidden" name="login" value="'.$login.'">
	  <input type="hidden" name="password" value="'.$password.'">
	  <input type="hidden" name="phone" value="'.$phone.'">
	  <input type="hidden" name="email" value="'.$email.'">
	  <br />
	  <label>
	  <input type="submit" name="submit" id="submit" value="Submit" />
	  </label>
	</form>
	';
	exit();
}

// функция выбора штата
function select_state($country=1)// $country: 1 - USA, 2 - Canada
{
	global $car_id,$sel1,$login,$password,$phone,$email;

	// считываем данные по штатам из крэйглиста
	$result = c_exec("http://www.craigslist.org/about/sites");	
	// обрезаем данные до нужного места в таблице
	$result=strstr($result,"</table>");


	// обрезаем, чтобы получить только список с ссылками на штаты
	$tt=explode("</td>",$result);
	if ($country==1)
		$result=$tt[2].$tt[3];// для штатов - это 3 и 4 куски ссылок
	else	
	{
		$result=$tt[4]; // для канады - это 5-й кусок, но не весь
		$tt=explode('<b>ca cities</b>',$result);
		$result=$tt[0]; // а только первая часть		
	}	
	
	// удаляем жирные выделения
	$result=str_replace("<b>","",$result);
	$result=str_replace("</b>","",$result);
	
	// разделяем на блоки с ссылками на штаты
	$tt=explode('<a href="',$result);

	$out=array();
	// и распарсиваем ссылки и названия штатов в массив
	for ($i=1;$i<count($tt);$i++)
	{
		$item=$tt[$i];
		$tt1=explode('"',$item);
		if (substr_count($tt1[0],"http://")<1) continue;
		$out[$i]["url"]=$tt1[0];
		//echo "$i - ".$tt1[0];
		$tt1=explode('>',$item);
		$tt1=explode('<',$tt1[1]);
		$out[$i]["title"]=$tt1[0];
		//echo " - ".$tt1[0]."<br>";
	}
	
	// если вдруг массив пустой, то заканчиваем работу - возможно, что сайт не работает или поменяли структуру ссылок
	if (count($out)<1)
	{
		echo "ERROR in parsing http://www.craigslist.org/about/sites for STATES";
		exit();
	}
	
	// выводим на экран форму с возможностью выбора нужного штата и ждем выбора пользователя
	echo '<form id="form1" name="form1" method="post" action="step1.php">
	  <label>STEP2 - Select State:<br />
	  <select name="sel2state" size="5" id="sel2">
	  ';
	for ($i=1;$i<=count($out);$i++)  
		if (strlen($out[$i]["url"])>5)
			echo '<option value="'.$out[$i]["url"].'">'.$out[$i]["title"].'</option>';
	echo '	
	  </select>
	  </label>
	  <input type="hidden" name="sel1" value="'.$sel1.'">
	  <input type="hidden" name="car_id" value="'.$car_id.'">
	  <input type="hidden" name="login" value="'.$login.'">
	  <input type="hidden" name="password" value="'.$password.'">
	  <input type="hidden" name="phone" value="'.$phone.'">
	  <input type="hidden" name="email" value="'.$email.'">
	  <br />
	  <label>
	  <input type="submit" name="submit" id="submit" value="Submit" />
	  </label>
	</form>
	';		
	exit();
}

// функция выбора города
function select_city($country=1,$buf='')// $country: 1 - USA, 2 - Canada, 3 - Список городов из строковой переменной
{
	global $car_id,$sel1,$login,$password,$phone,$email;

	// в зависимости от страны, список городов берется из разных мест
	if ($country==1)
	{
		// считываем данные по городам из крэйглиста
		$result = c_exec("http://www.craigslist.org/about/sites");	
		// обрезаем данные до нужного места в таблице для us cities
		$result=strstr($result,"</table>");

		// обрезаем снизу, чтобы получить только список с ссылками на города us cities
		$tt=explode("</td>",$result);
		$result=$tt[1];	
	}
	else
	if ($country==2)	
	{
		// считываем данные по городам из крэйглиста
		$result = c_exec("http://geo.craigslist.org/iso/ca");	
		// обрезаем данные до нужного места в таблице для canada
		$result=strstr($result,"</h4>");

		// обрезаем снизу, чтобы получить только список с ссылками на города us cities
		$tt=explode("</div>",$result);
		$result=$tt[0];	
	}
	else
		$result=$buf;

	// удаляем жирные выделения
	$result=str_replace("<b>","",$result);
	$result=str_replace("</b>","",$result);
	
	// разделяем на куски с ссылками на города
	$tt=explode('<a href="',$result);

	$out=array();
	// и распарсиваем ссылки и названия городов в массив
	for ($i=1;$i<count($tt);$i++)
	{
		$item=$tt[$i];
		$tt1=explode('"',$item);
		if (substr_count($tt1[0],"http://")<1) continue;
		$out[$i]["url"]=$tt1[0];
		//echo $tt1[0];
		$tt1=explode('>',$item);
		$tt1=explode('<',$tt1[1]);
		$out[$i]["title"]=$tt1[0];
		//echo " - ".$tt1[0]."<br>";
	}
	
	// если вдруг массив пустой, то заканчиваем работу - возможно, что сайт не работает или поменяли структуру ссылок
	if (count($out)<1)
	{
		echo "ERROR in parsing for CITIES";
		exit();
	}
	
	// выводим на экран форму с возможностью выбора нужного города и ждем выбора пользователя
	echo '<form id="form1" name="form1" method="post" action="step1.php">
	  <label>STEP2 - Select City:<br />
	  <select name="sel2" size="5" id="sel2">
	  ';
	for ($i=1;$i<=count($out);$i++)  
		if (strlen($out[$i]["url"])>5)
			echo '<option value="'.$out[$i]["url"].'">'.$out[$i]["title"].'</option>';
	echo '	
	  </select>
	  </label>
	  <input type="hidden" name="sel1" value="'.$sel1.'">
	  <input type="hidden" name="car_id" value="'.$car_id.'">
	  <input type="hidden" name="login" value="'.$login.'">
	  <input type="hidden" name="password" value="'.$password.'">
	  <input type="hidden" name="phone" value="'.$phone.'">
	  <input type="hidden" name="email" value="'.$email.'">
	  <br />
	  <label>
	  <input type="submit" name="submit" id="submit" value="Submit" />
	  </label>
	</form>
	';	
	exit();	
}


// получили все переменные от форм, какие только можно
$sel1=$_POST["sel1"];
$sel2=$_POST["sel2"];
$sel2state=$_POST["sel2state"];
$sel3=$_POST["sel3"];
$purl=$_POST["posturl"];
$login=$_POST["login"];
$password=$_POST["password"];
$phone=$_POST["phone"];
$email=$_POST["email"];

$car_id=$_POST["car_id"];
if ($car_id<1) $car_id=$_GET["car_id"];
if ($car_id<1) 
{
	echo "NO car_id";
	exit();
}
	
// если первого выбора страны еще не было
if ($sel1<1)	
	select_country();

// итак - выбор страны уже сделан
// теперь нужно выбрать город или штат, если этого еще не было сделано
if (strlen($sel2)<5 and strlen($sel2state)<5)	
{
	switch ($sel1) {
		case 1:// были выбраны us cities
			select_city(1);
			break;
		case 2:// были выбраны united states
			select_state(1);
			break;			
		case 3:// была выбрана canada
			select_state(2);
			break;			
		case 4:// были выбраны us cities
			select_city(2);
			break;
	}
	exit();
}

// если мы выбрали штаты, то щас надо выбрать город
if (strlen($sel2state)>5)	
{
	$result = c_exec($sel2state);	

	// смотрим - а есть ли на выбор города, и если вдруг нет, то считай, что штат - это и есть город
	if (strpos($result,"choose the site nearest you")<5)
	{
		$result=strstr($result,'<a href="https://post.craigslist.org/');
		
		// находим на странице ссылку на постинг объявления
		$tt=explode('"',$result);
		$posturl=$tt[1]."/ctd/";
		//echo "posturl=$posturl<br>";
		//exit();

		echo '<form id="form1" name="form1" method="post" action="craigslist.php">
		  <input type="hidden" name="car_id" value="'.$car_id.'">
		  <input type="hidden" name="main_url" value="'.$posturl.'">
	  <input type="hidden" name="login" value="'.$login.'">
	  <input type="hidden" name="password" value="'.$password.'">
	  <input type="hidden" name="phone" value="'.$phone.'">
	  <input type="hidden" name="email" value="'.$email.'">
	  <input type="submit" name="submit" id="submit" value="Submit to Craigslist" />
		</form>
		<script>document.forms["form1"].submit();</script>
		';	
		exit();		
	}
	else
	{
		// обрезаем данные до нужного места в таблице для canada
		$result=strstr($result,"</h4>");
	
		// обрезаем снизу, чтобы получить только список с ссылками на города us cities
		$tt=explode("</div>",$result);
		$buf=$tt[0];	
	
		switch ($sel1) {
			case 2:// были выбраны united states
				select_city(3,$buf);
				break;			
			case 3:// была выбрана canada
				select_city(3,$buf);
				break;			
		}
		exit();
	}
}



// если до этого были уже сделаны все выборы и в итоге пришлось выбирать дополнительный регион
// то в принципе уже ничего не надо парсить - идем на форму отправки
if (strlen($sel3)>2)	
{
	echo '<form id="form1" name="form1" method="post" action="craigslist.php">
	  <input type="hidden" name="car_id" value="'.$car_id.'">
	  <input type="hidden" name="main_url" value="https://post.craigslist.org'.$sel3.'">
	  <input type="hidden" name="login" value="'.$login.'">
	  <input type="hidden" name="password" value="'.$password.'">
	  <input type="hidden" name="phone" value="'.$phone.'">
	  <input type="hidden" name="email" value="'.$email.'">
	  <input type="submit" name="submit" id="submit" value="Submit to Craigslist" />
	</form>
	<script>document.forms["form1"].submit();</script>
	';	
	exit();		
}

// выбор города сделан
// теперь скачиваем главную страницу для выбранного города
$result = c_exec($sel2."cta/");	

$result=strstr($result,'<a href="https://post.craigslist.org/');

// находим на странице ссылку на постинг объявления
$tt=explode('"',$result);
$posturl=$tt[1]."/ctd/";
//echo "$sel2"."cta/ - posturl=$posturl<br>";
//exit();

// заходим на эту страницу
$result = c_exec($posturl);	

// и если вдруг нам предложат выбрать еще и местность вокруг города, то придется предоставлять пользователю еще одну форму с выбором
if (strpos($result,"choose the area nearest you")>5)
// значит нужно выбрать еще дополнительный регион
{
	// распарсиваем страницу на ссылки
	$result=strstr($result,'<ul><li>');

	$tt=explode('<a href="',$result);

	$out=array();
	for ($i=0;$i<count($tt);$i++)
	{
		$item=$tt[$i];
		$tt1=explode('"',$item);
		if (substr_count($tt1[0],"/")<1) continue;
		$out[$i]["url"]=$tt1[0];
		//echo $tt1[0];
		$tt1=explode('>',$item);
		$tt1=explode('<',$tt1[1]);
		$out[$i]["title"]=$tt1[0];
		//echo " - ".$tt1[0]."<br>";
	}
	
	if (count($out)<1)
	{
		echo "ERROR in parsing $posturl";
		exit();
	}
	
	// и выводим на экран форму
	echo '<form id="form1" name="form1" method="post" action="step1.php">
	  <label>STEP3 - Select area:<br />
	  <select name="sel3" size="5" id="sel3">
	  ';
	for ($i=0;$i<count($out);$i++)  
		if (strlen($out[$i]["url"])>5)
			echo '<option value="'.$out[$i]["url"].'">'.$out[$i]["title"].'</option>';
	echo '	
	  </select>
	  </label>
	  <input type="hidden" name="sel1" value="'.$sel1.'">
	  <input type="hidden" name="sel2" value="'.$sel2.'">
	  <input type="hidden" name="car_id" value="'.$car_id.'">
	  <input type="hidden" name="posturl" value="'.$posturl.'">
	  <input type="hidden" name="login" value="'.$login.'">
	  <input type="hidden" name="password" value="'.$password.'">
	  <input type="hidden" name="phone" value="'.$phone.'">
	  <input type="hidden" name="email" value="'.$email.'">
	  <br />
	  <label>
	  <input type="submit" name="submit" id="submit" value="Submit" />
	  </label>
	</form>
	';		
	exit();
}
else
// иначе отправляем данные в путь
{
	echo '<form id="form1" name="form1" method="post" action="craigslist.php">
	  <input type="hidden" name="car_id" value="'.$car_id.'">
	  <input type="hidden" name="main_url" value="'.$posturl.'">
	  <input type="hidden" name="login" value="'.$login.'">
	  <input type="hidden" name="password" value="'.$password.'">
	  <input type="hidden" name="phone" value="'.$phone.'">
	  <input type="hidden" name="email" value="'.$email.'">
	  <input type="submit" name="submit" id="submit" value="Submit to Craigslist" />
	</form>
	<script>document.forms["form1"].submit();</script>
	';	
	exit();		
}



	


?>