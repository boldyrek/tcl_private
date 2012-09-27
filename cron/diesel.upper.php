<?php
/* Move's Up diesel.elcat.kg Makmalauto topic
 */
error_reporting(E_ALL ^ E_WARNING);
//ini_set('open_basedir', '');

$user = 'makmalNA';
$pass = 'rhextdct[';
$ch = curl_init();

function curlit($link, $postdata, $referer = '') {
    global $ch;
    curl_setopt($ch, CURLOPT_URL,$link);
	curl_setopt($ch, CURLOPT_TIMEOUT, 20);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_COOKIESESSION, true);
	curl_setopt($ch, CURLOPT_COOKIEFILE, "cookiefile");
	curl_setopt($ch, CURLOPT_COOKIEJAR, "cookiefile");
	curl_setopt($ch, CURLOPT_COOKIE, session_name() . '=' . session_id());
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
    if ($referer != '') curl_setopt($ch, CURLOPT_REFERER, $referer);
    return curl_exec($ch);
}
function findKey($content) {
	$cut = strpos($content, 'auth_key" value="');
	$key = substr($content, $cut + 17, 32);
	return $key;
}

require_once '../inc/baseconf.php';

$conn = mysql_connect($db_host, $db_user, $db_pass);
mysql_select_db($db_base);

$r = mysql_fetch_assoc(mysql_query("SELECT * FROM ccl_posting WHERE `id`=1"));
$forum = $r['forumid'];
$topic = $r['topicid'];
$lasttime = $r['lastupdate'];
$message = '
[i]up it![/i]';

if(file_exists("./cookiefile")) unlink("./cookiefile");

// Logging in
$postdata = 'UserName='.$user.'&PassWord='.$pass;
$link = 'http://diesel.elcat.kg?act=Login&CODE=01';
$content = curlit($link, $postdata);

// Getting page to get key
$postdata = '';
$link = 'http://diesel.elcat.kg/index.php?showtopic='.$topic;
$data = curlit($link, $postdata);
$key = findKey($data);
echo "<br>The key is: <span style='color:green'>$key</span><br><br>";

// Posting message
$link = 'http://diesel.elcat.kg/index.php?';
$postdata = 'f=' . $forum . '&t=' . $topic . '&act=Post&CODE=03&fast_reply_used=1&st=0&auth_key=' . $key
	. '&Post=' . $message . '&enablesig=yes&enableemo=no';
$data = curlit($link, $postdata);

// Getting topic posts list
$postdata = '';
$link = 'http://diesel.elcat.kg/index.php?showtopic='.$topic;
$data = curlit($link, $postdata);

// Getting link to delete
preg_match_all('/javascript\:delete_post\(\'(.*?)\'\)/', $data, $matches);

$dellink = $matches[1][count($matches[1])-1];

echo "<br><br>$dellink";

$postdata = '';
// Hopefully this will delete last message :)
$data = curlit($dellink, $postdata, 'http://diesel.elcat.kg/index.php?showtopic='.$topic);
file_put_contents('diesel_debug_4.htm', $data);
if(strstr($data, 'Сообщение удалено'))
	echo 'Message\'ve been deleted';
?>
