<?

class Stakes extends Proto {
	
	var $content;
	var $bg;
	var $zero_age = 120;
	var $change;
	var $list;
	var $error;
	var $menu;
	var $has_new = true;
	var $stakes_header;
	var $matched_stakes;
	var $topStakes;
	var $has_new_header = false;
		
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
		
		if($_GET['sw']=='accept') {
			$this->acceptStake();
			
		}
		elseif($_GET['sw']=='reject') {
			$this->rejectStake();
			$this->listStakes();
		}
		elseif($_GET['sw']=='complete') {
			if(isset($_GET['result']) and $_GET['result']!='' and $this->exists($_GET['id'])) {
				$this->completeStake();
			}
		}
		
		
		$this->listStakes();
	}
	
	function listStakes() {
		$this->change = array();
		$this->getData();
		$num = @mysql_num_rows($this->content);
		if($num>0) {
			$this->matched_stakes = $this->compareStakes();
			$this->stakes_header = '
			<tr class="stakesRow" style="background-color:#ddd;">
				<td width="50">дата</td>
				<td width="50">лот №</td>
				<td width="160">аукцион</td>
				<td width="50">ставка</td>
				<td width="150">описание</td>
				<td>клиент</td>
				<td width="60" align="right">результат</td>
			</tr>';
			
			$i = 0;
			while($i<$num) {
				$line = mysql_fetch_array($this->content);
				$stakes .= $this->drawRow($line);
				$i++;
			}
			
			$this->list .= '<table width="100%" cellpadding="2" cellspacing="0">
			'.$stakes.'
			</table>';
			}
			else $this->list .= 'Не найдено активных ставок!';
			$this->contentWrap($this->list);
		}
	
	function addStake() {
		$this->error = false;
		if(intval($_POST['lot_time']['h'])<0 or intval($_POST['lot_time']['h'])>24) {
			$this->error .= '<span style="color:#f00"><b>Время</b> указано неверно!</span><br>';	
		}
		if(intval($_POST['lot_date']['d'])>31 or intval($_POST['lot_date']['d'])<0) {
			$this->error .= '<span style="color:#f00"><b>День</b> указан неверно!</span><br>';
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
			'".mysql_real_escape_string($_POST['model'])."')";
			
			$this->mysqlQuery($request);
			$this->redirect('/public/?mod=stakes');
			/*if(!$_SESSION['add_counter']) $_SESSION['add_counter'] = 1;
			else $_SESSION['add_counter']++;
			if($_SESSION['add_counter']==19) sleep(5);
			elseif($_SESSION['add_counter']>19) sleep(10);	*/
		}
	}
	
	function removeStake() {
		// sql to remove stake
	}
	
	function getData() {
		$this->content = $this->mysqlQuery("
		SELECT ccl_".ACCOUNT_SUFFIX."stakes_list.*, ccl_".ACCOUNT_SUFFIX."stakes_auc.name as auc, ccl_".ACCOUNT_SUFFIX."customers.name as client_name, ccl_".ACCOUNT_SUFFIX."customers.approved
		FROM `ccl_".ACCOUNT_SUFFIX."stakes_list` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."stakes_auc` 
		ON (ccl_".ACCOUNT_SUFFIX."stakes_auc.id = ccl_".ACCOUNT_SUFFIX."stakes_list.auction)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers`
		ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."stakes_list.user)
		WHERE ccl_".ACCOUNT_SUFFIX."stakes_list.status = '0'
		OR ccl_".ACCOUNT_SUFFIX."stakes_list.status = '1'
		ORDER BY ccl_".ACCOUNT_SUFFIX."stakes_list.status ASC,
		ccl_".ACCOUNT_SUFFIX."stakes_list.created DESC");
	}
	
	function drawRow($info) {
		if($this->bg=='') $this->bg = ' bgcolor="#efefef"';
		else $this->bg = '';
		
		$u_image = '';
		
		if(count($this->matched_stakes)>0) {
			$j=0;
			foreach ($this->matched_stakes as $k=>$v) {
				if($k==$info['id']) {
					$have_match = array($j=>$v);					
					$j++;
				}
				elseif($v==$info['id']) {
					$have_match = array($j=>$k);
					$j++;
				}
			}
		}
		if(count($have_match)>0) {
			foreach ($have_match as $key=>$value){
			 if($info['status']=='0') $matches = '<br><span style="color:#f00;">ВТОРАЯ СТАВКА НА ЛОТ!</span> ';
			 elseif ($info['status']=='1') {
			 	$matches = '<br><span style="color:#f00;">ПАРНАЯ СТАВКА НА ЛОТ!</span>';
			 	$this->bg = ' bgcolor="#ffec95"';
			 }
			}
		}
		else $matches = '';
		
		if($this->topStakes[$info['lot']]['id']==$info['id'] and $this->topStakes[$info['lot']]['id']!='same') $maximum = '<br><u>МАКС.!</u>';
		elseif($this->topStakes[$info['lot']]['id']=='same') $maximum = 'ОДИНАКОВЫЕ СТАВКИ!';
		else $maximum = '';
		
		switch ($info['status']) {
			case '0':
				$stake_age = mktime() - $info['created'];
				$status = '
				<a href="'.$this->root_path.'?mod=stakes&sw=reject&id='.$info['id'].'"><img src="/img/stakes/result_rej.gif" border="0" alt="ОТКЛОНИТЬ эту ставку" title="ОТКЛОНИТЬ эту ставку"></a> &nbsp; 
				<a href="'.$this->root_path.'?mod=stakes&sw=accept&id='.$info['id'].'"><img src="/img/stakes/result_yes.gif" border="0" alt="ПРИНЯТЬ эту ставку" title="ПРИНЯТЬ эту ставку"></a>';
				$this->bg = ' bgcolor="#ffdddd"';
				if(!$this->has_new_header) $out = '
				<tr class="stakesRow">
				<td colspan="7"><b style="color:#f11;">НОВЫЕ СТАВКИ:</b>&nbsp;</td>
				</tr>
				'.$this->stakes_header;
				$this->has_new = true;
				$this->has_new_header = true;
				
				if($info['approved']=='1') $u_type = 'approved';
				else $u_type = 'new';
				$u_image = '<img src="/img/stakes/'.$u_type.'_user.gif" align="absmiddle"hspace="3">';
				break;
								
			case '1':
				$control = '<img src="'.$this->root_path.'img/stakes/circ.gif" onMouseOver="this.src=\'/img/stakes/circ_on.gif\'" onMouseOut="this.src=\'/img/stakes/circ.gif\'" style="cursor:pointer; border:1px solid #ddd;">';
				$status = '
				<a href="'.$this->root_path.'?mod=stakes&sw=complete&result=yes&id='.$info['id'].'">
				'.$control.'</a> &nbsp; 
				<a href="'.$this->root_path.'?mod=stakes&sw=complete&result=no&id='.$info['id'].'">
				<img src="'.$this->root_path.'img/stakes/circ.gif" onMouseOver="this.src=\'/img/stakes/circ_no.gif\'" onMouseOut="this.src=\'/img/stakes/circ.gif\'" style="cursor:pointer; border:1px solid #ddd;"></a>';
				if($this->has_new) {
					
					$out = '<tr class="stakesRow"><td colspan="7"><br>
					<b style="color:#999;">ПРИНЯТЫЕ СТАВКИ:</b>&nbsp;</td></tr>
					'.$this->stakes_header;
					$this->has_new = false;
				}
				break;
		}
		$lot_date = explode('-',$info['date']);
				
		$out .= '
		<tr'.$this->bg.' class="stakesRow">';
		$out .= '<td>'.$lot_date[2].','.$this->month($lot_date[1]).'</td>';
		$out .= '<td>'.$info['lot'].'</td>';
		$out .= '<td>'.$info['auc'].'</td>';
		$out .= '<td>'.$info['sum'].$maximum.'</td>';
		$out .= '<td>'.$info['desc'].'&nbsp;'.$matches.'</td>';
		$out .= '<td>'.$u_image.'<a href="'.$this->root_path.'?mod=clients&sw=form&customer_id='.$info['user'].'">'.$info['client_name'].'</a>&nbsp;</td>';
		$out .= '<td align="right">'.$status.'</td>';
			
		return $out.'
		</tr>
		<tr class="stakesRow"><td colspan="7"><img src="/img/ccl/sp.gif" height="5"></tr>';
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
		'.$this->error.'
		'.$content.'
		</div>';
	}
	
	function drawMenu() {
		$list = 'class="stakesBlur"';
		$archive = 'class="stakesBlur"';
		if($_GET['sw']=='archive') {
			$archive = 'class="stakesTabSel"';
		}
		else $list = 'class="stakesTabSel"';
		$this->menu = '
			<td '.$list.' align="center" width="150" onClick="document.location=\'/?mod=stakes\'"><img src="/imgs/sp.gif" height="24" width="1" align="absmiddle">АКТИВНЫЕ СТАВКИ</td>
			<td '.$archive.' align="center" width="150" onClick="document.location=\'/?mod=stakes&sw=archive\'">АРХИВ СТАВОК</td>
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
	}
	
	function changeStatus($status) {
		if(count($this->change)>0) {
			foreach ($this->change as $k=>$v) {
				$request .= "`id` = '".$v."' OR ";
			}
			
			$this->mysqlQuery("
			UPDATE `ccl_".ACCOUNT_SUFFIX."stakes_list` 
			SET `status` = '".$status."' 
			WHERE ".rtrim($request, ' OR '));
		}
	}
	
	// принимаем ставку
	function acceptStake() {
		if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id'])!=0) {
			$this->change = array('0'=> intval($_GET['id']));
			$this->changeStatus(1);
		}	
		$this->getNewStakesNumber();	
	}
	
	function rejectStake() {
		if(isset($_GET['id']) and intval($_GET['id'])!='' and intval($_GET['id'])!=0) {
			$this->mysqlQuery("
				UPDATE `ccl_".ACCOUNT_SUFFIX."stakes_list` 
				SET `status` = '9' 
				WHERE `id` = '".intval($_GET['id'])."'");
		}
		$this->getNewStakesNumber();
	}
	
	function compareStakes() {
		$active_stakes = $this->mysqlQuery("
		SELECT * FROM `ccl_".ACCOUNT_SUFFIX."stakes_list`
		WHERE `status` = '0'
		OR `status` = '1' 
		ORDER BY `auction` ASC,
		`lot` ASC");
		$num = mysql_num_rows($active_stakes);
		$arrStakes = array();
		$i = 0;
		while($i<$num) {
			$line = mysql_fetch_array($active_stakes);
			$arrStakes[$i] =  $line;
			$i++;
		}
		$matches = array();
		
		$i = 0;
		while($i<$num) {
			if($arrStakes[$i]['auction']==$arrStakes[($i+1)]['auction']) {
				// одинаковый аукцион
				if($arrStakes[$i]['lot']==$arrStakes[($i+1)]['lot']) {
					// одинаковый лот
					// сохраняем совпадение в массив совпадений
					$matches[$arrStakes[$i]['id']] = $arrStakes[$i+1]['id'];
					$this->findMaximumStake($arrStakes, $arrStakes[$i]['lot']);
				}
			}
			$i++;
		}
		return $matches;
	}
	
	function findMaximumStake($stakes, $lot) {
		$i = 0;
		while($i<count($stakes)) {
			$next = $i + 1;
			if($next<count($stakes)) 
				if($stakes[$i]['lot']==$lot and $stakes[$next]['lot']==$lot) {
					
					if($stakes[$i]['sum']>$stakes[$next]['sum'] and $stakes[$i]['sum']>$this->topStakes[$lot]['sum'])
						$this->topStakes[$lot] = array('id'=>$stakes[$i]['id'], 'sum'=>$stakes[$i]['sum']);						
											
					elseif($stakes[$i]['sum']<$stakes[$next]['sum'] and $stakes[$next]['sum']>$this->topStakes[$lot]['sum']) 
						$this->topStakes[$lot] = array('id'=>$stakes[$next]['id'], 'sum'=>$stakes[$next]['sum']);
					elseif($stakes[$i]['sum']==$stakes[$next]['sum'] and $stakes[$i]['sum']>$this->topStakes[$lot]['sum']) $this->topStakes[$lot] = array('id'=>'same', 'sum' => 0);
				}
			$i++;
		}
		
	}
	
	function completeStake() {
		switch($_GET['result']) {
			case 'yes':
				$this->change = array('0' => intval($_GET['id']));
				$status = 3;
				break;
			case 'no':
				$this->change = array('0' => intval($_GET['id']));
				$status = 2;
				break;
		}
		$this->changeStatus($status);
	}
	
}

?>