<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awbnr = get_post_meta($_GET['order_id'], 'awb_GLS', true);
$all_pcls = get_post_meta($_GET['order_id'], 'awb_GLS_all_pcls', true);

$courier = new SafealternativeGLSClass();
$json_params = [
    'awb' => $awbnr,
    'printer_template' => get_option('GLS_printertemplate'),
    'senderid' => get_option('GLS_senderid'),
    'all_pcls' => $all_pcls
];
$json_params = json_encode($json_params);

$result = $courier->callMethod("downloadPdf", $json_params, 'POST');

if ($result['status'] != "200") {
    wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
    exit;
} else {
    $pdf      = $result['message'];
    $decoded_pdf = base64_decode($pdf);
    $filename = $awbnr . '_awb_gls.pdf';

    header("Content-Type:application/pdf", true);
    header('Content-Disposition:inline;filename="' . $filename . '"');
    header('Accept-Ranges: bytes');

    echo $decoded_pdf;
    exit;
}
