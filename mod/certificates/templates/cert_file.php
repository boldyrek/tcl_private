<?

$cert_file = '
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251" />
<title>СВИДЕТЕЛЬСТВО О РЕГИСТРАЦИИ</title>
<style>
p,div,td,body {
	font-size:10pt;
	font-family:Arial, Helvetica, sans-serif;
}
.bold_title {
	font-size:11px; font-weight:bold;
}
.bigTable td { 
	background-color:#ffffff;
	padding:1px;
	border-left:1px solid #000;
	border-top:1px solid #000;
}
.tableWidth {
	width:940px;
}	
.noBrdr td {
	border:0px;
}
.rightBrdr {
	border-right:1px solid #000;
}
</style>
</head>

<body>
<table cellspacing="0" cellpadding="0" class="bigTable tableWidth noBrdr" border="0" >
  <tbody>
    <tr height="31">
      <td valign="bottom" nowrap="nowrap" width="39" height="31">&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" width="39" height="31">&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" width="103" height="31" align="right">'.$cert['cert_reg_number'].'</td>
      <td valign="bottom" nowrap="nowrap" colspan="4" height="31"><p><strong> № СВИДЕТЕЛЬСТВА О РЕГИСТРАЦИИ</strong></p></td>
	  <td valign="bottom" nowrap="nowrap" height="31">&nbsp;</td>
    </tr>
    <tr height="18">
      <td valign="bottom" nowrap="nowrap" height="18">&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" height="18">&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" height="18">&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" colspan="2" height="18"><p>УЧЕТНЫЙ НОМЕР '.$cert['uchet_nomer'].'</p></td>
      <td valign="bottom" nowrap="nowrap" colspan="9" height="18">&nbsp;</td>
      <td width="67" height="18" valign="bottom" nowrap="nowrap">&nbsp;</td>
    </tr></tbody>
	</table>

	<table cellspacing="0" cellpadding="0" class="bigTable" border="0" >
  <tbody>
    <tr>
      <td colspan="2" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>РЕГИСТРАЦИОННЫЙ <br />
        НОМЕР</strong></p></td>
      <td width="135" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>ДАТА СНЯТИЯ   С УЧЕТА</strong></p></td>
      <td valign="bottom" nowrap="nowrap" colspan="2"><p class="bold_title"><strong>ДАТА   ВЫПУСКА</strong></p></td>
      <td colspan="7" valign="bottom" nowrap="nowrap" class="rightBrdr"><p class="bold_title"><strong>НОМЕР   КУЗОВА</strong></p></td>
      </tr>
    <tr height="35">
      <td height="35" colspan="2" valign="bottom" nowrap="nowrap">'.$cert['car_reg_number'].'&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" height="35">'.$cert['reg_cancel_date'].'&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" colspan="2" height="35">'.$cert['export_date'].'&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" colspan="7" height="35" class="rightBrdr">'.$car['frame'].'&nbsp;</td>
      </tr>
    <tr>
      <td colspan="5" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>НАЗВАНИЕ   АВТОМОБИЛЯ</strong></p></td>
      <td valign="bottom" nowrap="nowrap" colspan="3"><p class="bold_title"><strong>МОДЕЛЬ   КУЗОВА </strong></p></td>
      <td colspan="4" valign="bottom" nowrap="nowrap" class="rightBrdr"><p class="bold_title"><strong>МОДЕЛЬ ДВИГАТЕЛЯ</strong></p></td>
      </tr>
    <tr height="33">
      <td height="33" colspan="5" valign="bottom" nowrap="nowrap">'.$car['model'].'&nbsp;</td>
      <td height="33" colspan="3" valign="bottom" nowrap="nowrap">'.$cert['frame_model'].'&nbsp;</td>
      <td height="33" colspan="4" valign="bottom" nowrap="nowrap" class="rightBrdr">'.$cert['engine_model'].'&nbsp;</td>
      </tr>
    <tr height="33">
      <td height="33" colspan="2" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>ИМЯ   ВЛАДЕЛЬЦА</strong></p></td>
      <td height="33" colspan="10" valign="bottom" nowrap="nowrap" class="rightBrdr">'.$cert['supplier_name'].'</td>
      </tr>
    <tr height="33">
      <td height="29" colspan="2" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>АДРЕС   ВЛАДЕЛЬЦА</strong></p></td>
      <td valign="bottom" nowrap="nowrap" colspan="10" height="29" class="rightBrdr">'.$cert['supplier_address'].'</td>
      </tr>
    <tr height="33">
      <td height="33" colspan="2" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>ИМЯ   ПОЛЬЗОВАТЕЛЯ</strong></p></td>
      <td height="33" colspan="10" valign="bottom" nowrap="nowrap" class="rightBrdr"><p>***</p></td>
      </tr>
    <tr height="33">
      <td height="33" colspan="2" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>АДРЕС   ПОЛЬЗОВАТЕЛЯ</strong></p></td>
      <td height="33" colspan="10" valign="bottom" nowrap="nowrap" class="rightBrdr"><p>***</p></td>
      </tr>
    <tr height="33">
      <td height="33" colspan="2" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>МЕСТОНАХОЖДЕНИЕ АВТОМОБИЛЯ</strong></p></td>
      <td height="33" colspan="10" valign="bottom" nowrap="nowrap" class="rightBrdr"><p>***</p></td>
      </tr>
    <tr height="15">
      <td nowrap="nowrap" height="15"><p class="bold_title"><strong>ТИП КУЗОВА</strong></p></td>
      <td height="15" colspan="-1" nowrap="nowrap"><p class="bold_title"><strong>КЛАССИФИКАЦИЯ</strong></p></td>
      <td nowrap="nowrap" height="15"><p class="bold_title"><strong>  ВИД   ЭКСПЛУАТАЦИИ</strong></p></td>
      <td nowrap="nowrap" colspan="2" height="15"><p class="bold_title"><strong>  ВИД   АВТОМОБИЛЯ</strong></p></td>
      <td nowrap="nowrap" height="15"><p class="bold_title"><strong>КОЛИЧЕСТВО<br>МЕСТ</strong></p></td>
      <td nowrap="nowrap" colspan="2" height="15"><p class="bold_title"><strong>ГРУЗО-<br>ПОДЪЕМНОСТЬ</strong></p></td>
      <td height="15" colspan="2" align="left" nowrap="nowrap"><p class="bold_title"><strong>ВЕС<br>АВТОМОБИЛЯ</strong></p></td>
      <td nowrap="nowrap" colspan="2" height="15" class="rightBrdr"><p class="bold_title"><strong>СНАРЯЖЕННАЯ  <br>МАССА</strong></p></td>
    </tr>
    <tr height="33">
      <td height="33" valign="bottom" nowrap="nowrap">&nbsp;</td>
      <td height="33" colspan="-1" valign="bottom" nowrap="nowrap"><p>'.$cert['classification'].'</p>&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" height="33"><p>'.$cert['exp_type'].'</p>&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" colspan="2" height="33"><p>'.$cert['car_type'].'</p>&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" height="33">&nbsp;</td>
      <td height="33" colspan="2" valign="bottom" nowrap="nowrap">&nbsp;</td>
      <td height="33" colspan="2" valign="bottom" nowrap="nowrap">'.$car['weight'].' KG</p></td>
      <td height="33" colspan="2" valign="bottom" nowrap="nowrap" class="rightBrdr">'.$cert['car_brutto'].' KG</td>
      </tr>
    <tr height="15">
      <td height="15" colspan="2" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>ОБЪЕМ   ДВИГАТЕЛЯ</strong></p></td>
      <td valign="bottom" nowrap="nowrap" height="15"><p class="bold_title"><strong>ВИД ТОПЛИВА </strong></p></td>
      <td width="65" height="15" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>№   ФОРМЫ</strong></p></td>
      <td width="59" height="15" valign="bottom" nowrap="nowrap"><p class="bold_title"><strong> №КЛАС.</strong></p></td>
      <td valign="bottom" nowrap="nowrap" height="15"><p class="bold_title"><strong>ДЛИНА</strong></p></td>
      <td valign="bottom" nowrap="nowrap" height="15"><p class="bold_title"><strong>ШИРИНА</strong></p></td>
      <td valign="bottom" nowrap="nowrap" height="15"><p class="bold_title"><strong>ВЫСОТА</strong></p></td>
      <td valign="bottom" nowrap="nowrap" height="15"><p class="bold_title"><strong>НАГР. НА   <br />
        ПЕР.<br /> 
        МОСТ</strong></p></td>
      <td valign="bottom" nowrap="nowrap" height="15">&nbsp;</td>
      <td valign="bottom" nowrap="nowrap" height="15"><p class="bold_title"><strong>НАГР. НА<br>ЗАДН.<br>МОСТ</strong></p></td>
      <td valign="bottom" nowrap="nowrap" height="15" class="rightBrdr">&nbsp;</td>
    </tr>
    <tr height="33">
      <td height="33" colspan="2" align="right" valign="bottom" nowrap="nowrap"><p>'.$car['engine'].'СМ3</p></td>
      <td valign="bottom" nowrap="nowrap" height="33"><p>БЕНЗИН</p></td>
      <td valign="bottom" nowrap="nowrap" height="33">'.$cert['form_number'].'</td>
      <td valign="bottom" nowrap="nowrap" height="33">'.$cert['class_number'].'</td>
      <td valign="bottom" nowrap="nowrap" height="33">'.$cert['car_length'].'СМ</td>
      <td valign="bottom" nowrap="nowrap" height="33">'.$cert['car_width'].'СМ</td>
      <td valign="bottom" nowrap="nowrap" height="33">'.$cert['car_height'].'СМ</td>
      <td valign="bottom" nowrap="nowrap" height="33">'.$cert['front_weight'].'КГ</p></td>
      <td valign="bottom" nowrap="nowrap" height="33"><p align="right">KG</p></td>
      <td valign="bottom" nowrap="nowrap" height="33">'.$cert['back_weight'].' КГ</td>
      <td valign="bottom" nowrap="nowrap" height="33" class="rightBrdr">KG</td>
    </tr>
    <tr>
      <td valign="bottom" nowrap="nowrap"><p class="bold_title"><strong>ДАТА   ИСТЕЧЕНИЯ<br />
      </strong><strong>СРОКА</strong></p></td>
      <td valign="bottom" nowrap="nowrap">'.$cert['term_date'].'&nbsp;</td>
      <td colspan="10" valign="bottom" nowrap="nowrap" class="rightBrdr">&nbsp;</td>
      </tr>
    
    <tr>
      <td colspan="5" valign="bottom" nowrap="nowrap" style="border-bottom:1px solid #000;"><p class="bold_title"><strong>ЗАМЕТКИ,   ПРИМЕЧАНИЯ</strong></p>
        <p class="bold_title">&nbsp;</p>
        <p class="bold_title">&nbsp;</p>
        <p class="bold_title">&nbsp;</p>
       </td>
      <td valign="bottom" nowrap="nowrap" colspan="7" class="rightBrdr" style="border-bottom:1px solid #000;">&nbsp;</td>
      </tr>
  </tbody>
</table>
 
<div style="font-size:10px;padding-left:250px;">
<img src="/img/ccl/gmc.gif" align="left" style="margin-top:-10px;">&copy; Перевод с японского языка на русский язык организован <br> 
 Центром языковых переводов  “GMC Translation Service”<br> 
 Тел.: + 996 (312) 900-666  Факс: + 996 (312) 69-60-80</div>
 

</body>
</html>
';

?>