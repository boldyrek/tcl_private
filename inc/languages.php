<?
$LangHash = array(
	// Массив Русского языка
	"rus" => array(
		// Мелкие часто используемые словечки :)
		'and' => 'и',
		
		// class.Proto.php # makeTopMenu # Top Menu
		'top_menu_hello' => 'Привет',
		'top_menu_exit' => 'выйти',
		'top_menu_autotrade' => 'продажа автомобилей',
		'top_menu_stufftrade' => 'продажа товаров',
		'top_menu_auct_list' => 'аукционы',
		'top_menu_reports' => 'отчеты',
		'top_menu_auto' => 'авто',
		'top_menu_containers' => 'контейнеры',
		'top_menu_users' => 'пользователи',
		'top_menu_backup' => 'резервное копирование',
		'top_menu_files' => 'файлы',
		'top_menu_error_no_access' => 'У вас нет доступа для просмотра этой страницы!',
		'top_menu_error_no_menu' => 'Для данного типа пользователя меню не существует!',
		
		// class.Proto.php # getMenu # Get Menu
		'get_menu_error_no_menu_file' => 'Не найден файл содержащий меню пользователей',
		'get_menu_error_no_rights_file' => 'Не найден файл с правами пользователей!',
		
		// class.Proto.php # pageBrowse # Browse Pages
		'pagebrowse_next_page' => 'на следующую страницу',
		'pagebrowse_last_page' => 'на последнюю страницу',
		//----------------------------------------------

		// class.auctionsform.php # getContent # 
		'auction_error_not_in_base' => 'Ошибка! Аукцион с такими параметрами в базе не обнаружен',
		'auction_change_saved' => 'Изменения сохранены',
		'auction_auction' => 'Аукцион',
		'auction_name' => 'название',
		'auction_address' => 'адрес',
		'auction_phone' => 'телефоны',
		'auction_comment' => 'комментарии',
		'auction_confirm_realy_delete' => 'Вы действительно хотите удалить этот аукцион?',
		'auction_delete' => 'удалить',
		'auction_save' => 'Сохранить',
		
		// class.BackupTemplates.php # Backups
		'backup_ask_delete' => 'удалить',
		'backup_download' => 'скачать',
		'backup_autosave' => 'автосохранение',
		'backup_backup' => 'Резервное копирование',
		'backup_create' => 'Создать',
		'backup_upload' => 'Загрузить (и Восстановить)',
		'backup_nobackups' => 'Резервных копий нет',
		'backup_data_lines_saved' => 'Сохранено строк данных',
		'backup_total_data_volume' => 'Суммарный объем данных',
		'backup_title_autosave' => 'Автосохранение',
		'backup_restored_from_backup' => 'Востановленно из резервной копии',
		'backup_restored_from_uploaded_backup' => 'Востановленно из загруженной резервной копии',
		'backup_data_lines' => 'Строк данных',
		'backup_data_volume' => 'Объем данных',
		
		//	class.Backupcreate.php
		'backup_restore_data_by_date' => 'Восстановить данные из резервной копии за',
		'backup_restore_data_by_date_description' => 'все данные после это даты будут утеряны, <br>но их можно будет восстановить из резервной копии которая будет создана сейчас.',
		'backup_restore' => 'Восстановить',
		'backup_create_new_backup' => 'Создать новую резервную копию?',
		
		// class.Backupload.php
		'backup_restore_data_from_file' => 'Восстановить данные из файла резервной копии?',
		'backup_restore_data_from_file_description' => 'все данные внесенные после создания загружаемой копии будут утеряны, <br>но их можно будет восстановить из резервной копии которая будет создана сейчас.',
		
		
		// class.Carscomment.php # AddEditComment
		'comment_fill_comment_text' => 'Заполните текст комментария',
		'comment_comment_saved' => 'Комментарий сохранен',
		'comment_comment_was_not_saved' => 'Комментарий сохранить не удалось',
		
		// class.Carsform.php # getContent
		'carsform_error_there_is_no_car' => 'Ошибка! Автомобиль с такими параметрами в базе не обнаружен',
		'carsform_changes_saved' => 'Изменения сохранены',
		'carsform_car_with_vin_code' => 'Автомобиль с таким вин кодом',
		'carsform_already_in_a_base' => 'уже есть в базе',
		'carsform_owner' => 'владелец',
		'carsform_not_specified' => 'не выбран',
		'carsform_not_specifiedo' => 'не выбрано',
		'carsform_empty_field' => 'пусто поле',
		'carsform_photo_realy_delete' => 'Вы действительно хотите удалить это фото?',
		'carsform_cr_realy_delete' => 'Вы действительно хотите удалить Отчет о состоянии автомобиля или CR?',
		'carsform_delete' => 'удалить',
		'carsform_expeditors_photos' => 'Фотографии экспедитора',
		'carsform_no_ports' => ' нет портов! ',
		'carsform_no_invoice' => 'нет инвойса',
		'carsform_time_left_from_buy' => 'с покупки прошло',
		'carsform_left' => 'прошло',
		'carsform_ready_date' => 'дата готовности',
		'carsform_ready_to_send' => 'готова к отправке',
		'carsform_realy_delete' => 'Действительно удалить?',
		'carsform_realy_delete_postsell_inspection' => 'Вы действительно хотите удалить После Продажную инспекцию?',
		'carsform_realy_delete_file' => 'Вы действительно хотите удалить этот файл?',
		'carsform_realy_delete_support_doc' => 'Вы действительно хотите удалить Сопроводительный документ?',
		'carsform_user_created_and_invited' => 'Пользователь создан.<br>Приглашение клиенту успешно отправлено.',
		'carsform_user_created_but_not_invited' => 'Пользователь создан.<br>Приглашение клиенту отправить НЕ удалось!',
		'carsform_user_not_created_and_not_invited' => 'Пользователь не создан! <br>Приглашение клиенту НЕ было отправлено! <br>Возможно у него не указаны email или имя.',
		'carsform_user_already_exists' => 'У этого клиента уже есть логин. Приглашение не требуется.',
		
		// Форма добавления платежа
		'carsform_payment_by_owner' => 'Платеж от имени владельца',
		'carsform_date' => 'Дата',
		'carsform_summ' => 'Сумма',
		'carsform_created' => 'Создал',
		'carsform_comment' => 'Комментарий',
		'carsform_add' => 'Добавить',
		'carsform_auto_payments' => 'Платежи по автомобилю',
		'carsform_add_service' => 'Добавить услугу',
		'carsform_add_payment' => 'добав. <b>приход</b>',
		'carsform_total_payments' => 'всего платежей',
		'carsform_no_payments_for_auto' => 'Нет платежей для данного автомобиля',
		
		// Форма добавления расхода
		'carsform_expense_for_auto' => 'Расход на автомобиль',
		'carsform_expenses_by_auto' => 'Расходы по автомобилю',
		'carsform_expense_purpose' => 'Назначение расхода',
		'carsform_expense_paid' => 'Оплачено?',
		'carsform_add_expense' => 'добав. <b>расход</b>',
		'carsform_signed' => 'Подтв.',
		'carsform_paid' => 'Оплачено',
		'carsform_total_expenses' => 'всего расходов',
		'carsform_no_expenses_for_auto' => 'Нет расходов для данного автомобиля',
		
		// Форма добавления и того и того :) (бухгалтерия)
		'carsform_accounting_for_auto' => 'Запись по автомобилю (расход-приход)',
		'carsform_accounting_by_auto' => 'Балланс по автомобилю',
		'carsform_expense_purpose' => 'Назначение расхода',
		'carsform_expense_paid' => 'Оплачено?',
		'carsform_add_record' => '<b>добавить</b>',
		'carsform_signed' => 'Подтв.',
		'carsform_paid' => 'Оплачено',
		'carsform_total_ballance' => 'балланс',
		'carsform_no_accounting_for_auto' => 'Нет данных для данного автомобиля',
		'carsform_is_expense' => 'Расход',
		'carsform_is_payment' => 'Приход',
		
		// Java-скрипты
		'carsform_confirm' => 'Подтв....',
		'carsform_yes' => 'да',
		'carsform_no' => 'нет',
		'carsform_error' => 'Ошибка!',
		'carsform_unknown' => 'неизвестно',
		
		// Форма информации по автомобилю
		'carsform_automobile' => 'Автомобиль',
		'carsform_full_car_price' => 'всего за автомобиль',
		'carsform_ballance' => 'баланс',
		'carsform_clear_profit' => 'чистая прибыль',
		'carsform_expenses_for_auto' => 'Расходы на автомобиль',
		'carsform_price_in_auction_in_america' => 'цена в Америке на аукционе',
		'carsform_auction_fee' => 'Аукционный сбор',
		'carsform_dealer_fee' => 'Комиссия дилера',
		'carsform_pp_inspection' => 'ПП инспекция',
		'carsform_transportation_to_port' => 'Доставка до порта',
		'carsform_transportation_to_destination' => 'Доставка до места назначения',
		'carsform_unload' => 'Разгрузка',
		'carsform_insurance' => 'Страховка',
		'carsform_other' => 'Прочее',
		'carsform_total' => 'ВСЕГО',
		'carsform_add_client' => 'Добавить клиента',
		'carsform_add_receiver' => 'Добавить получателя',
		'carsform_owner' => 'владелец',
		'carsform_purchase_date' => 'дата покупки',
		'carsform_receiver' => 'получатель',
		'carsform_name' => 'Название',
		'carsform_brand_name' => 'Марка автомобиля',
		'carsform_model' => 'Модель',
		'carsform_add_brand' => 'Добавить марку',
		'carsform_add_model' => 'Добавить модель',
		'carsform_vin_code' => 'вин код',
		'carsform_production_date' => 'дата выпуска',
		'carsform_engine_volume' => 'объем двигателя',
		'carsform_invoice_price' => 'цена в инвойсе',
		'carsform_weight' => 'вес',
		'carsform_container' => 'контейнер',
		'carsform_mileage' => 'пробег',
		'carsform_transporter' => 'транспортник',
		'carsform_port' => 'порт',
		'carsform_date_realy_deliver' => 'дата реальной доставки',
		'carsform_transportation_status' => 'статус транспортировки',
		'carsform_delivered' => 'доставлена',
		'carsform_auto_location' => 'Местонахождение автомобиля',
		'carsform_title' => 'Тайтл',
		'carsform_auction' => 'Аукцион',
		'carsform_destination' => 'место<br>назначения',
		'carsform_invite_client' => 'пригласить клиента',
		'carsform_realy_delete_auto' => 'Вы действительно хотите удалить этот автомобиль?',
		'carsform_sell' => 'Продать',
		'carsform_save' => 'Сохранить',
		
		// Форма выставления авто на продажу
		'carsform_move_to_sell' => 'выставить на продажу',
		'carsform_edit_template' => 'Редактировать шаблоны',
		'carsform_sell_template' => 'шаблон',
		'carsform_sell_comment' => 'комментарий',
		'carsform_sell_price' => 'цена',
		'carsform_sell_sold' => 'продан',
		'carsform_sell_till' => 'выставить до',
		
		// Форма дополнительных файлов
		'carsform_photos' => 'Фотографии',
		'carsform_all_photos' => 'все фотографии',
		'carsform_condition_report' => 'Отчет о состоянии автомобиля или CR',
		'carsform_show_on_site' => 'показывать на сайте',
		'carsform_pp_inspection' => 'ПП инспекция',
		'carsform_show_to_client' => 'показывать клиенту',
		'carsform_accomp_forms' => 'Сопроводительные документы',
		'carsform_upload' => 'Загрузить',
		
		// Скрытые формы добавления клиента и продажи другому клиенту
		'carsform_add_another_client' => 'Добавить еще одного клиента',
		'carsform_close_window' => 'Закрыть окно',
		'carsform_auto_sell' => 'Продажа автомобиля',
		'carsform_seller' => 'Продавец',
		'carsform_buyer' => 'Покупатель',
		'carsform_form_price' => 'Цена',
		'carsform_send' => 'Отправить',
		'carsform_do_you_realy_want_to_change_owner' => 'Вы действительно хотите сменить владельца автомобиля?',
		'carsform_buyer_and_owner_the_same' => 'Покупатель и продавец одно лицо!',
		'carsform_price_not_filled' => 'Не заполнено поле ЦЕНА!',
		
		// Form unknown
		'carsform_order' => 'заказ',
		'carsform_for_sale' => 'на продажу',
		'carsform_decline' => 'отказ',
		'carsform_part' => 'раздел',
		
		'carsform_days' => 'дней',
		
		// class.CarsList.php # CarsList
		
		'carslist_autos' => 'Автомобили',
		'carslist_add' => 'добавить',
		'carslist_places_list' => 'Список мест',
		'carslist_ports_list' => 'Список портов',
		'carslist_by_name' => 'по названию',
		'carslist_by_vincode' => 'по Вин коду',
		'carslist_from' => 'с',
		'carslist_till' => 'по',
		'carslist_brands_list' => 'Список марок',
		'carslist_models_list' => 'Список моделей',
		'carslist_auto_location' => 'место нахождения авто',
		'carslist_auto_destination' => 'место назначения',
		'carslist_in_port' => 'в порту',
		'carslist_owner' => 'владелец',
		'carslist_search' => 'найти',
		'carslist_new' => 'новые',
		'carslist_in_transit' => 'в пути',
		'carslist_archive' => 'архив',
		'carslist_no_search_results' => 'по вашему запросу ничего не найдено',
		'carslist_was_not_choosen' => '- = не выбран = -',
		
		// CarsList  # list
		'carslist_lst_buy_date' => 'дата покупки',
		'carslist_lst_vincode' => 'вин код',
		'carslist_lst_owner' => 'владелец',
		'carslist_lst_model' => 'модель',
		'carslist_lst_container' => 'контейнер',
		'carslist_lst_transporter' => 'транспортник',
		'carslist_lst_port' => 'порт',
		'carslist_lst_auto_location' => 'местонахождение авто',
		'carslist_lst_title_location' => 'местонахождение тайтла',
		'carslist_lst_auction' => 'Аукцион',
		
		
		// class.CommentTemplates.php # Comments template
		'comments_comments' => 'Комментарии',
		'comments_hidden' => 'скрытый',
		'comments_for_all' => 'для всех',
		'comments_author' => 'Автор',
		'comments_date' => 'Дата',
		'comments_regards' => 'С уважением',
		'comments_add_comment' => 'Добавить комментарий',
		'comments_edit_comment' => 'Редактировать комментарий',
		'comments_comment_deleted' => 'комментарий удален',
		'comments_edit' => 'Редактировать',
		'comments_delete' => 'Удалить',
		'comments_realy_delete' => 'Вы действительно хотите удалить этот комментарий?',
		'comments_save' => 'Cохранить',
		'comments_comment_text' => 'Текст комментария',
		'comments_type' => 'Тип',
		
		
		// class.carsToBuylist.php # carsToBuylist
		'carstobuy_cars_to_buy' => 'Машины к покупке',
		'carstobuy_add' => 'Добавить',
		'carstobuy_archive' => 'Архив',
		
		// class.carsToBuyform.php # carsToBuyform
		'carstobuy_error_not_found' => 'Ошибка! Автомобиль с такими параметрами в базе не обнаружен',
		'carstobuy_changes_saved' => 'Изменения сохранены',
		'carstobuy_car_to_buy' => 'Автомобиль к покупке',
		'carstobuy_name' => 'название',
		'carstobuy_date' => 'дата',
		'carstobuy_body_color' => 'цвет кузова',
		'carstobuy_auction_max_price' => 'Макс. цена покупки<br> на аукционе',
		'carstobuy_saloon_color' => 'цвет салона',
		'carstobuy_current_price' => 'Текущая цена',
		'carstobuy_year' => 'Год(а)',
		'carstobuy_prepay' => 'предоплата',
		'carstobuy_client' => 'Клиент',
		'carstobuy_other' => 'Прочее',
		'carstobuy_bought' => 'куплена',
		'carstobuy_save' => 'Сохранить',
		'carstobuy_delete' => 'удалить',
		'carstobuy_realy_delete' => 'Вы действительно хотите удалить этот автомобиль?',
		
		// class.ClientsList.php
		'clients_clients' => 'Клиенты',
		'clients_add' => 'добавить',
		'clients_receivers' => 'Получатели',
		'clients_search' => 'найти',
		'clients_total' => 'ВСЕГО',
		
		
		// class.InvoicesList.php
		'invoices_invoices' => 'Инвойсы',
		'invoices_add' => 'добавить',
		'invoices_services_list' => 'Список услуг',
		'invoices_date_added' => 'Дата добавления',
		'invoices_number' => 'Номер',
		'invoices_summ' => 'Сумма',
		'invoices_client' => 'Клиент',
		'invoices_automobile' => 'Автомобиль',
		'invoices_vin_code' => 'Вин код',
		
		// class.Invoicesform.php
		'invoices_field_list' => array('Название услуги', 'Описание услуги', 'Количество', 'Цена', 'Сумма'),
		'invoices_invoices_list' => 'Список инвойсов',
		'invoices_services_list' => 'Список услуг',
		'invoices_add' => 'Добавить',
		'invoices_add_and_edit' => 'Добаление/редактирование инвойса',
		'invoices_choose_a_car' => 'Выберите машину',
		'invoices_error_connecting_server' => 'Ошибка соединения с сервером!',
		'invoices_choose_a_service' => 'Выбирите услугу',
		'invoices_get_cars_without_invoices' => 'Получить список авто без инвойса',
		'invoices_show_to_client' => 'показывать клиенту',
		'invoices_printer_friendly' => 'Печатная версия',
		'invoices_send_to_client' => 'Отправить на почту клиенту',
		'invoices_invoice_number' => 'Номер инвойса',
		'invoices_date' => 'Дата',
		'invoices_add_service' => 'Добавить услугу',
		'invoices_remove_service' => 'Удалить услугу',
		'invoices_sub_total' => 'Под итого',
		'invoices_paid' => 'Оплачено',
		'invoices_total' => 'Итого',
		'invoices_wanna_remove_invoice' => 'Удалить инвойс?',
		'invoices_remove_invoice' => 'Удалить инвойс',
		'invoices_save' => 'Сохранить',
		'invoices_not_chosen' => 'не выбран',
		
		// class.Invoicesmail.php
		'invoices_was_not_sent' => 'инвойс не отправлен, не известен адрес клиента'
		
		
	),
	
	
// ========================================================================================================
// =========================================================================================================
// ==========================================================================================================
// =========================================================================================================
// ========================================================================================================
	// Массив Английского языка
	"eng" => array(
		'top_menu_hello' => 'Hello',
		'top_menu_exit' => 'exit',
		'top_menu_autotrade' => 'auto sell',
		'top_menu_stufftrade' => 'stuff sell',
		'top_menu_auct_list' => 'auctions list',
		'top_menu_reports' => 'reports',
		'top_menu_auto' => 'auto',
		'top_menu_containers' => 'containers',
		'top_menu_users' => 'user management',
		'top_menu_backup' => 'backup',
		'top_menu_files' => 'files',
		'top_menu_error_no_access' => 'You have no access to view this page!',
		'top_menu_error_no_menu' => 'There is no menu for this user type!',
		
		
		
		
		
		
		
		
		
		
		// Форма добавления платежа
		'carsform_payment_by_owner' => 'Payment by owner',
		'carsform_date' => 'Date',
		'carsform_summ' => 'Summ',
		'carsform_created' => 'Created',
		'carsform_comment' => 'Comment',
		'carsform_add' => 'Add',
		'carsform_auto_payments' => 'Payments for vehicle',
		'carsform_add_service' => 'Add service',
		'carsform_add_payment' => 'add. <b>payment</b>',
		'carsform_total_payments' => 'total payments',
		'carsform_no_payments_for_auto' => 'There are no payments for this vehicle',
		
		// Форма добавления расхода
		'carsform_expense_for_auto' => 'Expenses for vehicle',
		'carsform_expenses_by_auto' => 'Expenses to vehicle',
		'carsform_expense_purpose' => 'expense purpose',
		'carsform_expense_paid' => 'Paid?',
		'carsform_add_expense' => 'add. <b>expense</b>',
		'carsform_signed' => 'Confirmed',
		'carsform_paid' => 'Paid',
		'carsform_total_expenses' => 'Total expenses',
		'carsform_no_expenses_for_auto' => 'There are no expenses for this vehicle',
		
		
		// Java-скрипты
		'carsform_confirm' => 'Confirm..',
		'carsform_yes' => 'yes',
		'carsform_no' => 'no',
		'carsform_error' => 'Error!',
		'carsform_unknown' => 'unknown',
		
		// Форма информации по автомобилю
		'carsform_automobile' => 'Automobile',
		'carsform_full_car_price' => 'total for auto',
		'carsform_ballance' => 'balance',
		'carsform_clear_profit' => 'Clear profit',
		'carsform_expenses_for_auto' => 'Expenses for auto',
		'carsform_price_in_auction_in_america' => 'auction price in USA',
		'carsform_auction_fee' => 'Auction fee',
		'carsform_dealer_fee' => 'Dealer fee',
		'carsform_pp_inspection' => 'PP inspection',
		'carsform_transportation_to_port' => 'Transportation to port',
		'carsform_transportation_to_destination' => 'Transportation to destination',
		'carsform_unload' => 'Unload',
		'carsform_insurance' => 'Insurance',
		'carsform_other' => 'Other',
		'carsform_total' => 'TOTAL',
		'carsform_add_client' => 'Add client',
		'carsform_add_receiver' => 'Add receiver',
		'carsform_owner' => 'owner',
		'carsform_purchase_date' => 'purchase date',
		'carsform_receiver' => 'receiver',
		'carsform_name' => 'Name',
		'carsform_brand_name' => 'Brand name',
		'carsform_model' => 'Model',
		'carsform_add_brand' => 'Add brand',
		'carsform_add_model' => 'Add model',
		'carsform_vin_code' => 'VIN code',
		'carsform_production_date' => 'Production date',
		'carsform_engine_volume' => 'engine volume',
		'carsform_invoice_price' => 'invoice price',
		'carsform_weight' => 'weight',
		'carsform_container' => 'container',
		'carsform_mileage' => 'mileage',
		'carsform_transporter' => 'transporter',
		'carsform_port' => 'port',
		'carsform_date_realy_deliver' => 'real delivery date',
		'carsform_transportation_status' => 'transportation status',
		'carsform_delivered' => 'delivered',
		'carsform_auto_location' => 'Vehicle location',
		'carsform_title' => 'Title',
		'carsform_auction' => 'Auction',
		'carsform_destination' => 'destination',
		'carsform_invite_client' => 'invite client',
		'carsform_realy_delete_auto' => 'Do you realy want to delete this vehicle?',
		'carsform_sell' => 'Sell',
		'carsform_save' => 'Save',
		
		
		
		
		
		
		
		
		// class.CarsList.php # CarsList
		
		'carslist_autos' => 'Vehicles',
		'carslist_add' => 'add',
		'carslist_places_list' => 'Locations list',
		'carslist_ports_list' => 'Ports list',
		'carslist_by_name' => 'by name',
		'carslist_by_vincode' => 'by VIN code',
		'carslist_from' => 'from',
		'carslist_till' => 'till',
		'carslist_brands_list' => 'Brands list',
		'carslist_models_list' => 'Models list',
		'carslist_auto_location' => 'vehicle location',
		'carslist_auto_destination' => 'destination',
		'carslist_in_port' => 'in a port',
		'carslist_owner' => 'owner',
		'carslist_search' => 'search',
		'carslist_new' => 'new',
		'carslist_in_transit' => 'in transit',
		'carslist_archive' => 'archive',
		'carslist_no_search_results' => 'There are no results',
		'carslist_was_not_choosen' => '- = not chosen = -',
		
		// CarsList  # list
		'carslist_lst_buy_date' => 'purchase date',
		'carslist_lst_vincode' => 'vin code',
		'carslist_lst_owner' => 'owner',
		'carslist_lst_model' => 'model',
		'carslist_lst_container' => 'container',
		'carslist_lst_transporter' => 'transporter',
		'carslist_lst_port' => 'port',
		'carslist_lst_auto_location' => 'vehicle location',
		'carslist_lst_title_location' => 'title location',
		'carslist_lst_auction' => 'auction',


		// class.Carscomment.php # AddEditComment
		'comment_fill_comment_text' => 'Please fill comment text',
		'comment_comment_saved' => 'Comment successfully saved',
		'comment_comment_was_not_saved' => 'Comment was NOT saved',

		
		
		// class.CommentTemplates.php # Comments template
		'comments_comments' => 'Comments',
	)
);
?>