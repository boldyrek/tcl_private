<?

function array_qsort2 (&$array, $column=0, $order="ASC", $first=0, $last= -2){
 if($last == -2) $last = count($array) - 1;
 if($last > $first) {
   $alpha = $first;
   $omega = $last;
   $guess = $array[$alpha][$column];
   while($omega >= $alpha) {
     if($order == "ASC") {
       while($array[$alpha][$column] < $guess) $alpha++;
       while($array[$omega][$column] > $guess) $omega--;
     } else {
       while($array[$alpha][$column] > $guess) $alpha++;
       while($array[$omega][$column] < $guess) $omega--;
     }
     if($alpha > $omega) break;
     $temporary = $array[$alpha];
     $array[$alpha++] = $array[$omega];
     $array[$omega--] = $temporary;
   }
   array_qsort2 ($array, $column, $order, $first, $omega);
   array_qsort2 ($array, $column, $order, $alpha, $last);
 }
}
function ByteSize($bytes) {
	$size = $bytes / 1024;
	if($bytes < 1024){
		$size = $bytes;//number_format($size, 2);
		$size .= ' b';
	} else {
                if($size < 1024){
   		        $size = number_format($size, 2);
   		        $size .= ' Kb';
	        } elseif($size / 1024 < 1024) {
			$size = number_format($size / 1024, 2);
			$size .= ' Mb';
		} elseif($size / 1024 / 1024 < 1024) {
			$size = number_format($size / 1024 / 1024, 2);
			$size .= ' Gb';
		} else {
			$size = number_format($size / 1024 / 1024 / 1024, 2);
			$size .= ' Tb';
		}
	}
	return $size;
}
function get_extension($file){
   $ext=strtolower(substr($file,strrpos($file,".")+1));
   return $ext;
}
?>