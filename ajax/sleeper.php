<?php
// Sleeps for a given time and returns that time
// Made especially for AJAX requests

$time = intval($_GET['sleep']);
sleep($time);
echo $time;

?>