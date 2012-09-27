<?php
class CommentTemplates{
	var $root_path;
	var $stuff_id;
	var $commentClass='comment_item';
	function CommentTemplates($root_path, $stuff_id)
	{
		$this->root_path=$root_path;
		$this->stuff_id=$stuff_id;
	}
	function header()
	{
		return "<div class='comment'><h3>Комментарии</h3>";
	}
	function footer()
	{
		return '</div>';
	}
	function error($txt)
	{
		return "<div class='mes'>".$txt."</div>";
	}

	function showOneComment($comment_items)
	{
		$text=$this->topComment();
		if($comment_items['type']=='2')	$comInfo = '[ скрытый ]&nbsp;';
		else $comInfo = '';
		$text.='
		<div id="comment_'.$comment_items['id'].'" class="comment_'.$comment_items['type'].'">
		<table width="100%" border="0" cellspacing="0" cellpadding="3" id="comment_header">
				  <tr>
				    <td style="color:#626262;">'.$comInfo.'Автор: '.$comment_items['login'].'</td>
				    <td align=right style="color:#626262;">Дата: <span class="dat">'.date('j.m.Y, H:i', $comment_items['dat']).'</span></td>
				  </tr>
				</table>';
		$text.='<div style="margin:10px;">'.stripslashes($comment_items['text']).'</div>
		<input type="hidden" id="type_'.$comment_items['id'].'" value="'.$comment_items['type'].'">
		<input type="hidden" id="text_'.$comment_items['id'].'" value="'.str_replace('<br />', '' ,stripslashes($comment_items['text'])).'">
		</div>
		';
		
		return $text;
	}
	function EditButton($commentId)
	{
		return '<div style="text-align:right;padding:5px;background:#CBD8F6;" id="handlers_'.$commentId.'">
		<a href="javascript:getText('.$commentId.');">Редактировать</a>&nbsp;&nbsp;
		<a href="javascript:HideAndDel('.$commentId.');" onClick="return confirm(\'Вы действительно хотите удалить этот комментарий?\');">Удалить</a>
		</div>
		<div id="edit'.$commentId.'"></div>
		';
	}
	function topComment()
	{
		return "<div class='{$this->commentClass}'>";
	}

	function footerComment()
	{
		return "</div>";
	}
	function Message($user,$text)
	{
		return "
		<table width='100%' border='0' cellspacing='0' cellpadding='5' bgcolor=#E3E2E2>
				  <tr>
				    <td style='color:#626262;' width='160'>Автор: ".$user."</td>
				    <td align=left style='color:#626262;'>Дата: ".date("j.m.Y, H:i")."</td>
				  </tr>
				</table>
				<div class='comment_item' style='padding:5px;'>
				".$text."</div>
				<p>С уважением, Makmal Auto.
				<hr size=1 noshade>
				<div style='padding:5px; background-color:#E3E2E2;'>Cars&Clients - <a href='http://tcl.makmalauto.com'>http://tcl.makmalauto.com</a></div>";
	}
	function carInfo($data, $type) {
		if($type=='1') {
			$color = '#c3d8ff';
			$txt = 'для всех';
		}
		elseif ($type=='2') {
			$color = '#fff2c9';
			$txt = 'скрытый';
		}
		return '<div style="background-color:'.$color.'; padding:5px;">
		['.$txt.']<br>
		Тема: '.$data['model'].' - '.$data['year'].' - '.$data['frame'].'
		</div>
		<hr size=1 noshade>
		';
	}
	
	function getJquery()
	{
		return "<script src=../js/jquery.js></script>";
	}
	function getAddCommentFormTop()
	{
		return "<div style='background:#EFEFEF;padding:5px;color:#0066CC;font-size:12px;' ><b>Добавить комментарий</b></div>";
	}
	function getEditCommentFormTop()
	{
		return "<div style='background:#EFEFEF;padding:5px;color:#0066CC;font-size:12px;'><b>Редактировать комментарий</b></div>";
	}
	function getEditJs()
	{
		$text="
		<script language=\"JavaScript\">			
		 function getText(commentId)
		 {
		 	$('#comment_text').val($('#text_'+commentId).val());
		 	$('#comment_id').val(commentId);
		 	$('#comment_type').val($('#type_'+commentId).val());
		 	setTimeout('scroll(0,scroll_y_max)', 250); 
		 }
		 
		 function HideAndDel(commentId) 
		 {
		 	$.get('/?mod=stuff&sw=comment&what=del', { c_id: commentId },
		 	function(data) {
		 		document.getElementById('handlers_'+commentId).innerHTML = '';
		 		document.getElementById('comment_'+commentId).innerHTML='<div class=\'notice\' style=\'padding:10px;\'>комментарий удален</div>';
		 	} );
		 	
		 }
		</script> 
 		";
		return $text;
	}
	function getAddJs()
	{
		$text="
		<script language=\"JavaScript\">
		var scroll_y_max;
		scroll_y_max = document.body.scrollHeight+230;
		</script>
		
		<style>
		.comment_item_sel{
		font-weight:bold;
		}
		</style>	
 		";
		return $text;
	}
	function getForm($action)
	{
		$text='
		<div id="showForm">
		<form  action="'.$action.'" method="post" name="comment">
		<input type="hidden" name="stuff_id" id="stuff_id" value="'.$this->stuff_id.'">
		<input type="hidden" name="comment_id" id="comment_id" value="">
		<table width="100%" border="0" cellspacing="0" cellpadding="4" bgcolor="#D7DFF1" style="border:1px solid #CCCCCC;">
		  
		    
		    '.($_SESSION["user_type"]!=2?'<tr>
		    <td><b><b>Тип: </b></b><select name="comment_type" id="comment_type" style="width:100px;">
		    <option value="1" selected="selected">для всех</option>
		    <option value="2">скрытый</option>
		    </select></td>
		  </tr>':'').''; 
		
		$text.='
		  <tr>
		    <td><b>Текст комментария:</b><br>
		      <textarea name="comment_text" id="comment_text" style="width:100%;height:100px;"></textarea>
			</td>
		  </tr>
		  <tr>
		    <td align="right"><input name="ok" type="submit" value="Cохранить" class="button"></td>
		  </tr>
		</table></form>
		</div>
		<br>	
		<br>	
		';
		return $text;
	}
	function showAddCommentForm()
	{
		$text = $this->getJquery();
		$text.=$this->getAddJs();
		$text.=$this->getAddCommentFormTop();
		$text.=$this->getForm($this->root_path."?mod=stuff&sw=comment&what=add");

		return $text;
	}
}


?>