<?

require_once($_SERVER['DOCUMENT_ROOT'].'/mod/invoices/class.Invoicesfile.php');

class InvoicesExport extends Proto {

	var $info;
	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

	}


	function Process() {
		//чистим директорию
		$dir = $_SERVER['DOCUMENT_ROOT'].$this->root_path.'export/invoices';
		$dh = opendir($dir);
		$files = array();
		while (($filename = readdir($dh)) !== false)
		{
				if($filename!='.' and $filename!='..') unlink($dir.'/'.$filename);
		}
		closedir($dh);
	
		// создаем текст файла инвойса
		$invoice = new InvoicesFile();
		$invoice->setInv_id();
		$invoice->getInfo();
		$invoice->getServ();
		$invoice->checkAuthor();
		$this->info = $invoice->info;
		require_once("templates/printInvoice.php");
		$txt = $html;
		
		
		$filename = $this->root_path.'export/invoices/invoice_'.mktime().'.doc';
		
		//пишем текст инвойса в выходной файл
		$handle = fopen($_SERVER['DOCUMENT_ROOT'].$filename, 'w+');
		
		   if (fwrite($handle, $txt) === FALSE) {
		       echo "Cannot write to file ($filename)";
		       exit;
			}
		   
		   fclose($handle);
		
		$this->redirect($filename);
	}
	

}
?>