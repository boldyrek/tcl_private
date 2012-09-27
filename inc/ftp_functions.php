<?
/* parameters list:
 1: DB table to store filee data
 2: File type (pdf,htm etc.)
 3: prefix (insp_, achk_ etc.)
 4: path -?!?!?!?!?
 5: Owner id (customer or car id)
 6: Owner type, table's row name ('customer' or 'car')
 ...
*/
function upload_img ($table, $file_type, $file_prefix, $path, $owner, $owner_type, $root_path, $ftpserver, $ftpuser, $ftppass)
{
	
	
	global $_FILES, $ftp;
	if (!$ftp)
	{
		include_once $_SERVER['DOCUMENT_ROOT'].$root_path."/ftp/ftp_class.php";
		include_once $_SERVER['DOCUMENT_ROOT'].$root_path."/ftp/function.php";
		include $_SERVER['DOCUMENT_ROOT'].$root_path."/ftp/ftp.connect.php";
	}
	////////////////////////////////////////
	$createFolder =  '/domains/tcl.makmalauto.com/html'.$root_path.'photos/';
	//$createFolder =  '/www'.$root_path.'photos/';
	
	if (!empty($_FILES['file']['name'])){
		$_FILES['file']['name'] = cleanFilename($_FILES['file']['name']);
	}
	if ($owner_type=='stuff') {$createFolder.='stuff/';}
	// modified
	if($table != 'autocheck' and $table != 'scan' and $table != 'container_files' and $table!= 'inspections' and $table!='cars_cert' and $table!='adddoc'and $table!='stuff_photos') $createFolder .= ACCOUNT_SUFFIX.$owner.$path;
	else $createFolder .= $path.ACCOUNT_SUFFIX.$owner;
	
	if($file_type=='jpg' or $file_type=='jpeg') $thumbfolder = $createFolder."/thumb";
	$ftp->mkdir($createFolder);
	// modified
	if($table != 'autocheck' and $table != 'scan' and $table != 'container_files' and ($file_type=='jpg' or $file_type=='jpeg')) $ftp->mkdir($thumbfolder);

	list($usec, $sec) = explode(" ", microtime());
	$file_name = $file_prefix.time()."_".substr($usec,2).".".$file_type;
	// modified
	if(($file_type == 'jpg' or $file_type=='jpeg') and $table != 'autocheck' and $table != 'scan' and $table != 'container_files' and $table!='inspections' and $table!='cars_cert' and $table!='adddoc')
	{
		resize_img($_FILES['file']['tmp_name'],512);
		if (!$ftp->put($_FILES['file']['tmp_name'],$createFolder."/".$file_name)){
			return ;
		}

		resize_img($_FILES['file']['tmp_name'],120, "w");
		if (!$ftp->put($_FILES['file']['tmp_name'],$thumbfolder."/".$file_name))
		return;
	}
	else {
		if (!$ftp->put($_FILES['file']['tmp_name'],$createFolder."/".$file_name))
		return ;
	}

	if($table=='cars_photos' or $table=='stuff_photos') {
		$fields .= ", `folder`";
		$values .= ", '".intval($_POST['folder'])."'";
	}
	$sql="INSERT INTO `ccl_".ACCOUNT_SUFFIX."".$table."` (`".$owner_type."`, `file`".$fields.", `filename`)
	VALUES ('".$owner."', '".$file_name."'".$values.", '".$_FILES['file']['name']."')";
	mysql_query($sql);

}

function del_img ($table, $id, $owner, $type, $file, $root_path, $ftpserver, $ftpuser, $ftppass)
{
	
	global $ftp;
	if (!$ftp)
	{
		include_once $_SERVER['DOCUMENT_ROOT'].$root_path."/ftp/ftp_class.php";
		include_once $_SERVER['DOCUMENT_ROOT'].$root_path."/ftp/function.php";
		include $_SERVER['DOCUMENT_ROOT'].$root_path."/ftp/ftp.connect.php";
	}
	//////////////////////////////////////
	$createFolder = '/www/'.$root_path.'photos/'.($table=='stuff_photos'?"stuff/":'');
	if($type!='scan' and $type!='containers') $createFolder .= ACCOUNT_SUFFIX.$owner;
	else $createFolder .= $type.'/'.ACCOUNT_SUFFIX.$owner;
	$thumbfolder = $createFolder."/thumb";

	$sql="DELETE FROM `ccl_".ACCOUNT_SUFFIX.$table."` where `id`='".$id."' limit 1";
	mysql_query($sql);
	if(!$ftp->delete($createFolder."/".$file))
	return;

	if(!$ftp->delete($thumbfolder."/".$file))
	return;

}

function resize_img($source,$lrsize,$bywidth=false)
{
	$imginfo=getimagesize($source);

	$photoImage = imagecreatefromjpeg($source) or die('cannot create image from jpeg!');
	$photoImageWoriginal = $imginfo[0];
	$photoImageHoriginal = $imginfo[1];
	if ($photoImageWoriginal>$lrsize or $photoImageHoriginal>$lrsize)
	{
		if($bywidth==false){
			if($photoImageWoriginal>$photoImageHoriginal){
				$thW=$lrsize;
				$thH=$photoImageHoriginal/($photoImageWoriginal/$lrsize);
			}elseif($photoImageHoriginal>$photoImageWoriginal){
				$thH=$lrsize;
				$thW=$photoImageWoriginal/($photoImageHoriginal/$lrsize);
			}else{
				$thW=$lrsize;
				$thH=$lrsize;
			}
		}elseif($bywidth=="h") {
			$thH=$lrsize;
			$thW=$photoImageWoriginal/($photoImageHoriginal/$lrsize);
		}else{
			$thW=$lrsize;
			$thH=$photoImageHoriginal/($photoImageWoriginal/$lrsize);
		}
		$thumb = ImageCreateTrueColor($thW, $thH) or die('cannot create True Color image!');
		if(function_exists("imagecopyresampled")){
			imagecopyresampled($thumb, $photoImage, 0, 0, 0, 0, $thW, $thH, $photoImageWoriginal, $photoImageHoriginal) or die('cannot copy resampled image!');
		}else{
			imagecopyresized ($thumb, $photoImage, 0, 0, 0, 0, $thW, $thH, $photoImageWoriginal, $photoImageHoriginal) or die('cannot copy resized image!');
		}
		ImageJPEG($thumb,$source,80) or die('cannot create JPEG from image!');
		imagedestroy($thumb) or die('cannot destroy thumb!');
	}
}

function dirCrasher($path) {
	$empty = true;

	$dh=opendir($path);
	if(false!==($file=readdir($dh))) $empty = false;
	else $empty = true;

	if($empty) {
		if(rmdir($path)) {
			echo $path.' is deleted';
			return true;
		}
		else {
			echo $path.' is NOT deleted';
			return false;
		}
	}
	else return false;
}
?>