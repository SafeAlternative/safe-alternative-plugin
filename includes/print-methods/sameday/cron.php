<?php

add_action('wp', function (){
    if (!wp_next_scheduled('safealternative_sameday_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_sameday_awb_update');
    }
});

add_action('safealternative_sameday_awb_update', 'update_sameday_awb_status');
function update_sameday_awb_status(){
    global $wpdb;

    $key      = 'awb_sameday_status';
    $value    = 'Colete livrate';
    $metaList = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key=%s AND meta_value!=%s", $key, $value));

    if (count($metaList)) {
        foreach ($metaList as $meta) {
            $awb_sameday_for_update = get_post_meta($meta->post_id, 'awb_sameday', true);
            if (empty($awb_sameday_for_update)) continue;

            $courier  = new APISamedayClass();
            $awb_status = $courier->getLatestStatus($awb_sameday_for_update);

            if(!$awb_status) continue;
            
            if($awb_status !== "failed")
            update_post_meta($meta->post_id, 'awb_sameday_status', $awb_status);
            mark_sameday_order_complete($meta->post_id, $awb_status);
        }
    } 
}

function mark_sameday_order_complete($post_id, $awb_status){
    if(get_option('sameday_auto_mark_complete') !== "da") return;

    $order = wc_get_order( $post_id );
    if($awb_status == 'Colete livrate'){
        $order->update_status( 'completed' );
    }
}
