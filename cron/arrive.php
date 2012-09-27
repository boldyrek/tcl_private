<?
define('ACCOUNT_SUFFIX', '');
require('/home/clients/boldyrek_ftp0/domains/tcl.makmalauto.com/html/inc/baseconf.php');
$h=mysql_connect($dbhost,$dbuser,$dbpass);
mysql_select_db($dbbase);

function microtime_float()
{
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
}
$time_start = microtime_float();

// Writing headers
echo '<html><head>
<meta http-equiv="refresh" content="1200; url=" />
<title>Days to arrive counter</title>
<style>
body{
	font-family: Verdana, Tahoma, Arial;
	font-size: 12px;
	color:#222;
}
</style>
</head><body>
<h4>Started at '.date('Y-m-d H:i:s P').'</h4>';


/***********************/
//SELECT * FROM TABLE1 INNER JOIN TABLE2 ON TABLE1.NAME = TABLE2.NAME WHERE TABLE2.ID = MyNAME

echo "<h4>Starting `in containers cars`...</h4>";

// Запрос и обработка машин находящихся в контейнере
$q=mysql_query('SELECT * FROM `ccl_'.ACCOUNT_SUFFIX.'containers` AS `cont` INNER JOIN `ccl_'.ACCOUNT_SUFFIX.'cars` AS `car`
				ON car.id=cont.slot1 OR car.id=cont.slot2 OR car.id=cont.slot3 OR car.id=cont.slot4 OR car.id=cont.slot5
				WHERE car.`delivered`=0 AND car.`place_id1`<>0 AND car.`id` > 450
				ORDER BY car.id DESC');

if(mysql_errno()) echo mysql_error();	// Error_Checking!!!

if(mysql_num_rows($q)>0)
{
	while($r=mysql_fetch_assoc($q))
	{
/************************< Days calculation >***************************/

		// Обновить информацию о машине учитывая
		// последние введённые даты (в порядке очерёдности):
		//   - Погрузка на Ж.Д
		//   - Порт перегруза
		//   - Отправка из США
		if($r['rail']!='0000-00-00')	// Если выставленна дата загрузки на ЖД платформу
		{								// то подсчитать количество дней
			$t=mysql_query('SELECT 15-DATEDIFF(\''.date('Y-m-d').'\',\''.$r['rail'].'\') as `days`');	// 15 days till arrival
			$y=mysql_fetch_assoc($t);
			$days=$y['days'];
		}
		elseif($r['portdate']!='0000-00-00')
		{
			$t=mysql_query('SELECT 23-DATEDIFF(\''.date('Y-m-d').'\',\''.$r['portdate'].'\') as `days`');	// 15+8=23 days till arrival
			$y=mysql_fetch_assoc($t);
			$days=$y['days'];
            if($days < 21) $days = 21;
//            $days=21;   // Small modification to keep 21 days while 'portdate' is the last set date (Ksusha's request)
		}
		elseif($r['sent']!='0000-00-00')
		{
			$t=mysql_query('SELECT 48-DATEDIFF(\''.date('Y-m-d').'\',\''.$r['sent'].'\') as `days`');	// 15+8+25=48 days till arrival
			$y=mysql_fetch_assoc($t);
			$days=$y['days'];
            if($days < 48) $days = 48;
		}
		else
			$days=55;

		if(mysql_errno()) echo mysql_error();	// Error_Checking!!!

/************************< End of days calculation >***************************/

		if($days<0) $days=0;


/************************< Cars information updating >***************************/

		$ra=mysql_query('SELECT * FROM `ccl_'.ACCOUNT_SUFFIX.'daysleft` WHERE `cid`='.$r['id']);	// Проверяем есть ли в таблице такие машины
		if(mysql_num_rows($ra)==0)	// Если нету - то создаём...
		{
			// Вставляем машину в таблицу
			echo '<b>Operating</b> <i>vehicle</i>: ('.$r['id'].')  '.$r['model'].' <i>days set</i>: [ <span style="color:blue">'.$days.'</span> ] .................. ';
			$qq=mysql_query('INSERT IGNORE INTO `ccl_'.ACCOUNT_SUFFIX.'daysleft` (cid, left_days, last_status, was_in_container, last_update) VALUES ('.$r['id'].', '.$days.', '.$r['place_id1'].', '.$r['container'].', \''.date('Y-m-d H:i:s').'\')');
			if($qq)
				echo '<span style="color:lime">Done!</span><br>'."\n";
			else
				echo '<span style="color:red">Failed!</span><br>'.mysql_error()."\n";
		}
		else
		{
		    // Обновляем информацию о машине
			echo 'Status for <i>vehicle</i>: ('.$r['id'].')  '.$r['model'].' <i>days set</i>: [ <span style="color:blue">'.$days.'</span> ] .................. ';
			$qq=mysql_query('UPDATE `ccl_'.ACCOUNT_SUFFIX.'daysleft` SET cid='.$r['id'].', left_days='.$days.', last_status='.$r['place_id1'].', was_in_container='.$r['container'].', last_update=\''.date('Y-m-d H:i:s').'\' WHERE `cid`='.$r['id']);
			if($qq)
				echo '<span style="color:lime">Done!</span><br>'."\n";
			else
				echo '<span style="color:red">Failed!</span><br>'.mysql_error()."\n";
		}

		if(mysql_errno()) echo mysql_error();	// Error_Checking!!!

/************************< End of Updating >***************************/
	}
}
else echo 'Empty result! (for cars in containers)';
//=======================================================================================================
//=======================================================================================================
//=======================< Starting calculations for not in containers cars >============================
//=======================================================================================================
//=======================================================================================================

echo '<br><hr align="left" width="50%"><h4>Starting `not in containers` cars...</h4>';

$q=mysql_query('SELECT * FROM `ccl_'.ACCOUNT_SUFFIX.'cars` WHERE `delivered`=0  AND `container`=0 AND `id`>413 ORDER BY `id` DESC'); //AND `place_id1`<>0
if(mysql_num_rows($q)>0)
{
	while($r=mysql_fetch_assoc($q))
	{
		$statusflag=0;
		$presense='';
		$qqrr=mysql_query('SELECT * FROM `ccl_'.ACCOUNT_SUFFIX.'daysleft` WHERE `cid`='.$r['id']);
		if(mysql_num_rows($qqrr)>0)	// Проверка если существует авто в списке
		{
			$rr=mysql_fetch_assoc($qqrr);
			$statusflag=($rr['last_status']==$r['place_id1']) ? 0 : 1 ;		// Проверка местонахождения машины
			$presense=' <sub>in base + counting days</sub>';
		}

	    if($statusflag)
	    {
			if($r['place_id1']==1 and $r['place_id1']==0) $days=76;
			elseif($r['place_id1']>2) $days=61;
//			elseif($r['place_id1']==2) $days=61;
//			elseif($r['place_id1']>2 and $r['place_id1']<9) $days=61;
			else $days=60;	// если машина бродит по штатам :) то выставляем примерно 60 дней

		}
		else
		{	// '.$daycnt.'
			switch($r['place_id1'])
			{
				case 0:
				case 1:
					$daycnt=76;
					break;
				case 2:
					$daycnt=61;
					break;
				default:
					$daycnt=61;
			}
			$tmq=mysql_query('SELECT DATEDIFF(\''.date('Y-m-d').'\',\''.$r['created'].'\') as `daysfrombuy`');
			$tmr=mysql_fetch_array($tmq);
			$dddays=$daycnt-$tmr['daysfrombuy'];
			$tdddays=$dddays;
			// Пока машина в США - не считать больше 48 дней
			if($dddays<48) $dddays=48;

			$qq=mysql_query('UPDATE `ccl_'.ACCOUNT_SUFFIX.'daysleft` SET  left_days='.$dddays.', last_status='.$r['place_id1'].', was_in_container='.$r['container'].', last_update=\''.date('Y-m-d H:i:s').'\' WHERE `cid`='.$r['id']);
			echo 'Status for [ '.$r['id'].' ] were updated <span style="color:blue">'.$dddays."</span> days were set. [".$daycnt."] &nbsp;&nbsp;&nbsp; [".$tdddays.' = '.$daycnt.' - '.$tmr['daysfrombuy'].'] &nbsp;&nbsp;<span style="color:green">'.$presense."</span><br>\n";
			continue;
		}
//================< Updating/Inserting vehicle information >======================

		$ra=mysql_query('SELECT COUNT(*) as `here` FROM `ccl_'.ACCOUNT_SUFFIX.'daysleft` WHERE `cid`='.$r['id']);	// Проверяем есть ли в таблице такие машины
//		$cnt=mysql_num_rows($ra);
		$cnt_r=mysql_fetch_array($ra);
		$cnt=$cnt_r['here'];
		if($cnt)	// Если авто ещё не в списке - то...
		{
			// Обновляем информацию о машине
			echo '<b>Operating (UPdating)</b> <i>vehicle</i>: ('.$r['id'].')  '.$r['model'].' <i>days set</i>: [ <span style="color:blue">'.$days.'</span> ] .................. ';
			$qq=mysql_query('UPDATE `ccl_'.ACCOUNT_SUFFIX.'daysleft` SET cid='.$r['id'].', left_days='.$days.', last_status='.$r['place_id1'].', was_in_container='.$r['container'].', last_update=\''.date('Y-m-d H:i:s').'\' WHERE `cid`='.$r['id']);
			if($qq)
				echo '<span style="color:lime">Done!</span><br>'."\n";
			else
				echo '<span style="color:red">Failed!</span><br>'.mysql_error()."\n";
		}
		else
		{
			// Вставляем машину в таблицу
			echo '<b>Operating (INserting)</b> <i>vehicle</i>: ('.$r['id'].')  '.$r['model'].' <i>days set</i>: [ <span style="color:blue">'.$days.'</span> ] .................. ';
			$qq=mysql_query('INSERT IGNORE INTO `ccl_'.ACCOUNT_SUFFIX.'daysleft` (cid, left_days, last_status, was_in_container, last_update) VALUES ('.$r['id'].', '.$days.', \''.$r['place_id1'].'\', '.$r['container'].', \''.date('Y-m-d H:i:s').'\')');
			if($qq)
				echo '<span style="color:lime">Done!</span><br>'."\n";
			else
				echo '<span style="color:red">Failed!</span><br>'.mysql_error()."\n";
		}
	}
}
else echo 'Empty result! (for cars NOT in containers)';

/**************************/
echo '<b>All - ok! ;)</b>';

// log execution time
$time_end = microtime_float();
$time = $time_end - $time_start;

echo '<br>Done in <span title="'.$time.'">'.sprintf('%01.3f',$time).'</span> sec.<br><br>';

//file_put_contents('./arrive_cron.log','started: ['.date('Y-m-d h:i:s').'], finished: ['.date('Y-m-d h:i:s').'], status - ok!'."\n", FILE_APPEND);
$e=mysql_query("INSERT INTO `log` (date,message,ps) VALUES ('".date('Y-m-d H:i:s')."','Cars arrival time script - executed','".sprintf('%01.5f',$time)."')");

	// If error - show it
	echo '<div style="color:#aa0000"><b>';
	if(mysql_errno()) echo 'Error occured!!!<br><br>'.mysql_error();
	echo '</b></div>';

?>