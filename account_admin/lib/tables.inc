<?
$tables = array(

'accounting'=>
"
CREATE TABLE `%TABLE_NAME%` (
`id` int(15) NOT NULL auto_increment,
`client` int(15) NOT NULL default '0' COMMENT 'unused now',
`amount` mediumint(10) NOT NULL default '0',
`comment` mediumtext NOT NULL,
`date` date NOT NULL default '0000-00-00',
`user_added` int(15) NOT NULL default '0',
`status` tinyint(1) NOT NULL default '0',
`last_edited` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
`car` int(11) NOT NULL default '0',
`stuff` mediumint(9) NOT NULL COMMENT 'unused now',
`signer` varchar(20) NOT NULL default ' ',
`paid` tinyint(4) NOT NULL default '0',
`purpose` tinyint(4) NOT NULL default '0' COMMENT 'Expense purpose',
`type` tinyint(3) unsigned default NULL COMMENT 'type of accounting record',
PRIMARY KEY  (`id`),
KEY `client` (`client`),
KEY `status` (`status`),
KEY `car` (`car`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'adddoc'=>
"
CREATE TABLE `%TABLE_NAME%` (
`id` int(15) NOT NULL auto_increment,
`car` int(15) NOT NULL default '0',
`file` mediumtext NOT NULL,
`filename` varchar(255) NOT NULL,
PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",
	
'auctions'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  `address` mediumtext NOT NULL,
  `phones` mediumtext NOT NULL,
  `comment` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",
	
'autocheck'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `car` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `filename` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `client` (`car`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'car_comment'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `car_id` int(11) NOT NULL default '0',
  `type` char(1) NOT NULL default '',
  `text` text NOT NULL,
  `dat` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",
	
'cars'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `buyer` int(15) NOT NULL default '0',
  `reciever` int(15) NOT NULL default '0',
  `car_marka` int(11) NOT NULL,
  `car_model` int(11) NOT NULL,
  `model` tinytext NOT NULL,
  `frame` tinytext NOT NULL,
  `year` tinytext NOT NULL,
  `engine` tinytext NOT NULL,
  `price_jp` mediumint(10) NOT NULL default '0',
  `invoice` mediumint(10) NOT NULL default '0',
  `transfered` int(11) NOT NULL,
  `total` mediumint(10) NOT NULL default '0',
  `container` int(15) NOT NULL default '0',
  `delivered` tinyint(1) NOT NULL default '0',
  `transporter` int(15) default '0',
  `dealer` int(15) NOT NULL default '0',
  `port` int(15) NOT NULL default '0',
  `weight` mediumint(9) NOT NULL default '0',
  `prepay` smallint(6) NOT NULL default '0',
  `buy_date` date NOT NULL default '0000-00-00',
  `created` datetime NOT NULL default '0000-00-00 00:00:00',
  `invoice_id` int(15) NOT NULL default '0',
  `seats` tinyint(4) NOT NULL default '0',
  `fuel_type` tinytext NOT NULL,
  `volume` decimal(10,3) default NULL,
  `milage` int(11) NOT NULL default '0',
  `notice` text NOT NULL,
  `place_id1` int(10) unsigned NOT NULL,
  `place_id2` int(10) unsigned NOT NULL,
  `place_id3` int(10) unsigned NOT NULL,
  `aucfee` mediumint(9) default NULL,
  `dealer_comission` mediumint(9) NOT NULL,
  `cost_to_port` mediumint(9) NOT NULL,
  `cost_to_destination` mediumint(9) NOT NULL,
  `unload` mediumint(9) NOT NULL,
  `insurance` mediumint(9) NOT NULL,
  `other` mediumint(9) NOT NULL,
  `inspection` mediumint(15) NOT NULL,
  `auction` mediumint(15) NOT NULL,
  `ready` tinyint(4) NOT NULL,
  `date_ready` date NOT NULL,
  `pickedup` tinyint(4) default '0',
  `date_pickedup` date NOT NULL,
  `transstatus` tinyint(4) NOT NULL,
  `date_transstatus` date NOT NULL,
  `top_photo` int(15) NOT NULL default '0',
  `allow_inspection` tinyint(4) default '0',
  `allow_codocs` binary(1) NOT NULL default '0',
  `allow_cr` tinyint(4) NOT NULL default '0',
  `allow_carfax` tinyint(4) NOT NULL default '1',
  `date_realydeliver` date NOT NULL,
  `type` tinyint(4) NOT NULL default '1',
  `counted` tinyint(4) unsigned NOT NULL default '0',
  `sold` tinyint(2) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `buyer` (`buyer`),
  KEY `dealer` (`dealer`),
  KEY `delivered` (`delivered`),
  KEY `transstatus` (`transstatus`),
  KEY `allow_inspection` (`allow_inspection`),
  KEY `type` (`type`),
  KEY `allow_codocs` (`allow_codocs`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",	

'cars_cert'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `car` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `filename` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `car` (`car`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'cars_photos'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `car` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `folder` tinyint(4) NOT NULL default '0',
  `filename` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `car` (`car`),
  KEY `folder` (`folder`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'carstobuy'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `model` mediumtext NOT NULL,
  `body` mediumtext NOT NULL,
  `salon` mediumtext NOT NULL,
  `maxprice` mediumint(9) NOT NULL,
  `currentprice` mediumint(9) NOT NULL,
  `url` mediumtext NOT NULL,
  `years` mediumtext NOT NULL,
  `prepay` mediumint(9) NOT NULL,
  `client` int(15) NOT NULL,
  `other` mediumtext NOT NULL,
  `date` date default NULL,
  `status` tinyint(1) default '0',
  `archive` tinyint(4) NOT NULL default '0',
  `lane` varchar(7),
  `run` int(7),
  `time` varchar(7),
  `auctionname` varchar(20),
  `vin` varchar(20),
  PRIMARY KEY  (`id`),
  KEY `archive` (`archive`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'certificates'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `car` int(15) NOT NULL default '0',
  `frame_model` mediumtext NOT NULL,
  `engine_model` mediumtext NOT NULL,
  `car_brutto` smallint(6) NOT NULL default '0',
  `fuel_type` mediumtext NOT NULL,
  `form_number` mediumint(9) NOT NULL default '0',
  `class_number` mediumint(9) NOT NULL default '0',
  `car_length` mediumint(9) NOT NULL default '0',
  `car_width` mediumint(9) NOT NULL default '0',
  `car_height` mediumint(9) NOT NULL default '0',
  `front_weight` mediumint(9) NOT NULL default '0',
  `back_weight` mediumint(9) NOT NULL default '0',
  `cert_reg_number` varchar(20) NOT NULL default '0',
  `uchet_nomer` varchar(20) NOT NULL default '',
  `car_reg_number` varchar(10) NOT NULL default '',
  `reg_cancel_date` varchar(10) NOT NULL default '',
  `export_date` mediumtext NOT NULL,
  `supplier_name` mediumtext NOT NULL,
  `supplier_address` mediumtext NOT NULL,
  `term_date` varchar(12) NOT NULL default '',
  `classification` mediumtext NOT NULL,
  `exp_type` mediumtext NOT NULL,
  `car_type` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'container_files'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `container` int(11) NOT NULL,
  `file` mediumtext NOT NULL,
  `filename` mediumtext,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'containers'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `number` mediumtext NOT NULL,
  `sent` date NOT NULL default '0000-00-00',
  `portdate` date default '0000-00-00',
  `loaddate` date NOT NULL,
  `rail` date NOT NULL default '0000-00-00',
  `bishkek` date NOT NULL default '0000-00-00',
  `price` mediumint(4) NOT NULL default '0',
  `torail` mediumint(9) NOT NULL,
  `arrive_time` date NOT NULL,
  `station` mediumint(9) NOT NULL,
  `slot1` int(15) NOT NULL default '0',
  `slot2` int(15) NOT NULL default '0',
  `slot3` int(15) NOT NULL default '0',
  `slot4` int(15) NOT NULL default '0',
  `arrived` binary(1) NOT NULL default '0',
  `expeditor` int(15) NOT NULL default '0',
  `port` int(15) NOT NULL default '0',
  `invoice` int(15) NOT NULL default '0',
  `booking` mediumtext NOT NULL,
  `sealine` mediumtext NOT NULL,
  `platform` varchar(32) NOT NULL,
  `sell_price` mediumint(4) default '0',
  PRIMARY KEY  (`id`),
  KEY `port` (`port`),
  KEY `arrived` (`arrived`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'contracts'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `number` mediumint(10) default NULL,
  `date` date NOT NULL default '0000-00-00',
  `client` int(15) NOT NULL default '0',
  `sum` int(11) NOT NULL default '0',
  `agent` int(11) NOT NULL default '0',
  `paid` int(11) NOT NULL default '0',
  `dolg` int(11) NOT NULL default '0',
  `car` mediumtext,
  `data_source` tinytext NOT NULL,
  `name` mediumtext NOT NULL,
  `address` mediumtext NOT NULL,
  `passport` mediumtext NOT NULL,
  `contacts` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `client` (`client`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'customers'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  `contacts` text NOT NULL,
  `address` text NOT NULL,
  `name_en` mediumtext NOT NULL,
  `address_en` mediumtext NOT NULL,
  `passport` text NOT NULL,
  `email` mediumtext NOT NULL,
  `balance` mediumint(4) NOT NULL default '0',
  `real_balance` mediumint(7) NOT NULL default '0',
  `cars_delivered` mediumint(9) NOT NULL default '0',
  `cars` mediumint(4) NOT NULL default '0',
  `total_cars` mediumint(4) NOT NULL default '0',
  `payments` mediumint(4) NOT NULL default '0',
  `dealer` tinyint(1) NOT NULL default '0',
  `mydealer` int(15) NOT NULL default '0',
  `scan` mediumtext NOT NULL,
  `approved` binary(1) NOT NULL default '0',
  `allowspam` binary(1) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'exp_pay'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `to` int(15) NOT NULL default '0',
  `sum` mediumint(9) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `comment` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'exp_serv'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `from` int(15) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `comment` mediumtext NOT NULL,
  `sum` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'expeditors'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  `address` mediumtext NOT NULL,
  `phone` tinytext NOT NULL,
  `email` tinytext NOT NULL,
  `balance` mediumint(9) NOT NULL default '0',
  `ports` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'expeditors_photo'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `car` int(10) unsigned NOT NULL,
  `descr` varchar(128) NOT NULL,
  `file` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'expenses'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `client` int(15) NOT NULL default '0' COMMENT 'unused now',
  `amount` mediumint(10) NOT NULL default '0',
  `comment` mediumtext NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `user_added` int(15) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `last_edited` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `car` int(11) NOT NULL default '0',
  `stuff` mediumint(9) NOT NULL COMMENT 'unused now',
  `signer` varchar(20) NOT NULL default ' ',
  `paid` tinyint(4) NOT NULL default '0',
  `purpose` tinyint(4) NOT NULL default '0' COMMENT 'Expense purpose',
  PRIMARY KEY  (`id`),
  KEY `client` (`client`),
  KEY `status` (`status`),
  KEY `car` (`car`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'daysleft'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `cid` int(10) unsigned NOT NULL,
  `left_days` int(11) NOT NULL default '0' COMMENT 'Days left till car arrival',
  `last_status` tinyint(3) unsigned NOT NULL COMMENT 'Vehicle location from last script running',
  `was_in_container` tinyint(3) unsigned NOT NULL default '0' COMMENT 'If vehicle was in container last time script was running',
  `last_update` datetime NOT NULL default '0000-00-00 00:00:00' COMMENT 'Last time row was updated (not used)',
  PRIMARY KEY  (`cid`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 COMMENT='Days left till arrival for each car not delivered yet';

",

'fileshare'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `originfile` varchar(128) NOT NULL,
  `sizefile` int(10) unsigned NOT NULL,
  `title` varchar(128) NOT NULL,
  `text` varchar(255) NOT NULL,
  `dt_up` datetime NOT NULL,
  `access2` set('Администратор','Клиент / Дилер','Оператор','Менеджер','Поставщик','','админ') NOT NULL,
  `access2delete` smallint(5) unsigned NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'forsale'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `car` int(15) NOT NULL default '0',
  `price` int(10) NOT NULL default '0',
  `comment` text NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `active_through` date default NULL,
  `sold` binary(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `car` (`car`),
  KEY `sold` (`sold`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'inspections'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `car` int(15) NOT NULL,
  `file` mediumtext NOT NULL,
  `filename` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'invoices'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `carid` int(11) default '0',
  `client` int(15) NOT NULL default '0',
  `number` varchar(100) NOT NULL default '',
  `date` varchar(20) default NULL,
  `subitog` double(10,2) default '0.00',
  `opl` double(10,2) default '0.00',
  `itog` double(10,2) default '0.00',
  `access` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `client` (`client`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'invoices_old'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `number` tinytext NOT NULL,
  `file` mediumtext NOT NULL,
  `container` int(15) NOT NULL default '0',
  `supplier` mediumtext NOT NULL,
  `sup_address` mediumtext NOT NULL,
  `reciever` int(15) default NULL,
  `date` mediumtext NOT NULL,
  `save_date` date default NULL,
  `disch_port` mediumtext NOT NULL,
  `cars` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `container` (`container`),
  KEY `reciever` (`reciever`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",
	
'invoices_services'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `invoice_id` int(11) default '0',
  `item_id` int(11) default '0',
  `num` smallint(6) NOT NULL,
  `description` text NOT NULL,
  `cost` float(10,2) default '0.00',
  `quantity` int(10) default '1',
  `summ` float(10,2) default '0.00',
  PRIMARY KEY  (`id`),
  KEY `num` (`num`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",
/*
'logs'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` mediumint(9) NOT NULL auto_increment,
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `time` float NOT NULL default '0',
  `queries` text NOT NULL,
  `file` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",*/

'marka'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'model'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `marka_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'ove'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `car_id` int(11) NOT NULL,
  `serialize` text NOT NULL,
  `ove_id` varchar(50) NOT NULL,
  `posted` tinyint(1) NOT NULL,
  `csv` varchar(50) NOT NULL,
  PRIMARY KEY  (`car_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'payments'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `client` int(15) NOT NULL default '0',
  `amount` mediumint(10) NOT NULL default '0',
  `comment` mediumtext NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `user_added` int(15) NOT NULL default '0',
  `status` tinyint(1) NOT NULL default '0',
  `last_edited` timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  `car` int(11) NOT NULL default '0',
  `stuff` mediumint(9) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `client` (`client`),
  KEY `status` (`status`),
  KEY `car` (`car`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'places'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'ports'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'post'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'recievers'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  `phone` mediumtext NOT NULL,
  `passport` mediumtext NOT NULL,
  `address` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=270 DEFAULT CHARSET=cp1251;
",

'reload_ports'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(32) NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'scan'=>"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `client` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `filename` varchar(255) NOT NULL,
  KEY `id` (`id`),
  KEY `client` (`client`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'services'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `item` varchar(250) NOT NULL default '',
  `description` text NOT NULL,
  `cost` float(10,2) default '0.00',
  `quantity` int(10) default '1',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",
'stakes_auc'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` tinyint(4) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;

",

'stakes_list'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(20) NOT NULL auto_increment,
  `user` int(15) NOT NULL default '0',
  `status` tinyint(4) NOT NULL default '0',
  `created` int(11) default NULL,
  `auction` int(11) NOT NULL default '0',
  `lot` int(11) NOT NULL default '0',
  `sum` int(11) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `desc` mediumtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `user` (`user`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251 AUTO_INCREMENT=1 ;
",


'stuff'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `buyer` int(15) NOT NULL default '0',
  `name` varchar(250) NOT NULL default '',
  `count` int(11) NOT NULL default '1',
  `serials` text NOT NULL,
  `transporter_id` mediumint(9) NOT NULL default '0',
  `port_id` int(9) NOT NULL default '0',
  `date_buy` date NOT NULL default '0000-00-00',
  `post_id` int(11) NOT NULL default '0',
  `container` int(11) NOT NULL default '0',
  `place_in` int(11) NOT NULL default '0',
  `uid` varchar(8) NOT NULL default '',
  `price` decimal(10,2) default '0.00',
  `sold` mediumint(10) NOT NULL default '0',
  `delivery` decimal(10,2) default '0.00',
  `other` decimal(10,2) default '0.00',
  `total` decimal(10,2) default '0.00',
  `paid` decimal(10,2) default '0.00',
  `balance` decimal(10,2) default '0.00',
  `invoice` decimal(10,2) default '0.00',
  `current_place` int(11) NOT NULL default '0',
  `deliveried` enum('0','1') NOT NULL default '0',
  `inspection` mediumint(15) NOT NULL default '0',
  `allow_inspection` tinyint(4) default '0',
  `allow_codocs` binary(1) NOT NULL default '0',
  `top_photo` mediumint(9) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `buyer` (`buyer`),
  KEY `allow_inspection` (`allow_inspection`),
  KEY `allow_codocs` (`allow_codocs`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_adddoc'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `stuff` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_cert'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `stuff` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `car` (`stuff`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_comment'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL default '0',
  `stuff_id` int(11) NOT NULL default '0',
  `type` char(1) NOT NULL default '',
  `text` text NOT NULL,
  `dat` varchar(15) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_container'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `id_cont` int(11) NOT NULL default '0',
  `id_stuff` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_forsale'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `stuff` int(15) NOT NULL default '0',
  `price` int(10) NOT NULL default '0',
  `comment` text NOT NULL,
  `date` date NOT NULL default '0000-00-00',
  `active_through` date default NULL,
  `sold` binary(1) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `car` (`stuff`),
  KEY `sold` (`sold`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_inspections'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `stuff` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `filename` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_photos'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `stuff` int(15) NOT NULL default '0',
  `file` mediumtext NOT NULL,
  `folder` tinyint(4) NOT NULL default '0',
  `filename` varchar(250) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `id` (`id`),
  KEY `car` (`stuff`),
  KEY `folder` (`folder`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'stuff_sell'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `stuff_id` int(11) NOT NULL default '0',
  `sell_count` int(11) NOT NULL default '0',
  `sell_price` varchar(60) NOT NULL default '',
  `sell_dat` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'sup_pay'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `to` int(15) NOT NULL default '0',
  `sum` mediumint(15) default '0',
  `date` date NOT NULL default '0000-00-00',
  `comment` mediumtext NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'sup_serv'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `from` int(15) NOT NULL default '0',
  `date` date NOT NULL default '0000-00-00',
  `comment` mediumtext NOT NULL,
  `sum` mediumint(15) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'tpl'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(10) unsigned NOT NULL auto_increment,
  `name` varchar(100) NOT NULL default '',
  `txt` text NOT NULL,
  `type` enum('c','t') NOT NULL default 'c',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'transporters'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `name` mediumtext NOT NULL,
  `address` mediumtext NOT NULL,
  `phone` tinytext NOT NULL,
  `email` tinytext NOT NULL,
  `balance` int(11) default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'usrs'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(15) NOT NULL auto_increment,
  `log_name` varchar(15) default NULL,
  `pass_code` mediumtext NOT NULL,
  `type` tinyint(3) NOT NULL default '0',
  `u_id` int(15) NOT NULL default '0',
  `seed` tinytext NOT NULL,
  `last_log` int(11) NOT NULL default '0',
  `email` varchar(50) NOT NULL,
  `t_id` int(10) unsigned NOT NULL,
  `e_id` int(10) unsigned NOT NULL,
  `lang` varchar(4) NOT NULL default '0' COMMENT 'User language',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `log_name` (`log_name`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'webcars_marka'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `tcl_id` int(10) unsigned NOT NULL default '0',
  `webcars_id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL default '',
  UNIQUE KEY `tcl_id` (`tcl_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'webcars_model'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `tcl_id` int(10) unsigned default '0',
  `webcars_id` int(10) NOT NULL,
  `name` varchar(100) NOT NULL default '',
  `marka_id` int(11) NOT NULL default '0',
  UNIQUE KEY `tcl_id` (`tcl_id`),
  KEY `webcars_id` (`webcars_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'webcars_sent'=>
"
CREATE TABLE `%TABLE_NAME%` (
  `id` int(11) NOT NULL auto_increment,
  `tcl_id` int(11) NOT NULL,
  `webcars_id` int(11) NOT NULL,
  `data` text NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `tcl_id` (`tcl_id`,`webcars_id`)
) ENGINE=MyISAM DEFAULT CHARSET=cp1251;
",

'settings'=>
"
CREATE TABLE `%TABLE_NAME%` (
	`name` VARCHAR(20) NULL DEFAULT NULL,
	`data` TEXT NULL,
	UNIQUE INDEX `name` (`name`)
)
ENGINE=MyISAM
DEFAULT CHARSET=cp1251
ROW_FORMAT=DEFAULT;
"

);

	// ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~ ~