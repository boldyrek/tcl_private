<?


$out = '<html xmlns="http://www.w3.org/TR/REC-html40">
<head>
<meta http-equiv=Content-Type content="text/html; charset=utf-8">
<title>АКТ</title>
<style>
<!--
p.MsoBodyText, li.MsoBodyText, div.MsoBodyText
	{text-align:justify;
	text-autospace:none;
	font-size:12.0pt;
	font-family:"Times New Roman";}
p.MsoBodyText2, li.MsoBodyText2, div.MsoBodyText2
	{text-align:justify;
	font-size:10.0pt;
	font-family:"Courier New";}
p.MsoAcetate, li.MsoAcetate, div.MsoAcetate
	{font-size:8.0pt;
	font-family:Tahoma;}
span.SpellE
ol
	{ul
	{-->
</style>
</head>
<body bgcolor="#FFFFFF" lang=RU>
<div class=Section1>
  <p class=MsoBodyText align=center style="text-align:center;text-autospace:ideograph-numeric ideograph-other" ><b><span style=\'font-family:"Courier New";\'>АКТ</span></b></p>
  <p class=MsoBodyText align=center style="text-align:center;text-autospace:ideograph-numeric ideograph-other"><b><span style=\'font-family:"Courier New";\'>Приема оказанных услуг</span></b></p>
  <p class=MsoBodyText style="text-autospace:ideograph-numeric ideograph-other"><span
style=\'font-family:"Courier New";\'>Г. Бишкек                        «'.$this_day.'» '.$this_month.'
    <st1:metricconverter
ProductID="20'.$this_year.' г" w:st="on">
    20'.$this_year.' г.</span></p>
  <br>
  <br>
  <br>
  <br>
  <p class=MsoBodyText2 style="text-indent:50px;"><span style="font-size:12.0pt">Предприниматель <span class=SpellE>Болдырев</span> Василий Александрович, действующий на
    основании Свидетельства о государственной регистрации физического лица,
    занимающегося предпринимательской деятельностью серии БГР, №26419, от
   30.06.2004 г., с одной стороны и </span><span style=\'font-size:13.0pt;
font-family:"Courier New;"\'>'.$content['name'].'</span><span
style="font-size:12.0pt">, паспорт '.$content['passport'].',
    с другой стороны, составили настоящий Акт приема-передачи автомобиля.</span></p>
  <ol><li><p class=MsoBodyText2><span style="font-size:12.0pt">Услуги,
    предусмотренные Агентским договором №'.$content['number'].' от «'.$contract_date[2].'» '.$month[ltrim($contract_date[1],'0')].' '.$contract_date[0].' г., выполнены,
    Агентом в полном объеме, удовлетворяющим Принципала, в соответствии с
    условиями  данного договора.</span></p></li>
  <li><p class=MsoBodyText2><span style="font-size:12.0pt">Стороны  претензий друг к другу, по исполнению
    вышеуказанного Агентского договора, в том числе по денежным расчетам между
    сторонами и третьими лицами не имеют.</span></p></li>
    </ol>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <br>
  <p class=MsoBodyText2><b><span
style="font-size:12.0pt">Агент                                 Принципал</span></b></p>
  <p class=MsoBodyText2><span class=SpellE><span style="font-size:12.0pt">Болдырев</span></span><span
style="font-size:12.0pt"> В.А.                  </span><span style=\'font-size:13.0pt;font-family:"Courier New"\'>'.$content['name'].'</span></p>
  <p class=MsoBodyText2><span style="font-size:12.0pt">_________________                ____________________</span></p>
</div>
</body>
</html>
';

?>