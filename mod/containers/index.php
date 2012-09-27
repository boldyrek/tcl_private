<?
/*
	Индексная страница раздела контейнеров.
	Ниже выполняется проверка прав доступа пользователя к данному разделу.
	
	Проверка выполняется не по разграниченным правилам из файла lib/access.php,
	а просто путём проверки типа залогиненного пользователя.
	
	Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');	// запрет для группы 2 (Клиенты)
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');	// запрет для группы 11 (Админ. базы Ямато)

// Проверка подраздела и подключение соответствующего файла
if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/containers/class.Containers'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/containers/class.ContainersList.php');

// Проверка подраздела и создание соответствующего класса
switch($_GET['sw']) {
	case 'form':$page = new ContainersForm();break;
	case 'save':$page = new ContainersSave();break;
	case 'add':$page = new ContainersAdd();break;
	case 'delete':$page = new ContainersDelete();break;
	case 'cprint':$page = new ContainersEdit();break;
	case 'print':$page = new ContainersPrint();break;
        case 'download':$page = new ContainersDownload; break;
		
	default:
		$page = new ContainersList();
		break;
}

// Вызов главного оперирующего метода нового класса
$page -> drawContent();

?>