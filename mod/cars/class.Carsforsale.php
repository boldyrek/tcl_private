<?

class CarsForSale extends Proto {

	private $conv;

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
			$this->cleanSaleList();
		}
				
		$this->errorsPublisher();
		$this->publish();
	}
	
	private function Process() {
		// Including charset-convertor class
		require_once($_SERVER['DOCUMENT_ROOT'].'/lib_ext/ConvertCharset.class.php');
		$this->conv = new ConvertCharset("utf-8", "windows-1251", false);

		$spamit=false;
		$diesel = false;
		$dieselkg = false;
		$kazforum = false;
		$queue_posting = false;
		
		if(isset($_POST['sell']) and $_POST['sell']=='on' and $_POST['sell_id']==0) {

                   $comment = mysql_real_escape_string(strip_tags($_POST['sellComment']));

			$sql = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."forsale`
			(`car`, `price`, `comment`, `comment_admin`, `date`, `active_through`, `sold`)
			VALUES ( 
			'".intval($_GET['car_id'])."', 
			'".intval($_POST['sellPrice'])."', 
			'".$comment."',
                        '".$comment."',
			'".date('Y-m-d')."',
			'".mysql_real_escape_string($_POST['post_till'])."',
			'".($_POST['sold']=='on'?'1':'0')."')";
			$spamit=true;
			$diesel = true;
			$dieselkg = true;
			$kazforum = true;
			$queue_posting = true;
			
			
		} elseif ($_POST['sell_id']!=0 and $_POST['sell']=='on') {
			if($_POST['sold']=='on')
				$time_to_del = date('Y-m-d', (time()+60*60*24*14));
			else
				$time_to_del = '0000-00-00';

                        $comment = mysql_real_escape_string(strip_tags($_POST['sellComment']));
			
			$sql = "UPDATE `ccl_".ACCOUNT_SUFFIX."forsale`
			SET `price`='".intval($_POST['sellPrice'])."',
			`comment` = '".$comment."',
                        `comment_admin` = '".$comment."',
			`sold` = '".($_POST['sold']=='on'?'1':'0')."',
			`active_through` = '".$time_to_del."'
			WHERE id='".intval($_POST['sell_id'])."'";
		} 
		else {
			$sql = "DELETE 
			FROM `ccl_".ACCOUNT_SUFFIX."forsale` 
			WHERE id='".intval($_POST['sell_id'])."'";
		}
		
 		$this->mysqlQuery($sql);
 		$new_id = mysql_insert_id();
 		if($diesel) $this->diesel($new_id);
		if($dieselkg) $this->dieselkg($new_id);
		//if($kazforum) $this->bb_ct_kz($new_id);
		
		// Inserting posting job to queue
		if($queue_posting){
			$current_date = "'".date('Y-m-d H:i:s')."'";
			// Queue to "bb.ct.com.kz"
			$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."posting_queue` (carid,parent,forum,posted,date) VALUES (".$new_id.",".$_GET['car_id'].",'bbctkz',0,$current_date)");
			// Queue to "Webcars.kg"
			$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."posting_queue` (carid,forum,posted,date) VALUES (".$_GET['car_id'].",'webcars',0,$current_date)");
			// Queue to "kolesa.kz"
			$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."posting_queue` (carid,forum,posted,date) VALUES (".$new_id.",'kolesakz',0,$current_date)");
		}
		
 		if($spamit) {
 			$spamcar = "SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE id=".intval($_GET['car_id']);
 			$spamcar = $this->mysqlQuery($spamcar);
 			$carinfo=mysql_fetch_assoc($spamcar);
 			$carinfo["endprice"]=intval($_POST['sellPrice']);
 			$carinfo["endcomment"]=$_POST['sellComment'];
 			
 			$spamadr = "SELECT name, email FROM `ccl_".ACCOUNT_SUFFIX."customers`	WHERE allowspam=1";
 			$spamadr = $this->mysqlQuery($spamadr);
 			$txt="";
			while($l=mysql_fetch_row($spamadr)) {
				if($l[1] && eregi("^[[:alnum:]][a-z0-9_.-]*@[a-z0-9.-]+\.[a-z]{2,4}$",$l[1])) {
 					require_once 'templates/sellmail.tpl.php';
					$this->XMail(
						"MakmalAuto.com <info@makmalauto.com>",
						"",
						$SELLMAILtitle,
						$SELLMAILtxt);
				}
			}
		}

		$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_GET['car_id']));
	}
	
	// Auto-posting a car to diesel.elcat.kg
	private function diesel($new_id) {
		$saleInfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."forsale` WHERE `id` = '".intval($new_id)."'"));
		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car_id'])."'"));
