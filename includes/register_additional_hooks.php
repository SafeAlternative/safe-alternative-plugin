<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Add SafeAlternative cron interval
add_filter('cron_schedules', function ($schedules) {
    $schedules['twicedaily_safealternative'] = array(
        'interval' => 12 * HOUR_IN_SECONDS,
        'display' => __('Twice Daily Safealternative')
    );
    $schedules['safealternative_memex_daily_pickup'] = array(
        'interval' => 24 * HOUR_IN_SECONDS,
        'display' => __('Memex Pickup Safealternative')
    );
    return $schedules;
}, 1000);

// Add SafeAlternative WooCommerce search by AWB number
add_filter('woocommerce_shop_order_search_fields', 'safealternative_add_awb_search');
function safealternative_add_awb_search(array $search_fields)
{
    if (empty($search_fields)) $search_fields = array();
    return array_merge(
        array("awb_bookurier", "awb_urgent_cargus", "awb_dpd", "awb_fan", "awb_gls", "awb_sameday", "awb_memex"),
        $search_fields
    );
}

add_action('admin_notices', 'safealternative_show_admin_email_notification', 20);
function safealternative_show_admin_email_notification()
{
    if ($message = get_transient('email_sent_success')) {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php _e($message, 'safealternative-email-woocommerce'); ?></p>
        </div>
        <?php
        delete_transient('email_sent_success');
    }

    if ($message = get_transient('email_sent_error')) {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php _e($message, 'safealternative-email-woocommerce'); ?></p>
        </div>
        <?php
        delete_transient('email_sent_error');
    }
}

add_action("wp_ajax_safealternative_reset_mail", function () {
    $courier = sanitize_text_field($_POST['courier']);

    switch ($courier) {
        case 'fan':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/fancourier/templates/default-email-template.php';
            update_option('fan_email_template', $default_template);
            update_option('fan_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'urgent':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/cargus/templates/default-email-template.php';
            update_option('uc_email_template', $default_template);
            update_option('uc_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'gls':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/gls/templates/default-email-template.php';
            update_option('GLS_email_template', $default_template);
            update_option('GLS_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'nemo':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/nemo/templates/default-email-template.php';
            update_option('nemo_email_template', $default_template);
            update_option('nemo_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;
            
        case 'dpd':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/dpd/templates/default-email-template.php';
            update_option('dpd_email_template', $default_template);
            update_option('dpd_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'sameday':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/sameday/templates/default-email-template.php';
            update_option('sameday_email_template', $default_template);
            update_option('sameday_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'memex':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/memex/templates/default-email-template.php';
            update_option('memex_email_template', $default_template);
            update_option('memex_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'optimus':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/optimus/templates/default-email-template.php';
            update_option('optimus_email_template', $default_template);
            update_option('optimus_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'express':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/express/templates/default-email-template.php';
            update_option('express_email_template', $default_template);
            update_option('express_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;

        case 'team':
            include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/team/templates/default-email-template.php';
            update_option('team_email_template', $default_template);
            update_option('team_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
            break;
        
        default: throw new Exception('Case unhandled.');
    }

    wp_send_json_success();
    wp_die();
});

add_action('woocommerce_order_actions', 'safealternative_add_send_awb_email_action');
function safealternative_add_send_awb_email_action($actions)
{
    $actions['safealternative_send_awb_email'] = __('Trimite email cu AWB-ul generat', 'safealternative-plugin');
    return $actions;
}

add_action('woocommerce_order_action_safealternative_send_awb_email', 'safealternative_send_awb_email_action');
function safealternative_send_awb_email_action($order)
{
    $email = $order->get_billing_email();
    $order_id = $order->get_ID();

    if ($awb = get_post_meta($order_id, 'awb_bookurier', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/bookurier/initialize.php';
        return BookurierGenereazaAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_urgent_cargus', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/cargus/initialize.php';
        return CargusAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_dpd', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/dpd/initialize.php';
        return DPDAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_fan', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/fancourier/initialize.php';
        return FanGenereazaAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_GLS', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/gls/initialize.php';
        return GLSGenereazaAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_sameday', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/sameday/initialize.php';
        return SamedayAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_memex', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/memex/initialize.php';
        return MemexAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_nemo', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/nemo/initialize.php';
        return NemoWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_optimus', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/optimus/initialize.php';
        return OptimusAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_express', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/express/initialize.php';
        return ExpressAWB::send_mails($order_id, $awb, $email);
    }

    if ($awb = get_post_meta($order_id, 'awb_team', true)) {
        include_once SAFEALTERNATIVE_PLUGIN_PATH . '/includes/print-methods/team/initialize.php';
        return TeamAWB::send_mails($order_id, $awb, $email);
    }
}

add_action("wp_ajax_safealternative_send_awb_email", function () {
    $order_ids = $_POST['order_ids'] ?? array();

    foreach($order_ids as $order_id) {
        safealternative_send_awb_email_action(wc_get_order($order_id['value']));
    }

    wp_send_json_success();
    wp_die();
});

add_filter('woocommerce_package_rates', 'safealternative_woocommerce_rates_order', 100, 1);
function safealternative_woocommerce_rates_order($rates)
{
    $current_order = get_option('safealternative_shipping_methods_order', '');
    if (empty($current_order)) return $rates;

    $sorted_shipping_methods = array();
    foreach (explode(',', $current_order) as $method) {
        if (isset($rates[$method])) {
            $sorted_shipping_methods[$method] = $rates[$method];
            unset($rates[$method]);
        }
    }

    return array_merge($rates, $sorted_shipping_methods);
};