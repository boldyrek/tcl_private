<?
error_reporting(E_ALL);
set_time_limit(6000);
header("Expires: Tue, 1 Jul 2003 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=utf-8");
ob_implicit_flush();
session_start();
if(!function_exists('gzdecode'))
{
	function gzdecode($data){
		$g=tempnam('/tmp','ff');
		@file_put_contents($g,$data);
		ob_start();
		readgzfile($g);
		$d=ob_get_clean();
		return $d;
	}
}

include_once("class.curl.php");


$hostname = 'www.ove.com';

$c = new curl("https://".$hostname) ;
$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/authenticate');
$c->setopt(CURLOPT_HEADER, 1);
$c->setopt(CURLOPT_NOBODY, 1);

$c->setopt(CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/login');
//$c->setopt(CURLOPT_HTTPHEADERS,array('Content-Type: application/x-www-form-urlencoded'));
$c->setopt(CURLOPT_POST, true);

$c->setopt(CURLOPT_COOKIESESSION, true);
$c->setopt(CURLOPT_COOKIEFILE, 'cookie.txt');
$c->setopt(CURLOPT_COOKIEJAR, 'cookie.txt');
$c->setopt(CURLOPT_COOKIE, session_name() . '=' . session_id());
$theFieldsLogin = array
(
'commit' => 'Login',
'user' => array('password'=>'proof09', 'username'=>'makmal09')
) ;

$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($theFieldsLogin)) ;
$c->setopt(CURLOPT_RETURNTRANSFER, 1);
$c->setopt(CURLOPT_SSL_VERIFYPEER, 0);
$c->setopt(CURLOPT_SSL_VERIFYHOST, 0);


// это необходимо, чтобы cURL не высылал заголовок на ожидание
$c->setopt(CURLOPT_HTTPHEADER, array('Expect:'));


$c->setopt(CURLOPT_VERBOSE, '1');

//$c->setopt(CURLOPT_FOLLOWLOCATION, 0);
$result_auth = $c->exec();

if ($theError = $c->hasError())
{
	echo $theError;
}else{

	if ($result_auth = $c->follow_location())
	{
		echo gzdecode($result_auth);
	}else{
		echo gzdecode($result_auth);
	}
}