//		$title = 'Продаю '.$carinfo['model'];
//		$subtitle = intval($_POST['sellPrice']) ? 'цена: '.intval($_POST['sellPrice']).'$' : '';
		
		// Including charset-convertor class
        require_once($_SERVER['DOCUMENT_ROOT'].'/lib_ext/ConvertCharset.class.php');
        $conv = new ConvertCharset("utf-8", "windows-1251", false);

		$top_text = '[font="Georgia"][size=7][b][color="#FF0000"]"Макмал-Авто"[/color][/b][/size]

[i]Автомобили из США и Канады уже давно завоевали сердца автолюбителей по всему миру,
теперь оценить качество, мощь и надежность этих автомобилей можно и у нас.

Мы предлагаем Вам комплекс услуг по покупке и доставке
новых и подержанных автомобилей с аукционов США и Канады.

Мы надеемся, что Вы тоже станете нашим клиентом и будете с благодарностью вспоминать нас каждый раз,
садясь за руль приобретенного с нашей помощью легкового автомобиля, микроавтобуса или внедорожника![/i]

Посетите наш сайт [url="http://www.makmalauto.com"]www.makmalauto.com[/url]

[b]Наш адрес:[/b]
[i]Кыргызская Республика,
г. Бишкек, мр-н Юг-2
д.22, кв. 30
+996 (312) 592 592[/i]

----------------------------------------------------------------

[b]Автомобили на продажу:[/b]
[/font]
';
        $tobuy_list = $this->mysqlQuery("SELECT car, price, comment FROM `ccl_".ACCOUNT_SUFFIX."forsale` ORDER BY `id` DESC LIMIT 10");
        while($r = mysql_fetch_assoc($tobuy_list)){
            $t = mysql_fetch_assoc($this->mysqlQuery("SELECT model FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($r['car'])."'"));
            $top_text .= '[font="Georgia"]
[b][color="#0000FF"]'.$t['model'].'[/color][/b] [цена: [color="#FF0000"][b]'.$r['price'].'$[/b][/color]] [size=1]- '.substr($r['comment'],0,120).'...[/size]
[/font]
';
        }
        $top_text = $conv->Convert($top_text);  // Converting to cp1251

		$title = $conv->Convert('Автомобили от компании "Макмал-Авто"');  // Converting to cp1251
		$subtitle = $conv->Convert('ассортимент будет обновляться :)');  // Converting to cp1251

		// Generating reply post message
		$text = '[b]'.$carinfo['model'].'[/b]
        '.($saleInfo['price']!=0 ? ' цена: [b][i]'.$saleInfo['price'].'[/i] $[/b]' : '').'
		'.($carinfo['milage']!=0 ? ' пробег: [b][i]'.$carinfo['milage'].'[/i][/b]' : '').'

		'.str_replace('&', ' and ',$saleInfo['comment']).'

		[%main_photo%]

		[size="1"]По вопросам приобретения звоните [b]996-312-592-592[/b], либо пишите [b][i]dmitrii@makmalauto.com[/i][/b][/size] ';

        $text = $conv->Convert($text);  // Converting to cp1251

		//Preparing photo to upload
		if($carinfo['top_photo']!='0')	// Getting tagged or first photo filename
			$myimg = mysql_fetch_array(mysql_query("SELECT file FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE id = ".intval($carinfo['top_photo'])." LIMIT 1"));
		else
			$myimg = mysql_fetch_array(mysql_query("SELECT file FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE car = ".intval($_GET['car_id'])." ORDER BY id ASC LIMIT 1"));

		if(file_exists('upload_diesel.jpg')) unlink ('upload_diesel.jpg');	// deleting old file
		copy('photos/'.intval($_GET['car_id']).'/'.$myimg['file'], 'upload_diesel.jpg');	// copying new file


		require($_SERVER['DOCUMENT_ROOT'].'/diesel/class.Diesel.php');		
		
		$obj = new diesel();
		$obj->setForumNumber('16', '4423760', '14534852');
		$obj->setMessages($text,$top_text);
		$obj->setTitles($title,$subtitle);
		$obj->process();
	}

	// Auto-posting a car to diesel.kg
	private function dieselkg($new_id) {
		$saleInfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."forsale` WHERE `id` = '".intval($new_id)."'"));
		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car_id'])."'"));
