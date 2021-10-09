<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 18 Jun 2018 04:20:00 GMT");

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb_nr = get_post_meta($_GET['order_id'], 'awb_express', true);
$parameters = [
    'awbno' => $awb_nr
];

if (empty($awb_nr)) {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}

$courier = new SafealternativeExpressClass();
$response = $courier->callMethod("deleteAwb", $parameters, 'POST');
$message = json_decode($response['message']);

if ($response['status'] == 200 && empty($message->error)) {
    delete_post_meta($_GET['order_id'], 'awb_express');
    delete_post_meta($_GET['order_id'], 'awb_express_status');
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
} else {
    wp_die("<b> Eroare la stergere: </b> <br/> {$message->error->message}");
}
