<?
require_once('templates/class.CommentTemplates.php');
class CarsComment extends Proto {
	var $car_id=0;
	var $view;
	var $carOwnerId;
	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			if(!isset($_GET['add'])) $this->getContent();
		}
		$this->page .= $this->templates['footer'];

		$this->errorsPublisher();
		$this->publish();
	}

	function _LoadView()
	{
		$this->view=new CommentTemplates($this->root_path, $this->car_id);
	}

	function getContent()
	{
		$this->car_id=intval($_REQUEST['car_id']);

		if (isset($_GET['what']))
		{
			switch ($_GET['what'])
			{
				case 'add': $error=$this->AddEditComment();break;
				case 'edit': $error=$this->AddEditComment();break;
				case 'del': $error=$this->delComment();break;
				default: $error='';
			}
			$_SESSION['error']=$error;
			header("location:".$this->root_path."?mod=cars&sw=form&car_id=".$this->car_id);exit;
		}
		
		if(!isset($_GET['add'])) return $this->PrintContent();
	}

	function PrintContent()
	{
		$text = '';
		$this->_LoadView();
		$text.=$this->view->header();
		if(isset($_SESSION['error']) && $_SESSION['error']!='') {
			$text.=$this->view->error($_SESSION['error']);
			unset($_SESSION['error']);
		}


			$text.=$this->showCommentList($this->getCommentList());
			$text.=$this->view->showAddCommentForm();
		 
		$text.=$this->view->footer();
		return $text;
	}

	function setCarOwnerId($buyer, $reciever, $dealer)
	{
		$this->carOwnerId=array($buyer, $reciever, $dealer);
	}

	function showCommentList($arr)
	{
		$text='';
		if (!empty($arr))
		{
			$text.=$this->view->getEditJs();
			foreach ($arr as $num=>$comment_items)
			{
				if ($this->checkOwnerCommentCar($comment_items['u_id'])){
					$this->view->commentClass='comment_item_sel';
				}
				else{
					$this->view->commentClass='comment_item';
				}
				
				$text.=$this->view->showOneComment($comment_items);
				if ($this->checkOwnerComment($comment_items['user_id'])){
					$text.=$this->view->EditButton($comment_items['id']);
				}
				$text.=$this->view->footerComment();
			}
		}
		return $text;
	}

	function checkOwnerComment($user_id)
	{
		if ($user_id==$_SESSION['login_id']) return true; else return false;
	}

	function checkOwnerCommentCar($commentOwner) {
		if ($commentOwner!=0 && in_array($commentOwner, $this->carOwnerId)) return true;
		return false;
	}

	function AddEditComment()
	{
		if ($_POST['comment_text']=='') return "Заполните текст комментария";
		$commentId = intval($_POST['comment_id']);
		if($this->car_id!=0) {
			$type=($_SESSION['user_type']==2)?1:intval($_POST['comment_type']);
			if ($commentId==0)
			{
				$request="INSERT INTO ccl_".ACCOUNT_SUFFIX."car_comment SET user_id='".$_SESSION['login_id']."', car_id='".$this->car_id."',
				type='".$type."', text='".mysql_real_escape_string(nl2br(htmlspecialchars($_POST['comment_text'])))."', dat='".time()."';";
			}
			else
			{
				
				if ($this->_CheckOwner($commentId)==true)
				{
				$request="UPDATE ccl_".ACCOUNT_SUFFIX."car_comment SET type='".$type."',
				 text='".mysql_real_escape_string(nl2br(htmlspecialchars($_POST['comment_text'])))."',
				  dat='".time()."' 
				  WHERE id='{$commentId}';";
				}
				else die("Hacker-fucker?");
			}
		}
		if ($this->mysqlQuery($request)) {
			$this->SendMail();
			return "Комментарий сохранен";
		}
		else return "Комментарий сохранить не удалось";
	}

	function getCommentList()
	{
		$arr=false;
		$request="SELECT * FROM ccl_".ACCOUNT_SUFFIX."car_comment WHERE `car_id` = '".$this->car_id."'".($_SESSION['user_type']==2?"AND type='1'":"")."ORDER BY `dat`";
		
		if ($res=$this->mysqlQuery($request))
		{
			while ($tmp = mysql_fetch_assoc($res))
			{
				$getUser="SELECT log_name, u_id FROM ccl_".ACCOUNT_SUFFIX."usrs  WHERE `id` = '".$tmp['user_id']."';";
				
				if ($res2=$this->mysqlQuery($getUser))
				{
					list($login, $user_id) = mysql_fetch_row($res2);
				}
				else {
					$login='';
				}
				$tmp['login']=$login;
				$tmp['u_id']=$user_id;
				$arr[]=$tmp;
			}
		}
		return $arr;
	}

	function XMail($from, $to, $subj, $text) {
		$un        = strtoupper(uniqid(time()));
		$head      = "From: $from\n";
		$head     .= "Subject: $subj\n";
		$head     .= "X-Mailer: PHPMail Tool\n";
		$head     .= "Reply-To: $from\n";
		$head     .= "Mime-Version: 1.0\n";
		$head	  .= "Content-Type:text/html; charset=utf-8\n";
		$zag=$text;

		return mail("$to", "$subj", $zag, $head);
	}

	function SendMail()
	{
		$info=$this->GetCarInfo();
		$email=$this->GetAdminEmails();
		//if ($_SESSION['user_type']!=2 and intval($_POST['comment_type'])!=2) $email.=(($email!='')?",":'').$info['email'];
		$type=intval($_POST['comment_type']);
		
		$this->_LoadView();
		$text=$this->view->carInfo($info, $type).$this->view->Message($_SESSION['user_name'],nl2br(htmlspecialchars($_POST['comment_text'])));
		$subj="Новый комментарий для ".$info['model']."-".$info['year']."-".$info['frame'];
		// письмо админам
		$this->XMail("info@makmalauto.com", $email, '=?windows-1251?B?'.base64_encode($subj).'?=', stripslashes($text));
		// письмо с комментом владельцу авто
		if(intval($_POST['comment_type'])==1) {
			if(strlen($info['email'])>6) {
				$this->XMail("info@makmalauto.com", $info['email'], '=?windows-1251?B?'.base64_encode($subj).'?=', stripslashes($text));
			}
		}
	}
	function _CheckOwner($id)
	{
		$info=$this->GetCommentInfo($id);
		if ($info!=false)
		return $this->checkOwnerComment($info['user_id']);
		else return false;
	}
	function GetCommentInfo($id)
	{
		$sql="select * from ccl_".ACCOUNT_SUFFIX."car_comment where `id`='$id'";
		if ($res=$this->mysqlQuery($sql))
		{
			return mysql_fetch_assoc($res);
		}
		else return false;
	}
	function GetCarInfo()
	{
		$sql="select t1.model, t1.frame, t1.year, t2.email
		from `ccl_".ACCOUNT_SUFFIX."cars` as t1
		left join `ccl_".ACCOUNT_SUFFIX."customers` as t2
		on (t2.id = t1.buyer)
		WHERE t1.id='".$this->car_id."' LIMIT 1";
		if ($res=$this->mysqlQuery($sql))
		{
			$tmp=mysql_fetch_assoc($res);
			return $tmp;
		}
		else return false;

	}
	function GetAdminEmails()
	{
		$sql="SELECT `email` from `ccl_".ACCOUNT_SUFFIX."usrs` WHERE type!='2' AND `id`!='".$_SESSION['login_id']."' AND `email`!=''";

		if ($res=$this->mysqlQuery($sql))
		{
			while($tmp=mysql_fetch_assoc($res))
			{
				$arr[]=$tmp['email'];
			}
		}
		else return false;
		if (!empty($arr) and  is_array($arr))
		return implode(",", $arr);


	}
	
	function delComment() {
		
		$id = intval($_GET['c_id']);
		if($id!='' and $id!=0) {
			if($this->_CheckOwner($id)) {
				$sql = "DELETE FROM `ccl_".ACCOUNT_SUFFIX."car_comment` WHERE `id` = '".$id."'";
				$this->mysqlQuery($sql);
			}
			else die('property override!');
		}
		break;
		
	}

}

?>