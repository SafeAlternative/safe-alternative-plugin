<?php

add_action('wp', function () {
    if (!wp_next_scheduled('safealternative_memex_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_memex_awb_update');
    }
});

add_action('safealternative_memex_awb_update', 'update_memex_awb_status');
function update_memex_awb_status()
{
    global $wpdb;

    $key      = 'awb_memex_status';
    $value    = 'Livrat';
    $metaList = $wpdb->get_results($wpdb->prepare("SELECT * FROM " . $wpdb->postmeta . " WHERE meta_key=%s AND meta_value!=%s", $key, $value));

    if (count($metaList)) {
        foreach ($metaList as $meta) {
            if (!empty(get_post_meta($meta->post_id, 'awb_memex', true))) {
                $awb_memex_for_update = get_post_meta($meta->post_id, 'awb_memex', true);
                if (empty($awb_memex_for_update)) continue;

                $json_parameters = [
                    'packageNo' => $awb_memex_for_update
                ];

                $courier  = new SafealternativeMemexClass();
                $response = $courier->callMethod("trackParcel", $json_parameters, 'POST');

                if (!$response) continue;
                if (!isset($response['message'])) continue;
                if (!(json_decode($response['message'])->status)) continue;

                $awb_status = json_decode($response['message'])->status;

                if ($awb_status !== "failed")
                    update_post_meta($meta->post_id, 'awb_memex_status', $awb_status);
                mark_memex_order_complete($meta->post_id, $awb_status);
            }
        }
    }
}

function mark_memex_order_complete($post_id, $awb_status)
{
    if (get_option('memex_auto_mark_complete') !== "da") return;

    $order = wc_get_order($post_id);
    if ($awb_status == "Livrat") {
        $order->update_status('completed');
    }
}

add_action('safealternative_memex_call_pickup', 'memex_call_pickup');
function memex_call_pickup()
{
    $awbs = $parcels = $orders = [
        '38' => [],
        '121' => [],
    ];

    $orders = get_posts(array(
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'any',
        'meta_query' => array(
            'status_awb' => array(
                'key'   => 'awb_memex_status',
                'value' => 'Inregistrat',
                'compare' => '=='
            ),
            'awb_date' => array(
                'key' => 'memex_awb_generated_date',
                'value' => date('Y-m-d', strtotime("-1 day")), //awb urile generate cu o zi in urma 
                'compare' => '>='
            )
        )
    ));

    if (empty($orders)) return;

    foreach ($orders as $order) {
        $order_service_id = get_post_meta($order->ID, 'memex_awb_service_id', true);

        if ($order_service_id == '38') {
            $awbs['38'][] = get_post_meta($order->ID, 'awb_memex', true);
            $parcels['38'][] = json_decode(get_post_meta($order->ID, 'memex_parcels', true), true);
            $pickup_location38 = json_decode(get_post_meta($order->ID, 'memex_ship_from', true), true);
            $orders['38'][] = $order->ID;
        }

        if ($order_service_id == '121') {
            $awbs['121'][] = get_post_meta($order->ID, 'awb_memex', true);
            $parcels['121'][] = json_decode(get_post_meta($order->ID, 'memex_parcels', true), true);
            $pickup_location121 = json_decode(get_post_meta($order->ID, 'memex_ship_from', true), true);
            $orders['121'][] = $order->ID;
        }
    }

    if (!empty($awbs['38']))
        memex_call_pickup_request($pickup_location38, $awbs['38'], $parcels['38'], $orders['38']);

    if (!empty($awbs['121']))
        memex_call_pickup_request($pickup_location121, $awbs['121'], $parcels['121'], $orders['121']);
}

function memex_call_pickup_request($pickup_location, $awbs, $parcels, $orders)
{
    $courier  = new SafealternativeMemexClass();
    $response = $courier->callMethod("callPickup", array(
        'callPickupRequest' => array(
            'PickupLocation' => $pickup_location,
            'ReadyDate' => get_option('memex_pickup_date') . 'T' . get_option('memex_pickup_time'),
            'MaxPickupDate' => get_option('memex_max_pickup_date') . 'T' . get_option('memex_max_pickup_time'),
            'PackageNo' => $awbs,
            'Parcels' => $parcels
        )
    ));

    if (!$response) return;
    if (!isset($response['message'])) return;
    $response = json_decode($response['message'], true);
    if (!$response['success']) return;

    $pickupNo = $response['pickupNo'][0];
    foreach ($orders as $order) {
        update_post_meta($order, 'memex_pickup_no', $pickupNo);
        update_post_meta($order, 'memex_pickup_date', date('Y-m-d'));
        delete_post_meta($order, 'memex_parcels');
        delete_post_meta($order, 'memex_awb_service_id');
        delete_post_meta($order, 'memex_awb_generated_date');
        delete_post_meta($order, 'memex_ship_from');
    }
}
