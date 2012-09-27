<?php

error_reporting(E_ALL^E_NOTICE);
ini_set('display_erorrs', 'On');

require 'options.php';

if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 11)
   header('Location: /public');

$colors = new Options;

switch ($_GET['action'])
{
   default:
      $colors->index();
      break;
   case 'add':
      $colors->add();
      break;
   case 'delete':
      $colors->delete();
      break;
}
