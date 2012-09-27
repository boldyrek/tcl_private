<?
//include_once($_SERVER['DOCUMENT_ROOT'].'/mod/cars/class.Carscomment.php');
class TransportersCars extends Proto {

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
				case 'save':
					$this->save();
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
		if(isset($_POST["delivered"]) && $_POST["delivered"]) $W=""; else $W=" AND `transstatus`=0"; 
		$request = "
			SELECT *, ccl_".ACCOUNT_SUFFIX."cars.id as carsID, ccl_".ACCOUNT_SUFFIX."auctions.name as auction
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."auctions` 
			ON ( ccl_".ACCOUNT_SUFFIX."auctions.id = ccl_".ACCOUNT_SUFFIX."cars.auction )
			WHERE `transporter`='".intval($_SESSION['user_id'])."' AND `ready`=1 AND `delivered`=0{$W}
			ORDER BY `date_ready` ASC";
		$content = $this->mysqlQuery($request);

		$num = mysql_num_rows($content);
		if($num>0) {
			$class="rowA rowB";
			$this->page .= '
			<div class="location">Ваши автомобили
			<form class="myForm" style="margin:0px;" action="'.$this->root_path.'adm_transporters/?mod=cars" method="post"><input type="checkbox" name="delivered" value="on" onchange="this.form.submit()" '.((isset($_POST["delivered"]) && $_POST["delivered"])?"checked":"").' style="width:24px"> показывать доставленные</form>
			</div>
				<table width="920" border="0" cellspacing="0" cellpadding="0" class="list">
			 	 <tr class="title">
			    <td width="300">модель</td>
			    <td width="180">вин код </td>
			    <td width="80">аукцион</td>
			    <td width="110">дата готовности</td>
			    <td width="100">забрал</td>
			    <td width="100">доставил</td>
				<td>&nbsp;</td>
			  </tr>';

			$i=1;
			while ($i<=$num)
			{
				$line = mysql_fetch_array($content);
				$this->page .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'"  onclick="document.location=\''.$this->root_path.'adm_transporters/?mod=cars&sw=form&car_id='.$line['carsID'].'\'">
					<td>'.$line['model'].'&nbsp;</td>
					<td>'.$line['frame'].'&nbsp;</td>
					<td>'.$line['auction'].'&nbsp;</td>
					<td>'.($line['ready']?$line['date_ready']:"").'&nbsp;</td>
					<td>'.($line['pickedup']?$line['date_pickedup']:"").'&nbsp;</td>
					<td>'.($line['transstatus']?$line['date_transstatus']:"").'&nbsp;</td>
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
			SELECT *, ccl_".ACCOUNT_SUFFIX."auctions.name as auction
			FROM `ccl_".ACCOUNT_SUFFIX."cars` 
			LEFT JOIN `ccl_".ACCOUNT_SUFFIX."auctions` 
			ON ( ccl_".ACCOUNT_SUFFIX."auctions.id = ccl_".ACCOUNT_SUFFIX."cars.auction )
			WHERE `transporter`='".intval($_SESSION['user_id'])."' AND `ready`=1 AND `ccl_".ACCOUNT_SUFFIX."cars`.`id`='".$this->car_id."' AND `delivered`=0";
			$content = mysql_fetch_array($this->mysqlQuery($request));
		
			$this->page .= '
			<form class="myForm" style="margin:0px;" action="'.$this->root_path.'adm_transporters/?mod=cars&sw=save&car_id='.$this->car_id.'" method="post">
			<div class="cont_car">
			<h3>Автомобиль</h3>
			<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
			  <tr>
				<td width="113" align="right" class="title">название</td>
				<td width="202" class="rowA title">'.$content['model'].'</td>
				<td align="right" class="title">дата готовности</td>
				<td class="rowA title" width="200">'.($content['ready']?$content['date_ready']:"").'</td>
			  </tr>
			  <tr>
				<td align="right" class="title rowB">аукцион</td>
				<td class="rowA rowB title">'.$content['auction'].'</td>
				<td align="right" class="title rowB">вин код</td>
				<td class="rowA rowB title">'.$content['frame'].'</td>
			  </tr>
			  <tr>
				<td width="113" align="right" class="title">машину забрал</td>
				<td width="202" class="rowA title"><input type="checkbox" name="pickedup"'.($content['pickedup']?' checked':'').' style="width:24px;"> '.($content['pickedup']?$content['date_pickedup']:'').'</td>
				<td align="right" class="title">машину доставил</td>
				<td class="rowA title" width="200"><input type="checkbox" name="transstatus"'.($content['transstatus']?' checked':'').' style="width:24px;">'.($content['transstatus']?$content['date_transstatus']:'').'</td>
			  </tr>
			  </table>
			  </div>
		  
		  <table border="0" cellpadding="0" cellspacing="0" class="list" style="width:692px">
		  <tr>
			<td align="right" class="title"><input type="submit" name="Submit" value="Сохранить" id="save" style="width:240px;" /></td>
			<td width="8" align="right" class="title"><br /><br /></td>
		  </tr>
			</table>
		</form>
			';
		} else $this->redirect($this->root_path.'adm_transporters/');
	}
	private function save() {
		if($this->car_id!='0' and $this->car_id!='') {
			$this->mysqlQuery("
			UPDATE `ccl_".ACCOUNT_SUFFIX."cars` 
			SET `pickedup`=".(isset($_POST["pickedup"])?"1, `date_pickedup`=now()":"0").", 
				`transstatus`=".(isset($_POST["transstatus"])?"1, `date_transstatus`=now()":"0")."
			WHERE `transporter`='".intval($_SESSION['user_id'])."' AND `ready`=1 AND `delivered`=0 AND `id`=".$this->car_id);
			
			$this->redirect($this->root_path.$this->user_folder.'/?mod=cars&sw=form&car_id='.$this->car_id);
		} else 
			$this->redirect($this->root_path.$this->user_folder.'/?mod=cars&sw=form');
	}
}

?>