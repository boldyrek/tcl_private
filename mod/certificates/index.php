<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='2') $this->redirect('/public');

if(Proto::exists($_GET['sw'])) {
	$file = $_SERVER['DOCUMENT_ROOT'].'/mod/certificates/class.Certificates'.$_GET['sw'].'.php';
	if(file_exists($file)) require_once($file);
	else Proto::redirect('/');
}
else require_once($_SERVER['DOCUMENT_ROOT'].'/mod/certificates/class.Certificatesprint.php');

switch($_GET['sw']) {
	case 'form':$page = new CertificatesForm();	break;
	case 'save':$page = new CertificatesSave();	break;
	case 'print':$page = new CertificatesPrint();break;
	default:$page = new CertificatesPrint();	break;
}

$page -> drawContent();

?>