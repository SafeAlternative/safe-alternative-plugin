<?php
define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

// Should fix later in refactoring
if (!class_exists('SafealternativeFanCalcApi')) {
    include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/shipping-methods/fancourier/SafealternativeFanCalcApi.php';
}

$awb        = get_post_meta($_GET['order_id'], 'awb_fan', true);

$user       = rawurlencode(get_option('fan_user'));
$password   = rawurlencode(get_option('fan_password'));
$client_id  = rawurlencode(get_post_meta($_GET['order_id'], 'awb_fan_client_id', true) ?: get_option('fan_clientID'));
$parameters['nr'] = $awb;
$parameters['page_type'] = get_option('fan_page_type');

$fan_obj = new SafealternativeFanCalcApi($user, $password, $client_id);
$result = $fan_obj->printAwb($parameters);

if ($result['status'] != "200") {
    wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
    exit;
} else {
    $filename = $awb . '-awb-fan.pdf';
    $pdf      = $result['message'];

    header("Content-Type:application/pdf");
    header("Content-Disposition:inline;filename=" . $filename);
    header('Accept-Ranges: bytes');

    echo  $pdf;
    exit;
}
