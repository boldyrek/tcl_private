<?
$menu = '<script>
	var message;
	var notfilled;
	function checkForm() {
		notfilled = "Вы не заполнили поле ";
		forma = document.forms.addStakeFrom;
		message = "";
		if(forma.lot.value == \'\') message = message + notfilled + "<№ лота>\n";
		if(forma.sum.value == \'\') message = message + notfilled + "<Ставка>\n";
		if(forma.lot_date.value == \'\') message = message + notfilled + "<День>\n";
		if(message!=\'\') alert(message);
		else forma.submit();
	}
	
	function reloadPage() {
		document.location=\'/?mod=stakes\';
	}
	window.onload = setTimeout(\'reloadPage()\', 120000);
</script>
<table width="730" cellpadding="0" cellspacing="0"><tr>
	'.$this->menu.'
	<td style="border-bottom:1px solid #ccc;">&nbsp;</td>
</tr>
</table>';
?>