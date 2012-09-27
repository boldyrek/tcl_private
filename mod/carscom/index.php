<?
if(isset($_SESSION['user_type']) and $_SESSION['user_type']=='11') header('Location: /public');

class CarscomList extends Proto {

	public function drawContent() {
		$this->page .= $this->templates['header'];
		if($this->checkAuth()) {
			$this->page .= $this->makeTopMenu();
			$this->getContent();
		}
		$this->page .= $this->templates['footer'];

		$this->errorsPublisher();
		$this->publish();
	}

	public function getContent(){
		$i = 0;
		$this->page .= '<div style="width: 960px;" class="location">Cars.com cars</div>';
		$this->page .= '<table width="970" cellspacing="0" cellpadding="0" border="0" class="list vlines">';
		$this->page .= '<tr class="title sortButtons"><td>id</td><td>Model</td><td>VIN</td><td>Year</td><td>Price</td><td>Mileage</td><td>Exterior color</td><td>Interior color</td><td>Navigation</td><td>Date</td><td>Link</td></tr>';
		$res = $this->mysqlQuery("SELECT * FROM ccl_carscom_cars INNER JOIN ccl_carscom_vins ON ccl_carscom_cars.id = ccl_carscom_vins.cardata ORDER BY ccl_carscom_cars.date DESC");
		while ($r = mysql_fetch_assoc($res)){
			$gps = $r['gps'] ? 'navigation' : '&nbsp;';
			$class = ($i % 2) == 1 ? 'rowA rowB' : 'rowA';
//			$car_link = urldecode($r['link']);
			$car_link = $r['link'];
			$details_link = $car_link=='' ? 'No Details' : "<a href='{$car_link}' target='_blank'>Details...</a>";
			$this->page .=  '<tr onmouseout="this.className=\''.$class.'\'" onmouseover="this.className=\'rowA hovered\'" class="rowA rowB">'.
					"<td class='sm'>{$r['id']}</td><td class='sm'>{$r['name']}</td><td class='sm'>{$r['vin']}</td><td class='sm'>{$r['year']}</td>".
					"<td class='sm'>{$r['price']}</td><td class='sm'>{$r['mileage']}</td><td class='sm'>{$r['extcolor']}</td>".
					"<td class='sm'>{$r['intcolor']}</td><td class='sm'>$gps</td><td class='sm'>{$r['date']}</td><td class='sm'>{$details_link}</td></tr>";
		}
		
		$this->page .= '</table>';
		$i++;
	}
}

$page = new CarscomList();
$page -> drawContent();

?>