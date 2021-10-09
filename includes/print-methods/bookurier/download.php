<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awbnr = get_post_meta($_GET['order_id'], 'awb_bookurier', true);
$awb_list = explode(',', $awbnr);

$courier = new SafealternativeBookurierClass();
$pdfFile = new SafeAlternative\Clegginabox\PDFMerger\PDFMerger;

$pdfs = array();

foreach ($awb_list as $awb) {
    $params = [
        'awb' => $awb,
    ];

    $result = $courier->callMethod("downloadAwb", $params, 'POST');
    $mesage = json_decode($result['message'], true);

    if ($result['status'] == 200 && $mesage['success']) {
        $pdfs[$awb]['tmpfile']  = tmpfile();
        $pdfs[$awb]['pdf']      = fwrite($pdfs[$awb]['tmpfile'], base64_decode($mesage['awb']));
    } else {
        wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
        exit;
    }
}

foreach ($pdfs as $pdf) {
    $pdfFile->addPDF(stream_get_meta_data($pdf['tmpfile'])['uri'], 'all');
}

return $pdfFile->merge('browser', "$awb_list[0].pdf");
