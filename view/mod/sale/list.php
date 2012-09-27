<?

require($include_path.'inc/config.php');
if(!isset($_GET['export'])) {
	require($include_path.'mod/sale/class.php');
	$page = new carsForSale();
	$page->table = 'ccl_'.ACCOUNT_SUFFIX.'forsale';
	$page->default_sort = '`date` DESC';
	$page->sort_link = 'mod=sale';
	$page->per_page = 10;
	$page->root_path = $root_path;
	$page->request =  "SELECT ccl_".ACCOUNT_SUFFIX."forsale.*, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.year, ccl_".ACCOUNT_SUFFIX."cars_cert.file as auc_list 
		FROM `ccl_".ACCOUNT_SUFFIX."forsale` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
		ON (ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."forsale.car)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars_cert`
		ON (ccl_".ACCOUNT_SUFFIX."cars_cert.car = ccl_".ACCOUNT_SUFFIX."forsale.car)
		WHERE 1 ORDER BY ";

	$print.= $page->publish();
}
elseif(isset($_GET['export']) and isset($_GET['mode'])) {
	require($include_path.'mod/sale/export.php');
	if($_GET['mode']=='list') {
		$template = '
		[%photo%]<br>
		<a href="?mod=sale&export&mode=max&id=[%id%]">[%model%]</a><br>
		[%price%]<br>
		[%year%]<hr>';
		$content = getContent("SELECT ccl_".ACCOUNT_SUFFIX."forsale.*, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.year, ccl_".ACCOUNT_SUFFIX."cars_cert.file as auc_list 
		FROM `ccl_".ACCOUNT_SUFFIX."forsale` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars`
		ON (ccl_".ACCOUNT_SUFFIX."cars.id = ccl_".ACCOUNT_SUFFIX."forsale.car)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."cars_cert`
		ON (ccl_".ACCOUNT_SUFFIX."cars_cert.car = ccl_".ACCOUNT_SUFFIX."forsale.car)
		WHERE 1 ORDER BY ccl_".ACCOUNT_SUFFIX."forsale.date DESC");
		echo printList($content);
	}
	elseif (isset($_GET['id'])) {
		$item_template_max = '<a href="?mod=sale&export&mode=list">другие предложения</a><br>
		[%model%]<br>
		[%year%]<br>
		[%price%]<br>
		[%info%]<br>
		[%auc_list%]<br>
		';
		$item_template_min = '<br>
		<a href="/ru/sale/?mode=max&id=[%id%]">[%model%]</a><br>
		[%year%]<br>
		[%price%]<br>

		';
		echo printItem(intval($_GET['id']), $_GET['mode']);
	}
	$print = '<div style="display:none;">';
}



?>