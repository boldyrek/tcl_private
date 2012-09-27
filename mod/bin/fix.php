<?
//исправление ошибок возникших вследствие нелогичных действий пользователей

if(isset($_GET['what'])) {

	switch($_GET['what']) {
	
	//ошибка с портами автомобиля и поставщиков
	case 'port':
	$port = '';

	$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET `port` = '".$port."' WHERE `id` = '".mysql_real_escape_string($_GET['car'])."'";
	mysql_query($request);
	//SQL LOG
	$_SESSION['queries'] .= "|-|".$request;
	$_SESSION['files'] .= "|-|"."fix";
	//#######
	header('Location: '.$root_path.'?ref=form.car&car_id='.$_GET['car']);
	break;
	
	default:
	header('Location: '.$root_path);
	}

}
else header('Location: '.$root_path);


?>