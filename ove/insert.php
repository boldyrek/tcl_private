<?
//error_reporting(0);
set_time_limit(6000);
header("Expires: Tue, 1 Jul 2003 05:00:00 GMT");
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-Type: text/html; charset=utf-8");
ob_implicit_flush(false);
session_start();


if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='8') header('Location: /adm_transporters');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='9') header('Location: /adm_expeditors');

require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');

$car_id = intval($_GET['car_id']);
class LoadOveForm extends Proto {

	public function drawContent($car_id) {
		if($this->checkAuth()) {
			$sql="SELECT * FROM `ccl_".ACCOUNT_SUFFIX."ove` WHERE car_id='".$car_id."' LIMIT 1";
			return mysql_fetch_array($this->mysqlQuery($sql));
		}else return false;
	}
}
$proto = new LoadOveForm;
$data = $proto->drawContent($car_id);

if($car_id>0 && $data && is_array($data) && isset($_SESSION['authorised']) && $_SESSION['authorised'])
{
	include_once("class.curl.php");
	$form = unserialize($data['serialize']);

	//print_r ($data); die;

	//	$form['vin_code'] = 'JHMRA3863WC001017';

	$content = file_get_contents("page.html");
	echo $content;

	$hostname = 'www.ove.com';

	echo '<script>addContent("Соединяемся с сервером '.$hostname.'")</script>'."\n";

	$c = new curl("https://".$hostname."/authenticate") ;

	echo '<script>addContent("Соединение установлено.")</script>'."\n";

	$c->setopt(CURLOPT_HEADER, 1);
	$c->setopt(CURLOPT_NOBODY, 1);

	$c->setopt(CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.9.0.3) Gecko/2008092417 Firefox/3.0.3');
	$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/login');
	$c->setopt(CURLOPT_HTTPHEADERS,array('Content-Type: application/x-www-form-urlencoded'));
	$c->setopt(CURLOPT_POST, true);

	$c->setopt(CURLOPT_COOKIESESSION, true);
	$c->setopt(CURLOPT_COOKIEFILE, 'cookie.txt');
	$c->setopt(CURLOPT_COOKIEJAR, 'cookie.txt');
	$c->setopt(CURLOPT_COOKIE, session_name() . '=' . session_id());
	$theFieldsLogin = array
	(
	'commit' => 'Login',
	'user' => array('password'=>$form['ove_password'], 'username'=>$form['ove_login'])
	) ;

	$c->setopt(CURLOPT_POSTFIELDS, $c->asPostString($theFieldsLogin)) ;
	$c->setopt(CURLOPT_RETURNTRANSFER, 1);
	$c->setopt(CURLOPT_SSL_VERIFYPEER, 0);
	$c->setopt(CURLOPT_SSL_VERIFYHOST, 0);


	// это необходимо, чтобы cURL не высылал заголовок на ожидание
	$c->setopt(CURLOPT_HTTPHEADER, array('Expect:'));;


	$c->setopt(CURLOPT_VERBOSE, '1');

	$c->setopt(CURLOPT_FOLLOWLOCATION, 0);
	echo '<script>addContent("Запрос на авторизацию.")</script>'."\n";
	$result_auth = $c->exec() ;


	if ($theError = $c->hasError())
	{
		echo '<script>displayError("'.clean($theError).'")</script>'."\n";
		flush();
	}else{
		if(check_error_server($result_auth))die('<script>displayError("500 Internal Server Error!")</script>');
		flush();
		$result_auth = $c->follow_location($result_auth);

		if(preg_match('/(flash error)/',$result_auth))
		{
			preg_match_all('/flash error([^div]+)(.*)<\/div/sU', $result_auth,$out);
			$msg="Error autorization!";
			if(isset($out[2][0]))$msg=clean($out[2][0]);
			die('<script>displayError("'.$msg.'")</script>');
			flush();
		}else{
			echo '<script>addContent("Проверка авторизации прошла успешно.")</script>'."\n";
			flush();
			####################################################################################################################################
			$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/start');
			$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/buy');

			$start = $c->exec();

			if(check_error_server($start))die('<script>displayError("500 Internal Server Error!")</script>');
			flush();



			$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/select_entry_method');
			$c->setopt(CURLOPT_POST, 0);
			$result = $c->exec();

			/*ловим Accound - Id*/
			preg_match_all('#<input.*id="presenter_account_id".*value="([0-9]*)" />#sU', $result, $res);
			if (!isset($res[1][0])){
				die('<script>displayError("Не удалось поймать Account ID")</script>');
			}

			$accountId = $res[1][0];
			echo '<script>addContent("Ваш ID:'.$accountId.'")</script>'."\n";
			flush();



			$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/start');
			$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/buy');

			$start = $c->exec();

			echo '<script>addContent("'.$start.'")</script>'."\n";

			if(check_error_server($start))die('<script>displayError("500 Internal Server Error!")</script>');
			flush();


			####################################################################################################################################
			echo '<script>addContent("Переходим к размещению csv Файла.")</script>'."\n";
			echo '<script>addContent("Отправка файла: '.$_SERVER['DOCUMENT_ROOT'].'/photos/ove/'.$data['csv'].'")</script>'."\n";
			flush();

			$c->setopt(CURLOPT_URL, 'https://'.$hostname.'/listing_wizard/select_entry_method');
			$c->setopt(CURLOPT_REFERER, 'https://'.$hostname.'/listing_wizard/select_entry_method');

			$theFieldsAdd = array(
			'wizard_goto_step_2_button' => urlencode('Continue'),
			'presenter[import_type]'=>urlencode('csv'),
			'presenter[account_id]'=>urlencode($accountId),
			'csv_import[csv_email]'=>$form['ove_email'],
			'csv_import[seller_agreement]'=>urlencode(1),
			'csv_import[csv_data]'=>'@'.$_SERVER['DOCUMENT_ROOT'].'/photos/ove/'.$data['csv']
			);
					
			$c->setopt(CURLOPT_POSTFIELDS, $theFieldsAdd);

			$result_vin = $c->exec();

			

			if(check_error_server($result_vin))die('<script>displayError("500 Internal Server Error!")</script>');
			$result_vin = $c->follow_location($result_vin, 'https://'.$hostname.'/listing_wizard/select_entry_method');

			if (strstr($result_vin, 'Your file has been uploaded')){
				echo '<script>addContent("Отправка прошла успешно!")</script>'."\n";	
			}
			else{
				echo '<script>displayError("Невозможно отправить сейчас. попробуйте позже.")</script>';
				echo '<script>displayError("'.$result_vin.'")</script>';
				
					
			}
		}
	}



	$c->close() ;
	echo '<script>stopProgress()</script>'."\n";
}else {
	echo "<html><body><script>window.close();</script></body></html>"."\n";
}

function clean($text)
{
	$text = strip_tags($text);
	$text = preg_replace('/\'|"/','',$text);
	$text = preg_replace('/(<div)|(<\/)(<p)|([<>])|(div)/','',$text);
	$text = trim($text);
	return $text;
}
function check_error_server($html)
{
	return preg_match('/(Internal Server Error)/i',$html);
}
?>