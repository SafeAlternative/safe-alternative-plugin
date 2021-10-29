<?php

add_action('wp', function () {
    if (!wp_next_scheduled('safealternative_fan_courier_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_fan_courier_awb_update');
    }
});

add_action('safealternative_fan_courier_awb_update', 'update_fan_courier_awb_status');
function update_fan_courier_awb_status()
{
    global $wpdb;

    $key      = 'awb_fan_status_id';
    $value    = '2';
    $metaList = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key=%s AND meta_value!=%s", $key, $value));

    if (count($metaList)) {
        // scan each awbTrace
        foreach ($metaList as $meta) {
            $awb_fan_for_update = get_post_meta($meta->post_id, 'awb_fan', true);
            if (empty($awb_fan_for_update)) continue;

            $parameters = array(
                'AWB' => $awb_fan_for_update
            );

            $obj_fan = new CourierFan(get_option('fan_user'), get_option('fan_password'), get_option('fan_clientID'));
            $awb_fan_status_new = $obj_fan->getLatestStatus($parameters);

            if(!$awb_fan_status_new) continue;

            update_post_meta($meta->post_id, 'awb_fan_status', $awb_fan_status_new[0]);
            update_post_meta($meta->post_id, 'awb_fan_status_id', $awb_fan_status_new[1]);
            mark_fan_order_complete($meta->post_id, $awb_fan_status_new[0]);
        } // end foreach        
    } //count($metaList)

    // aducem op din ziua curenta

    $api_user = rawurlencode(get_option('user_safealternative'));
    $api_pass = rawurlencode(get_option('password_safealternative'));
    $user     = rawurlencode(get_option('fan_user'));
    $password = rawurlencode(get_option('fan_password'));
    $clientID = rawurlencode(get_option('fan_clientID'));

    $courier  = new CourierFan(get_option('fan_user'), get_option('fan_password'), get_option('fan_clientID'));

    $responseOP = $courier->callMethod("getPaymentReport/" . $api_user . "/" . $api_pass . "/" . $user . "/" . $password . "/" . $clientID . "/" . date('d.m.Y'), $json_parameters = '', 'POST');
    $opList     = json_decode($responseOP['message'], true);

    if (is_array($opList) and count($opList)) {
        foreach ($opList as $noOp => $opValue) {
            $dataVirament = $opValue['data_virament'];
            $sumaIncasata = $opValue['suma_incasata'];
            $numarAwb     = $opValue['numar_awb'];
            $idPost       = safealternative_get_post_id_by_meta('awb_fan', $numarAwb);
            // find awb 
            if ($idPost) {
                update_post_meta($idPost, 'ordin_plata_ramburs', $dataVirament);
                update_post_meta($idPost, 'ordin_plata_ramburs_value', $sumaIncasata);
            } //$idPost
        } //$opList as $noOp => $opValue
    } //is_array($opList) AND count($opList)
    // aducem op din ziua curenta
}

function mark_fan_order_complete($post_id, $awb_status)
{
    if (get_option('fan_auto_mark_complete') !== "da") return;

    $order = wc_get_order($post_id);
    if ($awb_status == "Livrat") {
        $order->update_status('completed');
    }
}
