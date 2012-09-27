<?
set_time_limit(60);
class rePost extends Proto {
	
	private $title;
	private $subtitle;
	private $text;
	private $top_text;
	private $forum;
	private $topic;
	private $mainpost;
	private $main_id;
	private $sale_id;
    private $conv;

		
	public function setId($item, $main) {
		$this->main_id = $main;	
		$this->sale_id = $item;	
	}
	
	function process($type) {
		if(intval($this->main_id)!=0 and intval($this->sale_id)!=0) {
			switch ($type) {
				case 'car': $this->car();
				break;
				
				case 'stuff': $this->stuff();
				break;
			}
		}
		else echo 'rePost failed!';
	}
	
	private function car() {
		$saleInfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."forsale` WHERE `id` = '".intval($this->sale_id)."'"));
		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($this->main_id)."'"));
//		$this->title = 'Продаю '.$carinfo['model'];

        // Including charset-convertor class
        require_once($_SERVER['DOCUMENT_ROOT'].'/lib_ext/ConvertCharset.class.php');
        $this->conv = new ConvertCharset("utf-8", "windows-1251", false);

		// New post message body
        $this->text = '[b]'.$carinfo['model'].'[/b]
        '.($saleInfo['price']!=0 ? ' цена: [b][i]'.$saleInfo['price'].'[/i] $[/b]' : '').'
		'.($carinfo['milage']!=0 ? ' пробег: [b][i]'.$carinfo['milage'].'[/i][/b]' : '').'

		'.str_replace('&', ' and ',$saleInfo['comment']).'

		[%main_photo%]

		[size="1"]По вопросам приобретения звоните [b]996-312-592-592[/b], либо пишите [b][i]dmitrii@makmalauto.com[/i][/b][/size] ';
		// [url="http://www.makmalauto.com/re.php?id='.intval($this->sale_id).'"][color="#0000FF"]Фотографии на сайте www.MakmalAuto.com.[/color][/url]
        $this->text = $this->conv->Convert($this->text);  // Converting to cp1251

        // Topic main post update
        $this->top_text = '[font="Georgia"][size=7][b][color="#FF0000"]"Макмал-Авто"[/color][/b][/size]

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
            $this->top_text .= '[font="Georgia"]
[b][color="#0000FF"]'.$t['model'].'[/color][/b] [цена: [color="#FF0000"][b]'.$r['price'].'$[/b][/color]] [size=1]- '.substr($r['comment'],0,120).'...[/size]
[/font]
';
        }
        $this->top_text = $this->conv->Convert($this->top_text);  // Converting to cp1251

		$this->title = $this->conv->Convert('Автомобили от компании "Макмал-Авто"');  // Converting to cp1251
		$this->subtitle = $this->conv->Convert('ассортимент обновляется');  // Converting to cp1251
		
        $this->forum = 16;
		$this->topic = '4423760';
		$this->mainpost = '14534852';

		//Preparing photo to upload
		if($carinfo['top_photo']!='0')	// Getting tagged or first photo filename
			$myimg = mysql_fetch_array(mysql_query("SELECT file FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE id = ".intval($carinfo['top_photo'])." LIMIT 1"));
		else
			$myimg = mysql_fetch_array(mysql_query("SELECT file FROM `ccl_".ACCOUNT_SUFFIX."cars_photos` WHERE car = ".intval($this->main_id)." ORDER BY id ASC LIMIT 1"));

		if(file_exists('upload_diesel.jpg')) unlink ('upload_diesel.jpg');	// deleting old file
		copy('photos/'.$this->main_id.'/'.$myimg['file'], 'upload_diesel.jpg');	// copying new file

		$this->makePost();

	}
	
	private function stuff() {
//		$saleInfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff_forsale` WHERE `id` = '".intval($this->sale_id)."'"));
//		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff` WHERE `id` = '".intval($this->main_id)."'"));
//		$this->title = 'Продаю '.$carinfo['name'];
//		$this->subtitle = 'цена: '.$saleInfo['price'].'$';
//
//		$this->text = 'Продаю
//		[b]'.$carinfo['name'].'[/b]
//		цена: '.$saleInfo['price'].'$
//
//		'.str_replace('&', ' and ',$saleInfo['comment']).'
//
//		[url="http://www.makmalauto.com/go.php?id='.intval($this->sale_id).'"]Фотографии на сайте www.MakmalAuto.com.[/url]
//
//		По вопросам приобретения звоните 996-312-902-600, либо пишите dmitrii@makmalauto.com ';
//
//		$this->forum = 13;
//
//
//		$this->makePost();

	}
	
	function makePost() {
		require($_SERVER['DOCUMENT_ROOT'].'/diesel/class.Diesel.php');	
		$obj = new diesel();
		$obj->setForumNumber($this->forum,  $this->topic, $this->mainpost);
		$obj->setMessages($this->text,$this->top_text);
		$obj->setTitles($this->title,$this->subtitle);
		$obj->process();
		
		echo 'New reply added!';
	}
}

?>