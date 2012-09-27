<?

class Recieversform extends Proto {
	var $id;
		
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

	public function getContent() {
		
		if(!isset($_GET['add'])) {
			$this->id = intval($_GET['id']);

			$this->content = @mysql_fetch_array($this->mysqlQuery("
			SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."recievers`
			WHERE `id` ='".$this->id."'"));

		}
		else {
			$this->id = '0';
			$this->content['id'] = '0';
		}

		if($this->content['id']=='') {
			$this->page .= '<div class="warn" style="width:900px;">'.$this->translate->_('Ошибка! Получатель с такими параметрами в базе не обнаружен').'</div>';
		}
		else {
			if (isset($_GET['success'])) $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
			$this->page .= $this->client_edit();
		}
	}

	function client_edit() {

		//адрес формы
		if(!isset($_GET['add'])) $form_link = '&sw=save&id='.$this->id;
		else $form_link = '&sw=add';

		$this->page .= '
		<script>var myDateFormat = new Array("yyyy-mm-dd");</script>
		<script src="'.$this->root_path.'js/datepicker.js"></script>
		<form class="myForm" action="'.$this->root_path.'?mod=recievers'.$form_link.'" method="post">
		<div class="cont_car">
		<b>Получатель</b> &nbsp <a href="'.$this->root_path.'?mod=recievers" class="title">'.$this->translate->_('список получателей').'</a><br><br>

		<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
		    <td align="right" class="title" width="100">'.$this->translate->_('ФИО').'</td>
		    <td class="title" width="210"><input type="text" name="name" value="'.$this->content['name'].'" tabindex="1" /></td>
			<td align="right" class="title">'.$this->translate->_('телефон').'</td>
			<td width="225" class="rowA title"><input type="text" name="phone" value="'.$this->content['phone'].'" tabindex="9" /></td>
		  </tr>
		  <tr>
		    <td align="right" class="rowB title">'.$this->translate->_('адрес').'</td>
		    <td class="rowB title"><input type="text" name="address" value="'.$this->content['address'].'" tabindex="2" /></td>
			<td align="right" class="rowB title">'.$this->translate->_('Паспорт').'</td>
			<td class="rowB title"><input type="text" name="passport" value="'.$this->content['passport'].'" tabindex="2" /></td>
		  </tr> 
		</table>
		</div>
		<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:702px">
		  <tr>
			<td class="title"><a href="'.$this->root_path.'?mod=recievers&sw=delete&id='.intval($this->id).'" class="delete" onClick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить этого получателя ?').'\')">'.$this->translate->_('удалить').'</a>
			</td>
			<td width="214" align="right" class="title">
			<input type="submit" name="Submit" value="'.$this->translate->_('Сохранить').' id="save" tabindex="17" /></td>
		 </tr>
		  	  
		</table>
		</form>
		';
	}
}

?>