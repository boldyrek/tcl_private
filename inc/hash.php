<?

function seedMaker() {
$symb = 'qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM1234567890,./?;:()-=+_!@#$%^&*{}[]';

$j = 1;
while($j<=6) {
$char .= substr($symb, rand(1,strlen($symb)),1);
$j++;
}
return $char;
}

function hashMaker($seed, $time) {
return  md5(md5($seed).md5($time));
}

?>