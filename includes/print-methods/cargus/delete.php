<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 18 Jun 2018 04:20:00 GMT");

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'urgent_cargus.class.php');

$url = get_option('uc_url');
$key = get_option('uc_key');
$UserName = rawurlencode(get_option('uc_username'));
$Password = rawurlencode(get_option('uc_password'));
$user_safealternative = rawurlencode(get_option('user_safealternative'));
$password_safealternative = rawurlencode(get_option('password_safealternative'));

$obj_urgent = new UrgentCargusAPI($url, $key);

$awb = get_post_meta($_GET['order_id'], 'awb_urgent_cargus', true);
$jsonAwb = json_encode(
    array('barCode' => $awb)
);

if (empty($awb)) {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}

$courier = new SafealternativeUCClass();
$result = $courier->callMethod(SAFEALTERNATIVE_API_URL . "/shipping/urgentcargus/deleteAwb/" . $user_safealternative . "/" . $password_safealternative . "/" . $UserName . "/" . $Password, $jsonAwb, 'POST');

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
