<?
/*
	Индексная страница раздела аукционов.
	Ниже выполняется проверка прав доступа пользователя к данному разделу.
	
	Проверка выполняется не по разграниченным правилам из файла lib/access.php,
	а просто путём проверки типа залогиненного пользователя.
	
	Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');

if(isset($_GET['sw'])) $switch = $_GET['sw'];
else $switch = '';

if(Proto::exists($switch)) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/auctions/class.auctions'.$switch.'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/auctions/class.auctionslist.php');

switch($switch) {
	case 'form':
		$page = new auctionsform();
		break;
	case 'save':
		$page = new auctionssave();
		break;
	case 'add':
		$page = new auctionsadd();
		break;	
	case 'delete':
		$page = new auctionsdelete();
		break;	
			
	default:
		$page = new auctionsList();
		break;
}

$page -> drawContent();

?>