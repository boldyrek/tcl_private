<?


//перевод чисел в текстовую форму, английская и русская версии
function num2strRU($number) {
//числа от 1 до 9
$first['1'] = 'один';
$first['2'] = 'два';
$first['3'] = 'три';
$first['4'] = 'четыре';
$first['5'] = 'пять';
$first['6'] = 'шесть';
$first['7'] = 'семь';
$first['8'] = 'восемь';
$first['9'] = 'девять';

//числа от 10 до 19
$second['10'] = 'десять';
$second['11'] = 'одиннадцать';
$second['12'] = 'двенадцать';
$second['13'] = 'тринадцать';
$second['14'] = 'четырнадцать';
$second['15'] = 'пятнадцать';
$second['16'] = 'шестнадцать';
$second['17'] = 'семнадцать';
$second['18'] = 'восемнадцать';
$second['19'] = 'девятнадцать';

//числа от 20 до 100
$deci['2'] = 'двадцать';
$deci['3'] = 'тридцать';
$deci['4'] = 'сорок';
$deci['5'] = 'пятьдесят';
$deci['6'] = 'шестьдесят';
$deci['7'] = 'семдесят';
$deci['8'] = 'восемьдесят';
$deci['9'] = 'девяносто';

//числа от 200 до 900
$hund['1'] = 'сто';
$hund['2'] = 'двести';
$hund['3'] = 'триста';
$hund['4'] = 'четыреста';
$hund['5'] = 'пятьсот';
$hund['6'] = 'шестьсот';
$hund['7'] = 'семьсот';
$hund['8'] = 'восемьсот';
$hund['9'] = 'девятьсот';

//числа от 1000 до 100.000
$thou['1'] = 'тысяча';
$thou['2'] = 'тысячи';
$thou['3'] = 'тысяч';

//от 1 до 999 миллионов
$mil['1'] = 'миллион';
$mil['2'] = 'миллиона';
$mil['3'] = 'миллиона';
$mil['4'] = 'миллиона';
$mil['5'] = 'миллионов';
$mil['6'] = 'миллионов';
$mil['7'] = 'миллионов';
$mil['8'] = 'миллионов';
$mil['9'] = 'миллионов';


if($number<100000000)
	{
	$str = $number;
	$p = 1;
	$digs = strlen($str);
	//шаг первый (единицы)
	while($p<=$digs) {
	switch($p) {
	case 1:	
	if($digs>1) {
		//проверка 10 - 19
		if(substr($str, -2, 1)!=0) {
		$chk = substr($str, -2, 1)*10 + substr($str, -1, 1);
		if($chk >=10 and $chk <=19) {
			$strResult .= $second[$chk].'';
		}
		elseif(substr($chk,0,1)>=2) //больше либо равно 20
		{
			$strResult .= $deci[substr($chk,0,1)].' ';
			if(substr($str, -1, 1)!=0)
			{
				$strResult .= $first[substr($str, -1, 1)];
				$strResult .= lastDigs(substr($str, -1, 1));
			}
			else $strResult .= '';
		}
		}
		elseif(substr($str,-1,1)!=0)
		{
			$strResult .= $first[substr($str, -1, 1)];
			$strResult .= lastDigs(substr($str, -1, 1));
		}
		else $strResult .= '';
	}
	else {
	$strResult .= $first[substr($str, -1, 1)];
	$strResult .= lastDigs(substr($str, -1, 1));
	}
		$p = 3;
		break;
	//сотни
	case 3:
		if(substr($str, -3, 1)!=0) {
			$strResult = $hund[substr($str, -3, 1)].' '.$strResult;
		}
		$p = 4;
		break;
	//тысячи
	case 4:
		if($digs>4) $nextDig = substr($str,-5,1);
		else $nextDig = 0;
		if(substr($str,-4,1)!=0 and $nextDig!=1) $strResult = thouSwitch(substr($str,-4,1), $thou, $first).$strResult;
		$p = 5;
		break;
	case 5:
		if(substr($str,-5,1)==1) $thnd_end = 'тысяч ';
		elseif($thnd_end == '' and substr($str,-4,1)==0) $thnd_end = 'тысяч ';
		else $thnd_end = '';
		
		if(substr($str,-5,1)==1) $strResult = $second[substr($str,-5,1).substr($str,-4,1)].' '.$thnd_end.$strResult;

		elseif(substr($str,-5,1)!=0) $strResult = $deci[substr($str,-5,1)].' '.$thnd_end.$strResult;

		$p = 6;
			break;
	//сотни тысяч
	case 6:
		if(substr($str,-5,1)==0 and substr($str,-4,1)==0 and substr($str, -6, 1)!=0) $thnd_end = 'тысяч ';
		
		else $thnd_end = '';
		$strResult = $hund[substr($str, -6, 1)].' '.$thnd_end.$strResult;
		$p = 7;
		break;
		
	case 7: 
	if($digs>7) $nextDig = substr($str,-8,1);
	else $nextDig = 0;
		if(substr($str,-7,1)!=0 and $nextDig!='1') $strResult = $first[substr($str,-7,1)].' '.$mil[substr($str,-7,1)].' '.$strResult;
		else $strResult = ' '.$mil[9].' '.$strResult;
		$p = 8;
		break;
	case 8:
		if(substr($str,-8,1)!=0 and substr($str,-8,1)!=1) $strResult = $deci[substr($str,-8,1)].' '.$strResult;
		elseif(substr($str,-8,1)==1) $strResult = $second[substr($str,-8,1).substr($str,-7,1)].' '.$strResult;
		$p = 9;
	}
	}
	}
else $strResult = 'Больше ста миллионов сомов!';
//$strResult = substr_replace($strResult, '<span style="text-transform:uppercase;">'.substr($strResult,0,1).'</span>', 0, 1);
$strResult = '<span style="first-letter:uppercase;">'.$strResult.'</span>';
return $strResult;
}
	
