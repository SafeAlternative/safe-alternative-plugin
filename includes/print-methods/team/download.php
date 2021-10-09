<?php

define('WP_USE_THEMES', false);
include '../../../../../../wp-load.php';

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

$courier = new SafealternativeTeamClass();
$awb = get_post_meta($_GET['order_id'], 'awb_team', true);

$parameters = [
    'awbno'=>$awb,
	'format'=> get_option('team_page_type'),
	'pdf'=>'true'
];

$result = $courier->callMethod("downloadAwb", $parameters, 'POST');

if ($result['status'] != "200") {
    wp_die("<b class='bad'>Eroare la tiparire: </b>" . $result['message'] ?? 'AWB-ul nu a putut fi gasit.');
    exit;
} else {
    $filename = $awb . '-awb-team.pdf';
    $pdf = $result['message'];

    header("Content-Type:application/pdf");
    header("Content-Disposition:inline;filename=" . $filename);
    header('Accept-Ranges: bytes');

    echo $pdf;
    exit;
}
