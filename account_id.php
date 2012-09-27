<?php
if(isset($_SESSION['acc_id'])){
	define('ACCOUNT_ID', intval($_SESSION['acc_id']));
	if (ACCOUNT_ID===0){
		define('ACCOUNT_SUFFIX', '');
	}
	else{
		define('ACCOUNT_SUFFIX', ACCOUNT_ID.'_');
	}
}
?>