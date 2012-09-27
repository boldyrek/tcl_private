<?
/*
	Индексная страница раздела резервирования данных.
	Ниже выполняется проверка прав доступа пользователя к данному разделу.
	
	Проверка выполняется не по разграниченным правилам из файла lib/access.php,
	а просто путём проверки типа залогиненного пользователя.
	
	Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/

if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');

// Проверка подраздела и подключение соответствующего файла
if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/backup/class.Backup'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/backup/class.BackupList.php');

// Проверка подраздела и создание соответствующего класса
switch($_GET['sw']) {
	case 'create':
		$page = new BackupCreate();
		break;
	case 'load':
		$page = new BackupLoad();
		break;
	default:
		$page = new BackupList();
		break;
}

// Вызов главного оперирующего метода нового класса
$page -> drawContent();

?>