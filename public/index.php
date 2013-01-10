<?php

session_start();
require_once($_SERVER['DOCUMENT_ROOT'].'/account_id.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/lib/class.Proto.php');

if (isset($_GET['mod']) and $_GET['mod'] != '')
{
   $file = $_SERVER['DOCUMENT_ROOT'].'/public/lib/class.Public'.$_GET['mod'].'.php';
   if (file_exists($file))
      require_once($file);
   else
      Proto::redirect('/public');
}
else
{
   $default = $_SERVER['DOCUMENT_ROOT'].'/public/lib/class.Publiccars.php';
   require_once($default);
}

switch ($_GET['mod'])
{
   case 'cars':
      $page = new PublicCars();
      break;
   case 'payments':
      $page = new PublicPayments();
      break;
   case 'balance':
      $page = new PublicBalance();
      break;
   case 'private':
      $page = new PublicPrivate();
      break;
   case 'sale':
   case 'print':
   case 'export_certificate':
      $page = false;
      break;
   case 'marksold':
      $page = new markSold();
      break;
   case 'showsold':
      $page = new showSold();
      break;
   case 'printbalance':
      $page = new PublicPrintBalance();
      break;
   case 'tpl':
      $page = new PublicTpl;
      break;
   case 'autocheck':
      $page = new PublicAutocheck;
      break;
   case 'datebyvin':
      $page = new PublicDateByVin;
      break;
   default:
      $page = new PublicCars();
      break;
}
if ($page)
{
   $page->makePage();
}
?>