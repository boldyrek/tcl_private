<?php defined('SYSPATH') or die('No direct script access.'); ?>

2011-07-21 05:52:53 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds_searches`.`id` AS `id`, `ccl_mds_searches`.`parent_id` AS `parent_id`, `ccl_mds_searches`.`year` AS `year`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds_searches` WHERE `ccl_mds_searches`.`id` = '298' LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:53:46 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds_searches`.`id` AS `id`, `ccl_mds_searches`.`parent_id` AS `parent_id`, `ccl_mds_searches`.`year` AS `year`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds_searches` WHERE `ccl_mds_searches`.`parent_id` = '12' AND `ccl_mds_searches`.`year` = '2010' ORDER BY `ccl_mds_searches`.`id` DESC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:53:48 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds`.`id` AS `id`, `ccl_mds`.`parent_id` AS `parent_id`, `ccl_mds`.`search_id` AS `search_id`, `ccl_mds`.`year` AS `year`, `ccl_mds`.`vpd` AS `vpd`, `ccl_mds`.`mds` AS `mds`, `ccl_mds`.`date_added` AS `date_added`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds` JOIN `ccl_mds_searches` ON (`ccl_mds`.`search_id` = `ccl_mds_searches`.`id`) WHERE `ccl_mds`.`parent_id` = 1 AND `ccl_mds`.`year` = 2006 AND `ccl_mds`.`date_added` = '2011-07-20' AND `ccl_mds`.`mds` != 0 ORDER BY `ccl_mds`.`mds` ASC LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:53:54 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds_searches`.`id` AS `id`, `ccl_mds_searches`.`parent_id` AS `parent_id`, `ccl_mds_searches`.`year` AS `year`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds_searches` WHERE `ccl_mds_searches`.`parent_id` = '12' AND `ccl_mds_searches`.`year` = '2010' ORDER BY `ccl_mds_searches`.`id` DESC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:53:56 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds`.`id` AS `id`, `ccl_mds`.`parent_id` AS `parent_id`, `ccl_mds`.`search_id` AS `search_id`, `ccl_mds`.`year` AS `year`, `ccl_mds`.`vpd` AS `vpd`, `ccl_mds`.`mds` AS `mds`, `ccl_mds`.`date_added` AS `date_added`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds` JOIN `ccl_mds_searches` ON (`ccl_mds`.`search_id` = `ccl_mds_searches`.`id`) WHERE `ccl_mds`.`parent_id` = 1 AND `ccl_mds`.`year` = 2006 AND `ccl_mds`.`date_added` = '2011-07-20' AND `ccl_mds`.`mds` != 0 ORDER BY `ccl_mds`.`mds` ASC LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:54:04 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds`.`id` AS `id`, `ccl_mds`.`parent_id` AS `parent_id`, `ccl_mds`.`search_id` AS `search_id`, `ccl_mds`.`year` AS `year`, `ccl_mds`.`vpd` AS `vpd`, `ccl_mds`.`mds` AS `mds`, `ccl_mds`.`date_added` AS `date_added`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds` JOIN `ccl_mds_searches` ON (`ccl_mds`.`search_id` = `ccl_mds_searches`.`id`) WHERE `ccl_mds`.`parent_id` = 1 AND `ccl_mds`.`year` = 2006 AND `ccl_mds`.`date_added` = '2011-07-20' AND `ccl_mds`.`mds` != 0 ORDER BY `ccl_mds`.`mds` ASC LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:54:07 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds_searches`.`id` AS `id`, `ccl_mds_searches`.`parent_id` AS `parent_id`, `ccl_mds_searches`.`year` AS `year`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds_searches` WHERE `ccl_mds_searches`.`parent_id` = '12' AND `ccl_mds_searches`.`year` = '2010' ORDER BY `ccl_mds_searches`.`id` DESC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:55:11 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds`.`id` AS `id`, `ccl_mds`.`parent_id` AS `parent_id`, `ccl_mds`.`search_id` AS `search_id`, `ccl_mds`.`year` AS `year`, `ccl_mds`.`vpd` AS `vpd`, `ccl_mds`.`mds` AS `mds`, `ccl_mds`.`date_added` AS `date_added`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds` JOIN `ccl_mds_searches` ON (`ccl_mds`.`search_id` = `ccl_mds_searches`.`id`) WHERE `ccl_mds`.`parent_id` = 1 AND `ccl_mds`.`year` = 2006 AND `ccl_mds`.`date_added` = '2011-07-20' AND `ccl_mds`.`mds` != 0 ORDER BY `ccl_mds`.`mds` ASC LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:55:15 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds_searches`.`id` AS `id`, `ccl_mds_searches`.`parent_id` AS `parent_id`, `ccl_mds_searches`.`year` AS `year`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds_searches` WHERE `ccl_mds_searches`.`parent_id` = '12' AND `ccl_mds_searches`.`year` = '2010' ORDER BY `ccl_mds_searches`.`id` DESC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:55:20 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds`.`id` AS `id`, `ccl_mds`.`parent_id` AS `parent_id`, `ccl_mds`.`search_id` AS `search_id`, `ccl_mds`.`year` AS `year`, `ccl_mds`.`vpd` AS `vpd`, `ccl_mds`.`mds` AS `mds`, `ccl_mds`.`date_added` AS `date_added`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds` JOIN `ccl_mds_searches` ON (`ccl_mds`.`search_id` = `ccl_mds_searches`.`id`) WHERE `ccl_mds`.`parent_id` = 1 AND `ccl_mds`.`year` = 2006 AND `ccl_mds`.`date_added` = '2011-07-20' AND `ccl_mds`.`mds` != 0 ORDER BY `ccl_mds`.`mds` ASC LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:55:36 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds`.`id` AS `id`, `ccl_mds`.`parent_id` AS `parent_id`, `ccl_mds`.`search_id` AS `search_id`, `ccl_mds`.`year` AS `year`, `ccl_mds`.`vpd` AS `vpd`, `ccl_mds`.`mds` AS `mds`, `ccl_mds`.`date_added` AS `date_added`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds` JOIN `ccl_mds_searches` ON (`ccl_mds`.`search_id` = `ccl_mds_searches`.`id`) WHERE `ccl_mds`.`parent_id` = 1 AND `ccl_mds`.`year` = 2006 AND `ccl_mds`.`date_added` = '2011-07-20' AND `ccl_mds`.`mds` != 0 ORDER BY `ccl_mds`.`mds` ASC LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:56:18 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds_searches`.`id` AS `id`, `ccl_mds_searches`.`parent_id` AS `parent_id`, `ccl_mds_searches`.`year` AS `year`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds_searches` WHERE `ccl_mds_searches`.`parent_id` = '13' AND `ccl_mds_searches`.`year` = '2006' ORDER BY `ccl_mds_searches`.`id` DESC ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:56:20 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_searches.main' in 'field list' [ SELECT `ccl_mds`.`id` AS `id`, `ccl_mds`.`parent_id` AS `parent_id`, `ccl_mds`.`search_id` AS `search_id`, `ccl_mds`.`year` AS `year`, `ccl_mds`.`vpd` AS `vpd`, `ccl_mds`.`mds` AS `mds`, `ccl_mds`.`date_added` AS `date_added`, `ccl_mds_searches`.`condition` AS `condition`, `ccl_mds_searches`.`exception` AS `exception`, `ccl_mds_searches`.`main` AS `main` FROM `ccl_mds` JOIN `ccl_mds_searches` ON (`ccl_mds`.`search_id` = `ccl_mds_searches`.`id`) WHERE `ccl_mds`.`parent_id` = 1 AND `ccl_mds`.`year` = 2006 AND `ccl_mds`.`date_added` = '2011-07-20' AND `ccl_mds`.`mds` != 0 ORDER BY `ccl_mds`.`mds` ASC LIMIT 1 ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 05:59:09 --- ERROR: Database_Exception [ 1054 ]: Unknown column 'ccl_mds_items.date_sold' in 'field list' [ SELECT `ccl_mds_items`.`id` AS `id`, `ccl_mds_items`.`parent_id` AS `parent_id`, `ccl_mds_items`.`year` AS `year`, `ccl_mds_items`.`name` AS `name`, `ccl_mds_items`.`url` AS `url`, `ccl_mds_items`.`is_new` AS `is_new`, `ccl_mds_items`.`sold` AS `sold`, `ccl_mds_items`.`date_added` AS `date_added`, `ccl_mds_items`.`date_sold` AS `date_sold`, CONCAT_WS(', ', title, detalis, technical) AS `detalis` FROM `ccl_mds_items` INNER JOIN `ccl_calc_cache` ON (`ccl_mds_items`.`url` = `ccl_calc_cache`.`url`) WHERE `ccl_mds_items`.`parent_id` = '1' AND `ccl_mds_items`.`year` = '2006' AND `ccl_mds_items`.`sold` = '0' ] ~ MODPATH/database/classes/kohana/database/mysql.php [ 181 ]
2011-07-21 06:08:13 --- INFO: PTM. Mercedes-Benz M-Class, 2006: found - 8, passed - 8
2011-07-21 06:08:16 --- INFO: PTM. Mercedes-Benz M-Class, 2007: found - 8, passed - 8
2011-07-21 06:08:26 --- INFO: PTM. Mercedes-Benz M-Class, 2008: found - 29, passed - 28
2011-07-21 06:08:39 --- INFO: PTM. Infiniti FX, 2009: found - 21, passed - 21
2011-07-21 06:08:43 --- INFO: PTM. Honda Accord Crosstour, 2010: found - 9, passed - 9
2011-07-21 06:09:13 --- INFO: PTM. Toyota Venza, 2009: found - 73, passed - 67
2011-07-21 06:09:21 --- INFO: PTM. Toyota Venza, 2010: found - 25, passed - 23
2011-07-21 06:09:44 --- INFO: PTM. BMW X5, 2007: found - 54, passed - 50
2011-07-21 06:10:14 --- INFO: PTM. BMW X5, 2008: found - 66, passed - 62
2011-07-21 06:10:22 --- INFO: PTM. Subaru Forester, 2009: found - 20, passed - 18
2011-07-21 06:10:27 --- INFO: PTM. Subaru Forester, 2010: found - 16, passed - 16
2011-07-21 06:10:51 --- INFO: PTM. Toyota Highlander, 2008: found - 59, passed - 55
2011-07-21 06:10:58 --- INFO: PTM. Toyota Highlander, 2009: found - 15, passed - 13
2011-07-21 06:11:18 --- INFO: PTM. Toyota Sienna, 2006: found - 57, passed - 54
2011-07-21 06:12:11 --- INFO: PTM. Toyota Sienna, 2007: found - 142, passed - 131
2011-07-21 06:12:32 --- INFO: PTM. Toyota Sienna, 2008: found - 56, passed - 56
2011-07-21 06:12:36 --- INFO: PTM. Toyota Sienna, 2009: found - 8, passed - 8
2011-07-21 06:12:42 --- INFO: PTM. Toyota Sienna, 2010: found - 16, passed - 15
2011-07-21 06:13:21 --- INFO: PTM. Acura MDX, 2007: found - 105, passed - 96
2011-07-21 06:14:13 --- INFO: PTM. Acura MDX, 2008: found - 141, passed - 130
2011-07-21 06:14:26 --- INFO: PTM. Acura MDX, 2009: found - 33, passed - 29
2011-07-21 06:14:52 --- INFO: PTM. Acura RDX, 2007: found - 76, passed - 69
2011-07-21 06:15:17 --- INFO: PTM. Acura RDX, 2008: found - 75, passed - 69
2011-07-21 06:15:24 --- INFO: PTM. Acura RDX, 2009: found - 24, passed - 22
2011-07-21 06:15:41 --- INFO: PTM. Acura TL, 2006: found - 45, passed - 41
2011-07-21 06:16:14 --- INFO: PTM. Acura TL, 2007: found - 83, passed - 73
2011-07-21 06:16:42 --- INFO: PTM. Acura TL, 2008: found - 80, passed - 73
2011-07-21 06:16:49 --- INFO: PTM. Acura TL, 2009: found - 26, passed - 24
2011-07-21 06:16:54 --- INFO: PTM. Acura TSX, 2006: found - 14, passed - 14
2011-07-21 06:17:16 --- INFO: PTM. Acura TSX, 2007: found - 60, passed - 57
2011-07-21 06:17:25 --- INFO: PTM. Acura TSX, 2008: found - 29, passed - 29
2011-07-21 06:17:33 --- INFO: PTM. Acura TSX, 2009: found - 26, passed - 24
2011-07-21 06:17:34 --- INFO: PTM. Acura TSX, 2010: found - 1, passed - 1
2011-07-21 06:17:51 --- INFO: PTM. Honda Odyssey, 2006: found - 48, passed - 46
2011-07-21 06:19:12 --- INFO: PTM. Honda Odyssey, 2007: found - 188, passed - 168
2011-07-21 06:19:41 --- INFO: PTM. Honda Odyssey, 2008: found - 71, passed - 66
2011-07-21 06:19:55 --- INFO: PTM. Honda Odyssey, 2009: found - 35, passed - 32
2011-07-21 06:20:01 --- INFO: PTM. Honda Odyssey, 2010: found - 17, passed - 15
2011-07-21 06:21:51 --- INFO: PTM. Honda CR-V, 2007: found - 274, passed - 256
2011-07-21 06:23:21 --- INFO: PTM. Honda CR-V, 2008: found - 247, passed - 233
2011-07-21 06:23:53 --- INFO: PTM. Honda CR-V, 2009: found - 76, passed - 70
2011-07-21 06:24:08 --- INFO: PTM. Honda CR-V, 2010: found - 29, passed - 24
2011-07-21 06:24:10 --- INFO: PTM. Honda CR-V, 2011: found - 4, passed - 3
2011-07-21 06:24:48 --- INFO: PTM. Honda Pilot, 2007: found - 100, passed - 84
2011-07-21 06:25:11 --- INFO: PTM. Honda Pilot, 2008: found - 53, passed - 52
2011-07-21 06:25:28 --- INFO: PTM. Honda Pilot, 2009: found - 54, passed - 46
2011-07-21 06:25:35 --- INFO: PTM. Honda Pilot, 2010: found - 19, passed - 18
2011-07-21 06:25:46 --- INFO: PTM. Honda Pilot, 2011: found - 30, passed - 27
2011-07-21 06:25:48 --- INFO: PTM. Mitsubishi Outlander, 2011: found - 5, passed - 5
2011-07-21 06:26:17 --- INFO: PTM. Nissan Murano, 2009: found - 70, passed - 68
2011-07-21 06:26:21 --- INFO: PTM. Nissan Murano, 2010: found - 10, passed - 10
2011-07-21 06:26:26 --- INFO: PTM. Nissan Maxima, 2009: found - 11, passed - 10
2011-07-21 06:26:34 --- INFO: PTM. Nissan Maxima, 2010: found - 23, passed - 22
2011-07-21 06:26:40 --- INFO: PTM. Nissan Pathfinder, 2006: found - 24, passed - 22
2011-07-21 06:26:46 --- INFO: PTM. Nissan Pathfinder, 2007: found - 17, passed - 16
2011-07-21 06:26:53 --- INFO: PTM. Nissan Pathfinder, 2008: found - 22, passed - 21
2011-07-21 06:26:56 --- INFO: PTM. Nissan Pathfinder, 2009: found - 8, passed - 6
2011-07-21 06:27:03 --- INFO: PTM. Nissan Pathfinder, 2010: found - 20, passed - 20
2011-07-21 06:28:58 --- INFO: PTM. Honda Accord, 2008: found - 288, passed - 264
2011-07-21 06:29:36 --- INFO: PTM. Honda Accord, 2009: found - 90, passed - 82
2011-07-21 06:29:55 --- INFO: PTM. Honda Accord, 2010: found - 53, passed - 48
2011-07-21 06:31:22 --- INFO: PTM. Infiniti G, 2007: found - 221, passed - 211
2011-07-21 06:32:15 --- INFO: PTM. Infiniti G, 2008: found - 140, passed - 127
2011-07-21 06:32:27 --- INFO: PTM. Infiniti G, 2009: found - 32, passed - 31
2011-07-21 06:32:38 --- INFO: PTM. Infiniti G, 2010: found - 30, passed - 28
2011-07-21 06:37:00 --- INFO: MDS. Mercedes-Benz M-Class, 2006: VPD - 0.07, MDS - 85.71, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:37:00 --- INFO: MDS. Mercedes-Benz M-Class, 2006: VPD - 0.09, MDS - 133.33, CONDITION - a, EXCEPTION - 
2011-07-21 06:37:00 --- INFO: MDS. Mercedes-Benz M-Class, 2006: VPD - 0.09, MDS - 133.33, CONDITION - , EXCEPTION - 
2011-07-21 06:37:03 --- INFO: MDS. Mercedes-Benz M-Class, 2007: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 06:37:03 --- INFO: MDS. Mercedes-Benz M-Class, 2007: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 06:37:14 --- INFO: MDS. Mercedes-Benz M-Class, 2008: VPD - 0.11, MDS - 281.82, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:37:14 --- INFO: MDS. Mercedes-Benz M-Class, 2008: VPD - 0.13, MDS - 269.23, CONDITION - a, EXCEPTION - 
2011-07-21 06:37:14 --- INFO: MDS. Mercedes-Benz M-Class, 2008: VPD - 0.13, MDS - 269.23, CONDITION - , EXCEPTION - 
2011-07-21 06:37:24 --- INFO: MDS. Infiniti FX, 2009: VPD - 0.02, MDS - 550, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:37:24 --- INFO: MDS. Infiniti FX, 2009: VPD - 0.09, MDS - 277.78, CONDITION - fx, EXCEPTION - 
2011-07-21 06:37:24 --- INFO: MDS. Infiniti FX, 2009: VPD - 0.09, MDS - 277.78, CONDITION - , EXCEPTION - 
2011-07-21 06:37:29 --- INFO: MDS. Honda Accord Crosstour, 2010: VPD - 0.02, MDS - 500, CONDITION - a, EXCEPTION - 
2011-07-21 06:37:29 --- INFO: MDS. Honda Accord Crosstour, 2010: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:37:29 --- INFO: MDS. Honda Accord Crosstour, 2010: VPD - 0.02, MDS - 500, CONDITION - , EXCEPTION - 
2011-07-21 06:37:55 --- INFO: MDS. Toyota Venza, 2009: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:37:55 --- INFO: MDS. Toyota Venza, 2009: VPD - 0.16, MDS - 500, CONDITION - toyota, EXCEPTION - 
2011-07-21 06:37:55 --- INFO: MDS. Toyota Venza, 2009: VPD - 0.16, MDS - 500, CONDITION - , EXCEPTION - 
2011-07-21 06:38:04 --- INFO: MDS. Toyota Venza, 2010: VPD - 0.02, MDS - 100, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:38:04 --- INFO: MDS. Toyota Venza, 2010: VPD - 0.13, MDS - 238.46, CONDITION - toyota, EXCEPTION - 
2011-07-21 06:38:04 --- INFO: MDS. Toyota Venza, 2010: VPD - 0.13, MDS - 238.46, CONDITION - , EXCEPTION - 
2011-07-21 06:38:25 --- INFO: MDS. BMW X5, 2007: VPD - 0.09, MDS - 211.11, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:38:25 --- INFO: MDS. BMW X5, 2007: VPD - 0.2, MDS - 310, CONDITION - bmw, EXCEPTION - 
2011-07-21 06:38:25 --- INFO: MDS. BMW X5, 2007: VPD - 0.2, MDS - 315, CONDITION - , EXCEPTION - 
2011-07-21 06:38:52 --- INFO: MDS. BMW X5, 2008: VPD - 0.29, MDS - 196.55, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:38:52 --- INFO: MDS. BMW X5, 2008: VPD - 0.89, MDS - 117.98, CONDITION - bmw, EXCEPTION - 
2011-07-21 06:38:52 --- INFO: MDS. BMW X5, 2008: VPD - 0.89, MDS - 117.98, CONDITION - , EXCEPTION - 
2011-07-21 06:39:00 --- INFO: MDS. Subaru Forester, 2009: VPD - 0, MDS - 0, CONDITION - nav|navi|navigation, EXCEPTION - 
2011-07-21 06:39:00 --- INFO: MDS. Subaru Forester, 2009: VPD - 0.07, MDS - 328.57, CONDITION - subaru, EXCEPTION - 
2011-07-21 06:39:00 --- INFO: MDS. Subaru Forester, 2009: VPD - 0.07, MDS - 328.57, CONDITION - , EXCEPTION - 
2011-07-21 06:39:06 --- INFO: MDS. Subaru Forester, 2010: VPD - 0.09, MDS - 222.22, CONDITION - subaru, EXCEPTION - 
2011-07-21 06:39:06 --- INFO: MDS. Subaru Forester, 2010: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:39:06 --- INFO: MDS. Subaru Forester, 2010: VPD - 0.09, MDS - 222.22, CONDITION - , EXCEPTION - 
2011-07-21 06:39:30 --- INFO: MDS. Toyota Highlander, 2008: VPD - 0.07, MDS - 200, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 06:39:30 --- INFO: MDS. Toyota Highlander, 2008: VPD - 0.27, MDS - 262.96, CONDITION - , EXCEPTION - 
2011-07-21 06:39:37 --- INFO: MDS. Toyota Highlander, 2009: VPD - 0.04, MDS - 100, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:39:37 --- INFO: MDS. Toyota Highlander, 2009: VPD - 0.07, MDS - 257.14, CONDITION - , EXCEPTION - 
2011-07-21 06:39:56 --- INFO: MDS. Toyota Sienna, 2006: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:39:56 --- INFO: MDS. Toyota Sienna, 2006: VPD - 0.16, MDS - 400, CONDITION - toyota, EXCEPTION - 
2011-07-21 06:39:56 --- INFO: MDS. Toyota Sienna, 2006: VPD - 0.16, MDS - 400, CONDITION - , EXCEPTION - 
2011-07-21 06:40:44 --- INFO: MDS. Toyota Sienna, 2007: VPD - 0.04, MDS - 225, CONDITION - navi|navigation|nav, EXCEPTION - 
2011-07-21 06:40:44 --- INFO: MDS. Toyota Sienna, 2007: VPD - 0.49, MDS - 334.69, CONDITION - , EXCEPTION - 
2011-07-21 06:41:05 --- INFO: MDS. Toyota Sienna, 2008: VPD - 0.16, MDS - 393.75, CONDITION - toyota, EXCEPTION - 
2011-07-21 06:41:05 --- INFO: MDS. Toyota Sienna, 2008: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 06:41:05 --- INFO: MDS. Toyota Sienna, 2008: VPD - 0.16, MDS - 393.75, CONDITION - , EXCEPTION - 
2011-07-21 06:41:08 --- INFO: MDS. Toyota Sienna, 2009: VPD - 0.02, MDS - 450, CONDITION - toyota, EXCEPTION - 
2011-07-21 06:41:08 --- INFO: MDS. Toyota Sienna, 2009: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:41:08 --- INFO: MDS. Toyota Sienna, 2009: VPD - 0.02, MDS - 450, CONDITION - , EXCEPTION - 
2011-07-21 06:41:34 --- INFO: MDS. Toyota Sienna, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:41:34 --- INFO: MDS. Toyota Sienna, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:41:34 --- INFO: MDS. Toyota Sienna, 2010: VPD - 0.04, MDS - 450, CONDITION - , EXCEPTION - 
2011-07-21 06:42:15 --- INFO: MDS. Acura MDX, 2007: VPD - 0.16, MDS - 481.25, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:42:15 --- INFO: MDS. Acura MDX, 2007: VPD - 0.47, MDS - 265.96, CONDITION - acura, EXCEPTION - 
2011-07-21 06:42:15 --- INFO: MDS. Acura MDX, 2007: VPD - 0.47, MDS - 265.96, CONDITION - , EXCEPTION - 
2011-07-21 06:43:07 --- INFO: MDS. Acura MDX, 2008: VPD - 0.51, MDS - 211.76, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:43:07 --- INFO: MDS. Acura MDX, 2008: VPD - 0.69, MDS - 247.83, CONDITION - mdx, EXCEPTION - 
2011-07-21 06:43:07 --- INFO: MDS. Acura MDX, 2008: VPD - 0.69, MDS - 247.83, CONDITION - , EXCEPTION - 
2011-07-21 06:43:21 --- INFO: MDS. Acura MDX, 2009: VPD - 0.11, MDS - 327.27, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:43:21 --- INFO: MDS. Acura MDX, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:43:21 --- INFO: MDS. Acura MDX, 2009: VPD - 0.13, MDS - 300, CONDITION - , EXCEPTION - 
2011-07-21 06:43:55 --- INFO: MDS. Acura RDX, 2007: VPD - 0.27, MDS - 325.93, CONDITION - a, EXCEPTION - 
2011-07-21 06:43:55 --- INFO: MDS. Acura RDX, 2007: VPD - 0.2, MDS - 240, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:43:55 --- INFO: MDS. Acura RDX, 2007: VPD - 0.27, MDS - 325.93, CONDITION - , EXCEPTION - 
2011-07-21 06:44:22 --- INFO: MDS. Acura RDX, 2008: VPD - 0.2, MDS - 225, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:44:22 --- INFO: MDS. Acura RDX, 2008: VPD - 0.51, MDS - 192.16, CONDITION - , EXCEPTION - 
2011-07-21 06:44:32 --- INFO: MDS. Acura RDX, 2009: VPD - 0.13, MDS - 230.77, CONDITION - acura, EXCEPTION - 
2011-07-21 06:44:32 --- INFO: MDS. Acura RDX, 2009: VPD - 0.07, MDS - 228.57, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:44:32 --- INFO: MDS. Acura RDX, 2009: VPD - 0.13, MDS - 230.77, CONDITION - , EXCEPTION - 
2011-07-21 06:44:53 --- INFO: MDS. Acura TL, 2006: VPD - 0.11, MDS - 190.91, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:44:53 --- INFO: MDS. Acura TL, 2006: VPD - 0.24, MDS - 233.33, CONDITION - acura, EXCEPTION - 
2011-07-21 06:44:53 --- INFO: MDS. Acura TL, 2006: VPD - 0.24, MDS - 233.33, CONDITION - , EXCEPTION - 
2011-07-21 06:45:27 --- INFO: MDS. Acura TL, 2007: VPD - 0.2, MDS - 220, CONDITION - nav|navi|navigation, EXCEPTION - 
2011-07-21 06:45:27 --- INFO: MDS. Acura TL, 2007: VPD - 0.73, MDS - 158.9, CONDITION - acura, EXCEPTION - 
2011-07-21 06:45:27 --- INFO: MDS. Acura TL, 2007: VPD - 0.73, MDS - 158.9, CONDITION - , EXCEPTION - 
2011-07-21 06:45:57 --- INFO: MDS. Acura TL, 2008: VPD - 0.33, MDS - 287.88, CONDITION - acura, EXCEPTION - 
2011-07-21 06:45:57 --- INFO: MDS. Acura TL, 2008: VPD - 0.16, MDS - 300, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:45:57 --- INFO: MDS. Acura TL, 2008: VPD - 0.33, MDS - 287.88, CONDITION - , EXCEPTION - 
2011-07-21 06:46:06 --- INFO: MDS. Acura TL, 2009: VPD - 0.07, MDS - 328.57, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:46:06 --- INFO: MDS. Acura TL, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:46:06 --- INFO: MDS. Acura TL, 2009: VPD - 0.09, MDS - 333.33, CONDITION - , EXCEPTION - 
2011-07-21 06:46:11 --- INFO: MDS. Acura TSX, 2006: VPD - 0.04, MDS - 125, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:46:11 --- INFO: MDS. Acura TSX, 2006: VPD - 0.07, MDS - 242.86, CONDITION - acura, EXCEPTION - 
2011-07-21 06:46:11 --- INFO: MDS. Acura TSX, 2006: VPD - 0.07, MDS - 242.86, CONDITION - , EXCEPTION - 
2011-07-21 06:46:30 --- INFO: MDS. Acura TSX, 2007: VPD - 0.11, MDS - 136.36, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:46:30 --- INFO: MDS. Acura TSX, 2007: VPD - 0.58, MDS - 148.28, CONDITION - acura, EXCEPTION - 
2011-07-21 06:46:30 --- INFO: MDS. Acura TSX, 2007: VPD - 0.58, MDS - 148.28, CONDITION - , EXCEPTION - 
2011-07-21 06:46:41 --- INFO: MDS. Acura TSX, 2008: VPD - 0.11, MDS - 218.18, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:46:41 --- INFO: MDS. Acura TSX, 2008: VPD - 0.11, MDS - 309.09, CONDITION - acura, EXCEPTION - 
2011-07-21 06:46:41 --- INFO: MDS. Acura TSX, 2008: VPD - 0.11, MDS - 309.09, CONDITION - , EXCEPTION - 
2011-07-21 06:46:52 --- INFO: MDS. Acura TSX, 2009: VPD - 0.02, MDS - 250, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:46:52 --- INFO: MDS. Acura TSX, 2009: VPD - 0.36, MDS - 116.67, CONDITION - acura, EXCEPTION - 
2011-07-21 06:46:52 --- INFO: MDS. Acura TSX, 2009: VPD - 0.36, MDS - 116.67, CONDITION - , EXCEPTION - 
2011-07-21 06:46:52 --- INFO: MDS. Acura TSX, 2010: VPD - 0.04, MDS - 50, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:46:52 --- INFO: MDS. Acura TSX, 2010: VPD - 0.29, MDS - 48.28, CONDITION - acura, EXCEPTION - 
2011-07-21 06:46:52 --- INFO: MDS. Acura TSX, 2010: VPD - 0.31, MDS - 48.39, CONDITION - , EXCEPTION - 
2011-07-21 06:47:11 --- INFO: MDS. Honda Odyssey, 2006: VPD - 0.31, MDS - 200, CONDITION - a, EXCEPTION - 
2011-07-21 06:47:11 --- INFO: MDS. Honda Odyssey, 2006: VPD - 0.11, MDS - 90.91, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:47:11 --- INFO: MDS. Honda Odyssey, 2006: VPD - 0.31, MDS - 200, CONDITION - , EXCEPTION - 
2011-07-21 06:48:28 --- INFO: MDS. Honda Odyssey, 2007: VPD - 0.24, MDS - 191.67, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:48:28 --- INFO: MDS. Honda Odyssey, 2007: VPD - 0.98, MDS - 232.65, CONDITION - , EXCEPTION - 
2011-07-21 06:49:01 --- INFO: MDS. Honda Odyssey, 2008: VPD - 0.04, MDS - 350, CONDITION - nav|naqvigation|navi, EXCEPTION - 
2011-07-21 06:49:01 --- INFO: MDS. Honda Odyssey, 2008: VPD - 0.2, MDS - 395, CONDITION - , EXCEPTION - 
2011-07-21 06:49:13 --- INFO: MDS. Honda Odyssey, 2009: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:49:14 --- INFO: MDS. Honda Odyssey, 2009: VPD - 0.07, MDS - 542.86, CONDITION - honda, EXCEPTION - 
2011-07-21 06:49:14 --- INFO: MDS. Honda Odyssey, 2009: VPD - 0.07, MDS - 542.86, CONDITION - , EXCEPTION - 
2011-07-21 06:49:20 --- INFO: MDS. Honda Odyssey, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:49:20 --- INFO: MDS. Honda Odyssey, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:49:20 --- INFO: MDS. Honda Odyssey, 2010: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 06:51:11 --- INFO: MDS. Honda CR-V, 2007: VPD - 0.38, MDS - 271.05, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:51:11 --- INFO: MDS. Honda CR-V, 2007: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:51:11 --- INFO: MDS. Honda CR-V, 2007: VPD - 1, MDS - 315, CONDITION - a, EXCEPTION - 
2011-07-21 06:51:11 --- INFO: MDS. Honda CR-V, 2007: VPD - 1, MDS - 315, CONDITION - , EXCEPTION - 
2011-07-21 06:52:41 --- INFO: MDS. Honda CR-V, 2008: VPD - 0.29, MDS - 372.41, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:52:41 --- INFO: MDS. Honda CR-V, 2008: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:52:41 --- INFO: MDS. Honda CR-V, 2008: VPD - 0.6, MDS - 456.67, CONDITION - , EXCEPTION - 
2011-07-21 06:53:11 --- INFO: MDS. Honda CR-V, 2009: VPD - 0.04, MDS - 750, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:53:11 --- INFO: MDS. Honda CR-V, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:53:11 --- INFO: MDS. Honda CR-V, 2009: VPD - 0.13, MDS - 630.77, CONDITION - , EXCEPTION - 
2011-07-21 06:53:23 --- INFO: MDS. Honda CR-V, 2010: VPD - 0.04, MDS - 425, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:53:23 --- INFO: MDS. Honda CR-V, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:53:23 --- INFO: MDS. Honda CR-V, 2010: VPD - 0.11, MDS - 309.09, CONDITION - , EXCEPTION - 
2011-07-21 06:53:25 --- INFO: MDS. Honda CR-V, 2011: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:53:25 --- INFO: MDS. Honda CR-V, 2011: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:53:25 --- INFO: MDS. Honda CR-V, 2011: VPD - 0.04, MDS - 150, CONDITION - , EXCEPTION - 
2011-07-21 06:54:07 --- INFO: MDS. Honda Pilot, 2007: VPD - 0.07, MDS - 171.43, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:54:07 --- INFO: MDS. Honda Pilot, 2007: VPD - 0.49, MDS - 242.86, CONDITION - a, EXCEPTION - 
2011-07-21 06:54:07 --- INFO: MDS. Honda Pilot, 2007: VPD - 0.49, MDS - 242.86, CONDITION - , EXCEPTION - 
2011-07-21 06:54:25 --- INFO: MDS. Honda Pilot, 2008: VPD - 0.02, MDS - 400, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:54:25 --- INFO: MDS. Honda Pilot, 2008: VPD - 0.16, MDS - 375, CONDITION - , EXCEPTION - 
2011-07-21 06:54:44 --- INFO: MDS. Honda Pilot, 2009: VPD - 0.04, MDS - 400, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:54:44 --- INFO: MDS. Honda Pilot, 2009: VPD - 0.04, MDS - 1050, CONDITION - pilot, EXCEPTION - nav|navigation|navi
2011-07-21 06:54:44 --- INFO: MDS. Honda Pilot, 2009: VPD - 0.11, MDS - 527.27, CONDITION - , EXCEPTION - 
2011-07-21 06:54:49 --- INFO: MDS. Honda Pilot, 2010: VPD - 0.04, MDS - 400, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:54:49 --- INFO: MDS. Honda Pilot, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:54:49 --- INFO: MDS. Honda Pilot, 2010: VPD - 0.04, MDS - 525, CONDITION - , EXCEPTION - 
2011-07-21 06:54:58 --- INFO: MDS. Honda Pilot, 2011: VPD - 0.04, MDS - 600, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:54:58 --- INFO: MDS. Honda Pilot, 2011: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:54:58 --- INFO: MDS. Honda Pilot, 2011: VPD - 0.04, MDS - 800, CONDITION - , EXCEPTION - 
2011-07-21 06:55:02 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - Mitsubishi, EXCEPTION - 
2011-07-21 06:55:02 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:55:02 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:55:02 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:55:02 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 06:55:33 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.04, MDS - 275, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:55:33 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.07, MDS - 1028.57, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:55:33 --- INFO: MDS. Nissan Murano, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:55:33 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.09, MDS - 822.22, CONDITION - nissan, EXCEPTION - 
2011-07-21 06:55:33 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.09, MDS - 822.22, CONDITION - , EXCEPTION - 
2011-07-21 06:55:39 --- INFO: MDS. Nissan Murano, 2010: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:55:39 --- INFO: MDS. Nissan Murano, 2010: VPD - 0.02, MDS - 550, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:55:39 --- INFO: MDS. Nissan Murano, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:55:39 --- INFO: MDS. Nissan Murano, 2010: VPD - 0.02, MDS - 550, CONDITION - nissan, EXCEPTION - 
2011-07-21 06:55:39 --- INFO: MDS. Nissan Murano, 2010: VPD - 0.02, MDS - 550, CONDITION - , EXCEPTION - 
2011-07-21 06:55:43 --- INFO: MDS. Nissan Maxima, 2009: VPD - 0.16, MDS - 112.5, CONDITION - maxima, EXCEPTION - 
2011-07-21 06:55:43 --- INFO: MDS. Nissan Maxima, 2009: VPD - 0.09, MDS - 66.67, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 06:55:43 --- INFO: MDS. Nissan Maxima, 2009: VPD - 0.16, MDS - 112.5, CONDITION - , EXCEPTION - 
2011-07-21 06:55:51 --- INFO: MDS. Nissan Maxima, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:55:51 --- INFO: MDS. Nissan Maxima, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:55:51 --- INFO: MDS. Nissan Maxima, 2010: VPD - 0.04, MDS - 625, CONDITION - , EXCEPTION - 
2011-07-21 06:55:58 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0.02, MDS - 1250, CONDITION - nissan, EXCEPTION - 
2011-07-21 06:55:58 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:55:58 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:55:58 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:55:58 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0.02, MDS - 1250, CONDITION - , EXCEPTION - 
2011-07-21 06:56:06 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:56:06 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0.09, MDS - 233.33, CONDITION - nissan, EXCEPTION - 
2011-07-21 06:56:06 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0.02, MDS - 200, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:56:06 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:56:06 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0.09, MDS - 233.33, CONDITION - , EXCEPTION - 
2011-07-21 06:56:14 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0.07, MDS - 357.14, CONDITION - nissan , EXCEPTION - 
2011-07-21 06:56:14 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:56:14 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:56:14 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0.07, MDS - 357.14, CONDITION - , EXCEPTION - 
2011-07-21 06:56:18 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - nissan, EXCEPTION - 
2011-07-21 06:56:18 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:56:18 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:56:18 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 06:56:26 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0.02, MDS - 1050, CONDITION - nissan , EXCEPTION - 
2011-07-21 06:56:26 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:56:26 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:56:26 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0.02, MDS - 1050, CONDITION - , EXCEPTION - 
2011-07-21 06:58:13 --- INFO: MDS. Honda Accord, 2008: VPD - 0.13, MDS - 376.92, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 06:58:13 --- INFO: MDS. Honda Accord, 2008: VPD - 0.73, MDS - 432.88, CONDITION - , EXCEPTION - 
2011-07-21 06:58:47 --- INFO: MDS. Honda Accord, 2009: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:58:47 --- INFO: MDS. Honda Accord, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:58:47 --- INFO: MDS. Honda Accord, 2009: VPD - 0.16, MDS - 606.25, CONDITION - , EXCEPTION - 
2011-07-21 06:59:07 --- INFO: MDS. Honda Accord, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 06:59:07 --- INFO: MDS. Honda Accord, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 06:59:07 --- INFO: MDS. Honda Accord, 2010: VPD - 0.18, MDS - 338.89, CONDITION - , EXCEPTION - 
2011-07-21 07:00:38 --- INFO: MDS. Infiniti G, 2007: VPD - 0.22, MDS - 718.18, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 07:00:38 --- INFO: MDS. Infiniti G, 2007: VPD - 0.02, MDS - 50, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 07:00:38 --- INFO: MDS. Infiniti G, 2007: VPD - 0.31, MDS - 754.84, CONDITION - , EXCEPTION - 
2011-07-21 07:01:26 --- INFO: MDS. Infiniti G, 2008: VPD - 0.07, MDS - 1585.71, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 07:01:26 --- INFO: MDS. Infiniti G, 2008: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 07:01:26 --- INFO: MDS. Infiniti G, 2008: VPD - 0.11, MDS - 1318.18, CONDITION - , EXCEPTION - 
2011-07-21 07:01:38 --- INFO: MDS. Infiniti G, 2009: VPD - 0.11, MDS - 336.36, CONDITION - , EXCEPTION - 
2011-07-21 07:01:49 --- INFO: MDS. Infiniti G, 2010: VPD - 0.07, MDS - 400, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 07:01:49 --- INFO: MDS. Infiniti G, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 07:01:49 --- INFO: MDS. Infiniti G, 2010: VPD - 0.09, MDS - 366.67, CONDITION - , EXCEPTION - 
2011-07-21 08:00:04 --- INFO: PTM. Mercedes-Benz M-Class, 2006: found - 8, passed - 8
2011-07-21 08:00:08 --- INFO: PTM. Mercedes-Benz M-Class, 2007: found - 8, passed - 8
2011-07-21 08:00:17 --- INFO: PTM. Mercedes-Benz M-Class, 2008: found - 29, passed - 28
2011-07-21 08:00:23 --- INFO: PTM. Infiniti FX, 2009: found - 21, passed - 21
2011-07-21 08:00:27 --- INFO: PTM. Honda Accord Crosstour, 2010: found - 9, passed - 9
2011-07-21 08:00:53 --- INFO: PTM. Toyota Venza, 2009: found - 73, passed - 67
2011-07-21 08:01:01 --- INFO: PTM. Toyota Venza, 2010: found - 25, passed - 23
2011-07-21 08:01:18 --- INFO: PTM. BMW X5, 2007: found - 54, passed - 50
2011-07-21 08:01:42 --- INFO: PTM. BMW X5, 2008: found - 66, passed - 62
2011-07-21 08:01:52 --- INFO: PTM. Subaru Forester, 2009: found - 20, passed - 18
2011-07-21 08:01:57 --- INFO: PTM. Subaru Forester, 2010: found - 16, passed - 16
2011-07-21 08:02:21 --- INFO: PTM. Toyota Highlander, 2008: found - 59, passed - 55
2011-07-21 08:02:27 --- INFO: PTM. Toyota Highlander, 2009: found - 15, passed - 13
2011-07-21 08:02:47 --- INFO: PTM. Toyota Sienna, 2006: found - 57, passed - 54
2011-07-21 08:03:38 --- INFO: PTM. Toyota Sienna, 2007: found - 142, passed - 131
2011-07-21 08:04:02 --- INFO: PTM. Toyota Sienna, 2008: found - 56, passed - 56
2011-07-21 08:04:06 --- INFO: PTM. Toyota Sienna, 2009: found - 8, passed - 8
2011-07-21 08:04:14 --- INFO: PTM. Toyota Sienna, 2010: found - 16, passed - 15
2011-07-21 08:04:55 --- INFO: PTM. Acura MDX, 2007: found - 105, passed - 96
2011-07-21 08:05:52 --- INFO: PTM. Acura MDX, 2008: found - 141, passed - 130
2011-07-21 08:06:05 --- INFO: PTM. Acura MDX, 2009: found - 33, passed - 29
2011-07-21 08:06:36 --- INFO: PTM. Acura RDX, 2007: found - 76, passed - 69
2011-07-21 08:07:03 --- INFO: PTM. Acura RDX, 2008: found - 75, passed - 69
2011-07-21 08:07:12 --- INFO: PTM. Acura RDX, 2009: found - 24, passed - 22
2011-07-21 08:07:28 --- INFO: PTM. Acura TL, 2006: found - 45, passed - 41
2011-07-21 08:07:57 --- INFO: PTM. Acura TL, 2007: found - 83, passed - 73
2011-07-21 08:08:25 --- INFO: PTM. Acura TL, 2008: found - 80, passed - 73
2011-07-21 08:08:34 --- INFO: PTM. Acura TL, 2009: found - 26, passed - 24
2011-07-21 08:08:44 --- INFO: PTM. Acura TSX, 2006: found - 14, passed - 14
2011-07-21 08:09:09 --- INFO: PTM. Acura TSX, 2007: found - 60, passed - 57
2011-07-21 08:09:19 --- INFO: PTM. Acura TSX, 2008: found - 29, passed - 29
2011-07-21 08:09:31 --- INFO: PTM. Acura TSX, 2009: found - 26, passed - 24
2011-07-21 08:09:31 --- INFO: PTM. Acura TSX, 2010: found - 1, passed - 1
2011-07-21 08:09:50 --- INFO: PTM. Honda Odyssey, 2006: found - 48, passed - 46
2011-07-21 08:11:04 --- INFO: PTM. Honda Odyssey, 2007: found - 188, passed - 168
2011-07-21 08:11:30 --- INFO: PTM. Honda Odyssey, 2008: found - 71, passed - 66
2011-07-21 08:11:43 --- INFO: PTM. Honda Odyssey, 2009: found - 35, passed - 32
2011-07-21 08:11:49 --- INFO: PTM. Honda Odyssey, 2010: found - 17, passed - 15
2011-07-21 08:13:35 --- INFO: PTM. Honda CR-V, 2007: found - 274, passed - 256
2011-07-21 08:15:11 --- INFO: PTM. Honda CR-V, 2008: found - 247, passed - 233
2011-07-21 08:15:44 --- INFO: PTM. Honda CR-V, 2009: found - 76, passed - 70
2011-07-21 08:15:54 --- INFO: PTM. Honda CR-V, 2010: found - 29, passed - 24
2011-07-21 08:15:55 --- INFO: PTM. Honda CR-V, 2011: found - 4, passed - 3
2011-07-21 08:16:32 --- INFO: PTM. Honda Pilot, 2007: found - 100, passed - 84
2011-07-21 08:16:51 --- INFO: PTM. Honda Pilot, 2008: found - 53, passed - 52
2011-07-21 08:17:12 --- INFO: PTM. Honda Pilot, 2009: found - 54, passed - 46
2011-07-21 08:17:20 --- INFO: PTM. Honda Pilot, 2010: found - 19, passed - 18
2011-07-21 08:17:33 --- INFO: PTM. Honda Pilot, 2011: found - 30, passed - 27
2011-07-21 08:17:35 --- INFO: PTM. Mitsubishi Outlander, 2011: found - 5, passed - 5
2011-07-21 08:18:02 --- INFO: PTM. Nissan Murano, 2009: found - 70, passed - 68
2011-07-21 08:18:07 --- INFO: PTM. Nissan Murano, 2010: found - 10, passed - 10
2011-07-21 08:18:12 --- INFO: PTM. Nissan Maxima, 2009: found - 11, passed - 10
2011-07-21 08:18:23 --- INFO: PTM. Nissan Maxima, 2010: found - 23, passed - 22
2011-07-21 08:18:32 --- INFO: PTM. Nissan Pathfinder, 2006: found - 24, passed - 22
2011-07-21 08:18:39 --- INFO: PTM. Nissan Pathfinder, 2007: found - 17, passed - 16
2011-07-21 08:18:49 --- INFO: PTM. Nissan Pathfinder, 2008: found - 22, passed - 21
2011-07-21 08:18:52 --- INFO: PTM. Nissan Pathfinder, 2009: found - 8, passed - 6
2011-07-21 08:19:00 --- INFO: PTM. Nissan Pathfinder, 2010: found - 20, passed - 20
2011-07-21 08:20:50 --- INFO: PTM. Honda Accord, 2008: found - 288, passed - 264
2011-07-21 08:21:23 --- INFO: PTM. Honda Accord, 2009: found - 90, passed - 82
2011-07-21 08:21:43 --- INFO: PTM. Honda Accord, 2010: found - 53, passed - 48
2011-07-21 08:23:10 --- INFO: PTM. Infiniti G, 2007: found - 221, passed - 211
2011-07-21 08:24:07 --- INFO: PTM. Infiniti G, 2008: found - 140, passed - 127
2011-07-21 08:24:22 --- INFO: PTM. Infiniti G, 2009: found - 32, passed - 31
2011-07-21 08:24:32 --- INFO: PTM. Infiniti G, 2010: found - 30, passed - 28
2011-07-21 08:30:04 --- INFO: MDS. Mercedes-Benz M-Class, 2006: VPD - 0.07, MDS - 85.71, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:30:04 --- INFO: MDS. Mercedes-Benz M-Class, 2006: VPD - 0.09, MDS - 133.33, CONDITION - a, EXCEPTION - 
2011-07-21 08:30:04 --- INFO: MDS. Mercedes-Benz M-Class, 2006: VPD - 0.09, MDS - 133.33, CONDITION - , EXCEPTION - 
2011-07-21 08:30:09 --- INFO: MDS. Mercedes-Benz M-Class, 2007: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 08:30:09 --- INFO: MDS. Mercedes-Benz M-Class, 2007: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 08:30:18 --- INFO: MDS. Mercedes-Benz M-Class, 2008: VPD - 0.11, MDS - 281.82, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:30:18 --- INFO: MDS. Mercedes-Benz M-Class, 2008: VPD - 0.13, MDS - 269.23, CONDITION - a, EXCEPTION - 
2011-07-21 08:30:18 --- INFO: MDS. Mercedes-Benz M-Class, 2008: VPD - 0.13, MDS - 269.23, CONDITION - , EXCEPTION - 
2011-07-21 08:30:26 --- INFO: MDS. Infiniti FX, 2009: VPD - 0.02, MDS - 550, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:30:26 --- INFO: MDS. Infiniti FX, 2009: VPD - 0.09, MDS - 277.78, CONDITION - fx, EXCEPTION - 
2011-07-21 08:30:26 --- INFO: MDS. Infiniti FX, 2009: VPD - 0.09, MDS - 277.78, CONDITION - , EXCEPTION - 
2011-07-21 08:30:31 --- INFO: MDS. Honda Accord Crosstour, 2010: VPD - 0.02, MDS - 500, CONDITION - a, EXCEPTION - 
2011-07-21 08:30:31 --- INFO: MDS. Honda Accord Crosstour, 2010: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:30:31 --- INFO: MDS. Honda Accord Crosstour, 2010: VPD - 0.02, MDS - 500, CONDITION - , EXCEPTION - 
2011-07-21 08:30:59 --- INFO: MDS. Toyota Venza, 2009: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:30:59 --- INFO: MDS. Toyota Venza, 2009: VPD - 0.16, MDS - 500, CONDITION - toyota, EXCEPTION - 
2011-07-21 08:30:59 --- INFO: MDS. Toyota Venza, 2009: VPD - 0.16, MDS - 500, CONDITION - , EXCEPTION - 
2011-07-21 08:31:09 --- INFO: MDS. Toyota Venza, 2010: VPD - 0.02, MDS - 100, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:31:09 --- INFO: MDS. Toyota Venza, 2010: VPD - 0.13, MDS - 238.46, CONDITION - toyota, EXCEPTION - 
2011-07-21 08:31:09 --- INFO: MDS. Toyota Venza, 2010: VPD - 0.13, MDS - 238.46, CONDITION - , EXCEPTION - 
2011-07-21 08:31:30 --- INFO: MDS. BMW X5, 2007: VPD - 0.09, MDS - 211.11, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:31:30 --- INFO: MDS. BMW X5, 2007: VPD - 0.2, MDS - 310, CONDITION - bmw, EXCEPTION - 
2011-07-21 08:31:30 --- INFO: MDS. BMW X5, 2007: VPD - 0.2, MDS - 315, CONDITION - , EXCEPTION - 
2011-07-21 08:31:51 --- INFO: MDS. BMW X5, 2008: VPD - 0.29, MDS - 196.55, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:31:51 --- INFO: MDS. BMW X5, 2008: VPD - 0.89, MDS - 117.98, CONDITION - bmw, EXCEPTION - 
2011-07-21 08:31:51 --- INFO: MDS. BMW X5, 2008: VPD - 0.89, MDS - 117.98, CONDITION - , EXCEPTION - 
2011-07-21 08:32:00 --- INFO: MDS. Subaru Forester, 2009: VPD - 0, MDS - 0, CONDITION - nav|navi|navigation, EXCEPTION - 
2011-07-21 08:32:00 --- INFO: MDS. Subaru Forester, 2009: VPD - 0.07, MDS - 328.57, CONDITION - subaru, EXCEPTION - 
2011-07-21 08:32:00 --- INFO: MDS. Subaru Forester, 2009: VPD - 0.07, MDS - 328.57, CONDITION - , EXCEPTION - 
2011-07-21 08:32:05 --- INFO: MDS. Subaru Forester, 2010: VPD - 0.09, MDS - 222.22, CONDITION - subaru, EXCEPTION - 
2011-07-21 08:32:05 --- INFO: MDS. Subaru Forester, 2010: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:32:05 --- INFO: MDS. Subaru Forester, 2010: VPD - 0.09, MDS - 222.22, CONDITION - , EXCEPTION - 
2011-07-21 08:32:25 --- INFO: MDS. Toyota Highlander, 2008: VPD - 0.07, MDS - 200, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 08:32:25 --- INFO: MDS. Toyota Highlander, 2008: VPD - 0.27, MDS - 262.96, CONDITION - , EXCEPTION - 
2011-07-21 08:32:30 --- INFO: MDS. Toyota Highlander, 2009: VPD - 0.04, MDS - 100, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:32:30 --- INFO: MDS. Toyota Highlander, 2009: VPD - 0.07, MDS - 257.14, CONDITION - , EXCEPTION - 
2011-07-21 08:32:49 --- INFO: MDS. Toyota Sienna, 2006: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:32:49 --- INFO: MDS. Toyota Sienna, 2006: VPD - 0.16, MDS - 400, CONDITION - toyota, EXCEPTION - 
2011-07-21 08:32:49 --- INFO: MDS. Toyota Sienna, 2006: VPD - 0.16, MDS - 400, CONDITION - , EXCEPTION - 
2011-07-21 08:33:37 --- INFO: MDS. Toyota Sienna, 2007: VPD - 0.04, MDS - 225, CONDITION - navi|navigation|nav, EXCEPTION - 
2011-07-21 08:33:37 --- INFO: MDS. Toyota Sienna, 2007: VPD - 0.49, MDS - 334.69, CONDITION - , EXCEPTION - 
2011-07-21 08:34:00 --- INFO: MDS. Toyota Sienna, 2008: VPD - 0.16, MDS - 393.75, CONDITION - toyota, EXCEPTION - 
2011-07-21 08:34:00 --- INFO: MDS. Toyota Sienna, 2008: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 08:34:00 --- INFO: MDS. Toyota Sienna, 2008: VPD - 0.16, MDS - 393.75, CONDITION - , EXCEPTION - 
2011-07-21 08:34:04 --- INFO: MDS. Toyota Sienna, 2009: VPD - 0.02, MDS - 450, CONDITION - toyota, EXCEPTION - 
2011-07-21 08:34:04 --- INFO: MDS. Toyota Sienna, 2009: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:34:04 --- INFO: MDS. Toyota Sienna, 2009: VPD - 0.02, MDS - 450, CONDITION - , EXCEPTION - 
2011-07-21 08:34:09 --- INFO: MDS. Toyota Sienna, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:34:09 --- INFO: MDS. Toyota Sienna, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:34:09 --- INFO: MDS. Toyota Sienna, 2010: VPD - 0.04, MDS - 450, CONDITION - , EXCEPTION - 
2011-07-21 08:34:48 --- INFO: MDS. Acura MDX, 2007: VPD - 0.16, MDS - 481.25, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:34:48 --- INFO: MDS. Acura MDX, 2007: VPD - 0.47, MDS - 265.96, CONDITION - acura, EXCEPTION - 
2011-07-21 08:34:48 --- INFO: MDS. Acura MDX, 2007: VPD - 0.47, MDS - 265.96, CONDITION - , EXCEPTION - 
2011-07-21 08:35:38 --- INFO: MDS. Acura MDX, 2008: VPD - 0.51, MDS - 211.76, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:35:38 --- INFO: MDS. Acura MDX, 2008: VPD - 0.69, MDS - 247.83, CONDITION - mdx, EXCEPTION - 
2011-07-21 08:35:38 --- INFO: MDS. Acura MDX, 2008: VPD - 0.69, MDS - 247.83, CONDITION - , EXCEPTION - 
2011-07-21 08:35:51 --- INFO: MDS. Acura MDX, 2009: VPD - 0.11, MDS - 327.27, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:35:51 --- INFO: MDS. Acura MDX, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:35:51 --- INFO: MDS. Acura MDX, 2009: VPD - 0.13, MDS - 300, CONDITION - , EXCEPTION - 
2011-07-21 08:36:23 --- INFO: MDS. Acura RDX, 2007: VPD - 0.27, MDS - 325.93, CONDITION - a, EXCEPTION - 
2011-07-21 08:36:23 --- INFO: MDS. Acura RDX, 2007: VPD - 0.2, MDS - 240, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:36:23 --- INFO: MDS. Acura RDX, 2007: VPD - 0.27, MDS - 325.93, CONDITION - , EXCEPTION - 
2011-07-21 08:36:47 --- INFO: MDS. Acura RDX, 2008: VPD - 0.2, MDS - 225, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:36:47 --- INFO: MDS. Acura RDX, 2008: VPD - 0.51, MDS - 192.16, CONDITION - , EXCEPTION - 
2011-07-21 08:36:55 --- INFO: MDS. Acura RDX, 2009: VPD - 0.13, MDS - 230.77, CONDITION - acura, EXCEPTION - 
2011-07-21 08:36:55 --- INFO: MDS. Acura RDX, 2009: VPD - 0.07, MDS - 228.57, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:36:55 --- INFO: MDS. Acura RDX, 2009: VPD - 0.13, MDS - 230.77, CONDITION - , EXCEPTION - 
2011-07-21 08:37:10 --- INFO: MDS. Acura TL, 2006: VPD - 0.11, MDS - 190.91, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:37:10 --- INFO: MDS. Acura TL, 2006: VPD - 0.24, MDS - 233.33, CONDITION - acura, EXCEPTION - 
2011-07-21 08:37:10 --- INFO: MDS. Acura TL, 2006: VPD - 0.24, MDS - 233.33, CONDITION - , EXCEPTION - 
2011-07-21 08:37:42 --- INFO: MDS. Acura TL, 2007: VPD - 0.2, MDS - 220, CONDITION - nav|navi|navigation, EXCEPTION - 
2011-07-21 08:37:42 --- INFO: MDS. Acura TL, 2007: VPD - 0.73, MDS - 158.9, CONDITION - acura, EXCEPTION - 
2011-07-21 08:37:42 --- INFO: MDS. Acura TL, 2007: VPD - 0.73, MDS - 158.9, CONDITION - , EXCEPTION - 
2011-07-21 08:38:10 --- INFO: MDS. Acura TL, 2008: VPD - 0.33, MDS - 287.88, CONDITION - acura, EXCEPTION - 
2011-07-21 08:38:10 --- INFO: MDS. Acura TL, 2008: VPD - 0.16, MDS - 300, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:38:10 --- INFO: MDS. Acura TL, 2008: VPD - 0.33, MDS - 287.88, CONDITION - , EXCEPTION - 
2011-07-21 08:38:24 --- INFO: MDS. Acura TL, 2009: VPD - 0.07, MDS - 328.57, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:38:24 --- INFO: MDS. Acura TL, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:38:24 --- INFO: MDS. Acura TL, 2009: VPD - 0.09, MDS - 333.33, CONDITION - , EXCEPTION - 
2011-07-21 08:38:29 --- INFO: MDS. Acura TSX, 2006: VPD - 0.04, MDS - 125, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:38:29 --- INFO: MDS. Acura TSX, 2006: VPD - 0.07, MDS - 242.86, CONDITION - acura, EXCEPTION - 
2011-07-21 08:38:29 --- INFO: MDS. Acura TSX, 2006: VPD - 0.07, MDS - 242.86, CONDITION - , EXCEPTION - 
2011-07-21 08:38:52 --- INFO: MDS. Acura TSX, 2007: VPD - 0.11, MDS - 136.36, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:38:52 --- INFO: MDS. Acura TSX, 2007: VPD - 0.58, MDS - 148.28, CONDITION - acura, EXCEPTION - 
2011-07-21 08:38:52 --- INFO: MDS. Acura TSX, 2007: VPD - 0.58, MDS - 148.28, CONDITION - , EXCEPTION - 
2011-07-21 08:39:05 --- INFO: MDS. Acura TSX, 2008: VPD - 0.11, MDS - 218.18, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:39:05 --- INFO: MDS. Acura TSX, 2008: VPD - 0.11, MDS - 309.09, CONDITION - acura, EXCEPTION - 
2011-07-21 08:39:05 --- INFO: MDS. Acura TSX, 2008: VPD - 0.11, MDS - 309.09, CONDITION - , EXCEPTION - 
2011-07-21 08:39:13 --- INFO: MDS. Acura TSX, 2009: VPD - 0.02, MDS - 250, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:39:13 --- INFO: MDS. Acura TSX, 2009: VPD - 0.36, MDS - 116.67, CONDITION - acura, EXCEPTION - 
2011-07-21 08:39:13 --- INFO: MDS. Acura TSX, 2009: VPD - 0.36, MDS - 116.67, CONDITION - , EXCEPTION - 
2011-07-21 08:39:14 --- INFO: MDS. Acura TSX, 2010: VPD - 0.04, MDS - 50, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:39:14 --- INFO: MDS. Acura TSX, 2010: VPD - 0.29, MDS - 48.28, CONDITION - acura, EXCEPTION - 
2011-07-21 08:39:14 --- INFO: MDS. Acura TSX, 2010: VPD - 0.31, MDS - 48.39, CONDITION - , EXCEPTION - 
2011-07-21 08:39:29 --- INFO: MDS. Honda Odyssey, 2006: VPD - 0.31, MDS - 200, CONDITION - a, EXCEPTION - 
2011-07-21 08:39:29 --- INFO: MDS. Honda Odyssey, 2006: VPD - 0.11, MDS - 90.91, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:39:29 --- INFO: MDS. Honda Odyssey, 2006: VPD - 0.31, MDS - 200, CONDITION - , EXCEPTION - 
2011-07-21 08:40:43 --- INFO: MDS. Honda Odyssey, 2007: VPD - 0.24, MDS - 191.67, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:40:43 --- INFO: MDS. Honda Odyssey, 2007: VPD - 0.98, MDS - 232.65, CONDITION - , EXCEPTION - 
2011-07-21 08:41:08 --- INFO: MDS. Honda Odyssey, 2008: VPD - 0.04, MDS - 350, CONDITION - nav|naqvigation|navi, EXCEPTION - 
2011-07-21 08:41:08 --- INFO: MDS. Honda Odyssey, 2008: VPD - 0.2, MDS - 395, CONDITION - , EXCEPTION - 
2011-07-21 08:41:21 --- INFO: MDS. Honda Odyssey, 2009: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:41:22 --- INFO: MDS. Honda Odyssey, 2009: VPD - 0.07, MDS - 542.86, CONDITION - honda, EXCEPTION - 
2011-07-21 08:41:22 --- INFO: MDS. Honda Odyssey, 2009: VPD - 0.07, MDS - 542.86, CONDITION - , EXCEPTION - 
2011-07-21 08:41:28 --- INFO: MDS. Honda Odyssey, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:41:28 --- INFO: MDS. Honda Odyssey, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:41:28 --- INFO: MDS. Honda Odyssey, 2010: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 08:43:20 --- INFO: MDS. Honda CR-V, 2007: VPD - 0.38, MDS - 271.05, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:43:20 --- INFO: MDS. Honda CR-V, 2007: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:43:20 --- INFO: MDS. Honda CR-V, 2007: VPD - 1, MDS - 315, CONDITION - a, EXCEPTION - 
2011-07-21 08:43:20 --- INFO: MDS. Honda CR-V, 2007: VPD - 1, MDS - 315, CONDITION - , EXCEPTION - 
2011-07-21 08:44:57 --- INFO: MDS. Honda CR-V, 2008: VPD - 0.29, MDS - 372.41, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:44:57 --- INFO: MDS. Honda CR-V, 2008: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:44:57 --- INFO: MDS. Honda CR-V, 2008: VPD - 0.6, MDS - 456.67, CONDITION - , EXCEPTION - 
2011-07-21 08:45:30 --- INFO: MDS. Honda CR-V, 2009: VPD - 0.04, MDS - 750, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:45:30 --- INFO: MDS. Honda CR-V, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:45:30 --- INFO: MDS. Honda CR-V, 2009: VPD - 0.13, MDS - 630.77, CONDITION - , EXCEPTION - 
2011-07-21 08:45:41 --- INFO: MDS. Honda CR-V, 2010: VPD - 0.04, MDS - 425, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:45:41 --- INFO: MDS. Honda CR-V, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:45:41 --- INFO: MDS. Honda CR-V, 2010: VPD - 0.11, MDS - 309.09, CONDITION - , EXCEPTION - 
2011-07-21 08:45:45 --- INFO: MDS. Honda CR-V, 2011: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:45:45 --- INFO: MDS. Honda CR-V, 2011: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:45:45 --- INFO: MDS. Honda CR-V, 2011: VPD - 0.04, MDS - 150, CONDITION - , EXCEPTION - 
2011-07-21 08:46:30 --- INFO: MDS. Honda Pilot, 2007: VPD - 0.07, MDS - 171.43, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:46:30 --- INFO: MDS. Honda Pilot, 2007: VPD - 0.49, MDS - 242.86, CONDITION - a, EXCEPTION - 
2011-07-21 08:46:30 --- INFO: MDS. Honda Pilot, 2007: VPD - 0.49, MDS - 242.86, CONDITION - , EXCEPTION - 
2011-07-21 08:46:50 --- INFO: MDS. Honda Pilot, 2008: VPD - 0.02, MDS - 400, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:46:50 --- INFO: MDS. Honda Pilot, 2008: VPD - 0.16, MDS - 375, CONDITION - , EXCEPTION - 
2011-07-21 08:47:08 --- INFO: MDS. Honda Pilot, 2009: VPD - 0.07, MDS - 228.57, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:47:08 --- INFO: MDS. Honda Pilot, 2009: VPD - 0.04, MDS - 1050, CONDITION - pilot, EXCEPTION - nav|navigation|navi
2011-07-21 08:47:08 --- INFO: MDS. Honda Pilot, 2009: VPD - 0.13, MDS - 446.15, CONDITION - , EXCEPTION - 
2011-07-21 08:47:13 --- INFO: MDS. Honda Pilot, 2010: VPD - 0.04, MDS - 400, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:47:13 --- INFO: MDS. Honda Pilot, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:47:13 --- INFO: MDS. Honda Pilot, 2010: VPD - 0.04, MDS - 525, CONDITION - , EXCEPTION - 
2011-07-21 08:47:26 --- INFO: MDS. Honda Pilot, 2011: VPD - 0.04, MDS - 600, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:47:26 --- INFO: MDS. Honda Pilot, 2011: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:47:26 --- INFO: MDS. Honda Pilot, 2011: VPD - 0.04, MDS - 800, CONDITION - , EXCEPTION - 
2011-07-21 08:47:28 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - Mitsubishi, EXCEPTION - 
2011-07-21 08:47:28 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:47:28 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:47:28 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:47:28 --- INFO: MDS. Mitsubishi Outlander, 2011: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 08:47:59 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.04, MDS - 275, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:47:59 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.07, MDS - 1028.57, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:47:59 --- INFO: MDS. Nissan Murano, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:47:59 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.09, MDS - 822.22, CONDITION - nissan, EXCEPTION - 
2011-07-21 08:47:59 --- INFO: MDS. Nissan Murano, 2009: VPD - 0.09, MDS - 822.22, CONDITION - , EXCEPTION - 
2011-07-21 08:48:02 --- INFO: MDS. Nissan Murano, 2010: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:48:02 --- INFO: MDS. Nissan Murano, 2010: VPD - 0.02, MDS - 550, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:48:02 --- INFO: MDS. Nissan Murano, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:48:02 --- INFO: MDS. Nissan Murano, 2010: VPD - 0.02, MDS - 550, CONDITION - nissan, EXCEPTION - 
2011-07-21 08:48:02 --- INFO: MDS. Nissan Murano, 2010: VPD - 0.02, MDS - 550, CONDITION - , EXCEPTION - 
2011-07-21 08:48:08 --- INFO: MDS. Nissan Maxima, 2009: VPD - 0.16, MDS - 112.5, CONDITION - maxima, EXCEPTION - 
2011-07-21 08:48:08 --- INFO: MDS. Nissan Maxima, 2009: VPD - 0.09, MDS - 66.67, CONDITION - nav|navigation|navi , EXCEPTION - 
2011-07-21 08:48:08 --- INFO: MDS. Nissan Maxima, 2009: VPD - 0.16, MDS - 112.5, CONDITION - , EXCEPTION - 
2011-07-21 08:48:18 --- INFO: MDS. Nissan Maxima, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:48:18 --- INFO: MDS. Nissan Maxima, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:48:18 --- INFO: MDS. Nissan Maxima, 2010: VPD - 0.04, MDS - 625, CONDITION - , EXCEPTION - 
2011-07-21 08:48:28 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0.02, MDS - 1250, CONDITION - nissan, EXCEPTION - 
2011-07-21 08:48:28 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:48:28 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:48:28 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:48:28 --- INFO: MDS. Nissan Pathfinder, 2006: VPD - 0.02, MDS - 1250, CONDITION - , EXCEPTION - 
2011-07-21 08:48:35 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0, MDS - 0, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:48:35 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0.09, MDS - 233.33, CONDITION - nissan, EXCEPTION - 
2011-07-21 08:48:35 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0.02, MDS - 200, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:48:35 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:48:35 --- INFO: MDS. Nissan Pathfinder, 2007: VPD - 0.09, MDS - 233.33, CONDITION - , EXCEPTION - 
2011-07-21 08:48:47 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0.07, MDS - 357.14, CONDITION - nissan , EXCEPTION - 
2011-07-21 08:48:47 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:48:47 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:48:47 --- INFO: MDS. Nissan Pathfinder, 2008: VPD - 0.07, MDS - 357.14, CONDITION - , EXCEPTION - 
2011-07-21 08:48:50 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - nissan, EXCEPTION - 
2011-07-21 08:48:50 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:48:50 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:48:50 --- INFO: MDS. Nissan Pathfinder, 2009: VPD - 0, MDS - 0, CONDITION - , EXCEPTION - 
2011-07-21 08:48:59 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0.02, MDS - 1050, CONDITION - nissan , EXCEPTION - 
2011-07-21 08:48:59 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:48:59 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:48:59 --- INFO: MDS. Nissan Pathfinder, 2010: VPD - 0.02, MDS - 1050, CONDITION - , EXCEPTION - 
2011-07-21 08:50:59 --- INFO: MDS. Honda Accord, 2008: VPD - 0.13, MDS - 376.92, CONDITION - nav|navigation|navi, EXCEPTION - 
2011-07-21 08:50:59 --- INFO: MDS. Honda Accord, 2008: VPD - 0.73, MDS - 432.88, CONDITION - , EXCEPTION - 
2011-07-21 08:51:33 --- INFO: MDS. Honda Accord, 2009: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:51:33 --- INFO: MDS. Honda Accord, 2009: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:51:33 --- INFO: MDS. Honda Accord, 2009: VPD - 0.16, MDS - 606.25, CONDITION - , EXCEPTION - 
2011-07-21 08:51:53 --- INFO: MDS. Honda Accord, 2010: VPD - 0, MDS - 0, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:51:53 --- INFO: MDS. Honda Accord, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:51:53 --- INFO: MDS. Honda Accord, 2010: VPD - 0.18, MDS - 338.89, CONDITION - , EXCEPTION - 
2011-07-21 08:53:28 --- INFO: MDS. Infiniti G, 2007: VPD - 0.22, MDS - 718.18, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:53:28 --- INFO: MDS. Infiniti G, 2007: VPD - 0.02, MDS - 50, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:53:28 --- INFO: MDS. Infiniti G, 2007: VPD - 0.31, MDS - 754.84, CONDITION - , EXCEPTION - 
2011-07-21 08:54:24 --- INFO: MDS. Infiniti G, 2008: VPD - 0.07, MDS - 1585.71, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:54:24 --- INFO: MDS. Infiniti G, 2008: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:54:24 --- INFO: MDS. Infiniti G, 2008: VPD - 0.11, MDS - 1318.18, CONDITION - , EXCEPTION - 
2011-07-21 08:54:40 --- INFO: MDS. Infiniti G, 2009: VPD - 0.11, MDS - 336.36, CONDITION - , EXCEPTION - 
2011-07-21 08:54:55 --- INFO: MDS. Infiniti G, 2010: VPD - 0.07, MDS - 400, CONDITION - navigation|awd|2wd|4wd, EXCEPTION - 
2011-07-21 08:54:55 --- INFO: MDS. Infiniti G, 2010: VPD - 0, MDS - 0, CONDITION - navigation,camera,dvd, EXCEPTION - 
2011-07-21 08:54:55 --- INFO: MDS. Infiniti G, 2010: VPD - 0.09, MDS - 366.67, CONDITION - , EXCEPTION - 