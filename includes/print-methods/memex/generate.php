<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$parameters = $_POST['awb'];

//daca ambele coduri postale se afla in lista localitatilor pentru serviciul loco standard (ServiceID=121), atunci acesta este selectat automat
$localities = array("077106", "077040", "077085", "077041", "077086", "077145", "077191", "077160", "077042", "077190", "077010");

if ((in_array($parameters['shipmentRequest']['ShipFrom']['PostCode'], $localities) || strtolower($parameters['shipmentRequest']['ShipFrom']['City']) == 'bucuresti') && (in_array($parameters['shipmentRequest']['ShipTo']['PostCode'], $localities) || strtolower($parameters['shipmentRequest']['ShipTo']['City']) == 'bucuresti')) {
    $parameters['shipmentRequest']['ServiceId'] = '121';
}

if ($parameters['additional_sms'] == 'Da') {
    $parameters["shipmentRequest"]["AdditionalServices"]["AdditionalService"]["Code"] = 'SSMS';
}

$trimite_mail = get_option('memex_trimite_mail');
$courier = new SafealternativeMemexClass();

$response = $courier->callMethod("generateAwb", $parameters, 'POST');
$message = json_decode($response['message'], true);

if ($response['status'] == 200 && $message['success']) {
    if (!$message['success']) wp_die($message['error']);

    $awb = $message['awb'];

    if ($trimite_mail == 'da') {
        MemexAWB::send_mails($_GET['order_id'], $awb, $parameters['shipmentRequest']['ShipTo']['Email']);
    }

    update_post_meta($_GET['order_id'], 'awb_memex', $awb);
    update_post_meta($_GET['order_id'], 'awb_memex_status', 'Inregistrat');
    update_post_meta($_GET['order_id'], 'memex_parcels', json_encode($parameters['shipmentRequest']['Parcels']));
    update_post_meta($_GET['order_id'], 'memex_awb_service_id', $parameters['shipmentRequest']['ServiceId']);
    update_post_meta($_GET['order_id'], 'memex_awb_generated_date', date('Y-m-d'));
    update_post_meta($_GET['order_id'], 'memex_ship_from', json_encode($parameters['shipmentRequest']['ShipFrom']));

    do_action('safealternative_awb_generated', 'Memex', $awb);

    $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');
    $account_status = json_decode($account_status_response['message']);

    if ($account_status->show_message) {
        set_transient('memex_account_status', $account_status->message, MONTH_IN_SECONDS);
    } else {
        delete_transient('memex_account_status');
    }

    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
} else {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
}
