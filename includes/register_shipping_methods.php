<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

$safealternative_shipping_select_active = false;

if (get_option('enable_fan_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/fancourier/fan-courier-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_cargus_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/urgentcargus/urgentcargus-courier-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_gls_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/gls/gls-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_dpd_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/dpd/dpd-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_sameday_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/sameday/sameday-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_bookurier_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/bookurier/bookurier-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_nemo_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/nemo/nemo-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_memex_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/memex/memex-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_optimus_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/optimus/optimus-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_express_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/express/express-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if (get_option('enable_team_shipping') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/team/team-shipping-calculate.php';
    $safealternative_shipping_select_active = true;
}

if ($safealternative_shipping_select_active || get_option('enable_checkout_city_select')) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/wc_city_select/wc-city-select.php';

    add_action('wp_enqueue_scripts', function () {
        wp_enqueue_script(
            'city_autocomplete_hotfix',
            SAFEALTERNATIVE_PLUGIN_URL . '/includes/shipping-methods/wc_city_select/assets/js/script.js',
            array('jquery', 'wc-city-select'),
            '1.0.4'
        );
    });
}
