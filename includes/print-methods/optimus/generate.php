<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$parameters = $_POST['awb'];
$trimite_mail = get_option('optimus_trimite_mail');

$courier = new SafealternativeOptimusClass();
$response = $courier->callMethod("generateAwb", $parameters, 'POST');
$message = json_decode($response['message'], true);

if ($response['status'] == 200 && $message['success']) {
    if (!$message['success']) wp_die($message['error']);

    $awb = $message['awb'];
    $awb_id = $message['id'];

    if ($trimite_mail == 'da') {
        $order = wc_get_order($_GET['order_id']);
        OptimusAWB::send_mails($_GET['order_id'], $awb, $order->get_billing_email());
    }
    update_post_meta($_GET['order_id'], 'awb_optimus', $awb);
    update_post_meta($_GET['order_id'], 'awb_optimus_status', 'Inregistrat');
    update_post_meta($_GET['order_id'], 'awb_optimus_id', $awb_id);

    do_action('safealternative_awb_generated', 'Optimus', $awb);

    $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');
    $account_status = json_decode($account_status_response['message']);

    if ($account_status->show_message) {
        set_transient('optimus_account_status', $account_status->message, MONTH_IN_SECONDS);
    } else {
        delete_transient('optimus_account_status');
    }

    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
} else {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
}
