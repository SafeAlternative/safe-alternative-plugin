<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

include_once(plugin_dir_path(__FILE__) .'courierCargusSafe.class.php');


$trimite_mail = get_option('uc_trimite_mail');

$awb_details = $_POST['awb'];
$awb_details['SenderClientId'] = empty($awb_details['SenderClientId']) ? "" : $awb_details['SenderClientId'];
$awb_details['OpenPackage'] = $awb_details['OpenPackage'] ? true : false;
$awb_details['SaturdayDelivery'] = $awb_details['SaturdayDelivery'] ? true : false;
$awb_details['MorningDelivery'] = $awb_details['MorningDelivery'] ? true : false;
$awb_details['PriceTableId'] = ($awb_details['PriceTableId'] == 1) ? 0 : $awb_details['PriceTableId'];

$parcel_incrementor = count($awb_details['ParcelCodes'] ?? array());
$totalWeight = 0;

for ($i = 0; $i < $awb_details['Envelopes']; $i++) {
    $awb_details['ParcelCodes'][] = array(
        'Code' => (string) ($i + $parcel_incrementor),
        'Type' => "0"
    );
}

foreach (($awb_details['ParcelCodes'] ?? null) as $parcel_code) {
    $totalWeight += ($parcel_code['Weight'] ?? 0);
}

if (!$totalWeight) $totalWeight = 1;
$awb_details['TotalWeight'] = $totalWeight;



$awb_details['token'] =  get_option('token');
$awb_details['token_cargus'] =  get_option('uc_token');
$awb_details['subscriptionKey']  = get_option('uc_key');

$courier  = new CourierCargusSafe();
$response = $courier->callMethod("generateAwb", $awb_details, 'POST');


if (!$response['success']) {
    echo ("<b class='bad'> POST Awb: </b> <pre>" . $response['message'] . "</pre>");
    exit();
} else {
    if (!is_numeric(json_decode($response['message']))) {
        
        exit();
    } else {
        $awb = json_decode($response['message']);
        if ($trimite_mail == '1') {
            CargusAWB::send_mails($_GET['order_id'], $awb, $awb_details['Recipient']['Email']);
        }

        update_post_meta($_GET['order_id'], 'awb_urgent_cargus', $awb);
        do_action('safealternative_awb_generated', 'UrgentCargus', $awb, $_GET['order_id']);



        if (isset($_POST['paged'])) {
            header('Location: ' . safealternative_redirect_url() . 'edit.php?post_type=shop_order&paged=' . $_POST['paged']);
        } else {
            header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
        }
    }
}
