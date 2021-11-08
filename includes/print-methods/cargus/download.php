<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

include_once(plugin_dir_path(__FILE__) .'courierCargus.class.php');

if (isset($_GET['awbs'])) {
    $awb = explode('X', $_GET['awbs']);
} else {
    $awb = get_post_meta($_GET['order_id'], 'awb_urgent_cargus');
}

$jsonAwb = json_encode($awb);

$url = get_option('uc_url');
$key = get_option('uc_key');


$obj_urgent = new CourierCargus($url, $key);
$token = get_option('uc_token');


$_POST['format'] = get_option('uc_print_format') ?? 0;
$_POST['printMainOnce'] = get_option('uc_print_once') ?? 0;
$_POST['printOneAwbPerPage'] = 0;

$result = $obj_urgent->CallMethod('AwbDocuments?type=PDF&barCodes=' . $jsonAwb . '&format=' . (int) $_POST['format'] . '&printMainOnce=' . (int) $_POST['printMainOnce'] . '&printOneAwbPerPage=' . (int) $_POST['printOneAwbPerPage'], $json = '', 'GET', $token);

if ($result['status'] != "200") {
    wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
    exit;
} else {
    $response = base64_decode($result['message']);
    $filename = $awb[0] . '-awb-cargus.pdf';

    header("Content-Type:application/pdf");
    header("Content-Disposition:inline;filename=" . $filename);
    header('Accept-Ranges: bytes');

    echo $response;
    exit;
}
