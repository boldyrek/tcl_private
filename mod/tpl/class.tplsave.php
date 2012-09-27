<?
class tplSave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}
	function Process()
	{
		$error='';
		$Id=0;
		if (trim($_POST['name'])=='') $error =$this->translate->_('Заполните название шаблона');
		$Id=intval($_POST['id']);
		if ($error=='')
		{
			$type=htmlspecialchars($_POST['type']);
			$txt=stripslashes(htmlspecialchars($_POST['txt']));
			$name=htmlspecialchars($_POST['name']);
			if ($Id==0)
			{
				$request="INSERT INTO ccl_".ACCOUNT_SUFFIX."tpl SET name='{$name}', txt='{$txt}', type='{$type}'";
			}
			else
			{
				$request="UPDATE ccl_".ACCOUNT_SUFFIX."tpl SET name='{$name}', txt='{$txt}' WHERE id='{$Id}'";
			}

			if ($this->mysqlQuery($request)) {
				$error=$this->translate->_('Шаблон сохранен');
			}
			else $error=$this->translate->_('Шаблон сохранить не удалось');
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=tpl&type='.$type);exit;
		}
		else
		{
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=tpl&sw=form&type='.$type.'&id='.$Id);exit;
		}
	}
}
?>