<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb_details = $_POST['awb'];
$awb_details['domain'] = site_url();

$api_user      = rawurlencode(get_option('user_safealternative'));
$api_pass      = rawurlencode(get_option('password_safealternative'));
$user          = rawurlencode(get_option('fan_user'));
$password      = rawurlencode(get_option('fan_password'));
$clientID      = $awb_details['clientId'];
$trimite_mail  = get_option('fan_trimite_mail');

unset($awb_details['clientId']);

if (strlen($awb_details['judet']) <= 2)
    $awb_details['judet'] = safealternative_get_counties_list($awb_details['judet']);

$parameters      = $awb_details;
$json_parameters = json_encode($parameters);

$courier  = new SafealternativeFanClass();
$response = $courier->callMethod("generateAwb/" . $api_user . "/" . $api_pass . "/" . $user . "/" . $password . "/" . $clientID, $json_parameters, 'POST');

if ($response['status'] == 200) {
    $id = json_decode($response['message']);
    if (is_numeric($id)) {
        if ($trimite_mail == 'da') {
            FanGenereazaAWB::send_mails($_GET['order_id'], $id, $parameters['mail']);
        } //$trimite_mail == 'da'

        update_post_meta($_GET['order_id'], 'awb_fan', $id);
        update_post_meta($_GET['order_id'], 'awb_fan_client_id', $clientID);
        update_post_meta($_GET['order_id'], 'awb_fan_status_id', '0');
        update_post_meta($_GET['order_id'], 'awb_fan_status', 'AWB-ul a fost inregistrat de catre clientul expeditor.');
        update_post_meta($_GET['order_id'], 'awb_fan_generated_date', date('Y-m-d'));


        do_action('safealternative_awb_generated', 'FanCourier', $id, $_GET['order_id']);

        $account_status_response = $courier->callMethod("newAccountStatus/" . $api_user . "/" . $api_pass . "/" . $user . "/" . $password . "/" . $clientID, '', 'POST');
        $account_status = json_decode($account_status_response['message']);

        if ($account_status->show_message) {
            set_transient('fan_account_status', $account_status->message, MONTH_IN_SECONDS);
        } else {
            delete_transient('fan_account_status');
        }

        header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
        exit;
    } else {
        echo ("<b class='bad'> API FanCourier AWB: </b> <pre>" . $id . "</pre>");
        exit;
    }
} else {
    wp_die($response['message']);
}
