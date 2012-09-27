<?

class diesel {

	private $user = 'ghost';
	private $pass = '123';
	public $f = 6; // номер форума // private
	private $ch;
	private $content;
	private $key;
	private $TopicTitle;
	private $TopicDesc;
	private $message;
	public $forum_root;
	public $new_topic_id;
	
	function __construct() {
		//set_time_limit(10);
		// UserName PassWord
		// new topic url = http://diesel.elcat.kg/index.php?act=post&do=new_post&f=22
		// TopicTitle
		// TopicDesc
		// Post
		// f - номер форума
		// t - номер топика
	}
	
	function getData($TopicTitle, $TopicDesc, $message) {
		$this->TopicDesc = $TopicDesc;
		$fletter = substr($TopicTitle, 0, 1);
		$this->TopicTitle = $fletter.strtolower(substr($TopicTitle, 1, strlen($TopicTitle))); // меняем все буквы на маленькие, первую оставляем как есть
		$this->message = $message;

	}

	function setForumNumber($id) {
		$this->f = $id;
	}
	
	function process() {
		$this->ch = curl_init(); 
	
		$postdata = 'UserName='.$this->user.'&PassWord='.$this->pass;
		$link = $this->forum_root.'?act=Login&CODE=01';
		$this->curlit($link, $postdata);
//		echo 'login> ';

		$link = $this->forum_root.'?act=post&do=new_post&f='.$this->f;
		$this->curlit($link, $postdata);
		$this->findKey();
//		echo $this->content;
		$this->content='';
		
		$link = $this->forum_root.'?';
		$postdata = 'f='.$this->f.'&Post='.$this->message.'&s=&removeattachid=0&MAX_FILE_SIZE=0&TopicTitle='.$this->TopicTitle.'&TopicDesc='.$this->TopicDesc.'&CODE=01&act=Post&st=0&fast_reply_used=1&auth_key='.$this->key;
		
		$this->curlit($link, $postdata);
		
//		file_put_contents("_tmp_.htm",$this->content);
		
		$this->findTopicId();

		curl_close($this->ch);
	}
	
	function findKey() {
		$cut = strpos($this->content, 'auth_key');
		$this->key = substr($this->content, $cut+17, 32);
	}
	
	function findTopicId() {
//		if(file_put_contents("_tmp_.htm",$this->content)==0) echo "Out-Failed  ";
		preg_match("/act\=post\&amp\;do\=reply_post\&amp\;f\=.*\&amp\;t=([0-9]*)\"/i",$this->content,$t);
		$this->new_topic_id=$t[1];
//		echo sizeof($this->content);
	}
	
function curlit($link, $postdata) {
	curl_setopt($this->ch, CURLOPT_URL,$link);
	curl_setopt($this->ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($this->ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($this->ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($this->ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
	curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($this->ch, CURLOPT_POST, 1);
	curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postdata);

	$this->content = curl_exec($this->ch);
/*	if (curl_errno($creq)) {
		print curl_error($creq);
	}*/
//	echo $this->content.'<br>--------------------------<br>';
}
}
?>