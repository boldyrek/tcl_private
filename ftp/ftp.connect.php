<?
$ftp = new ftp(false,false);
//$ftp->Verbose = true;
$ftp->LocalEcho = FALSE;
if(!$ftp->SetServer($ftpserver)) {
        $ftp->quit();
        die("Server settings failed\n");
}

if (!$ftp->connect()) {
      die("Cannot connect\n");
}
if (!$ftp->login($ftpuser, $ftppass)) {
        $ftp->quit();
        die("Login failed\n");
}

if(!$ftp->SetType(FTP_AUTOASCII)) echo "SetType FAILS!\n";
if(!$ftp->Passive(TRUE)) echo "Passive FAILS!\n";

?>