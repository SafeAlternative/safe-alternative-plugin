<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

header("Cache-Control: no-cache, must-revalidate");
header("Expires: Mon, 18 Jun 2018 04:20:00 GMT");

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb_nr = get_post_meta($_GET['order_id'], 'awb_bookurier', true);
$awb_list = explode(',', $awb_nr);
$courier = new SafealternativebookurierClass();

if (empty($awb_nr)) {
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
}

$success = true;
foreach ($awb_list as $awb) {
    $parameters = ['awb' => $awb];
    $response = $courier->callMethod("deleteAwb", $parameters, 'POST');
    $decoded_response = json_decode($response['message'], true);

    if ($response['status'] == 200 && !$decoded_response['success']) {
        $success = false;
    } else {
        $awb_nr = trim(str_replace(',,', ',', str_replace($awb, '', $awb_nr)), ',');
    }
}

if ($response['status'] == 200 && $success) {
    delete_post_meta($_GET['order_id'], 'awb_bookurier');
    delete_post_meta($_GET['order_id'], 'awb_bookurier_status');
    delete_post_meta($_GET['order_id'], 'awb_bookurier_status_id');
    do_action('safealternative_awb_deleted', 'Bookurier', $_GET['order_id']);
    header('Location: ' . safealternative_redirect_url() . 'post.php?post=' . $_GET['order_id'] . '&action=edit');
    exit;
} else {
    update_post_meta($_GET['order_id'], 'awb_bookurier', $awb_nr);
    wp_die("<b class='bad'>Bookurier API: Eroare la stergere AWB.</b>");
}
