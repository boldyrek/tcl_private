<?

//меню для администраторов
$menu['1'] = array(
			array('item'=>	'clients',		'text'=>'КЛИЕНТЫ'),
			array('item'=>	'cars',			'text'=>'АВТОМОБИЛИ'),
			array('item'=>	'carstobuy',	'text'=>'МАШИНЫ К&nbsp;ПОКУПКЕ'),
			array('item'=>	'invoices',		'text'=>'ИНВОЙСЫ'),
			array('item'=>	'contracts',	'text'=>'КОНТРАКТЫ'),
			array('item'=>	'containers',	'text'=>'КОНТЕЙНЕРЫ'),
			array('item'=>	'payments',		'text'=>'ПЛАТЕЖИ'),
			array('item'=>	'transporters',	'text'=>'ТРАНСПОРТНИКИ'),
			array('item'=>	'expeditors',	'text'=>'ЭКСПЕДИТОРЫ'),
			);

$menu['7'] = $menu['1'];
$menu['5'] = $menu['1'];

// меню для оператора
$menu['3'] = array(
			array('item'=>'clients','text'=>'КЛИЕНТЫ'),
				array('item'=>'cars','text'=>'АВТОМОБИЛИ'),
				array('item'=>'containers','text'=>'КОНТЕЙНЕРЫ'),
				array('item'=>'invoices','text'=>'ИНВОЙСЫ'),
				array('item'=>'contracts','text'=>'КОНТРАКТЫ'));
				
// меню для менеджера
$menu['4'] = array(
			array('item'=>'clients','text'=>'КЛИЕНТЫ'),
				array('item'=>'cars','text'=>'АВТОМОБИЛИ'),
				array('item'=>'containers','text'=>'КОНТЕЙНЕРЫ'),
				array('item'=>'invoices','text'=>'ИНВОЙСЫ'),
				array('item'=>'contracts','text'=>'КОНТРАКТЫ'));

//меню для пользователей
$menu['2'] = array(
				array('item'=>'cars','text'=>'АВТОМОБИЛИ'),
				array('item'=>'payments','text'=>'ПЛАТЕЖИ'),
				array('item'=>'balance','text'=>'БАЛАНС'),
				array('item'=>'private','text'=>'ЛИЧНЫЕ ДАННЫЕ'));

//меню для транспортников
$menu['8'] = array(
				array('item'=>'cars','text'=>'АВТОМОБИЛИ'));

//меню для экспедиторов
$menu['9'] = array(
				array('item'=>'cars','text'=>'АВТОМОБИЛИ'));
								
?>