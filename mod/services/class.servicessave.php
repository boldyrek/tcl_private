<?
class ServicesSave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}
	}
	function Process()
	{
		$error='';
		$Id=0;
		if (trim($_POST['item'])=='') $error =$this->translate->_('Заполните название услуги');
		$Id=intval($_POST['id']);
		if ($error=='')
		{
			trim($_POST['cost'])==''?$cost=0:$cost=str_replace(',','.',htmlspecialchars($_POST['cost']));
			trim($_POST['quantity'])==''?$quantity=1:$quantity=intval($_POST['quantity']);
			if ($Id==0)
			{
				$request="INSERT INTO ccl_".ACCOUNT_SUFFIX."services SET
			item='".htmlspecialchars($_POST['item'])."',
			description='".nl2br(htmlspecialchars($_POST['description']))."',
			cost='".$cost."',
			quantity='".$quantity."'";
			}
			else
			{
				$request="UPDATE ccl_".ACCOUNT_SUFFIX."services SET
			item='".mysql_real_escape_string(nl2br(htmlspecialchars($_POST['item'])))."',
			description='".mysql_real_escape_string(nl2br(htmlspecialchars($_POST['description'])))."',
			cost='".$cost."',
			quantity='".$quantity."' WHERE id='{$Id}'";
			}

			if ($this->mysqlQuery($request)) {
				$error=$this->translate->_('Услуга сохранена');
			}
			else $error=$this->translate->_('Услугу сохранить не удалось');
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=services&sw=');exit;
		}
		else
		{
			$_SESSION['error']=$error;
			header('location:'.$this->root_path.'?mod=services&sw=form&id='.$Id);exit;
		}
	}
}
?>