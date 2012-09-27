<?
/*
	Индексная страница раздела клиентов.
	Ниже выполняется проверка прав доступа пользователя к данному разделу.
	
	Проверка выполняется не по разграниченным правилам из файла lib/access.php,
	а просто путём проверки типа залогиненного пользователя.
	
	Необходимо переписать метод разграничения прав пользователей согласно файлу lib/access.php
*/
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') header('Location: /public');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');
if(isset($_SESSION['user_type']) and $_SESSION['user_type']!='1') header('Location: /public');

if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/expenses/class.Expenses'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/expenses/class.ExpensesList.php');

switch($_GET['sw']) {
	case 'form':$page = new ExpensesForm();break;
	case 'save':$page = new ExpensesSave();break;
	case 'delete':$page = new ExpensesDelete();break;
	case 'add':$page = new ExpensesAdd();break;
	case 'cars':$page = new CarsList();break;
	case 'sign':$page = new SignExpense();break;
		
	default:
		$page = new ExpensesList();
		break;
}

$page -> drawContent();

?>