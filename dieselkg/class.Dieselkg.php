<?

class dieselkg {

	private $user = 'eldagiz@bk.ru';		// Forum login
	private $pass = 'pumpthetire';			// Forum password
	private $f = 30;		// Forum number
	private $t = 51111;		// Topic number
	private $ch;			// cURL handle
	private $content;		// Content of page returned by cURL
	private $key;			// Auth. key to post, delete and edit posts
	private $TopicTitle;	// Title of new topic
	private $TopicDesc;		// Description of new topic
	private $message;		// New post body
	private $top_message;	// Topic main post bosy
	private $mainpost;		// Topic main post id (would be stored in a base)
	
	function __construct() {
		// Unused now
	}
	
	function setMessages($message,$tmessage) {
		$this->message = $message;
		$this->top_message = $tmessage;
	}

	function setTitles($title, $subtitle){
		$this->TopicTitle = $title;
		$this->TopicDesc = $subtitle;
	}

	function setForumNumber($fid,$tid,$pid) {
		$this->f = $fid;
        $this->t = $tid;
		$this->mainpost = $pid;
	}
	
	function process() {
		$this->ch = curl_init(); 
	
		// Logging in
		$link = 'http://diesel.kg/index.php?app=core&module=global&section=login&do=process';
		$postdata = 'username='.$this->user.'&password='.$this->pass.'&anonymous=1';
		$this->curlit($link, $postdata);

		// Getting auth key from our topic
		$link = 'http://diesel.kg/index.php?showtopic='.$this->t;
		$postdata = '';
		$this->curlit($link, $postdata);
		$this->findKey();
		
		// Posting a new reply
		$link = 'http://diesel.kg/index.php?';
		$postdata = 'f='.$this->f.'&t='.$this->t.'&s=&removeattachid=0&MAX_FILE_SIZE=0&app=forums&module=post&section=post&p=0&do=reply_post_do&st=0&auth_key='.$this->key
			.'&Post='.$this->message.'&enablesig=yes&enableemo=no';
		$this->curlit($link, $postdata);
		
//		// Getting key
//		$link = 'http://diesel.kg/index.php?app=forums&module=post&section=post&do=edit_post&f='.$this->f.'&t='.$this->t.'&p='.$this->mainpost.'&st=0';
//		$postdata = '';
//		$this->curlit($link, $postdata);
//		$this->findKey2();
//
//		// Updating topic main post
//		$link = 'http://diesel.kg/index.php?';
//		$postdata = 'act=Post&s=&f='.$this->f.'&auth_key='.$this->key.'&CODE=09&t='.$this->t.'&p='.$this->mainpost.'&st=0'
//				.'&Post='.$this->top_message.'&enableemo=yes&enablesig=yes&iconid=11';
//		$this->curlit($link, $postdata);

		curl_close($this->ch);
	}
	
	function findKey() {
		$cut = strpos($this->content, 'auth_key" value="');
		$this->key = substr($this->content, $cut+17, 32);
	}

	function findKey2() {
		$cut = strpos($this->content, 'auth_key');
		$this->key = substr($this->content, $cut+17, 32);
	}
	
function curlit($link, $postdata) {

	curl_setopt($this->ch, CURLOPT_URL,$link);
	curl_setopt($this->ch, CURLOPT_TIMEOUT, 20);
		
	curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
	
	curl_setopt($this->ch, CURLOPT_COOKIEFILE, "cookiefile");
	curl_setopt($this->ch, CURLOPT_COOKIEJAR, "cookiefile");
	curl_setopt($this->ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
	curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);

	curl_setopt($this->ch, CURLOPT_POST, 1); 
	curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postdata);

    curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.6) Gecko/20100625 Firefox/3.6.6');
	
	$this->content = curl_exec($this->ch);
}
}
?>