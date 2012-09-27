<?php
/// Отправлялка очереди писем. 

require_once('../lib/class.Proto.php');

class mail extends Proto {

	function getMailPackage($limit=10)
	{
		$package = array();
		$res = mysql_query("SElECT * FROM ccl_mail_tmp ORDER BY id ASC LIMIT {$limit}");
		if ($res && mysql_num_rows($res)>0)
		{
			while ($arr = mysql_fetch_array($res))
			{
				$package[] = $arr;
			}
			return $package;
		} else { return false; }
	}

	function runSubscribe()
	{
		if (!$package = $this->getMailPackage()){die('no mail for sending');}

		foreach ($package as $mail)
		{
			if ($this->sMail($mail['email'], $mail['mail_body'], 'Напоминания tcl.makmalauto.com'))
			{
				$sql="DELETE FROM ccl_mail_tmp WHERE id='{$mail['id']}'";
				mysql_query($sql);
			}
		}
	}
}

$mail = new mail();
$mail->runSubscribe();
?>