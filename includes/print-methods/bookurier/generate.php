<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$parameters     =  $_POST['awb'];
$trimite_mail   =  get_option('bookurier_trimite_mail');

$courier = new SafealternativeBookurierClass();
$response = $courier->callMethod("generateAwb", $parameters, 'POST');
$mesage = json_decode($response['message'], true);

if ($response['status'] == 200 && $mesage['success']) {
    $awb = $mesage['awb'];

    if ($trimite_mail == 'da') {
        BookurierGenereazaAWB::send_mails($_GET['order_id'], $awb, $parameters['email']);
    }

    update_post_meta($_GET['order_id'], 'awb_bookurier', $awb);
    update_post_meta($_GET['order_id'], 'awb_bookurier_status', 'Inregistrat');
    update_post_meta($_GET['order_id'], 'awb_bookurier_status_id', '1');

    do_action('safealternative_awb_generated', 'Bookurier', $awb, $_GET['order_id']);

    $account_status_response = $courier->callMethod("newAccountStatus", array(), 'POST');
    $account_status = json_decode($account_status_response['message']);

    if ($account_status->show_message) {
        set_transient('bookurier_account_status', $account_status->message, MONTH_IN_SECONDS);
    } else {
        delete_transient('bookurier_account_status');
    }

    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');

    exit;
} else {
    wp_die("<b class='bad'>Bookurier API: Eroare la generare AWB.</b>");
    exit;
}
