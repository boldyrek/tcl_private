<?
class PostSave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}
	function Process()
	{
		$error='';
		$Id=0;
		if (trim($_POST['name'])=='') $error =$this->translate->_('Заполните название службы');
		$Id=intval($_POST['id']);
		if ($error=='')
		{
			if ($Id==0)
			{
				$request="INSERT INTO ccl_".ACCOUNT_SUFFIX."post SET
			name='".htmlspecialchars($_POST['name'])."'";
			}
			else
			{
				$request="UPDATE ccl_".ACCOUNT_SUFFIX."post SET
			name='".mysql_real_escape_string(nl2br(htmlspecialchars($_POST['name'])))."'
			WHERE id='{$Id}'";
			}

			if ($this->mysqlQuery($request)) {
				$error=$this->translate->_('Служба сохранена');
			}
			else $error=$this->translate->_('Службу сохранить не удалось');
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=post&sw=');exit;
		}
		else
		{
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=post&sw=form&id='.$Id);exit;
		}
	}
}
?>