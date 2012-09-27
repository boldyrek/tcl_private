<?
class ExpeditorsCars extends Proto {

	var $car_id = '';

	public function makePage() {
		$this->setCarId();
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->moduleContent();
		}

		$this->page .= $this->templates['footer'];

		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		$this->errorsPublisher();
		$this->publish();

	}

	private function setCarId() {
		if(isset($_GET['car_id'])) $id = intval($_GET['car_id']);
		else $id = '';
		$this->car_id = $id;
	}

	private function moduleContent() {
		if(isset($_GET['sw'])) $switch = $_GET['sw'];
		else $switch = '';
		if($this->exists($switch)) {
			switch($switch) {

				case 'form':
					$this->drawForm();
					break;
				case 'upload':
					$this->upload();
					break;
				case 'unlinkphoto':
					$this->unlinkphoto();
					break;
				default:
					$this->drawList();
					break;
			}
		}
		else {
			$this->drawList();
		}

	}

	public function drawList() {
		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."cars.id, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.frame, ccl_".ACCOUNT_SUFFIX."cars.invoice, ccl_".ACCOUNT_SUFFIX."containers.number, ccl_".ACCOUNT_SUFFIX."recievers.name
		FROM `ccl_".ACCOUNT_SUFFIX."cars`
		LEFT JOIN ccl_".ACCOUNT_SUFFIX."containers 
		ON ( ccl_".ACCOUNT_SUFFIX."cars.container=ccl_".ACCOUNT_SUFFIX."containers.id )
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."recievers` 
		ON ( ccl_".ACCOUNT_SUFFIX."cars.reciever = ccl_".ACCOUNT_SUFFIX."recievers.id)
		WHERE ccl_".ACCOUNT_SUFFIX."containers.expeditor=".intval($_SESSION['user_id']);

		$content = $this->mysqlQuery($request);

		$num = mysql_num_rows($content);
		if($num>0) {
			$class="rowA rowB";

			$this->page .= '
			<div class="location">Ваши автомобили</div>
				<table width="920" border="0" cellspacing="0" cellpadding="0" class="list">
			 	 <tr class="title">
			    <td width="300">модель</td>
			    <td width="180">вин код </td>
			    <td width="180">номер контейнера</td>
			    <td width="200">имя получателя</td>
			    <td width="80">цена в инвойсе</td>
				<td>&nbsp;</td>
			  </tr>';

			$i=1;
			while ($i<=$num)
			{
				$line = mysql_fetch_array($content);
				$this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$this->root_path.'adm_expeditors/?mod=cars&sw=form&car_id='.$line['id'].'\'">
					<td>'.$line['model'].'&nbsp;</td>
					<td>'.$line['frame'].'&nbsp;</td>
					<td>'.$line['number'].'&nbsp;</td>
					<td>'.$line['name'].'&nbsp;</td>
					<td>'.$line['invoice'].'&nbsp;</td>
					<td>&nbsp;</td>
					</tr>';
				$i++;
				if ($class=="rowA") $class="rowA rowB"; else $class="rowA";
			}

			$this->page .= '</table>';
		} else $this->page .= '<div class="notice">У вас пока не добавлено ни одного автомобиля!</div>';
	}

	public function drawForm() {
		if($this->car_id!='0' and $this->car_id!='') {

		$request = "
		SELECT ccl_".ACCOUNT_SUFFIX."cars.id, ccl_".ACCOUNT_SUFFIX."cars.model, ccl_".ACCOUNT_SUFFIX."cars.frame, ccl_".ACCOUNT_SUFFIX."cars.invoice, 
		ccl_".ACCOUNT_SUFFIX."containers.number, ccl_".ACCOUNT_SUFFIX."recievers.name
		FROM `ccl_".ACCOUNT_SUFFIX."cars`
		LEFT JOIN ccl_".ACCOUNT_SUFFIX."containers 
		ON ( ccl_".ACCOUNT_SUFFIX."cars.container=ccl_".ACCOUNT_SUFFIX."containers.id )
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."recievers` 
		ON ( ccl_".ACCOUNT_SUFFIX."cars.reciever = ccl_".ACCOUNT_SUFFIX."recievers.id)
		WHERE ccl_".ACCOUNT_SUFFIX."containers.expeditor=".intval($_SESSION['user_id'])." AND `ccl_".ACCOUNT_SUFFIX."cars`.`id`=".$this->car_id;
		
		$content = mysql_fetch_array($this->mysqlQuery($request));
		
		$photo=$this->mysqlQuery("SELECT file,descr FROM `ccl_".ACCOUNT_SUFFIX."expeditors_photo` WHERE `car`={$content["id"]}");
		$R="<table cellpadding=0 cellspacing=0>";
		while($l=mysql_fetch_row($photo)){
 			$R.="
 			<tr><td style='border:none;'><a href='/upload/expeditors_photo/{$l[0]}.jpg' target='blank'>{$l[1]}</a><td style='border:none;'><a href='".$this->root_path."adm_expeditors/?mod=cars&sw=unlinkphoto&car_id={$this->car_id}&id={$l[0]}' onclick='return confirm (\"удалить файл - ".$l[1]."?\")'><img src='/img/ccl/del_img.gif' border=0></a>
 			";
		}
		$R.="</table>";
		
			$this->page .= '
			<div class="cont_car">
			<h3>Автомобиль</h3>
			<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
			  <tr>
				<td width="113" align="right" class="title">название</td>
				<td width="200" class="rowA title">'.$content['model'].'</td>
				<td align="right" class="title">вин код</td>
				<td class="rowA title" width="200">'.$content['frame'].'</td>
			  </tr>
			  <tr>
				<td align="right" class="title rowB">номер контейнера</td>
				<td class="rowA rowB title">'.$content['number'].'</td>
				<td align="right" class="title rowB">имя получателя</td>
				<td class="rowA rowB title">'.$content['name'].'</td>
			  </tr>
			  <tr>
				<td align="right" class="title">цена в инвойсе</td>
				<td class="rowA title">'.$content['invoice'].'</td>
				<td align="right" class="title">&nbsp;</td>
				<td class="rowA title" width="200">&nbsp;</td>
			  </tr>
			 <tr>
				<td align="right" class="title rowB">фотографии</td>
				<td class="rowA title rowB" colspan=3>'.$R.'</td>
			  </tr>
			  </table>
			  </div>
		  <form class="myForm" style="margin:0px;" action="'.$this->root_path.'adm_expeditors/?mod=cars&sw=upload&car_id='.$this->car_id.'" method="post" enctype="multipart/form-data">
		  <table border="0" cellpadding="0" cellspacing="0" class="list" style="width:692px">
		  <tr>
		  	<td width="113" class="title" align="right">описание
		  	<td class="title" colspan=3><input type="text" name="descr">
		  <tr>
		  	<td align="right" class="title">файл
		  	<td align="right" class="title"><input type="file" name="UpPhoto" /></td>
			<td width="200" align="right" class="title"><input type="submit" name="Submit" value="загрузить" id="save" /></td>
			<td width="8" align="right" class="title"><br /><br /></td>
		  </tr>
			</table>
		</form>
			';
		} else $this->redirect($this->root_path.'adm_expeditors/');
	}
	
	private function upload() {
		$ext="";
		if(isset($_FILES['UpPhoto']['name'])) {
			$ext=explode(".",strtolower($_FILES['UpPhoto']['name'])); $ext=$ext[(sizeof($ext)-1)];
		}
		if($this->car_id!='0' && $this->car_id!='' && isset($_FILES['UpPhoto']['tmp_name']) && $ext=="jpg") {
			$descr=addslashes(@$_POST["descr"]?$_POST["descr"]:$_FILES['UpPhoto']['name']);
			$file=$this->hashMaker($this->seedMaker(),time());
			$this->mysqlQuery("INSERT INTO `ccl_".ACCOUNT_SUFFIX."expeditors_photo` (`car`,`descr`,`file`) VALUES ({$this->car_id},'{$descr}','{$file}')");
			if(mysql_insert_id()) {
				move_uploaded_file($_FILES["UpPhoto"]["tmp_name"],$_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/{$file}.jpg");
				require_once $_SERVER["DOCUMENT_ROOT"]."/inc/ftp_functions.php";
				resize_img($_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/{$file}.jpg",600);
				copy($_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/{$file}.jpg",$_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/{$file}.thumb.jpg");
				resize_img($_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/{$file}.thumb.jpg",120);
			}
			
			$this->redirect($this->root_path.'adm_expeditors/?mod=cars&sw=form&car_id='.$this->car_id);
		} else 
			$this->redirect($this->root_path.'adm_expeditors/?mod=cars&sw=form');
	}
	
	private function unlinkphoto() {
		if(isset($_GET["id"]) && $_GET["id"] && $this->car_id){
			$this->mysqlQuery("DELETE FROM `ccl_".ACCOUNT_SUFFIX."expeditors_photo` WHERE `file`='".addslashes($_GET["id"])."' AND `car`=".$this->car_id);
			if(mysql_affected_rows())
				unlink($_SERVER["DOCUMENT_ROOT"]."/upload/expeditors_photo/".strtr($_GET["id"],".","").".jpg");
			$this->redirect($this->root_path.'adm_expeditors/?mod=cars&sw=form&car_id='.$this->car_id);
		} else 
			$this->redirect($this->root_path.'adm_expeditors/?mod=cars&sw=form');
	}

}

?>