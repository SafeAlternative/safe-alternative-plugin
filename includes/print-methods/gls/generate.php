<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb_details    = $_POST['awb'];
$trimite_mail   =  get_option('GLS_trimite_mail');

$parameters = $awb_details;
$json_parameters = json_encode($parameters);

$courier = new SafealternativeGLSClass();
$response = $courier->callMethod("generateAwb", $json_parameters, 'POST');

if ($response['status'] == 200) {
    $mesage = json_decode($response['message'], true);
    $successfull = $mesage['successfull'];

    if ($successfull) {
        $awb = $mesage['pcls'][0];

        if ($trimite_mail == 'da') {
            GLSGenereazaAWB::send_mails($_GET['order_id'], $awb, $awb_details['consig_email']);
        }

        update_post_meta($_GET['order_id'], 'awb_GLS', $awb);
        update_post_meta($_GET['order_id'], 'awb_GLS_all_pcls', $mesage['all_pcls']);
        update_post_meta($_GET['order_id'], 'awb_GLS_status', 'Inregistrat');

        do_action('safealternative_awb_generated', 'GLS', $awb, $_GET['order_id']);

        $account_status_response = $courier->callMethod("newAccountStatus", '', 'POST');
        $account_status = json_decode($account_status_response['message']);

        if ($account_status->show_message) {
            set_transient('gls_account_status', $account_status->message, MONTH_IN_SECONDS);
        } else {
            delete_transient('gls_account_status');
        }

        header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');

        exit;
    } else {
        $errdesc = $mesage['errdesc'];
        echo ("<b class='bad'> GLS API: </b> <pre>" . $errdesc . "</pre>");
        exit;
    }
} else {
    wp_die($response['message']);
}
