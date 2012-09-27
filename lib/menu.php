<?

$translate = Zend_Registry::get('translation');

//меню для администраторов
$menu['1'] = array(
   array('item' => 'home', 'text' => $translate->_('ГЛАВНАЯ')),
   array('item' => 'clients', 'text' => $translate->_('КЛИЕНТЫ')),
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')),
   array('item' => 'stuff', 'text' => $translate->_('ТОВАРЫ')),
   array('item' => 'carstobuy', 'text' => $translate->_('МАШИНЫ К ПОКУПКЕ')),
   array('item' => 'invoices', 'text' => $translate->_('ИНВОЙСЫ')),
   array('item' => 'contracts', 'text' => $translate->_('КОНТРАКТЫ')),
   array('item' => 'containers', 'text' => $translate->_('КОНТЕЙНЕРЫ')),
   array('item' => 'carriages', 'text' => $translate->_('ВАГОНЫ')),
   array('item' => 'accounting', 'text' => $translate->_('БУХГАЛТЕРИЯ')),
   /* 			array('item'=>	'payments',		'text'=>'ПЛАТЕЖИ<br><span style="color:#aaa;text-decoration:none">совместимость</span>'),
     array('item'=>	'expenses',		'text'=>'РАСХОДЫ<br><span style="color:#aaa;text-decoration:none">совместимость</span>'),
    */ array('item' => 'transporters', 'text' => $translate->_('ТРАНСПОРТНИКИ')),
   array('item' => 'expeditors', 'text' => $translate->_('ЭКСПЕДИТОРЫ')),
);

$menu['7'] = $menu['1'];
$menu['5'] = $menu['1'];

// меню для оператора
$menu['3'] = array(
   array('item' => 'clients', 'text' => $translate->_('КЛИЕНТЫ')),
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')),
   array('item' => 'containers', 'text' => $translate->_('КОНТЕЙНЕРЫ')),
   array('item' => 'invoices', 'text' => $translate->_('ИНВОЙСЫ')),
   array('item' => 'contracts', 'text' => $translate->_('КОНТРАКТЫ')));

// меню для менеджера
$menu['4'] = array(
   array('item' => 'clients', 'text' => $translate->_('КЛИЕНТЫ')),
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')),
   array('item' => 'containers', 'text' => $translate->_('КОНТЕЙНЕРЫ')),
   array('item' => 'invoices', 'text' => $translate->_('ИНВОЙСЫ')),
   array('item' => 'contracts', 'text' => $translate->_('КОНТРАКТЫ')));

//меню для пользователей
$menu['2'] = array(
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')),
   array('item' => 'payments', 'text' => $translate->_('ПЛАТЕЖИ')),
   array('item' => 'balance', 'text' => $translate->_('БАЛАНС')),
   array('item' => 'private', 'text' => $translate->_('ЛИЧНЫЕ ДАННЫЕ')));
// Меню для Администратора базы Ямато
$menu['11'] = $menu['2'];

// меню пользователя отчетами
$menu['6'] = array(
   array('item' => 'clients', 'text' => $translate->_('КЛИЕНТЫ')),
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')),
   array('item' => 'stuff', 'text' => $translate->_('ТОВАРЫ')),
   array('item' => 'carstobuy', 'text' => $translate->_('МАШИНЫ К ПОКУПКЕ')),
   array('item' => 'invoices', 'text' => $translate->_('ИНВОЙСЫ')),
   array('item' => 'contracts', 'text' => $translate->_('КОНТРАКТЫ')),
   array('item' => 'containers', 'text' => $translate->_('КОНТЕЙНЕРЫ')),
   array('item' => 'payments', 'text' => $translate->_('ПЛАТЕЖИ')),
   array('item' => 'cars_reports', 'text' => $translate->_('ОТЧЕТЫ ПО АВТОМОБИЛЯМ')),
   array('item' => 'con_reports', 'text' => $translate->_('ОТЧЕТЫ ПО КОНТЕЙНЕРАМ')));


//меню для транспортников
$menu['8'] = array(
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')));

//меню для экспедиторов
$menu['9'] = array(
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')));

// таможенный брокер
$menu['10'] = array(
   array('item' => 'clients', 'text' => $translate->_('КЛИЕНТЫ')),
   array('item' => 'containers', 'text' => $translate->_('КОНТЕЙНЕРЫ')));

$menu['12'] = array(
   array('item' => 'home', 'text' => $translate->_('ГЛАВНАЯ')),
   array('item' => 'cars', 'text' => $translate->_('АВТОМОБИЛИ')),
   array('item' => 'transporters', 'text' => $translate->_('ТРАНСПОРТНИКИ')),
);

$menu['13'] = array(
   // array('item' => 'home', 'text' => $translate->_('ГЛАВНАЯ')),
   array('item' => 'containers', 'text' => $translate->_('КОНТЕЙНЕРЫ')),
);