<?php
class CSV{
	const SEPARATOR = ',';
	
	static function Array2Csv($arr){
		$headers = array_keys($arr);
		$values = $arr;

		$string	 = implode(self::SEPARATOR, $headers);
		$string.="\r\n";
		$string.= implode(self::SEPARATOR, $values);
		
		return $string;
	}
}
?>