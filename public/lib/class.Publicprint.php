<?

require_once($_SERVER['DOCUMENT_ROOT'].'/mod/invoices/class.Invoicesfile.php');

$page = new InvoicesFile();
$page -> drawContent();

?>