function lastDigs ($dig) {
	switch($dig) {
		case 1:
		$return = '';
		break;
		case 2:
		case 3:
		case 4:
		$return = '';
		break;
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
		$return = '';
		break;
		}
	return $return;
}

function thouSwitch ($dig, $thou, $first)
{
	if($dig==1) {
		$out = 'одна '.$thou['1'].' ';
	}
	else {
		switch($dig) {
		case 0: $thnd = ''; $curdig = '1'; break;
		case 2: $thnd = 'две '; $curdig = '2'; break;
		case 3: $thnd = 'три '; $curdig = '2'; break;
		case 4: $thnd = 'четыре '; $curdig = '2'; break;
		case 5:
		case 6:
		case 7:
		case 8:
		case 9:
		$thnd = $first[$dig].' ';
		$curdig = '3';
		break;
		}
		$out = $thnd.$thou[$curdig].' ';
	}
	return $out;
}

function lastDigsEN ($dig) {
	if($dig==1) $return = ' US dollar';
	else $return = ' US dollars';
	return $return;
}

function num2strEN($number) {
//числа от 1 до 9
$first['1'] = 'one';
$first['2'] = 'two';
$first['3'] = 'three';
$first['4'] = 'four';
$first['5'] = 'five';
$first['6'] = 'six';
$first['7'] = 'seven';
$first['8'] = 'eight';
$first['9'] = 'nine';

//числа от 10 до 19
$second['10'] = 'ten';
$second['11'] = 'ellewen';
$second['12'] = 'twelve';
$second['13'] = 'thirteen';
$second['14'] = 'fourteen';
$second['15'] = 'fifteen';
$second['16'] = 'sixteen';
$second['17'] = 'seventeen';
$second['18'] = 'eighteen';
$second['19'] = 'nineteen';

//числа от 20 до 100
$deci['2'] = 'twenty';
$deci['3'] = 'thirty';
$deci['4'] = 'fourty';
$deci['5'] = 'fifty';
$deci['6'] = 'sixty';
$deci['7'] = 'seventy';
$deci['8'] = 'eightty';
$deci['9'] = 'ninety';

//числа от 200 до 900
$hund['1'] = 'one hundred';
$hund['2'] = 'two hundred';
$hund['3'] = 'three hundred';
$hund['4'] = 'four hundred';
$hund['5'] = 'five hundred';
$hund['6'] = 'six hundred';
$hund['7'] = 'seven hundred';
$hund['8'] = 'eight hundred';
$hund['9'] = 'nine hundred';

//числа от 1000 до 100.000
$thou['1'] = 'thousand';

if($number<1000000)
	{
	$str = $number;
	$p = 1;
	$digs = strlen($str);
	//шаг первый (единицы)
	while($p<=$digs) {
	switch($p) {
	case 1:	
	if($digs>1) {
		//проверка 10 - 19
		if(substr($str, -2, 1)!=0) {
		$chk = substr($str, -2, 1)*10 + substr($str, -1, 1);
		if($chk >=10 and $chk <=19) {
			$strResult .= $second[$chk].' US dollars';
		}
		elseif(substr($chk,0,1)>=2) //больше либо равно 20
		{
			$strResult .= $deci[substr($chk,0,1)].' ';
			if(substr($str, -1, 1)!=0)
			{
				$strResult .= $first[substr($str, -1, 1)];
				$strResult .= lastDigsEN(substr($str, -1, 1));
			}
			else $strResult .= ' US dollars';
		}
		}
		elseif(substr($str,-1,1)!=0)
		{
			$strResult .= $first[substr($str, -1, 1)];
			$strResult .= lastDigsEN(substr($str, -1, 1));
		}
		else $strResult .= ' US dollars';
	}
	else {
	$strResult .= $first[substr($str, -1, 1)];
	$strResult .= lastDigsEN(substr($str, -1, 1));
	}
		$p = 3;
		break;
	case 3:
		if(substr($str, -3, 1)!=0) {
		if(substr($str, -2, 1)!=0 or substr($str, -1, 1)!=0) $and .= ' and';
			$strResult = $hund[substr($str, -3, 1)].$and.' '.$strResult;
		}
		
		$p = 4;
		break;
	case 4:
	if($digs==4){
		$strResult = $first[substr($str,-4,1)].' '.$thou['1'].' '.$strResult;
	}
	elseif($digs==5 or $digs==6)
	{
		if(substr($str,-5,1)==1)
		{
			$thnd = 10 + substr($str,-4,1);
			$strResult = $second[$thnd].' '.$thou['1'].' '.$strResult;
		}
		elseif(substr($str,-5,1)!=0) {
			if(substr($str,-4,1)==0) $strResult = $deci[substr($str,-5,1)].' '.$thou['1'].' '.$strResult;
			else {
			 $strResult = $deci[substr($str,-5,1)].' '. $first[substr($str,-4,1)].' '.$thou['1'].' '.$strResult;
			}
		}
	}
		$p = 6;
		break;
	case 6:
		if(substr($str,-5,1)==0 and substr($str,-4,1)!=0) $strResult =  $first[substr($str,-4,1)].' '.$thou['1'].' '.$strResult;
		if(substr($str,-5,1)==0 and substr($str,-4,1)==0) $strResult = ' '.$thou['1'].$strResult;
		$strResult = $hund[substr($str, -6, 1)].' '.$strResult;

		$p = 7;
		break;
	}
	}
	}
else $strResult = 'More than one million dollars';
$strResult = substr_replace($strResult, '<span style="text-transform:uppercase;">'.substr($strResult,0,1).'</span>', 0, 1);
return $strResult;
}

?>