<?php
class showSold extends Proto {
	
	public function makePage() {
		if($this->checkAuth()) {
			$this->showSoldCars();
		}
		else echo 'reloadpage';
	}

	public function showSoldCars() {
		if(isset($_GET['set']) and $_GET['set']!=''){
			if($_GET['set']=='0'){
				$_SESSION['show_sold'] = 'hide';
				echo 'set hide';
			}
			if($_GET['set']=='1'){
				$_SESSION['show_sold'] = 'show';
				echo 'set show';
			}
		}
	}
}
?>
