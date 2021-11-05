<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

register_deactivation_hook(SAFEALTERNATIVE_PLUGIN_FILE, function () {
    global $wpdb;

    $sql_delete_county = "DROP TABLE IF EXISTS `courier_counties`;";
    $wpdb->query($sql_delete_county);

    $sql_delete_localities = "DROP TABLE IF EXISTS `courier_localities`;";
    $wpdb->query($sql_delete_localities);

    $sql_delete_zipcodes = "DROP TABLE IF EXISTS `courier_zipcodes`;";
    $wpdb->query($sql_delete_zipcodes);

    // FAN
    update_option('enable_fan_print', '0');
    update_option('enable_fan_shipping', '0');

    // CARGUS
    update_option('enable_cargus_print', '0');
    update_option('enable_cargus_shipping', '0');

    // GLS
    update_option('enable_gls_print', '0');
    update_option('enable_gls_shipping', '0');

    // DPD
    update_option('enable_dpd_print', '0');
    update_option('enable_dpd_shipping', '0');

    
    // Bookurier
    update_option('enable_bookurier_print', '0');
    update_option('enable_bookurier_shipping', '0');

    // NemoExpress
    update_option('enable_nemo_print', '0');
    update_option('enable_nemo_shipping', '0');


    

    // Extra
    update_option('enable_checkout_city_select', '0');
    update_option('enable_pers_fiz_jurid', '0');
    delete_transient('dpd_sender_list');
  

    //Remove cron jobs
    wp_unschedule_event(wp_next_scheduled('safealternative_gls_awb_update'), 'safealternative_gls_awb_update');
    wp_unschedule_event(wp_next_scheduled('safealternative_fan_courier_awb_update'), 'safealternative_fan_courier_awb_update');
    wp_unschedule_event(wp_next_scheduled('safealternative_urgent_cargus_awb_update'), 'safealternative_urgent_cargus_awb_update');
    wp_unschedule_event(wp_next_scheduled('safealternative_dpd_awb_update'), 'safealternative_dpd_awb_update');
    wp_unschedule_event(wp_next_scheduled('safealternative_nemo_awb_update'), 'safealternative_nemo_awb_update');
 
});
