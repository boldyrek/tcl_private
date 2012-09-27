<?

//таблица прав доступа
//администратор
$matrix['1'] = array('type' => '1',
   'home' => '1',
   'edit' => '1',
   'clients' => '1',
   'cars' => '1',
   'stuff' => '1',
   'containers' => '1',
   'transporters' => '1',
   'expeditors' => '1',
   'accounting' => '1',
   'payments' => '1',
   'expenses' => '1',
   'users' => '1',
   'reports' => '1',
   'invoices' => '1',
   'service' => '1',
   'sale' => '1',
   'contracts' => '1',
   'certificates' => '1',
   'client_cars' => '1',
   'backup' => '1',
   'fileshare' => '1',
   'ports' => '1',
   'carstobuy' => '1',
   'con_reports' => '1',
   'cars_reports' => '1',
   'carriages' => '1',
   
   );
//пользователь
$matrix['2'] = array('type' => '2',
   'edit' => '0',
   'clients' => '0',
   'cars' => '1',
   'containers' => '0',
   'payments' => '1',
   'private' => '1',
   'invoices' => '0',
   'service' => '0',
   'balance' => '1',
   'stakes' => '1');

//оператор
$matrix['3'] = array('type' => '4',
   'edit' => '1',
   'clients' => '1',
   'cars' => '1',
   'containers' => '1',
   'transporters' => '0',
   'expeditors' => '0',
   'payments' => '0',
   'users' => '0',
   'invoices' => '0',
   'service' => '0',
   'reports' => '0',
   'sale' => '1',
   'contracts' => '1',
   'certificates' => '1',
   'client_cars' => '1');

//менеджер
$matrix['4'] = array('type' => '4',
   'edit' => '1',
   'clients' => '1',
   'cars' => '1',
   'containers' => '1',
   'transporters' => '0',
   'expeditors' => '0',
   'payments' => '0',
   'users' => '0',
   'invoices' => '0',
   'service' => '0',
   'reports' => '0',
   'sale' => '1',
   'contracts' => '1',
   'certificates' => '1',
   'client_cars' => '1',
   'fileshare' => '1');
//тренер Tokidoki
$matrix['5'] = array('type' => '5',
   'edit' => '1',
   'clients' => '1',
   'cars' => '1',
   'containers' => '1',
   'transporters' => '1',
   'expeditors' => '1',
   'payments' => '1',
   'users' => '0',
   'invoices' => '0',
   'service' => '0',
   'reports' => '0',
   'sale' => '1',
   'contracts' => '1',
   'certificates' => '1');
// пользователь для просмотра отчетов
$matrix['6'] = array('type' => '6',
   'con_reports' => '1',
   'cars_reports' => '1',
   'clients' => '1',
   'cars' => '1',
   'stuff' => '1',
   'containers' => '1',
   'transporters' => '1',
   'payments' => '1',
   'users' => '1',
   'reports' => '1',
   'invoices' => '1',
   'service' => '1',
   'sale' => '1',
   'contracts' => '1',
   'certificates' => '1',
   'client_cars' => '1',
   'backup' => '1',
   'fileshare' => '1',
   'ports' => '1',
   'carstobuy' => '1');

//скрытый админ
$matrix['7'] = array('type' => '7',
   'stuff' => '1',
   'clients' => '1',
   'cars' => '1',
   'containers' => '1',
   'transporters' => '1',
   'expeditors' => '1',
   'payments' => '1',
   'users' => '1',
   'reports' => '1',
   'invoices' => '1',
   'service' => '1',
   'sale' => '1',
   'contracts' => '1',
   'certificates' => '1',
   'client_cars' => '1',
   'backup' => '1',
   'carstobuy' => '1',
   'fileshare' => '1',
   'ports' => '1');

//Транспортник
$matrix['8'] = array('type' => '8',
   'cars' => '1');
//Экспедитор
$matrix['9'] = array('type' => '9',
   'cars' => '1');

// таможенный брокер
$matrix['10'] = array('type' => '10',
   'clients' => '1',
   'containers' => '1'
);

// Админ. базы Ямато
$matrix['11'] = array('type' => '11',
   'edit' => '0',
   'clients' => '0',
   'cars' => '1',
   'containers' => '0',
   'payments' => '1',
   'private' => '1',
   'invoices' => '0',
   'service' => '0',
   'balance' => '1',
   'stakes' => '1');

$matrix['12'] = array(
   'type' => '12',
   'home' => '1',
   'cars' => '1',
   'transporters' => '1',
);

$matrix['13'] = array(
   'type' => '13',
   'home' => '1',
   'containers' => '1',
);


//типы пользователей
$user_types[1] = 'Администратор';
$user_types[2] = 'Клиент / Дилер';
$user_types[3] = 'Оператор';
$user_types[4] = 'Менеджер';
$user_types[5] = 'Поставщик';
$user_types[6] = 'Отчеты';
$user_types[8] = 'Транспортник';
$user_types[9] = 'Экспедитор';
$user_types[10] = 'Брокер';
$user_types[11] = 'Администратор базы Ямато';
$user_types[12] = 'Менеджер';
$user_types[13] = 'Forwarding agent';
