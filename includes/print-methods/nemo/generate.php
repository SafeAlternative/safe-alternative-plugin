<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

include_once(plugin_dir_path(__FILE__).'courierNemo.class.php');

$awb_details = $_POST['awb'];
$trimite_mail = get_option('nemo_trimite_mail');

if($awb_details['retur'] == 'false'){
    unset($awb_details['retur_type']);
}

$awb_details['api_key'] = get_option('nemo_key');;
$awb_details['token'] = get_option('token');

$courier  = new CourierNemoSafe();
$response = $courier->callMethod("generateAwb", $awb_details, 'POST');


if ($response['status'] == 200) {
    if (!$response['success']) wp_die($response['error']);

    $awb = $response['message'];

    if ($trimite_mail == 'da') {
        NemoAWB::send_mails($_GET['order_id'], $awb, $awb_details['recipient_email']);
    }
    update_post_meta($_GET['order_id'], 'awb_nemo', $awb);
    update_post_meta($_GET['order_id'], 'awb_nemo_status', 'Inregistrat');

    //do_action('safealternative_awb_generated', 'Nemo', $awb);

    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
} else {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
}
