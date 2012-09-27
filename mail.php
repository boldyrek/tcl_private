<?php

XMail('no-reply@tcl.makmalauto.com', 'Larisashevchik@mail.ru', 'Тест', 'Тестовая отправка');

function XMail($from, $to, $subj, $text, $file=false, $filename=false) {
	$subj='=?utf-8?B?'.base64_encode($subj).'?=';

	if ($filename)
	$filename='=?utf-8?B?'.base64_encode($filename).'?=';
	if($file) $f = fopen($file,"rb");

	$un        = strtoupper(uniqid(time()));
	$head      = "From: $from\n";
	$head     .= "X-Mailer: PHPMail Tool\n";
	$head     .= "Reply-To: $from\n";
	$head     .= "Mime-Version: 1.0\n";
	if($filename) {
		$head .= "Content-Type:multipart/mixed;";
		$head .= "boundary=\"----------".$un."\"\n\n";
	}
	if($filename!='') {
		$zag  = "------------".$un."\nContent-Type:text/html; charset=utf-8\n";
		$zag .= "Content-Transfer-Encoding: 8bit\n\n$text\n\n";
		$zag .= "------------".$un."\n";
		$zag .= "Content-Type: application/octet-stream;";
		$zag .= "name=\"".basename($filename)."\"\n";
		$zag .= "Content-Transfer-Encoding:base64\n";
		$zag .= "Content-Disposition:attachment;";
		$zag .= "filename=\"".basename($filename)."\"\n\n";
		$zag .= chunk_split(base64_encode(fread($f,filesize($file))))."\n";
	} else {
		$head.="Content-Type:text/html; charset=utf-8\n";
		$zag=$text;
	}
	return mail("$to", "$subj", $zag, $head);
}