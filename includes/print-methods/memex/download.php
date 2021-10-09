<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

if (!current_user_can('manage_woocommerce')) exit;

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$awb = get_post_meta($_GET['order_id'], 'awb_memex', true);

$courier = new SafealternativeMemexClass();
$pdfFile = new SafeAlternative\Clegginabox\PDFMerger\PDFMerger;

$pdfs = array();
$parameters = [
    'getLabelRequest' => array(
        'PackageNo' => array(
            'string' => $awb
        ),
        'LabelFormat' => esc_attr(get_option('memex_label_format'))
    )
];

$result = $courier->callMethod("downloadAwb", $parameters, 'POST');
$index = 0;
if ($result['status'] == 200) {
    $messages = json_decode($result['message'], true);
    
    foreach($messages as $message){
        $pdfs[$index]['tmpfile']  = tmpfile();
        $pdfs[$index]['pdf']      = fwrite($pdfs[$index]['tmpfile'], base64_decode($message));
        $index++;
    }
    
} else {
    wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
    exit;
}

foreach ($pdfs as $pdf) {
    $pdfFile->addPDF(stream_get_meta_data($pdf['tmpfile'])['uri'], 'all');
}

return $pdfFile->merge('browser', "$awb.pdf");
