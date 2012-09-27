<?
/*
Индексная страница раздела клиентов.
Ниже выполняется проверка прав доступа пользователя к данному разделу.

Проверка выполняется не по разграниченным правилам из файла lib/access.php,
а просто путём проверки типа залогиненного пользователя.

Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');

if(Proto::exists($_GET['sw'])) {
    $file = $_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.Cars'.$_GET['sw'].'.php';
    if(file_exists($file)) require_once($file);
    else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.CarsList.php');

switch($_GET['sw']) {
    case 'form':
        $page = new CarsForm();
        break;
    case 'oveform':
        $page = new oveForm();
        break;
    case 'saveove':
        $page = new CarsSaveove();
        break;
    case 'save':
        $page = new CarsSave();
        break;
    case 'add':
        $page = new CarsAdd();
        break;
    case 'delete':
        $page = new CarsDelete();
        break;
    case 'sell':
        $page = new CarsSell();
        break;
    case 'forsale':
        $page = new CarsForSale();
        break;
    case 'comment':
        $page = new CarsComment();
        break;
    case 'settopphoto':
        $page = new setTopPhoto();
        break;
    case 'allphotos':
        $page = new Carsallphotos();
        break;
    case 'webcars':
        $page = new Webcars();
        break;

    case 'repair':
        $page = new Repair();
        break;

    case 'repairsave':
        $page = new Repairsave();
        break;


    case 'webcarssave':

        $page = new Webcarssave();
        break;

    case 'transporter':
        $page = new Transporter;
        break;
    case 'place2':
        $page = new Place2;
        break;

    default:

        $page = new CarsList();
        break;
}

$page -> drawContent();

?>