<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

register_activation_hook(SAFEALTERNATIVE_PLUGIN_FILE, 'CR_create_db');
function CR_create_db()
{
	global $wpdb;
	$query_limit = 300;

	CR_create_tables($wpdb);
	CR_insert_counties($wpdb);
	CR_insert_localities($wpdb, $query_limit);
	CR_insert_zipcodes($wpdb, $query_limit);

	update_option('safealternative_db_ver', SAFEALTERNATIVE_DB_VER);
}

function CR_create_tables($wpdb)
{
	$wpdb->query("CREATE TABLE IF NOT EXISTS `courier_localities` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`fan_locality_id` int(15) DEFAULT NULL,
				`cargus_locality_id` int(15) DEFAULT NULL,
				`locality_name` varchar(125) NOT NULL,
				`fan_locality_name` varchar(125) NULL DEFAULT NULL,
				`cargus_locality_name` varchar(125) NULL DEFAULT NULL,
				`county_initials` varchar(2) NOT NULL,
				`county_name` varchar(75) NOT NULL,
				`fan_extra_km` int(11) DEFAULT NULL,
				`cargus_extra_km` int(11) DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE KEY `county_locality_combo` (`county_initials`,`locality_name`)
				) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci;");

	$wpdb->query("CREATE TABLE IF NOT EXISTS `courier_zipcodes` (
				`id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
				`County` VARCHAR(25) NOT NULL,
				`City`  VARCHAR(50) NOT NULL,
				`Street` VARCHAR(150) DEFAULT NULL,
				`ZipCode` VARCHAR(10) NOT NULL,
				PRIMARY KEY (`id`)
				) ENGINE=InnoDB DEFAULT COLLATE=utf8mb4_unicode_ci;");

	$wpdb->query("CREATE TABLE IF NOT EXISTS `courier_counties` (
				`id_county` int(11) NOT NULL AUTO_INCREMENT,
				`county_name` varchar(125) NOT NULL,
				`county_code` varchar(2) NOT NULL,
				PRIMARY KEY (`id_county`),
				UNIQUE KEY `id_county` (`id_county`)
				) ENGINE=INNODB DEFAULT COLLATE=utf8mb4_unicode_ci AUTO_INCREMENT=43 ;");
}

function CR_insert_counties($wpdb)
{
	$wpdb->query("INSERT INTO `courier_counties` (`id_county`, `county_name`, `county_code`) VALUES (1, 'Alba', 'AB'), (2, 'Arad', 'AR'), (3, 'Arges', 'AG'), (4, 'Bacau', 'BC'), (5, 'Bihor', 'BH'), (6, 'Bistrita-Nasaud', 'BN'), (7, 'Botosani', 'BT'), (8, 'Braila', 'BR'), (9, 'Brasov', 'BV'), (10, 'Buzau', 'BZ'), (11, 'Calarasi', 'CL'), (12, 'Caras-Severin', 'CS'), (13, 'Cluj', 'CJ'), (14, 'Constanta', 'CT'), (15, 'Covasna', 'CV'), (16, 'Dambovita', 'DB'), (17, 'Dolj', 'DJ'), (18, 'Galati', 'GL'), (19, 'Giurgiu', 'GR'), (20, 'Gorj', 'GJ'), (21, 'Harghita', 'HR'), (22, 'Hunedoara', 'HD'), (23, 'Ialomita', 'IL'), (24, 'Iasi', 'IS'), (25, 'Ilfov', 'IF'), (26, 'Maramures', 'MM'), (27, 'Mehedinti', 'MH'), (28, 'Mures', 'MS'), (29, 'Neamt', 'NT'), (30, 'Olt', 'OT'), (31, 'Prahova', 'PH'), (32, 'Salaj', 'SJ'), (33, 'Satu Mare', 'SM'), (34, 'Sibiu', 'SB'), (35, 'Suceava', 'SV'), (36, 'Teleorman', 'TR'), (37, 'Timis', 'TM'), (38, 'Tulcea', 'TL'), (39, 'Valcea', 'VL'), (40, 'Vaslui', 'VS'), (41, 'Vrancea', 'VN'), (42, 'Bucuresti', 'B');");
}

function CR_insert_localities($wpdb, $query_limit)
{
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$sqlData = plugin_dir_path(__DIR__). DIRECTORY_SEPARATOR .'includes'.DIRECTORY_SEPARATOR.'courier_localities.sql';	
	$insertSql = file_get_contents($sqlData);
    $insertSqlChunks = explode(';', $insertSql);
    foreach ($insertSqlChunks as $query) {
		dbDelta( $query . ';' );

    }
}

function CR_insert_zipcodes($wpdb, $query_limit)
{
	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	$sqlData = plugin_dir_path(__DIR__). DIRECTORY_SEPARATOR .'includes'.DIRECTORY_SEPARATOR.'courier_zipcodes.sql';	
	$insertSql = file_get_contents($sqlData);
    $insertSqlChunks = explode(';', $insertSql);
    foreach ($insertSqlChunks as $query) {
		dbDelta( $query . ';' );

    }

}
