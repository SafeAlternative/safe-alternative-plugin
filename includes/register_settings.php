<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

add_action('admin_init', function () {

    //FacturarePersFizica/Juridica
    add_option('enable_pers_fiz_jurid', '0');
    register_setting('safealternative_settings', 'enable_pers_fiz_jurid');

    // FAN
    add_option('enable_fan_print', '0');
    add_option('enable_fan_shipping', '0');
    register_setting('safealternative_settings', 'enable_fan_print');
    register_setting('safealternative_settings', 'enable_fan_shipping');

    // CARGUS
    add_option('enable_cargus_print', '0');
    add_option('enable_cargus_shipping', '0');
    register_setting('safealternative_settings', 'enable_cargus_print');
    register_setting('safealternative_settings', 'enable_cargus_shipping');

    // GLS
    add_option('enable_gls_print', '0');
    add_option('enable_gls_shipping', '0');
    register_setting('safealternative_settings', 'enable_gls_print');
    register_setting('safealternative_settings', 'enable_gls_shipping');

    //Nemo
    add_option('enable_nemo_print', '0');
    add_option('enable_nemo_shipping', '0');
    register_setting('safealternative_settings', 'enable_nemo_print');
    register_setting('safealternative_settings', 'enable_nemo_shipping');

    // DPD
    add_option('enable_dpd_print', '0');
    add_option('enable_dpd_shipping', '0');
    register_setting('safealternative_settings', 'enable_dpd_print');
    register_setting('safealternative_settings', 'enable_dpd_shipping');


    // Bookurier
    add_option('enable_bookurier_print', '0');
    add_option('enable_bookurier_shipping', '0');
    register_setting('safealternative_settings', 'enable_bookurier_print');
    register_setting('safealternative_settings', 'enable_bookurier_shipping');

    
    // SafeAlternative
    add_option('user_safealternative', '');
    add_option('password_safealternative', '');
    add_option('auth_validity', '0');
    add_option('token', '');
    add_option('enable_checkout_city_select', '0');
    add_option('courier_email_from', '');
    add_option('safealternative_is_multisite', '0');
    register_setting('safealternative_settings', 'user_safealternative');
    register_setting('safealternative_settings', 'password_safealternative');
    register_setting('safealternative_settings', 'auth_validity');
    register_setting('safealternative_settings', 'token');
    register_setting('safealternative_settings', 'enable_checkout_city_select');
    register_setting('safealternative_settings', 'courier_email_from');
    register_setting('safealternative_settings', 'safealternative_is_multisite');

    // Shipping methods order
    add_option('safealternative_shipping_methods_order', '');
    register_setting('safealternative_shipping_methods_order', 'safealternative_shipping_methods_order');

    // SafeAlternative DB
    add_option('safealternative_db_ver', '1.0.0');
    add_option('safealternative_initial_user_report', '0');
});
