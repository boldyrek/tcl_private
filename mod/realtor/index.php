<?php

error_reporting(E_ALL^E_NOTICE);
ini_set('display_erorrs', 'On');

if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 11)
   header('Location: /public');

$action = (empty($_GET['action'])) ? 'grid' : $_GET['action'];

require_once 'realtor.php';

$realtor = new Realtor;

switch ($action)
{
   case 'rows':
      $realtor->rows();
      break;
   case 'grid':
   default:
      $realtor->grid();
      break;
}
