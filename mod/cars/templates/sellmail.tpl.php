<?
//заголовок сообещения
$SELLMAILtitle="Новая машина на продажу";

// текст сообщения
$SELLMAILtxt=<<<hdok
модель : {$carinfo["model"]}<br>
год  : {$carinfo["year"]}<br>
объем двигателя : {$carinfo["engine"]}<br>
пробег: {$carinfo["milage"]}<br>
цена : {$carinfo["endprice"]}<br>
комментарий к продаже : {$carinfo["endcomment"]}<br>
<a href="http://makmalauto.com/cars_for_sale/?mode=max&id={$new_id}">Фотографии авто можете посмотреть здесь</a>
<br><br>
<b>По вопросам приобретения авто звоните +996 (312) 592 - 592</b>
<br><br><hr>
<br>
<small> Если вы не хотите дальше получать рассылку от Макмала то отправьте письмо на
<a href="mailto:dmitrii@makmalauto.com">dmitrii@makmalauto.com</a> и в теме письма укажите <i>Отказ</i>.
hdok;
?>