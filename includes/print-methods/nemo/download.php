<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

include_once(plugin_dir_path(__FILE__) . 'courierNemo.class.php');

$courier = new CourierNemo();
$awb = get_post_meta($_GET['order_id'], 'awb_nemo', true);

$parameters = [
    'awbno'=>$awb,
	'format'=> get_option('nemo_page_type'),
	'pdf'=>'true'
];

$result = $courier->printAwb($parameters);

$filename = $awb . '-awb-nemo.pdf';
$pdf = $result;

header("Content-Type:application/pdf");
header("Content-Disposition:inline;filename=" . $filename);
header('Accept-Ranges: bytes');

echo $pdf;
exit;

