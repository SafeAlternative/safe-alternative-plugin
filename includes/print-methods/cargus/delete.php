<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 18 Jun 2018 04:20:00 GMT");

if (!current_user_can('manage_woocommerce')) exit;

include_once(plugin_dir_path(__FILE__) .'courierCargus.class.php');

$url = get_option('uc_url');
$key = get_option('uc_key');
$token = get_option('uc_token');
$obj_urgent = new CourierCargus($url, $key);

$awb = get_post_meta($_GET['order_id'], 'awb_urgent_cargus', true);


if (empty($awb)) {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}


$result = $obj_urgent->CallMethod('Awbs?barCode='.$awb, $json = '', 'DELETE', $token);


if ($result['status'] != "200") {
    echo ("<b class='bad'> DELETE Awb: </b> <pre>" . $result['message'] . "</pre>");
    exit();
} else {
    delete_post_meta($_GET['order_id'], 'awb_urgent_cargus');
    delete_post_meta($_GET['order_id'], 'awb_urgent_cargus_trace_status');
    delete_post_meta($_GET['order_id'], 'op_urgent_cargus');
    delete_post_meta($_GET['order_id'], 'op_urgent_cargus_value');
    do_action('safealternative_awb_deleted', 'UrgentCargus', $_GET['order_id']);
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}
