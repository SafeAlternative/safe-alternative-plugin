<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 18 Jun 2018 04:20:00 GMT");

if (!current_user_can('manage_woocommerce')) exit;

include_once(plugin_dir_path(__FILE__) . 'courierNemo.class.php');

$courier = new CourierNemo();

$awb_nr = get_post_meta($_GET['order_id'], 'awb_nemo', true);
$parameters = [
    'awbno' => $awb_nr
];

if (empty($awb_nr)) {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}

$courier = new CourierNemo();
$response = $courier->deleteAwb($parameters);


$message = json_decode($response);

if ($message && $message->status == "done") {
    delete_post_meta($_GET['order_id'], 'awb_nemo');
    delete_post_meta($_GET['order_id'], 'awb_nemo_status');
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
} else {
    wp_die("<b> Eroare la stergere: </b> <br/> {$response}");
}
