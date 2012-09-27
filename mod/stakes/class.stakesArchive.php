<?

class stakesArchive extends Stakes {
	
	function drawArchive() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->localMenu();
			$this->listArchive();
		}
		
		$this->page .= $this->templates['footer'];
		
		if($this->page=='') $this->errorHandler('Пустая страница!', 0);
		
		$this->errorsPublisher();
		$this->publish();
	}
	
	function listArchive() {
		
		$content = $this->mysqlQuery("
		SELECT ccl_".ACCOUNT_SUFFIX."stakes_list.*, ccl_".ACCOUNT_SUFFIX."stakes_auc.name as auc, ccl_".ACCOUNT_SUFFIX."customers.name as client_name, ccl_".ACCOUNT_SUFFIX."customers.approved
		FROM `ccl_".ACCOUNT_SUFFIX."stakes_list` 
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."stakes_auc` 
		ON (ccl_".ACCOUNT_SUFFIX."stakes_auc.id = ccl_".ACCOUNT_SUFFIX."stakes_list.auction)
		LEFT JOIN `ccl_".ACCOUNT_SUFFIX."customers`
		ON (ccl_".ACCOUNT_SUFFIX."customers.id = ccl_".ACCOUNT_SUFFIX."stakes_list.user)
		WHERE ccl_".ACCOUNT_SUFFIX."stakes_list.status != '0'
		AND ccl_".ACCOUNT_SUFFIX."stakes_list.status != '1'
		ORDER BY ccl_".ACCOUNT_SUFFIX."stakes_list.created DESC");
		
		$num = @mysql_num_rows($content);
		if($num>0) {
			$this->stakes_header = '
			<tr class="stakesRow" style="background-color:#ddd;">
				<td width="50">дата</td>
				<td width="50">лот №</td>
				<td width="160">аукцион</td>
				<td width="50">ставка</td>
				<td width="150">описание</td>
				<td>клиент</td>
				<td width="100" align="right">результат</td>
			</tr>';
			
			$i = 0;
			while($i<$num) {
				$line = mysql_fetch_array($content);
				$stakes .= $this->drawArchiveRow($line);
				$i++;
			}
			
			$this->list .= '<table width="100%" cellpadding="2" cellspacing="0">
			'.$this->stakes_header.'
			'.$stakes.'
			</table>';
			}
			else $this->list .= 'Не найдено активных ставок!';
			$this->contentWrap($this->list);
		
	}
	
	function drawArchiveRow($info) {
		if($this->bg=='') $this->bg = ' bgcolor="#efefef"';
		else $this->bg = '';
				
		switch ($info['status']) {
			case '0':
				
				break;
								
			case '1':
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
		';
	}
	
	

}

?>