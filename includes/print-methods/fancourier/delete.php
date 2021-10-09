<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 18 Jun 2018 04:20:00 GMT");

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb = get_post_meta($_GET['order_id'], 'awb_fan', true);

$api_user      = rawurlencode(get_option('user_safealternative'));
$api_pass      = rawurlencode(get_option('password_safealternative'));
$user          = rawurlencode(get_option('fan_user'));
$password      = rawurlencode(get_option('fan_password'));
$clientID      = rawurlencode(get_post_meta($_GET['order_id'], 'awb_fan_client_id', true) ?: get_option('fan_clientID'));

$courier = new SafealternativeFanClass();

if (empty($awb)) {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}

$result = $courier->callMethod("deleteAwb/" . $api_user . "/" . $api_pass . "/" . $user . "/" . $password . "/" . $clientID . "/" . $awb, null, 'POST');

if ($result['status'] != "200") {
    echo ("<b class='bad'>Eroare la stergere: </b>" . $result['message']);
    exit;
} else {
    delete_post_meta($_GET['order_id'], 'awb_fan');
    delete_post_meta($_GET['order_id'], 'awb_fan_client_id');
    delete_post_meta($_GET['order_id'], 'awb_fan_status_id');
    delete_post_meta($_GET['order_id'], 'awb_fan_status');
    do_action('safealternative_awb_deleted', 'FanCourier', $_GET['order_id']);
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
}
