<?

class diesel {

	private $user = 'makmalNA';		// Forum login
	private $pass = 'rhextdct[';	// Forum password
	private $f = 16;		// Forum number
	private $t = 4423760;   // Topic number
	private $ch;			// cURL handle
	private $content;		// Content of page returned by cURL
	private $key;			// Auth. key to post, delete and edit posts
	private $attachkey;		// Auth. key for attach section of post and edit
	private $TopicTitle;	// Title of new topic
	private $TopicDesc;		// Description of new topic
	private $message;		// New post body
	private $top_message;	// Topic main post bosy
	private $mainpost;		// Topic main post id (would be stored in a base)
	private $photo_attach;	// String containing photo attach string

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
		$link = 'http://diesel.elcat.kg?act=Login&CODE=01';
		$postdata = 'UserName='.$this->user.'&PassWord='.$this->pass;
		$this->curlit($link, $postdata);

        // Getting auth key from our topic
		$link = 'http://diesel.elcat.kg/index.php?showtopic='.$this->t;
        $postdata = '';
		$this->curlit($link, $postdata);
		$this->findKey();

		// Getting attach auth key from our topic
		$link = 'http://diesel.elcat.kg/index.php?act=post&do=reply_post&f='.$this->f.'&t='.$this->t;
		$this->curlit($link, $postdata);
		$this->findAttachKey();

		// Uploading image file
		$link = 'http://diesel.elcat.kg/index.php?&act=attach&code=attach_upload_process&attach_rel_module=post&attach_rel_id=0&attach_post_key='.$this->attachkey.'&forum_id='.$this->f.'&--ff--forum_id='.$this->f;
		$postdata = array(
			'MAX_FILE_SIZE' => '100000000',
			'FILE_UPLOAD' => '@upload_diesel.jpg'
		);
		$tmp_ref = 'http://diesel.elcat.kg/index.php?&act=attach&code=attach_upload_show&attach_rel_module=post&attach_rel_id=&attach_post_key='.$this->attachkey.'&--ff--forum_id='.$this->f;
		$this->curlit($link, $postdata, $tmp_ref);
		$this->findFileData();

		// Posting a new reply
		$this->message = str_replace('[%main_photo%]', $this->photo_attach, $this->message);
		$link = 'http://diesel.elcat.kg/index.php?';
		$postdata = 'f='.$this->f.'&t='.$this->t.'&act=Post&CODE=03&fast_reply_used=1&st=0&auth_key='.$this->key
            .'&Post='.$this->message.'&enablesig=yes&enableemo=no&attach_post_key='.$this->attachkey;
		$this->curlit($link, $postdata);

        // Getting key
		$link = 'http://diesel.elcat.kg/index.php?act=post&do=edit_post&f='.$this->f.'&t='.$this->t.'&p='.$this->mainpost.'&st=0';
		$postdata = '';
		$this->curlit($link, $postdata);
		$this->findKey2();

		// Updating topic main post
		$link = 'http://diesel.elcat.kg/index.php?';
		$postdata = 'act=Post&s=&f='.$this->f.'&auth_key='.$this->key.'&CODE=09&t='.$this->t.'&p='.$this->mainpost.'&st=0'
				.'&Post='.$this->top_message.'&enableemo=yes&enablesig=yes&iconid=11';
		$this->curlit($link, $postdata);

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

	function findAttachKey() {
		$cut = strpos($this->content, 'attach_post_key');
		$this->attachkey = substr($this->content, $cut+24, 32);
	}

	function findFileData() {
		$done = preg_match('/parent\.ipsattach\.add_current_item\( \'(\d+)\', \'(.+?)\'/i', $this->content,$r);
		$this->photo_attach = $done ? '[attachment='.$r[1].':'.$r[2].']' : '';
	}

function curlit($link, $postdata, $referer='') {

	curl_setopt($this->ch, CURLOPT_URL,$link);
	curl_setopt($this->ch, CURLOPT_TIMEOUT, 20);

	curl_setopt($this->ch, CURLOPT_RETURNTRANSFER,1); 
	curl_setopt($this->ch, CURLOPT_COOKIESESSION, true);

	curl_setopt($this->ch, CURLOPT_COOKIEFILE, "cookiefile");
	curl_setopt($this->ch, CURLOPT_COOKIEJAR, "cookiefile");
	curl_setopt($this->ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
	curl_setopt($this->ch, CURLOPT_FOLLOWLOCATION, 1);
	if($referer!='') curl_setopt($this->ch, CURLOPT_REFERER, $referer);

	curl_setopt($this->ch, CURLOPT_POST, 1); 
	curl_setopt($this->ch, CURLOPT_POSTFIELDS, $postdata); 

	$this->content = curl_exec($this->ch);
}
}
?>