<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

register_activation_hook(SAFEALTERNATIVE_PLUGIN_FILE, function () {
    global $wpdb;

    if (!function_exists('deactivate_plugins')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }

    $plugins = get_option('active_plugins');

    foreach ($plugins as $plugin) {

        if (strpos($plugin, 'safealternative-urgentcargus-shippingcalculate') !== false) {
            deactivate_plugins($plugin);

            $sql_delete_county = "DROP TABLE IF EXISTS `courier_counties`;";
            $wpdb->query($sql_delete_county);

            $sql_delete_localities = "DROP TABLE IF EXISTS `courier_localities`;";
            $wpdb->query($sql_delete_localities);

            $sql_delete_old_cargus = "DROP TABLE IF EXISTS `courier_localities_urgentcargus`;";
            $wpdb->query($sql_delete_old_cargus);
        }

        if (strpos($plugin, 'safealternative-fancourier-shippingcalculate') !== false) {
            deactivate_plugins($plugin);

            $sql_delete_county = "DROP TABLE IF EXISTS `courier_counties`;";
            $wpdb->query($sql_delete_county);

            $sql_delete_localities = "DROP TABLE IF EXISTS `courier_localities`;";
            $wpdb->query($sql_delete_localities);

            $sql_delete_old_fan = "DROP TABLE IF EXISTS `courier_localities_fan`;";
            $wpdb->query($sql_delete_old_fan);
        }

        if (strpos($plugin, 'safealternative-fancourier-printawb') !== false) {
            deactivate_plugins($plugin);
        }

        if (strpos($plugin, 'safealternative-urgentcargus-printawb') !== false) {
            deactivate_plugins($plugin);
        }

        if (strpos($plugin, 'gls-courier-printing-awb') !== false) {
            deactivate_plugins($plugin);
        }

        if (strpos($plugin, 'safealternative-shipping-methods') !== false) {
            deactivate_plugins($plugin);
        }
    }

    $timestamp_fan = wp_next_scheduled('fancourier_cron_job');
    wp_unschedule_event($timestamp_fan, 'fancourier_cron_job');

    $timestamp_cargus = wp_next_scheduled('mycronjob_urgent_cargus');
    wp_unschedule_event($timestamp_cargus, 'mycronjob_urgent_cargus');

    $timestamp_gls = wp_next_scheduled('gls_awb_cron_job');
    wp_unschedule_event($timestamp_gls, 'gls_awb_cron_job');
});
