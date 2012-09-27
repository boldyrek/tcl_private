<?

class CarsSave extends Proto {

	public function drawContent() {
		if($this->checkAuth()) {
			$this->Process();
		}

		$this->errorsPublisher();
		$this->publish();
	}

	private function Process() {
		require_once($_SERVER['DOCUMENT_ROOT'].$this->root_path.'bin/balance.php');

		if($_POST['transporter']==0) $port = '0';
		else $port = $_POST['port'];

			$isdealer = 'isDealer'.$_POST['buyer']; //определяем является ли текущий владелец машины дилером
			$mydealer = 'myDealer'.$_POST['buyer']; //дилер текущего владельца
			$lastowner_isdealer = 'isDealer'.$_POST['last_owner'];
			$lastowner_dealer = 'myDealer'.$_POST['last_owner'];

			if($_POST[$isdealer]==0) $dealer = $_POST[$mydealer];
			else $dealer = $_POST['buyer'];
			
			if($_POST['ready']=='on') { 
				$readiness = '1';
				$date_ready = $_POST['date_ready'];
			}
			else {
				$readiness = '0';
				$date_ready = '0000-00-00';
			}
			
			if(isset($_POST['notice'])) $save_notice = ", notice = '".mysql_real_escape_string($_POST['notice'])."'";
			else $save_notice = '';
			$request = "UPDATE `ccl_".ACCOUNT_SUFFIX."cars` SET buyer = '".intval($_POST['buyer'])."',
						reciever	= '".intval($_POST['reciever'])."',
						model 		= '".mysql_real_escape_string(strtoupper($_POST['model']))."',
						frame 		= '".mysql_real_escape_string(strtoupper(rtrim($_POST['frame'])))."',
						year 		= '".mysql_real_escape_string($_POST['year'])."',
						engine 		= '".mysql_real_escape_string($_POST['engine'])."',
						price_jp	= '".mysql_real_escape_string($_POST['price_jp'])."',
						invoice 	= '".mysql_real_escape_string($_POST['invoice'])."',
						total 		= '".mysql_real_escape_string($_POST['total'])."',
						port 		= '".intval($_POST['port'])."',
						dealer 		= '".mysql_real_escape_string($dealer)."',
						transporter 	= '".intval($_POST['transporter'])."',
						weight 		= '".mysql_real_escape_string($_POST['weight'])."',
						prepay 		= '".intval($_POST['prepay'])."',
						buy_date 	= '".mysql_real_escape_string($_POST['buy_date'])."',
						volume		= '".mysql_real_escape_string($_POST['volume'])."',
						milage		= '".intval($_POST['milage'])."'".$save_notice.",
						place_id1 	= '".intval($_POST['place1'])."',
						place_id2 	= '".intval($_POST['place2'])."',
						place_id3 	= '".intval($_POST['place3'])."',
						aucfee		= '".intval($_POST['aucfee'])."',
						dealer_comission 	= '".intval($_POST['dealer_comission'])."',
						cost_to_port		= '".intval($_POST['cost_to_port'])."',
						cost_to_destination	= '".intval($_POST['cost_to_destination'])."',
						unload		= '".intval($_POST['unload'])."',
						insurance	= '".intval($_POST['insurance'])."',
						other		= '".intval($_POST['other'])."',
						inspection 	= '".intval($_POST['inspection'])."',
						auction 	= '".intval($_POST['auction'])."',
						ready 		= '".$readiness."',
						date_ready 	= '".mysql_real_escape_string($date_ready)."',
						date_realydeliver 	= '".mysql_real_escape_string($_POST["date_realydeliver"])."',
						allow_inspection = '".intval($_POST['allow_inspection'])."',
						allow_codocs = '".intval($_POST['allow_codocs'])."',
						type = '".intval($_POST['type'])."'
						WHERE `id` 	= '".intval($_GET['id'])."'";
			$this->mysqlQuery($request);
			
			//обновление баланса текущего владельца/дилера и прошлого владельца/прошлого дилера
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
			if($_POST['transporter']!=0) updateSupplierBalance(intval($_POST['transporter']));

			$this->redirect($this->root_path.'?mod=cars&sw=form&car_id='.intval($_GET['id']).'&success');
	}
}
?>