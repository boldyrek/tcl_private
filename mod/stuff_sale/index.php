<?

require('class.php');

$page = new stuffForSale();
$page->table = 'ccl_".ACCOUNT_SUFFIX."stuff_forsale';
$page->default_sort = '`date` DESC';
$page->sort_link = 'mod=stuff_sale';
$page->per_page = 10;

$page->request =  "
	SELECT ccl_".ACCOUNT_SUFFIX."stuff_forsale.*, ccl_".ACCOUNT_SUFFIX."stuff.name, ccl_".ACCOUNT_SUFFIX."stuff.count, ccl_".ACCOUNT_SUFFIX."stuff.sold as stuff_sold
	FROM `ccl_".ACCOUNT_SUFFIX."stuff_forsale` 
	LEFT JOIN `ccl_".ACCOUNT_SUFFIX."stuff`
	ON (ccl_".ACCOUNT_SUFFIX."stuff.id = ccl_".ACCOUNT_SUFFIX."stuff_forsale.stuff)
	WHERE 1 ORDER BY ";
/*$page->request =  "
	SELECT ccl_stuff_forsale.*, ccl_stuff.name, ccl_stuff.mount, ccl_stuff_cert.file as auc_list 
	FROM `ccl_stuff_forsale` 
	LEFT JOIN `ccl_stuff`
	ON (ccl_stuff.id = ccl_stuff_forsale.car)
	LEFT JOIN `ccl_stuff_cert`
	ON (ccl_stuff_cert.car = ccl_stuff_forsale.car)
	WHERE 1 ORDER BY ";*/

$print.= $page->drawContent();

?>