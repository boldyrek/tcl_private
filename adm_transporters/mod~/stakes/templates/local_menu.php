<?
$menu = '<script>
	var message;
	var notfilled;
	function checkForm() {
		notfilled = "�� �� ��������� ���� ";
		forma = document.forms.addStakeFrom;
		message = "";
		if(forma.lot.value == \'\') message = message + notfilled + "<� ����>\n";
		if(forma.sum.value == \'\') message = message + notfilled + "<������>\n";
		if(forma.lot_date.value == \'\') message = message + notfilled + "<����>\n";
		if(message!=\'\') alert(message);
		else forma.submit();
	}
</script>
<table width="730" cellpadding="0" cellspacing="0"><tr>
	'.$this->menu.'
	<td style="border-bottom:1px solid #ccc;">&nbsp;</td>
</tr>
</table>';
?>