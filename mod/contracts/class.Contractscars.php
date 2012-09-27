<?

class ContractsCars {
	function drawContent() {
		
		if(intval($_GET['client'])>0) {
			require($_SERVER['DOCUMENT_ROOT'].'/mod/contracts/class.Contractsform.php');
		
			$list = new ContractsForm();
			$contract['client'] = intval($_GET['client']);
			echo iconv('WINDOWS-1251', 'UTF-8',$list->getCarList($contract));
		}
		else {

		}
	}
}

?>