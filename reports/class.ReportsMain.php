<?

class ReportsMain extends Proto {
	
	var $content;
	
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function getContent() {
			
		//if(isset($_POST['to_date'])) {
	
			$to_date = $_POST['to_date'];
			
			if(isset($_POST['from_date'])) {
				$from_date = $_POST['from_date'];
			}
			if($_POST['supplier']!='0') $sup = $_POST['supplier'];
			else $sup = '';
			
			if(isset($_POST['arrived'])) {
				switch($_POST['arrived']) {
				case 0:
				$addon = "";
				break;
				case 1:
				$addon = " and `delivered` = '1'";
				break;
				case 2:
				$addon = " and `delivered` = '0'";
				}
			}
			
			$query_result = array();
			
			$condition = ltrim(($sup!=""?"`supplier` = '".$sup."'":"").
				($from_date!=""?" and `buy_date`>='".$from_date."'":"").
				" and `buy_date` <= '".$to_date."'", " and");
				
				$main_request = "
				SELECT id,model,frame,buy_date 
				FROM `ccl_".ACCOUNT_SUFFIX."cars` 
				WHERE ".$condition.$addon."
				ORDER BY `buy_date` ASC";
			
			$this->content = $this->mysqlQuery($main_request);
			$this->process();
		//}
	}
		
	function process() {
		if(!isset($_POST['from_date'])) {
			$last_month = time() - (60*60*24*30);
			$from_date = date("Y-m-d", $last_month);
		}
		else $from_date = $_POST['from_date'];
		
		if(isset($_POST['from_date']) and $_POST['from_date']=='') $from_date = '';
		
		if(!isset($_POST['to_date'])) $to_date = date('Y-m-d');
		else $to_date = $_POST['to_date'];
		
		$suppliers = $this->mysqlQuery("SELECT * FROM `ccl_".ACCOUNT_SUFFIX."suppliers` WHERE 1");
		
		if(!isset($_POST['arrived']) or $_POST['arrived']==0) {
			$arr_checked1 = 'checked="checked"';
		}
		elseif($_POST['arrived']==1) $arr_checked2 = 'checked="checked"';
		elseif($_POST['arrived']==2) $arr_checked3 = 'checked="checked"';
		
		require($_SERVER['DOCUMENT_ROOT'].$root_path.'reports/template.php');
		
		$this->page .= '<div style="padding:20px; width:910px;">';
		
		$this->page .= $query_form;
	
		if(@mysql_num_rows($this->content)!='0' and isset($_POST['to_date'])) {
		
			$this->page .= makeResult();
					
		}
		elseif(@mysql_num_rows($query_result)=='0' and isset($_POST['to_date'])) echo $this->translate->_('За период').' "'.($_POST['from_date']!=''?'с '.$_POST['from_date'].' ':'').'по '.$_POST['to_date'].'" ничего не найдено.';
		
		$this->page .= '</div>
		<script>
		function swtichExtButton() {
		if(document.getElementById("result").style.height=="500px") {
			document.getElementById("result").style.height="";
			document.getElementById("extButton").innerHTML="<img src=\'/img/ccl/collapse.gif\' alt=\''.$this->translate->_('Свернуть список').'\'>";
		}
		else {
			document.getElementById("result").style.height="500px";
			document.getElementById("extButton").innerHTML="<img src=\'/img/ccl/extend.gif\' alt=\''.$this->translate->_('Развернуть список').'\'>";
		}
		}
		</script>';

	}

	function makeResult() {
		$num = @mysql_num_rows($this->content);
		$out = '<div style="padding:5px; background-color:#fff;width:585px;border:1px solid #ccc;">'.$this->translate->_('Всего автомобилей').' '.($_POST['from_date']!=''?'с '.$_POST['from_date'].' ':'').'по '.$_POST['to_date'].': '.'<b>'.@mysql_num_rows($this->content).'</b></div>';
		$class="rowA";
		$i = 0;
		$out .= '<table class="list" width="600">
		<tr class="title">
		<td width="80">'.$this->translate->_('дата').'</td>
		<td width="220">'.$this->translate->_('название').'</td>
		<td>'.$this->translate->_('номер кузова').'</td>
		</tr></table><div style="overflow:auto; height:500px; width:618px;" id="result">
		<table class="list" width="600" style="border-top:0px">';
		while($i<$num) {
			$line = mysql_fetch_array($this->content);
			
			$out .= '<tr class="'.$class.'" onmouseover="this.className=\'rowA hovered\'" onmouseout="this.className=\''.$class.'\'" onclick="document.location=\'/?ref=form.car&car_id='.$line['id'].'\'">
			<td width="80">'.$line['buy_date'].'</td>
			<td width="220">'.$line['model'].'</td>
			<td>'.$line['frame'].'</td>
			</tr>';
			if($class=="rowA") $class="rowA rowB";
			else $class="rowA";
			$i++;
		}
		$out .= '</table></div>
		<div onclick="swtichExtButton()" id="extButton" style="cursor:pointer;position:absolute; left:650px; top:140px;"><img src="/img/ccl/extend.gif" alt="'.$this->translate->_('Развернуть список').'></div>	';
		return $out;
	}	
	
}

?>