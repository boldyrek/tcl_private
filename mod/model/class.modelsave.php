<?
class ModelSave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}
	function Process()
	{
		$error='';
		$Id=0;
		if (intval($_POST['marka_id'])==0) $error =$this->translate->_('Заполните марку автомобиля');
		if (trim($_POST['name'])=='') $error =$this->translate->_('Заполните название модели');
		$Id=intval($_POST['id']);
		if ($error=='')
		{
			if ($Id==0)
			{
				$request="INSERT INTO ccl_".ACCOUNT_SUFFIX."model SET
			name='".mysql_real_escape_string(nl2br(htmlspecialchars($_POST['name'])))."',".
			"marka_id='".intval($_POST['marka_id'])."'";
			}
			else
			{
				$request="UPDATE ccl_".ACCOUNT_SUFFIX."model SET
			name='".mysql_real_escape_string(nl2br(htmlspecialchars($_POST['name'])))."', 
			marka_id='".intval($_POST['marka_id'])."'
			WHERE id='{$Id}'";
			}

			if ($this->mysqlQuery($request)) {
				$error=$this->translate->_('Модель сохранена');
			}
			else $error=$this->translate->_('Модель сохранить не удалось');
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=model&sw=');exit;
		}
		else
		{
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=model&sw=form&id='.$Id);exit;
		}
	}
}
?>