//		$title = 'Продаю '.$carinfo['model'];
//		$subtitle = intval($_POST['sellPrice']) ? 'цена: '.intval($_POST['sellPrice']).'$' : '';

		// Generating List of photos
		$photos_list = '';
		$photo_files = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_cars_photos`
			WHERE `car` = '".intval($_GET['car_id'])."' ORDER BY `folder` DESC, `id` ASC LIMIT 4");

		while($line=mysql_fetch_array($photo_files))
		{
			$photos_list .= '[img]http://tcl.makmalauto.com/photos/'.intval($_GET['car_id']).'/'.$line['file'].'[/img] ';
		}

		// Including charset-convertor class
        require_once($_SERVER['DOCUMENT_ROOT'].'/lib_ext/ConvertCharset.class.php');
        $conv = new ConvertCharset("utf-8", "windows-1251", false);

		$top_text = '[font="Georgia"][size=7][b][color="#FF0000"]"Макмал-Авто"[/color][/b][/size]

[i]Автомобили из США и Канады уже давно завоевали сердца автолюбителей по всему миру,
теперь оценить качество, мощь и надежность этих автомобилей можно и у нас.

Мы предлагаем Вам комплекс услуг по покупке и доставке
новых и подержанных автомобилей с аукционов США и Канады.

Мы надеемся, что Вы тоже станете нашим клиентом и будете с благодарностью вспоминать нас каждый раз,
садясь за руль приобретенного с нашей помощью легкового автомобиля, микроавтобуса или внедорожника![/i]

Посетите наш сайт [url="http://www.makmalauto.com"]www.makmalauto.com[/url]

[b]Наш адрес:[/b]
[i]Кыргызская Республика,
г. Бишкек, мр-н Юг-2
д.22, кв. 30
+996 (312) 592 592[/i]

----------------------------------------------------------------

[b]Автомобили на продажу:[/b]
[/font]
';
        $tobuy_list = $this->mysqlQuery("SELECT car, price, comment FROM `ccl_".ACCOUNT_SUFFIX."forsale` ORDER BY `id` DESC LIMIT 10");
        while($r = mysql_fetch_assoc($tobuy_list)){
            $t = mysql_fetch_assoc($this->mysqlQuery("SELECT model FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($r['car'])."'"));
            $top_text .= '[font="Georgia"]
[b][color="#0000FF"]'.$t['model'].'[/color][/b] [цена: [color="#FF0000"][b]'.$r['price'].'$[/b][/color]] [size=1]- '.substr($r['comment'],0,120).'...[/size]
[/font]
';
        }
        $top_text = $conv->Convert($top_text);  // Converting to cp1251

		$title = $conv->Convert('Автомобили от компании "Макмал-Авто"');  // Converting to cp1251
		$subtitle = $conv->Convert('ассортимент будет обновляться :)');  // Converting to cp1251

		// Generating reply post message
		$text = '[b]'.$carinfo['model'].'[/b]
        '.($saleInfo['price']!=0 ? ' цена: [b][i]'.$saleInfo['price'].'[/i] $[/b]' : '').'
		'.($carinfo['milage']!=0 ? ' пробег: [b][i]'.$carinfo['milage'].'[/i][/b]' : '').'

		'.str_replace('&', ' and ',$saleInfo['comment']).'

		'.$photos_list.'

		[size="1"]По вопросам приобретения звоните [b]996-312-592-592[/b], либо пишите [b][i]dmitrii@makmalauto.com[/i][/b][/size] ';

//        $text = $conv->Convert($text);  // Converting to cp1251
// No need to convert. diesel.kg is in utf-8 encoding already

		require($_SERVER['DOCUMENT_ROOT'].'/dieselkg/class.Dieselkg.php');

		$obj = new dieselkg();
		$obj->setForumNumber('30', '51111', '447361');
		$obj->setMessages($text,$top_text);
		$obj->setTitles($title,$subtitle);
		$obj->process();
	}

	// Auto-posting a car to bb.ct.kz
	private function bb_ct_kz($new_id) {
		
		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($_GET['car_id'])."'"));
		$title = $carinfo['model'];
		$subtitle = intval($_POST['sellPrice']) ? 'цена: '.intval($_POST['sellPrice']).'$' : '';
		
		$text = 'Продаю 
		[b]'.$carinfo['model'].'[/b]
		'.$subtitle.'
		'.($carinfo['milage']!=0?' пробег: '.$carinfo['milage']:'').'
		
		'.mysql_real_escape_string($_POST['sellComment']).'
				 
		[url="http://www.makmalauto.com/re.php?id='.intval($new_id).'"]Фотографии и дополнительная информация на сайте www.MakmalAuto.com.[/url]
		
		По вопросам приобретения звоните:
		Андрей
		телефон:   3283369
		мобильный: +77772301118
		
		либо пишите dmitrii@makmalauto.com  ';
		//echo $text;
		require($_SERVER['DOCUMENT_ROOT'].'/bb.ct.kz/class.Kaz.php');		
		
		$obj = new kazforum();
				
		$obj->getData($title, $subtitle, $text);
		$obj->process();
	}
	
	private function XMail($from, $to, $subj, $text) {
		return $this->sMail($to, $text, $subj, $from);
	}
	
	private function cleanSaleList() {
		$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."forsale` WHERE `active_through` < '".date('Y-m-d')."' and `active_through` != '0000-00-00'");
	}
}
?>