<?
require_once($_SERVER['DOCUMENT_ROOT'].'/public/mod/stakes/class.addStakeFrom.php');

class userStakes extends Proto {
	
	var $content;
	var $bg;
	var $zero_age = 120;
	var $change;
	var $list;
	var $error;
	var $menu;
	
	function drawContent() {
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
	
	function moduleContent() {
		$this->localMenu();
		
		if($_GET['sw']=='add_stake') $this->drawAddStakeForm();
		elseif($_GET['sw']=='add') {
			$this->addStake();
			$this->drawAddStakeForm();
		}
		elseif($_GET['sw']=='delete') {
			if(isset($_GET['id']) and intval($_GET['id'])!='0' and intval($_GET['id'])!='') {
				$this->validateID();
			}
			$this->listStakes();
		}
		else $this->listStakes();
	}
	
	function listStakes() {
		$this->change = array();
		$this->getData();
		$num = @mysql_num_rows($this->content);
		if($num>0) {
			$i = 0;
			while($i<$num) {
				$line = mysql_fetch_array($this->content);
				$stakes .= $this->drawRow($line);
				$i++;
			}
			//$this->changeStatus();
			$this->list .= '
			<table width="100%" cellpadding="2" cellspacing="0">
			<tr class="stakesRow" style="background-color:#ddd;">
				<td width="50" align="center">статус</td>	
				<td width="50">дата</td>
				<td width="50">лот №</td>
				<td width="160">аукцион</td>
				<td width="50">ставка</td>
				<td>описание</td>
			</tr>
			'.$stakes.'
			</table>';
			}
			else $this->list .= 'У вас пока нет ставок!';
			
			$legend = '
			<span style="font-size:12px; color:#555;">Пояснение к пиктограммам статусов</span>
			<table width="500" border="0" cellspacing="1" cellpadding="3" class="list">
					  <tr class="rowA">
					    <td width="50%"><img src="/img/stakes/result_yes.gif" align="absmiddle"> - вы купили этот лот </td>
					    <td><img src="/img/stakes/in_progress.gif" align="absmiddle" /> - ваша ставка принята</td>

					  </tr>
					  <tr class="rowB">
					    <td><img src="/img/stakes/result_no.gif" align="absmiddle" /> - ваша ставка не прошла </td>
					    <td><img src="/img/stakes/new_stake.gif" align="absmiddle"> - новая ставка, ее можно отменить</td>
					  </tr>
					  <tr class="rowA">
					    <td><img src="/img/stakes/result_rej.gif" align="absmiddle" /> - ОТКАЗАНО в ставке </td>
					    <td>&nbsp;</td>
					  </tr>
					</table>';
			$this->contentWrap('<p class="notice">РАЗДЕЛ "СТАВКИ" НАХОДИТСЯ В РЕЖИМЕ ТЕСТИРОВАНИЯ!</p>'.$this->error.$this->list);
			$this->contentWrap($legend);
		}
	
	function addStake() {
		$this->error = false;
		if(intval($_POST['lot'])==0 or intval($_POST['lot'])=='') {
			$this->error .= '<span class="notice">Номер <b>лота</b> указан неверно!</span><br>';	
		}
		if(intval($_POST['sum'])==0 or intval($_POST['sum'])=='') {
			$this->error .= '<span class="notice"><b>Сумма</b> указана неверно!</span><br>';	
		}
		if(intval($_POST['lot_date']['d'])>31 or intval($_POST['lot_date']['d'])<0) {
			$this->error .= '<span class="notice"><b>День</b> указан неверно!</span><br>';
		}
		if(intval($_POST['auction'])==0 or intval($_POST['auction'])=='') {
			$this->error .= '<span class="notice"><b>Аукцион</b> не указан!</span><br>';
		}
		if(intval($_POST['lot_date']['m'])<1 or intval($_POST['lot_date']['m'])>12) {
			$this->error .= '<span class="notice"><b>Месяц</b> указан не верно!</span><br>';
		}
		if(!$this->error) {
			if(intval($_POST['lot_time']['m'])>60 or intval($_POST['lot_time']['m'])<0) $_POST['lot_time']['m'] = 0;
			$request = "
			INSERT INTO `ccl_".ACCOUNT_SUFFIX."stakes_list` (`id`,`user`,`status`,`created`,`auction`,`lot`,`sum`,`date`,`desc`)
			VALUES (
			LAST_INSERT_ID(),
			'".$_SESSION['user_id']."',
			'0',
			'".mktime()."',
			'".intval($_POST['auction'])."',
			'".intval($_POST['lot'])."',
			'".intval($_POST['sum'])."',
			'".date('Y').'-'.mysql_real_escape_string((strlen($_POST['lot_date']['m'])>1?$_POST['lot_date']['m']:"0".$_POST['lot_date']['m']))."-".mysql_real_escape_string($_POST['lot_date']['d'])."',
			'".mysql_real_escape_string(htmlspecialchars(strip_tags($_POST['model'])))."')";
			
			$this->mysqlQuery($request);
			$this->redirect('/public/?mod=stakes');
			/*if(!$_SESSION['add_counter']) $_SESSION['add_counter'] = 1;
			else $_SESSION['add_counter']++;
			if($_SESSION['add_counter']==19) sleep(5);
			elseif($_SESSION['add_counter']>19) sleep(10);	*/
		}
	}
	
	function drawAddStakeForm() {
		$form = new addStakeForm();
		$form->auc_list = $this->mysqlQuery("
		SELECT * 
		FROM `ccl_".ACCOUNT_SUFFIX."stakes_auc` 
		WHERE 1 
		ORDER BY `name` ASC");
		$this->contentWrap('
		<p class="notice">РАЗДЕЛ "СТАВКИ" НАХОДИТСЯ В РЕЖИМЕ ТЕСТИРОВАНИЯ!</p>
		'.$this->error.$form->makeForm());
	}
	
	function removeStake() {
		// sql to remove stake
	}
	
	function getData() {
		$this->content = $this->mysqlQuery("
		SELECT ccl_".ACCOUNT_SUFFIX."stakes_list.*, ccl_".ACCOUNT_SUFFIX."stakes_auc.name as auc 
		FROM `ccl_".ACCOUNT_SUFFIX."stakes_list` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."stakes_auc` 
		ON (ccl_".ACCOUNT_SUFFIX."stakes_auc.id = ccl_".ACCOUNT_SUFFIX."stakes_list.auction)
		WHERE ccl_".ACCOUNT_SUFFIX."stakes_list.user = '".$_SESSION['user_id']."' 
		ORDER BY ccl_".ACCOUNT_SUFFIX."stakes_list.id DESC");
	}
	
	function drawRow($info) {
		if($this->bg=='') $this->bg = ' bgcolor="#efefef"';
		else $this->bg = '';
		
		switch ($info['status']) {
			case '0':
				//$stake_age = mktime() - $info['created'];
				/*if($stake_age>$this->zero_age) {
					array_push($this->change, $info['id']);
					$status = '<img src="/img/stakes/in_progress.gif">';
					$this->bg = ' bgcolor="#E2FECF"';
				}
				else {*/
					$status = '<a href="'.$this->root_path.$this->user_folder.'?mod=stakes&sw=delete&id='.$info['id'].'" onclick="return confirm(\'Вы действительно хотите удалить эту ставку?\')"><img src="/img/stakes/new_stake.gif" border="0" alt="Нажмите, чтобы отменить эту ставку" title="Нажмите, чтобы отменить эту ставку"></a>';
					$this->bg = ' bgcolor="#FFE6CE"';
				//}
				
				break;
			case '1':
				$status = '<img src="/img/stakes/in_progress.gif">';
				$this->bg = ' bgcolor="#E2FECF"';
				break;
			case '2':
				$status = '<img src="/img/stakes/result_no.gif">';
				break;
			case '3':
				$status = '<img src="/img/stakes/result_yes.gif">';
				break;
			case '9':
				$status = '<img src="/img/stakes/result_rej.gif">';
				break;
		}
		$lot_date = explode('-',$info['date']);
				
		$out = '<tr'.$this->bg.' class="stakesRow">';
		$out .= '<td align="center">'.$status.'</td>';
		$out .= '<td>'.$lot_date[2].','.$this->month($lot_date[1]).'</td>';
		$out .= '<td>'.$info['lot'].'</td>';
		$out .= '<td>'.$info['auc'].'</td>';
		$out .= '<td>'.$info['sum'].'</td>';
		$out .= '<td>'.$info['desc'].'&nbsp;</td>';
		
			
		return $out.'
		</tr>';
	}
	
	function localMenu() {
		$file = $_SERVER['DOCUMENT_ROOT'].$this->root_path.$this->user_folder.'/mod/stakes/templates/local_menu.php';
		if(file_exists($file)) {
			$this->drawMenu();
			require_once($file);
			$this->page .= $menu;
		}
		else $this->errorHandler('Не найден файл '.$file, 2);
	}
	
	function contentWrap($content) {
		$this->page .= '<div style="background-color:#fff; width:730px; padding:20px;border-left:1px solid #ccc;">
		'.$content.'
		</div>';
	}
	
	function drawMenu() {
		$form = 'class="stakesBlur"';
		$list = 'class="stakesBlur"';
	
		if($_GET['sw']=='add_stake') {
			$form = 'class="stakesTabSel"';
		}
		else $list = 'class="stakesTabSel"';
		$this->menu = '
			<td '.$list.' align="center" width="150" onClick="document.location=\'/public/?mod=stakes\'"><img src="/imgs/sp.gif" height="24" width="1" align="absmiddle">Мои ставки</td>
			<td '.$form.' align="center" width="150" onClick="document.location=\'/public/?mod=stakes&sw=add_stake\'"><img src="/img/plus.gif" align="absmiddle" hspace="3" border="0">Добавить ставку</td>
			';
	}
	
	function month($num) {
		$monthes = array(
		'1' => 'янв',
		'2'	=> 'фев',
		'3'	=> 'мар',
		'4'	=> 'апр',
		'5'	=> 'май',
		'6'	=> 'июн',
		'7'	=> 'июл',
		'8'	=> 'авг',
		'9'	=> 'сен',
		'10'=> 'окт',
		'11'=> 'ноя',
		'12'=> 'дек');
		return $monthes[ltrim($num,'0')];
		
	}
	
	function validateID() {
		
		$stakes = $this->mysqlQuery("
		SELECT id 
		FROM `ccl_".ACCOUNT_SUFFIX."stakes_list` 
		WHERE `user` = '".$_SESSION['user_id']."' 
		AND `status`='0' 
		AND `id` = '".intval($_GET['id'])."'");
		
		$num = @mysql_num_rows($stakes);
		
		if($num>0) {
			$this->deleteStake(intval($_GET['id']));
		}
	}
	
	function deleteStake($id) {
		$this->mysqlQuery("
			DELETE 
			FROM `ccl_".ACCOUNT_SUFFIX."stakes_list` 
			WHERE `id` = '".$id."'");	
		$this->error .= '<span class="notice">Ставка удалена.</span>';
	}
	
	function changeStatus() {
		if(count($this->change)>0) {
			foreach ($this->change as $k=>$v) {
				$request .= "`id` = '".$v."' OR ";
			}
			
			$this->mysqlQuery("
			UPDATE `ccl_".ACCOUNT_SUFFIX."stakes_list` 
			SET `status` = '1' 
			WHERE ".rtrim($request, ' OR '));
		}
	}
}

?>