<?php

error_reporting(E_ALL^E_NOTICE);
ini_set('display_erorrs', 'On');

if (isset($_SESSION['user_type']) and $_SESSION['user_type'] == 11)
   header('Location: /public');

$action = (empty($_GET['action'])) ? 'grid' : $_GET['action'];

require_once 'tobuilt.php';

$tb = new Tobuilt;

switch ($action)
{
   case 'grid':
   default:
      $tb->grid();
      break;
   case 'view':
      $tb->view();
      break;
   case 'add':
      $tb->add();
      break;
   case 'edit':
      $tb->edit();
      break;
   case 'delete':
      $tb->delete();
      break;
}
