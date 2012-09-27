<?php
//require($_SERVER['DOCUMENT_ROOT'].'/inc/config.php');
$pear_path = '/data/ccl/www/pear/PEAR';
ini_set('include_path', $pear_path . PATH_SEPARATOR
        . ini_get('include_path'));

require_once "Spreadsheet/Excel/Writer.php";

$xls =& new Spreadsheet_Excel_Writer();

$xls->send($invoice_name);
$i = 0;

while($i<$totalSheets) {
$units = 0;
$sheet[$i] =& $xls->addWorksheet($sheets[$i]['sheet']);
$sheet[$i]->setColumn(0,0,0);
$sheet[$i]->setColumn(1,1,3);
$sheet[$i]->setColumn(2,2,3);
$sheet[$i]->setColumn(3,3,10);
$sheet[$i]->setColumn(4,4,7);
$sheet[$i]->setColumn(5,5,10);
$sheet[$i]->setColumn(6,6,10);
$sheet[$i]->setColumn(7,7,5);
$sheet[$i]->setColumn(8,8,5);
$sheet[$i]->setColumn(9,9,4);
$sheet[$i]->setColumn(10,10,7);

$sheet[$i]->setRow(22,2);

$sheet[$i]->setRow(8,2);
$sheet[$i]->setRow(6,20);
$sheet[$i]->setRow(7,16);

//заголовок
$bold_16 =& $xls->addFormat();
$bold_16->setBold();
$bold_16->setSize(16);
$bold_16->setItalic();
$bold_16->setFontFamily('Arial Narrow');

//стиль с выравниванием вправо
$a =& $xls->addFormat();
$a->setBold();
$a->setSize(10);
$a->setFontFamily('Arial Narrow');
$a->setAlign('right');

//основной стиль
$s =& $xls->addFormat();
$s->setBold();
$s->setSize(10);
$s->setFontFamily('Arial Narrow');
$s->setAlign('left');
$s->setAlign('top');
$s->setTextWrap();

//стиль для чисел с десятичным окончанием
$decimal =& $xls->addFormat();
$decimal->setBold();
$decimal->setSize(10);
$decimal->setFontFamily('Arial Narrow');
$decimal->setAlign('left');
$decimal->setAlign('top');
$decimal->setTextWrap();
$decimal->setNumFormat('0.000');

//стиль для чисел с десятичным окончанием
$money =& $xls->addFormat();
$money->setBold();
$money->setSize(10);
$money->setFontFamily('Arial Narrow');
$money->setAlign('left');
$money->setAlign('top');
$money->setTextWrap();
$money->setNumFormat('0.00');

//черный фон
$black_bg =& $xls->addFormat();
$black_bg->setBgColor(8);

$sheet[$i]->write(0,3,'INVOICE/PACKING', $bold_16);

//3-й ряд
$sheet[$i]->write(2,1,'SHIPPERS NAME', $s);

//4-й ряд
$sheet[$i]->write(3,1,$invoice['sup_name'], $s);

//5-й ряд
$sheet[$i]->write(4,1,'ADDRESS', $s);
$sheet[$i]->write(2,7,'INV. NO.', $s);
$sheet[$i]->write(2,9,$invoice['number'], $s);

//6-й ряд
$sheet[$i]->write(5,1,$invoice['sup_address'], $s);

//7-й ряд
$sheet[$i]->write(7,1,'TEL', $s);
$sheet[$i]->write(7,2,$invoice['sup_phone'], $s);
$sheet[$i]->write(7,5,'FAX '.$invoice['sup_phone'], $s);
$sheet[$i]->write(7,8,'DATE', $s);
$sheet[$i]->write(7,9,strtoupper($invoice['date']), $s);
//8-й ряд
$sheet[$i]->write(8,1,'', $black_bg);

//9-й ряд
$sheet[$i]->write(10,1,'LOCAL VESSEL', $s);
$sheet[$i]->write(10,4,'CARRIER SYMS', $s);

//10-й ряд
$sheet[$i]->write(11,1,'OCEAN VESSEL', $s);
$sheet[$i]->write(11,4,'FREIGHT COLLECT', $s);

//11-й ряд
$sheet[$i]->write(10,1,'SAILING ON', $s);
$sheet[$i]->write(10,4,'CONTAINER No.:', $s);
$sheet[$i]->write(10,6,$invoice['container_num'], $s);

//12-й ряд
$sheet[$i]->write(11,1,'LOADING PORT', $s);
$sheet[$i]->write(11,4,$invoice['port'], $s);

//13-й ряд
$sheet[$i]->write(12,1,'DISCHARGING PORT', $s);
$sheet[$i]->write(12,4,$invoice['disch_port'], $s);

//15-й ряд
$sheet[$i]->write(14,1,'PAYMENT', $s);
$sheet[$i]->write(14,4,'BY REMITTANCE', $s);

//17-й ряд
$sheet[$i]->write(16,1,'CONSIGNEE', $s);
$sheet[$i]->write(16,4,$sheets[$i]['name_en'], $s);

//18-й ряд
$sheet[$i]->write(17,1,'ADDRESS', $s);
$sheet[$i]->write(17,4,$sheets[$i]['address_en'], $s);

//19-й ряд
$sheet[$i]->write(18,1,'TEL', $s);
$sheet[$i]->write(18,4,$sheets[$i]['contacts'], $s);

//20-й ряд
$sheet[$i]->write(19,1,'NOTIFY PARTY', $s);
$sheet[$i]->write(19,4,'SAME AS CONSIGNEE', $s);

//21-й ряд
$sheet[$i]->write(20,10,'CPT US$', $s);



//22-й ряд
//$sheet[$i]->write(21,0,'MARK', $a);
//$sheet[$i]->write(21,1,'NO.', $a);
$sheet[$i]->write(21,1,'DESCRIPTION OF GOODS', $s);
$sheet[$i]->write(21,5,'CONSIGNEE', $s);
$sheet[$i]->write(21,7,'KGS', $s);
$sheet[$i]->write(21,8,'M3', $s);
$sheet[$i]->write(21,10,'PRICE', $s);

//23-й ряд
$sheet[$i]->write(22,1,'', $black_bg);

//24-й ряд
$sheet[$i]->write(23,1,'USED VEHICLES',$s);
$location = 20;

$total_kgs = 0;
$total_volume = 0;
$total_usd = 0;

$totalCars = count($sheets[$i]['cars']);
foreach($sheets[$i]['cars'] as $k => $car) {
	$location = $location + 5;
	$address_re = str_replace(',', ', ',$car['address_en']);
	//вывод автомибля
	$sheet[$i]->write($location,1,$car['model'],$s);
	$sheet[$i]->write($location,5,$car['name_en'],$s);
	$sheet[$i]->write($location+2,5,$address_re,$s);
	$sheet[$i]->write($location,7,$car['weight'],$s);
	$sheet[$i]->write($location,8,$car['volume'],$decimal);
	$sheet[$i]->write($location,9,'US$',$a);
	$sheet[$i]->write($location,10,$car['invoice'],$money);

	$sheet[$i]->write(($location+1),1,'CHASSIS '.$car['frame'],$s);
	$sheet[$i]->write(($location+2),1,'('.$car['year'].'/'.$car['seats'].'P/'.$car['engine'].'/'.$car['fuel_type'].')',$s);
	 
	$total_kgs = $total_kgs + $car['weight'];
	$total_volume = $total_volume + $car['volume'];
	$total_usd = $total_usd + $car['invoice'];
	$units++;
}
$location = $location + 5;

//30-й ряд
$sheet[$i]->write($location,5,'UNIT(S) ',$a); 
$sheet[$i]->write($location,7,'KGS',$s); 
$sheet[$i]->write($location,8,'M3',$s); 

//31-й ряд
$sheet[$i]->write(($location+1),1,'', $black_bg);

//32-й ряд
$sheet[$i]->write(($location+2),1,'TOTAL:', $s);
$sheet[$i]->write(($location+2),5,$units, $a);
$sheet[$i]->write(($location+2),7,$total_kgs, $s);
$sheet[$i]->write(($location+2),8,$total_volume, $decimal);
$sheet[$i]->write(($location+2),10,$total_usd,$money);

$sheet[$i]->mergeCells(8,1,8,10); 
$sheet[$i]->mergeCells(22,1,22,10); 

$sheet[$i]->mergeCells(2,1,2,5); 
$sheet[$i]->mergeCells(2,7,2,8); 
$sheet[$i]->mergeCells(2,9,2,10); 
$sheet[$i]->mergeCells(3,1,3,6); 
$sheet[$i]->mergeCells(4,1,4,6); 
$sheet[$i]->mergeCells(5,1,5,6); 
$sheet[$i]->mergeCells(6,1,6,6); 
$sheet[$i]->mergeCells(5,1,6,1); 
$sheet[$i]->mergeCells(7,2,7,4); 
$sheet[$i]->mergeCells(7,5,7,6); 
$sheet[$i]->mergeCells(8,4,8,5); 
$sheet[$i]->mergeCells(9,4,9,5); 
$sheet[$i]->mergeCells(10,4,10,5); 
$sheet[$i]->mergeCells(10,6,10,7); 
$sheet[$i]->mergeCells(8,1,8,3); 
$sheet[$i]->mergeCells(9,1,9,3); 
$sheet[$i]->mergeCells(10,1,10,3); 
$sheet[$i]->mergeCells(11,1,11,3); 
$sheet[$i]->mergeCells(12,1,12,3); 
$sheet[$i]->mergeCells(14,1,14,3); 
$sheet[$i]->mergeCells(16,1,16,3); 
$sheet[$i]->mergeCells(17,1,17,3); 
$sheet[$i]->mergeCells(19,1,19,3); 
$sheet[$i]->mergeCells(14,4,14,5); 
$sheet[$i]->mergeCells(16,4,16,10); 
$sheet[$i]->mergeCells(17,4,17,10); 
$sheet[$i]->mergeCells(18,4,18,10); 
$sheet[$i]->mergeCells(19,4,19,10);
$sheet[$i]->mergeCells(7,9,7,10); 
$sheet[$i]->mergeCells(11,4,11,6); 
$sheet[$i]->mergeCells(12,4,12,6); 
$sheet[$i]->mergeCells(21,1,21,4); 
$sheet[$i]->mergeCells(21,5,21,6); 
$sheet[$i]->mergeCells(23,1,23,4); 
$sheet[$i]->mergeCells(5,5,5,6); 

//слияние ячеек для автомибля
$j = 0;	
$start = 25;
$sheet[$i]->setRow(($start-1),3);
while($j<$totalCars) {
	$sheet[$i]->mergeCells($start,1,$start,4); 
	$sheet[$i]->mergeCells($start,5,$start,6); 
	$sheet[$i]->mergeCells($start+1,5,$start+1,6);
	$sheet[$i]->mergeCells($start+2,5,$start+2,6);
	$sheet[$i]->mergeCells($start,5,$start+1,5);
		
	$sheet[$i]->mergeCells(($start+1),1,($start+1),4); 
	$sheet[$i]->mergeCells(($start+2),1,($start+2),4); 
 
	$sheet[$i]->mergeCells(($start+3),5,($start+3),6);
	$sheet[$i]->mergeCells(($start+2),5,($start+3),5);
	$sheet[$i]->setRow(($start+4),7);
	
	$start = $start+5;
	$j++;
}
//черная полоса под списком автомобилей
$sheet[$i]->setRow(($location+1),2);
$sheet[$i]->mergeCells(($location+1),1,($location+1),10); 

// для TOTAL
$sheet[$i]->mergeCells(($location+2),1,($location+2),2);

if($invoice['sup_id']!='') {
	$image = $_SERVER['DOCUMENT_ROOT'].'/img/invoice/'.$invoice['sup_id'].'/stamp.bmp';
	if(file_exists($image)) $sheet[$i]->insertBitmap(($location+5),4,$image);
	
}	

$sheet[$i]->hideGridlines();
$i++;
}

$xls->close();

?>