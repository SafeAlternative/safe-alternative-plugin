<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

add_action('plugins_loaded', 'safealternative_plugin_db_update');
function safealternative_plugin_db_update()
{
    if (get_option('safealternative_db_ver') != SAFEALTERNATIVE_DB_VER) {
        global $wpdb;

        $sql_delete_county = "DROP TABLE IF EXISTS `courier_counties`;";
        $wpdb->query($sql_delete_county);

        $sql_delete_localities = "DROP TABLE IF EXISTS `courier_localities`;";
        $wpdb->query($sql_delete_localities);

        $sql_delete_zipcodes = "DROP TABLE IF EXISTS `courier_zipcodes`;";
        $wpdb->query($sql_delete_zipcodes);

        // Wait for the last table to be dropped
        if ($wpdb->get_var("SHOW TABLES LIKE 'courier_zipcodes'") == 'courier_zipcodes') {
            sleep(3);
        }

        // Defined in handle_plugin_shipping_prereq
        CR_create_db();
    }
}
