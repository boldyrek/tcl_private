<?

class kazforum {

	private $user = 'makmajito';		// Forum login
	private $pass = 'makmal123';	// Forum password
	private $f = 96;		// Forum number
	private $t = 349911;   // Topic number
	private $ch;			// cURL handle
	private $content;		// Content of page returned by cURL
	private $key;			// Auth. key to post, delete and edit posts
	private $TopicTitle;	// Title of new topic
	private $TopicDesc;		// Description of new topic
	private $message;		// New post body
	private $top_message;	// Topic main post body
	private $mainpost;		// Topic main post id (would be stored in a base)
	private $session;		// Current (remote) PHP session id
	
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

		// Clearing previous cookies file
		file_put_contents('cookie.txt','');

		// Loading page to get session_id
		$link = 'http://bb.ct.com.kz/index.php?app=core&module=global&section=login';
		$this->findKey2();
		$this->curlit($link);
		file_put_contents('bb-ct-kz-before-login.htm', $this->content);

		// Logging in
		$link = 'http://bb.ct.com.kz/index.php?app=core&module=global&section=login&do=process';
		$postdata = 'username='.$this->user.'&password='.'&rememberMe=0'.
				$this->pass.'&referer=http://bb.ct.com.kz/index.php?'.'&auth_key='.$this->key.'&auth_key='.$this->key;
		$this->curlit($link, $postdata);
		file_put_contents('bb-ct-kz-after-login.htm', $this->content);

		// Getting auth key from our topic
		$link = 'http://bb.ct.com.kz/index.php?showtopic='.$this->t;
        $postdata = '';
		$this->curlit($link, $postdata);
		$this->findKey();

		file_put_contents('kolesa-kz-getkey-dump.htm', $this->content);
		echo $this->key.' ';

		
		// Posting a new reply
		$link = 'http://bb.ct.com.kz/index.php?';
//		$postdata = 'f='.$this->f.'&t='.$this->t.'&act=Post&CODE=03&fast_reply_used=1&st=0&auth_key='.$this->key
//            .'&Post='.$this->message.'&enablesig=yes&enableemo=no';
		$postdata = 'f='.$this->f.'&t='.$this->t.'&s=&removeattachid=0&MAX_FILE_SIZE=0&app=forums&module=post&section=post&p=0&do=reply_post_do&st=0&auth_key='.$this->key
			.'&Post='.$this->message.'&enablesig=yes&enableemo=no';

		$this->curlit($link, $postdata);
		//file_put_contents('bb-ct-kz-after-posting_reply.htm', $this->content);
		
//		// Getting key
//		$link = 'http://bb.ct.com.kz/index.php?act=post&do=edit_post&f='.$this->f.'&t='.$this->t.'&p='.$this->mainpost.'&st=0';
//		$postdata = '';
//		$this->curlit($link, $postdata);
//		$this->findKey2();
//
//		// Updating topic main post
//		$link = 'http://bb.ct.com.kz/index.php?';
//		$postdata = 'act=Post&s=&f='.$this->f.'&auth_key='.$this->key.'&CODE=09&t='.$this->t.'&p='.$this->mainpost.'&st=0'
//				.'&Post='.$this->top_message.'&enableemo=yes&enablesig=yes&iconid=3';
//		$this->curlit($link, $postdata);

		curl_close($this->ch);
	}

	function getSession() {
		$cookies = file_get_contents('cookie.txt');
		$cut = strpos($cookies, 'session_id');
		$this->session = substr($cookies, $cut+11, 31);
		echo $this->session."<br>\n";
	}

	function findKey() {
		$cut = strpos($this->content, 'auth_key" value="');
		$this->key = substr($this->content, $cut+17, 32);
	}

	function findKey2() {
		$cut = strpos($this->content, 'auth_key');
		$this->key = substr($this->content, $cut+17, 32);
	}
	

function curlit($link, $postdata='', $referer='') {
	curl_setopt($this->ch, CURLOPT_URL,$link);
	curl_setopt($this->ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($this->ch, CURLOPT_COOKIEFILE, "cookie.txt");
	curl_setopt($this->ch, CURLOPT_COOKIEJAR, "cookie.txt");
	curl_setopt($this->ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
	curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($this->ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.2.12) Gecko/20101026 (build 03757) Firefox/3.6.12');
	if($postdata!=''){
		curl_setopt($this->ch, CURLOPT_POST, 1);
		curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postdata);
	}
	if($referer!='')
		curl_setopt($this->ch, CURLOPT_REFERER, $referer);

	$this->content = curl_exec($this->ch);
}
}
?>