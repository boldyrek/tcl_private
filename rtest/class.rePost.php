<?
set_time_limit(60);
class rePost extends Proto {
	
	private $title;
	private $subtitle;
	private $text;
	private $forum;
	private $main_id;
	private $sale_id;
	private $price;

		
	public function setId($item, $main) {
		$this->main_id = $main;	
		$this->sale_id = $item;	
	}
	
	public function setPrice($price) {
		$this->price = $price;
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
		header("Content-type: text/html; charset=utf-8");
		$saleInfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."forsale` WHERE `id` = '".intval($this->sale_id)."'"));
		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."cars` WHERE `id` = '".intval($this->main_id)."'"));
		$this->title = $carinfo['model'];
		$this->subtitle = 'цена: '.$this->price.'$';	//$saleInfo['price']
		
		$this->text = 'Продаю 
		[b]'.$carinfo['model'].'[/b]
		цена: '.$this->price.'$
		'.($carinfo['milage']!=0?' пробег: '.$carinfo['milage']:'').'
		
		'.str_replace('&', ' and ',$saleInfo['comment']).'
				 
		[url="http://www.makmalauto.com/re.php?id='.intval($this->sale_id).'"]Фотографии и подробная информация на сайте www.MakmalAuto.com.[/url]
		
		По вопросам приобретения звоните 996-312-902-600, либо пишите dmitrii@makmalauto.com ';

// =--------------------=-=-=-
// Photos addition
/*		$photos = "\n\n[b]Фотографии:[/b]\n";
		$this->files = $this->mysqlQuery(
			"SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."cars_photos`
			WHERE `car` = '".$this->main_id."' ORDER BY `folder` DESC, `id` DESC LIMIT 5");

		while($line=mysql_fetch_array($this->files))
		{
			$photos .= '[img]http://makmalauto.com/photos/'.$this->main_id.'/'.$line['file'].'[/img] ';
		}

		$this->text .= $photos;*/
// =-=-=-==-==-=-=-=-=-=-=-=-=-=-=-=-

		$this->forum = 6;
		
		$this->makePost();

	}
	
	private function stuff() {
		$saleInfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff_forsale` WHERE `id` = '".intval($this->sale_id)."'"));
		$carinfo = mysql_fetch_array($this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stuff` WHERE `id` = '".intval($this->main_id)."'"));
		$this->title = 'Продаю '.$carinfo['name'];
		$this->subtitle = 'цена: '.$saleInfo['price'].'$';
		
		$this->text = 'Продаю 
		[b]'.$carinfo['name'].'[/b]
		цена: '.$saleInfo['price'].'$

		'.str_replace('&', ' and ',$saleInfo['comment']).'
				 
		[url="http://www.makmalauto.com/go.php?id='.intval($this->sale_id).'"]Фотографии на сайте www.MakmalAuto.com.[/url]
		
		По вопросам приобретения звоните 996-312-902-600, либо пишите dmitrii@makmalauto.com ';
		$this->forum = 6;
		$this->makePost();
	}
	
	function makePost() {
		require($_SERVER['DOCUMENT_ROOT'].'/rtest/class.Test.php');	
		$obj = new diesel();
		$obj->setForumNumber($this->forum);
		$obj->getData($this->title, $this->subtitle, $this->text);
		$obj->forum_root='http://www.fsa.kg/ipb/index.php';
		//http://92.245.99.230/ipb/index.php
		$obj->process();
		
//		echo 'rePost done!';
		echo "Done! - <a href=\"".$obj->forum_root.'?showforum='.$obj->f."\" target=\"_blank\">Forum</a>";
//		<a href=\"".$obj->forum_root.'?showtopic='.$obj->new_topic_id."\" target=\"_blank\">Link</a>";
	}
}

?>