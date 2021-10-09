<?php

add_action('wp', function () {
    if (!wp_next_scheduled('safealternative_bookurier_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_bookurier_awb_update');
    }
});

add_action('safealternative_bookurier_awb_update', 'update_bookurier_awb_status');
function update_bookurier_awb_status()
{
    global $wpdb;

    $key      = 'awb_bookurier_status_id';
    $value    = '4';
    $metaList = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key=%s AND meta_value!=%s", $key, $value));

    if (count($metaList)) {
        foreach ($metaList as $meta) {
            $awb_bookurier_for_update = get_post_meta($meta->post_id, 'awb_bookurier', true);
            if (empty($awb_bookurier_for_update)) continue;
            $awb_list = explode(',', $awb_bookurier_for_update);

            $parameters = [
                'awb' => $awb_list[0]
            ];

            $courier  = new SafealternativebookurierClass();
            $response = $courier->callMethod("trackParcel", $parameters, 'POST');
            $mesage = json_decode($response['message']);

            if (!$response) continue;
            if (!isset($response['message'])) continue;
            if (!$mesage->success) continue;

            $awb_status = $mesage->status->status_desc;
            $awb_status_id = $mesage->status->status_id;

            if (!empty($awb_status) && !empty($awb_status_id)) {
                update_post_meta($meta->post_id, 'awb_bookurier_status', $awb_status);
                update_post_meta($meta->post_id, 'awb_bookurier_status_id', $awb_status_id);
            }
            mark_bookurier_order_complete($meta->post_id, $awb_status_id);
        }
    }
}

function mark_bookurier_order_complete($post_id, $awb_status_id)
{
    if (get_option('bookurier_auto_mark_complete') !== "da") return;

    $order = wc_get_order($post_id);
    if ($awb_status_id == "4") {
        $order->update_status('completed');
    }
}
