<?php

add_action('wp', function () {
    if (!wp_next_scheduled('safealternative_optimus_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_optimus_awb_update');
    }
});

add_action('safealternative_optimus_awb_update', 'update_optimus_awb_status');
function update_optimus_awb_status()
{
    global $wpdb;

    $key = 'awb_optimus_status';
    $value = 'Livrat';
    $metaList = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key=%s AND meta_value!=%s", $key, $value));

    if (count($metaList)) {
        foreach ($metaList as $meta) {
            if (!empty(get_post_meta($meta->post_id, 'awb_optimus_id', true))) {
                $awb_id_optimus_for_update = get_post_meta($meta->post_id, 'awb_optimus_id', true);
                if (empty($awb_id_optimus_for_update)) continue;

                $json_parameters = [
                    'awb' => $awb_id_optimus_for_update
                ];

                $courier = new SafealternativeOptimusClass();
                $response = $courier->callMethod("trackParcel", $json_parameters, 'POST');

                if (!$response) continue;
                if (!isset($response['message'])) continue;
                if (!(json_decode($response['message'])->status)) continue;

                $awb_status = json_decode($response['message'])->status;

                if ($awb_status !== "failed")
                    update_post_meta($meta->post_id, 'awb_optimus_status', $awb_status);
                mark_optimus_order_complete($meta->post_id, $awb_status);
            }
        }
    }
}

function mark_optimus_order_complete($post_id, $awb_status)
{
    if (get_option('optimus_auto_mark_complete') !== "da") return;

    $order = wc_get_order($post_id);
    if ($awb_status == "Livrat") {
        $order->update_status('completed');
    }
}
