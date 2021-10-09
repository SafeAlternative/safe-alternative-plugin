<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if ( get_option('enable_fan_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/fancourier/initialize.php';
}

if ( get_option('enable_cargus_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/cargus/initialize.php';
}

if ( get_option('enable_gls_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/gls/initialize.php';
}

if ( get_option('enable_dpd_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/dpd/initialize.php';
}

if ( get_option('enable_sameday_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/sameday/initialize.php';
}

if ( get_option('enable_bookurier_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/bookurier/initialize.php';
}

if ( get_option('enable_nemo_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/nemo/initialize.php';
}

if ( get_option('enable_memex_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/memex/initialize.php';
}

if ( get_option('enable_optimus_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/optimus/initialize.php';
}

if ( get_option('enable_express_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/express/initialize.php';
}

if ( get_option('enable_team_print') == '1' ) {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/team/initialize.php';
}

//Global bulk download
add_action('admin_footer', 'safealternative_add_bulk_download_action');
function safealternative_add_bulk_download_action()
{
    global $post_type;

    if ('shop_order' == $post_type) {
        wp_enqueue_script(
            'bulk_download_admin_js',
            SAFEALTERNATIVE_PLUGIN_URL . '/includes/print-methods/assets/js/bulkDownload.min.js',
            'jquery',
            '1.0.3'
        );
        wp_enqueue_script(
            'bulk_send_emails_admin_js',
            SAFEALTERNATIVE_PLUGIN_URL . '/includes/print-methods/assets/js/bulkSendEmails.min.js',
            'jquery',
            '1.0.0'
        );
    }
}

