<?php

add_action('wp', function (){
    if (!wp_next_scheduled('safealternative_nemo_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_nemo_awb_update');
    }
});

add_action('safealternative_nemo_awb_update', 'update_nemo_awb_status');
function update_nemo_awb_status()
{
    global $wpdb;

    $key = 'awb_nemo_status';
    $value = 'Livrat';
    $metaList = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key=%s AND meta_value!=%s", $key, $value));

    if (count($metaList)) {
        foreach ($metaList as $meta) {
            $awb_nemo_for_update = get_post_meta($meta->post_id, 'awb_nemo', true);
            if (empty($awb_nemo_for_update)) continue;

            $json_parameters = [
                'awbno' => $awb_nemo_for_update
            ];

            $courier  = new APINemoClass();
            $awb_status = $courier->getLatestStatus($json_parameters);
            
            if(!$awb_status) continue;
            
            if($awb_status !== "failed")
            update_post_meta($meta->post_id, 'awb_nemo_status', $awb_status);
            mark_nemo_order_complete($meta->post_id, $awb_status);
        }
    } 
}

function mark_nemo_order_complete($post_id, $awb_status)
{
    if(get_option('nemo_auto_mark_complete') !== "da") return;

    $order = wc_get_order( $post_id );
    if($awb_status == "Livrat"){
        $order->update_status( 'completed' );
    }
}
