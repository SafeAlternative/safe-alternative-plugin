<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$courier = new SafealternativeSamedayClass();
$awb = get_post_meta($_GET['order_id'], 'awb_sameday', true);

$parameters = [
    'awb' => $awb,
    'page_type' => get_option('sameday_page_type'),
];

$result = $courier->callMethod("downloadAwb", $parameters, 'POST');

if ($result['status'] != "200") {
    wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
    exit;
} else {
    $filename = $awb . '-awb-sameday.pdf';
    $pdf      = $result['message'];

    header("Content-Type:application/pdf");
    header("Content-Disposition:inline;filename=" . $filename);
    header('Accept-Ranges: bytes');

    echo $pdf;
    exit;
}
