<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'urgent_cargus.class.php');

$url = get_option('uc_url');
$key = get_option('uc_key');
$UserName = rawurlencode(get_option('uc_username'));
$Password = rawurlencode(get_option('uc_password'));
$user_safealternative = rawurlencode(get_option('user_safealternative'));
$password_safealternative = rawurlencode(get_option('password_safealternative'));
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
$jsonAwb = json_encode($awb_details);

$courier = new SafealternativeUCClass();
$result = $courier->callMethod(SAFEALTERNATIVE_API_URL . "/shipping/urgentcargus/generateAwb/" . $user_safealternative . "/" . $password_safealternative . "/" . $UserName . "/" . $Password, $jsonAwb, 'POST');

if ($result['status'] != "200") {
    echo ("<b class='bad'> POST Awb: </b> <pre>" . $result['message'] . "</pre>");
    exit();
} else {
    if (!is_numeric(json_decode($result['message']))) {
        set_transient('urgent_account_settings', $result['message'], MONTH_IN_SECONDS);
        exit();
    } else {
        $awb = json_decode($result['message']);
        if ($trimite_mail == '1') {
            CargusAWB::send_mails($_GET['order_id'], $awb, $awb_details['Recipient']['Email']);
        }

        update_post_meta($_GET['order_id'], 'awb_urgent_cargus', $awb);
        do_action('safealternative_awb_generated', 'UrgentCargus', $awb, $_GET['order_id']);

        $account_status_response = $courier->callMethod(SAFEALTERNATIVE_API_URL . "/shipping/urgentcargus/newAccountStatus/" . $user_safealternative . "/" . $password_safealternative . "/" . $UserName . "/" . $Password, '', 'POST');
        $account_status = json_decode($account_status_response['message']);

        if ($account_status->show_message) {
            set_transient('urgent_account_status', $account_status->message, MONTH_IN_SECONDS);
        } else {
            delete_transient('urgent_account_status');
        }

        if (isset($_POST['paged'])) {
            header('Location: ' . safealternative_redirect_url() . 'edit.php?post_type=shop_order&paged=' . $_POST['paged']);
        } else {
            header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
        }
    }
}
