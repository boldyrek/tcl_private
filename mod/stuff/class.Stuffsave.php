<?

class StuffSave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
			
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
		require_once($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');

		if($_POST['transporter_id']==0) $port = '0';
		else $port = intval($_POST['port_id']);

/*			$isdealer = 'isDealer'.$_POST['buyer']; //определяем является ли текущий владелец машины дилером
			$mydealer = 'myDealer'.$_POST['buyer']; //дилер текущего владельца
			$lastowner_isdealer = 'isDealer'.$_POST['last_owner'];
			$lastowner_dealer = 'myDealer'.$_POST['last_owner'];

			if($_POST[$isdealer]==0) $dealer = $_POST[$mydealer];
			else $dealer = $_POST['buyer'];
			
			if($_POST['ready']=='on') { 
				$readiness = '1';
				$date_ready = $_POST['date_buy'];
			}
			else {
				$readiness = '0';
				$date_ready = '0000-00-00';
			}
			*/
			if(isset($_POST['notice'])) $save_notice = ", notice = '".mysql_real_escape_string($_POST['notice'])."'";
			else $save_notice = '';
			//var_dump($_POST);exit;
			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."stuff` SET buyer = '".intval($_POST['buyer'])."',
						name 		= '".mysql_real_escape_string(strtoupper($_POST['name']))."',
						count 		= '".intval($_POST['count'])."',
						serials		= '".mysql_real_escape_string($_POST['serials'])."',
						transporter_id 	= '".intval($_POST['transporter_id'])."',
						port_id 	= '".$port."',												
						date_buy 	= '".mysql_real_escape_string($_POST['date_buy'])."',
						post_id 	= '".intval($_POST['post_id'])."',							
						place_in 	= '".intval($_POST['place_in'])."',							
						price  	= '".floatval($_POST['price'])."',							
						delivery  	= '".floatval($_POST['delivery'])."',							
						other  	= '".floatval($_POST['other'])."',							
						invoice  	= '".floatval($_POST['invoice'])."',							
						current_place  	= '".intval($_POST['current_place'])."',							
						deliveried  	= '".($_POST['deliveried']=='on'?1:0)."',
						inspection 	= '".intval($_POST['inspection'])."',													
						allow_inspection = '".intval($_POST['allow_inspection'])."',
						allow_codocs = '".intval($_POST['allow_codocs'])."'
						WHERE id=".intval($_GET['id']);
		//	var_dump($request); exit;
			$this->mysqlQuery($request);
			if (intval($_POST['post_id'])!=-1)
			{
				$this->mysqlQuery("DELETE FROM ccl_".ACCOUNT_SUFFIX."stuff_container WHERE id_stuff='".intval($_GET['id'])."'");
				$this->mysqlQuery("UPDATE `ccl_".ACCOUNT_SUFFIX."stuff` SET container='0' WHERE id='".intval($_GET['id'])."'");				
			}

			/*//обновление баланса текущего владельца/дилера и прошлого владельца/прошлого дилера
			if($_POST['total']!=$_POST['lastPrice'] or $_POST['buyer']!=$_POST['last_owner'] or $_POST['prepay']!=$_POST['lastPrepay']) {
				if($_POST['buyer']==$_POST['last_owner'] and $_POST['last_owner']!='') {
					if($_POST[$isdealer]==0) {
						updateBalance($_POST['buyer'],0);
						if($_POST[$mydealer]!=0) updateBalance($_POST[$mydealer],1);
					}
					elseif($_POST[$isdealer]==1) updateBalance($_POST['buyer'],1);
				}
				elseif($_POST['buyer']!=$_POST['last_owner'] and $_POST['last_owner']!='') {
					if($_POST[$isdealer]==1) updateBalance($_POST['buyer'],1);
					elseif($_POST[$isdealer]==0) {
						updateBalance($_POST['buyer'],0);
						if($_POST[$mydealer]!=0) updateBalance($_POST[$mydealer],1);
					}
					if($_POST[$lastowner_isdealer]==1 and $_POST[$lastowner_dealer]!=$_POST[$mydealer]) updateBalance($_POST['last_owner'],1);
					elseif($_POST[$lastowner_isdealer]==0) {
						updateBalance($_POST['last_owner'],0);
						if($_POST[$lastowner_dealer] != 0 and $_POST[$lastowner_dealer]!=$_POST['buyer']) updateBalance($_POST[$lastowner_dealer], 1);
					}
				}
				elseif($_POST['buyer']!=0 and $_POST['last_owner']=='') {
					if($_POST[$isdealer]==1) updateBalance($_POST['buyer'],1);
					elseif($_POST[$isdealer]==0) {
						updateBalance($_POST['buyer'],0);
						if($_POST[$mydealer]!=0) updateBalance($_POST[$mydealer],1);
					}
				}
				elseif($_POST['buyer']==0 and $_POST['last_owner']!='') {
					if($_POST[$lastowner_isdealer]==1) updateBalance($_POST['last_owner'],1);
					elseif($_POST[$lastowner_isdealer]==0) {
						updateBalance($_POST['last_owner'],0);
						if($_POST[$lastowner_dealer] != 0) updateBalance($_POST[$lastowner_dealer], 1);
					}
				}
			}

			//обновление баланса поставщика
			if($_POST['transporter']!=0) updateSupplierBalance(intval($_POST['transporter']));*/

			$this->redirect($this->root_path.'?mod=stuff&sw=form&stuff_id='.intval($_GET['id']).'&success');
	}
}
?>