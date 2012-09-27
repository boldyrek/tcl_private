<?php
define('SECRET_KEY', 'dsjksdjksd934LIsi23983');
require('../inc/baseconf.php');

//fucking shit
$link = mysql_connect($dbhost, $dbuser, $dbpass);
if (!$link) {die('Could not connect: ' . mysql_error());}
$db=mysql_select_db($dbbase, $link);
mysql_query("SET NAMES utf8");

if (!isset($_POST) || $_POST['secret-key']!=SECRET_KEY){
    header('Location: /', 404);
    exit;
}

if (!isset($_POST['repair_id']) && !isset($_POST['id'])){
    _answer('Нет корректных параметров идентификации');
}

//Если передается ID Машины
if (isset($_POST['id']) && $_POST['id']){
    $carInfo = getCarInfoById($_POST['id']);
    isCarHasBeenImportedToRepairByCarId($carInfo['id']);
    if (false != isTicketHasBeenImported($_POST['repair_ticket_id'])){
        $trasnactionId = addTransaction($_POST['amount'], $carInfo['id'], $_POST['purpose'], $_POST['comment'], $_POST['repair_ticket_id'], 2);
        _answer($trasnactionId, 'success');
    }
    _answer('Не удалось произвести импорт');
}

//Если передается ID машины из БД repair
if (isset($_POST['repair_id']) && $_POST['repair_id']){
    $sentInfo = isCarHasBeenImportedToRepairByRepairId($_POST['repair_id']);
    
    //если машина есть в таблице об отправленных маниш
    if ($sentInfo!==false){
        if (false != isTicketHasBeenImported($_POST['repair_ticket_id'])){
            $trasnactionId = addTransaction($_POST['amount'], $sentInfo['tcl_id'], $_POST['purpose'], $_POST['comment'], $_POST['repair_ticket_id'], 2);
            _answer($trasnactionId, 'success');
        }
        _answer('Не удалось произвести импорт');
    }
    
    //а если нету - логично было бы запретить импорт проводок, но они оказывается часть машин руками вносили - берем за основу вин код
    else{
        if (isset($_POST['vin'])){
            if (!validate_vin($_POST['vin'])){
                _answer('Вин код указан некорректно');
            }

            $carInfo = getCarInfoByVin($_POST['vin']);

            if (false != isTicketHasBeenImported($_POST['repair_ticket_id'])){
                $trasnactionId = addTransaction($_POST['amount'], $carInfo['id'], $_POST['purpose'], $_POST['comment'], $_POST['repair_ticket_id'], 2);
                _answer($trasnactionId, 'success');
            }
            _answer('Не удалось произвести импорт');
        }
    }
}


function getCarInfoById($id){
    $sql = "SELECT * FROM `ccl_cars` WHERE `id`='".intval($id)."'";
    $tmp = mysql_query($sql);
    $res = mysql_fetch_assoc($tmp);
    if (empty($res)){
        _answer('Указанной машины не найдено');
    }
    return $res;
}

function getCarInfoByVin($vin){
    $sql = "SELECT * FROM `ccl_cars` WHERE `frame`='".mysql_real_escape_string($vin)."'";
    $tmp = mysql_query($sql);
    $res = mysql_fetch_assoc($tmp);
    if (empty($res)){
        _answer('Машины с вин кодом '.$vin.' не найдено');
    }
    return $res;
}

function isCarHasBeenImportedToRepairByCarId($id){
    $sql = "SELECT * FROM `ccl_repair_sent` WHERE `tcl_id`='".intval($id)."'";
    $tmp = mysql_query($sql);
    $res = mysql_fetch_assoc($tmp);
    if (empty($res)){
        _answer('Машина с указанным ID не была импортирована');
    }
    return $res;
}


function isCarHasBeenImportedToRepairByRepairId($repairId){
    $sql = "SELECT * FROM `ccl_repair_sent` WHERE `repair_id`='".intval($repairId)."'";
    $tmp = mysql_query($sql);
    $res = mysql_fetch_assoc($tmp);
    if (empty($res)){
        return false;
    }
    return $res;
}


function isTicketHasBeenImported($ticketId){
    $sql = "SELECT * FROM `ccl_repair_transactions` WHERE `ticket_id` = '".intval($ticketId)."'";
    $tmp = mysql_query($sql);
    $res = mysql_fetch_assoc($tmp);
    if (!empty($res)){
        _answer('Тикет с таким ID уже был импортирован');
    }
    return true;

}

function addTransaction($ammount, $carId, $purpose, $comment, $ticketId, $type=2){
    global $link;

    $sql="INSERT INTO `ccl_accounting` (`client`, `amount`, `comment`, `date`, `user_added`, `status`, `last_edited`, `car`, `stuff`, `signer`, `paid`, `purpose`, `type`)
    VALUES(
    '0',
    '".intval($ammount)."',
    '".mysql_real_escape_string($comment)."',
    '".date('Y-m-d')."',
    '28',
    '0',
    '".date('Y-m-d H:i:s')."',
    '".intval($carId)."',
    '0',
    '',
    '0',
    '".intval($purpose)."',
    '".intval($type)."'
    )";

    mysql_query($sql);

    $transactionId = mysql_insert_id();

    $sql = "INSERT INTO `ccl_repair_transactions` (`ticket_id`,	`transaction_id`, `add_date`) VALUES ('".intval($ticketId)."', '".$transactionId."', '".date('Y-m-d H:i:s')."')";
    mysql_query($sql);

    return $transactionId;
}

function _answer($message = false, $status='error'){
    $result = array('state'=>$status, 'message'=>$message);
    echo json_encode($result); die;


}


function validate_vin($vin) {

    $vin = strtolower($vin);
    if (!preg_match('/^[^\Wioq]{17}$/', $vin)) {
        return false;
    }

    $weights = array(8, 7, 6, 5, 4, 3, 2, 10, 0, 9, 8, 7, 6, 5, 4, 3, 2);

    $transliterations = array(
    "a" => 1, "b" => 2, "c" => 3, "d" => 4,
    "e" => 5, "f" => 6, "g" => 7, "h" => 8,
    "j" => 1, "k" => 2, "l" => 3, "m" => 4,
    "n" => 5, "p" => 7, "r" => 9, "s" => 2,
    "t" => 3, "u" => 4, "v" => 5, "w" => 6,
    "x" => 7, "y" => 8, "z" => 9
    );

    $sum = 0;

    for($i = 0 ; $i < strlen($vin) ; $i++ ) { // loop through characters of VIN
        // add transliterations * weight of their positions to get the sum
        if(!is_numeric($vin{$i})) {
            $sum += $transliterations[$vin{$i}] * $weights[$i];
        } else {
            $sum += $vin{$i} * $weights[$i];
        }
    }

    // find checkdigit by taking the mod of the sum

    $checkdigit = $sum % 11;

    if($checkdigit == 10) { // checkdigit of 10 is represented by "X"
        $checkdigit = "x";
    }

    return ($checkdigit == $vin{8});
}
