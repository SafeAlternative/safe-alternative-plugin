<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb = get_post_meta($_GET['order_id'], 'awb_fan', true);

$api_user      = rawurlencode(get_option('user_safealternative'));
$api_pass        = rawurlencode(get_option('password_safealternative'));
$user          = rawurlencode(get_option('fan_user'));
$password      = rawurlencode(get_option('fan_password'));
$clientID      = rawurlencode(get_post_meta($_GET['order_id'], 'awb_fan_client_id', true) ?: get_option('fan_clientID'));

$courier = new SafealternativeFanClass();

$json_parameters = json_encode(
    array(
        'page_type' => get_option('fan_page_type')
    )
);

$result = $courier->callMethod("viewAwb/" . $api_user . "/" . $api_pass . "/" . $user . "/" . $password . "/" . $clientID . "/" . $awb, $json_parameters, 'POST');

if ($result['status'] != "200") {
    wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
    exit;
} else {
    $filename = $awb . '-awb-fan.pdf';
    $pdf      = $result['message'];

    header("Content-Type:application/pdf");
    header("Content-Disposition:inline;filename=" . $filename);
    header('Accept-Ranges: bytes');

    echo $pdf;
    exit;
}
