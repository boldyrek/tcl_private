<?
require('class.php');

$page = new carsForSale();
$page->table = 'ccl_'.ACCOUNT_SUFFIX.'forsale';
$page->default_sort = '`date` DESC';
$page->sort_link = 'mod=sale';
$page->per_page = 10;

$page->request =  "
	SELECT ccl_".ACCOUNT_SUFFIX."forsale.*, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.year, ccl_".ACCOUNT_SUFFIX."cars_cert.file as auc_list 
	FROM `ccl_".ACCOUNT_SUFFIX."forsale` 
	LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
	ON (ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."forsale.car)
	LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars_cert`
	ON (ccl_".ACCOUNT_SUFFIX."cars_cert.car = ccl_".ACCOUNT_SUFFIX."forsale.car)
	WHERE 1 ORDER BY ";

$print.= $page->drawContent();

?>