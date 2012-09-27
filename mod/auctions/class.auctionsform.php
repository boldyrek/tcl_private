<?

class auctionsform extends Proto {
	var $id;
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

	public function getContent() {
		
		if(!isset($_GET['add'])) {
			if(isset($_GET['id'])) $this->id = intval($_GET['id']);
			else $this->id = '';
			
			if($this->id != '') $this->content = @mysql_fetch_array($this->mysqlQuery("
			SELECT *
			FROM `ccl_".ACCOUNT_SUFFIX."auctions` 
			WHERE id = '".$this->id."'"));
		}
		else {
			$this->id = '0';
			$this->content['id'] = '0';
		}

		if($this->content['id']=='') {
			$this->page .= '<div class="warn" style="width:900px;">Ошибка! Аукцион с такими параметрами в базе не обнаружен</div>';
		}
		else {
			if (isset($_GET['success'])) $this->page .='<h4 class="report">'.$this->translate->_('Изменения сохранены').'</h4>';
			$this->page .= $this->auction_edit();
		}
	}

	function auction_edit() {

		//адрес формы
		if(!isset($_GET['add'])) $form_link = '&sw=save&id='.$this->id;
		else $form_link = '&sw=add';
		//#######################################

		$this->page .= '
		<form class="myForm" action="'.$this->root_path.'?mod=auctions'.$form_link.'" method="post">
		<div class="cont_customer">
		<h3>'.$this->translate->_('Аукцион').'</h3>
		<table width="692" border="0" cellpadding="0" cellspacing="0" class="list">
		  <tr>
		    <td align="right" class="title" width="200">'.$this->translate->_('Название аукциона').'</td>
		    <td class="title"><input type="text" name="name" value="'.$this->content['name'].'" tabindex="1" /></td>
		  </tr>
		  <tr>
			<td align="right" class="rowB title">'.$this->translate->_('адрес').'</td>
			<td class="rowB title"><input type="text" name="address" value="'.$this->content['address'].'" tabindex="9" /></td>
		  </tr>
		  <tr>
		    <td align="right" class="rowA title">'.$this->translate->_('телефоны').'</td>
		    <td class="rowA title"><input type="text" name="phones" value="'.$this->content['phones'].'" tabindex="2" /></td>
		</tr>
		<tr>
			<td align="right" class="rowB title">'.$this->translate->_('комментарии').'</td>
			<td class="rowB title"><input type="text" name="comment" value="'.$this->content['comment'].'" tabindex="2" /></td>
		  </tr>
		</table>
		</div>
		<table border="0" cellpadding="0" cellspacing="0" class="list" style="width:702px">
		  <tr>
			<td class="title"><a href="'.$this->root_path.'?mod=auctions&sw=delete&id='.$this->id.'" class="delete" onClick="return confirm(\''.$this->translate->_('Вы действительно хотите удалить этот аукцион?').'\')">'.$this->translate->_('удалить').'</a>
			</td>
			<td width="214" align="right" class="title">
			<input type="submit" name="Submit" value="'.$this->translate->_('Сохранить').'" id="save" tabindex="17" /></td>
		 </tr>
		  	  
		</table>
		</form>
		';
	}
}

?>