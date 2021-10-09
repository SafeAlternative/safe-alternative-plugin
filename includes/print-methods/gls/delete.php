<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 18 Jun 2018 04:20:00 GMT");

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb_nr = get_post_meta($_GET['order_id'], 'awb_GLS', true);
$all_pcls = get_post_meta($_GET['order_id'], 'awb_GLS_all_pcls', true);
$senderid = get_option('GLS_senderid');
$json_parameters = json_encode(['senderid' => $senderid, 'awb' => $awb_nr, 'all_pcls' => $all_pcls]);

if (empty($awb_nr)) {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}

$courier = new SafealternativeGLSClass();
$response = $courier->callMethod("deleteAwb", $json_parameters, 'POST');

if ($response['status'] == 200) {
    delete_post_meta($_GET['order_id'], 'awb_GLS');
    delete_post_meta($_GET['order_id'], 'awb_GLS_all_pcls');
    delete_post_meta($_GET['order_id'], 'awb_GLS_status');
    do_action('safealternative_awb_deleted', 'GLS', $_GET['order_id']);
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
} else {
    echo "<b class='bad'>Eroare la stergere </b>";
    exit;
}
