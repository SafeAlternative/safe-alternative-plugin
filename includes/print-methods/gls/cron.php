<?php

add_action('wp', function (){
    if (!wp_next_scheduled('safealternative_gls_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_gls_awb_update');
    }
});

add_action('safealternative_gls_awb_update', 'update_gls_awb_status');
function update_gls_awb_status(){
    global $wpdb;
    
    $key      = 'awb_GLS_status';
    $value    = '05-Livrat';
    $metaList = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key=%s AND meta_value!=%s", $key, $value));

    if (count($metaList)) {
        foreach ($metaList as $meta) {
            $awb_GLS_for_update = get_post_meta($meta->post_id, 'awb_GLS', true);
            if (empty($awb_GLS_for_update)) continue;

            $json_parameters = json_encode([
                'awb' => $awb_GLS_for_update
            ]);

            $courier  = new SafealternativeGLSClass();
            $response = $courier->callMethod("trackParcel", $json_parameters, 'POST');
            
            if(!$response) continue;
            if(!isset($response['message'])) continue;
            if(!json_decode($response['message'])->status) continue;

            $awb_status = json_decode($response['message'])->status;
            
            if($awb_status !== "failed")
            update_post_meta($meta->post_id, 'awb_GLS_status', $awb_status);
            mark_gls_order_complete($meta->post_id, $awb_status);
        }
    } 
}

function mark_gls_order_complete($post_id, $awb_status){
    if(get_option('GLS_auto_mark_complete') !== "da") return;

    $order = wc_get_order( $post_id );
    if($awb_status == "05-Livrat"){
        $order->update_status( 'completed' );
    }
}
