<?
require_once($include_path.'bin/balance.php');

class addPayment {

var $customerID;
var $pay_date;
var $pay_client;
var $pay_comment;
var $pay_amount;

// рисуем форму добавления платежа	
function makeForm() {
	$this->getData();
	return '
	<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
	<script src="'.$root_path.'js/datepicker.js"></script>
	<form action="/?mod=clients&sw=detail&id='.$this->customerID.'&addpayment" method="post" style="margin:0px">
	<table class="list" width="96%" style="border:0px;margin:5px;">
	<tr class="rowA">
		<td class="title">сумма:</td>
		<td class="title"><input type="text" name="amount" size="10"></td>
		<td class="title">дата:</td>
		<td class="title" nowrap><input type="text" name="date" id="payDate" style="width:80px;" value="'.date('Y-m-d').'">
    	<img src="'.$root_path.'img/ccl/cal.gif" border=0 onClick="show_calendar(\'payDate\', \'\', myDateFormat);" class="datePicker" id="datePicker" style="margin:0px;margin-bottom:-3px;z-index:199;"></td>
    </tr>
	<tr class="rowB">
		<td>примечание:</td>
		<td colspan="3"><input type="text" name="comment" size="40"></td>
    </tr>
	<tr>
		<td>&nbsp;<input type="hidden" name="cust_id" value="'.$this->customerID.'"></td>
	  	<td>&nbsp;</td>
	  	<td colspan="2"><input name="submit" type="submit" id="save" value="добавить"></td>
    </tr>
    </table>

	</form>';
	
}

// сохраняем внесенные данные
function saveData() {
	$this->getData();
	if($_SESSION['user_type']=='1' or $_SESSION['user_type']=='7') $status = '1';
	else $status = '0';
	$request = "INSERT INTO `ccl_".ACCOUNT_SUFFIX."accounting` (`id`, `client`, `amount`, `comment`, `date`, `user_added`, `status`, `last_edited`,`type`)
	VALUES (LAST_INSERT_ID(),'".$this->pay_client."','".$this->pay_amount."','".strtoupper($this->pay_comment)."','".$this->pay_date."',
	'".$_SESSION['login_id']."', '".$status."', NOW(), '1')";
	
	mysql_query($request);
	updateBalance($this->customerID, 0);
	

}

// получаем служебные данные для формы
function getData() {
		$this->customerID = intval($_GET['id']);
		$this->pay_client = intval($_POST['cust_id']);
		$this->pay_amount = intval($_POST['amount']);
		$this->pay_date = mysql_real_escape_string($_POST['date']);
		$this->pay_comment = mysql_real_escape_string($_POST['comment']);
}

function process() {
	
	if(isset($_POST['cust_id'])) {
		
		$this->saveData();
		return true;
	}
	else return false;
}

}

?>