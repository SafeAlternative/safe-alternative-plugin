<?php

add_action('wp', function () {
    if (!wp_next_scheduled('safealternative_urgent_cargus_awb_update')) {
        wp_schedule_event(time(), 'twicedaily_safealternative', 'safealternative_urgent_cargus_awb_update');
    }
});

add_action('safealternative_urgent_cargus_awb_update', 'update_urgent_cargus_awb_status');
function update_urgent_cargus_awb_status()
{
    $url = get_option('uc_url');
    $key = get_option('uc_key');
    $UserName = rawurlencode(get_option('uc_username'));
    $Password = rawurlencode(get_option('uc_password'));

    $obj_urgent = new UrgentCargusAPI($url, $key);

    $fields = array(
        'UserName' => urldecode($UserName),
        'Password' => urldecode($Password)
    );

    $json_login = json_encode($fields);
    $login = $obj_urgent->CallMethod('LoginUser', $json_login, 'POST');

    if ($login['status'] != "200") {
        echo "<b>Autentificare esuata cu status: " . $login['status'] . " si mesaj: " . $login['message'] . "</b>";
        $_SESSION['Token'] = '';
    } else {
        $token = json_decode($login['message']);
        $_SESSION['Token'] = $token;
    }

    if (empty($token)) return;

    $FromDate = date('Y-m-d\TH:i:s', strtotime('-24 hour', strtotime(date("Y-m-d H:i:s"))));
    $ToDate = date('Y-m-d\TH:i:s');

    $resultTrace = $obj_urgent->CallMethod('AwbStatus/GetAwbSyncStatus?FromDate=' . $FromDate . '&ToDate=' . $ToDate, $json = "", 'GET', $token);

    if ($resultTrace['status'] == "200") {
        $resultMessage = $resultTrace['message'];
        $arrayResultTrace = json_decode($resultMessage, true);

        // scan each awbTrace
        foreach ($arrayResultTrace as $awbTrace) {
            $awbBarCode = $awbTrace['BarCode'];
            $awbStatus = $awbTrace['StatusExpression'];
            $awbDeductionId = $awbTrace['DeductionId'];
            $RepaymentValue = $awbTrace['RepaymentValue'];

            $current_post_id = safealternative_get_post_id_by_meta('awb_urgent_cargus', $awbBarCode);
            if (empty($current_post_id)) continue;

            update_post_meta($current_post_id, 'awb_urgent_cargus_trace_status', $awbStatus);
            update_post_meta($current_post_id, 'op_urgent_cargus', $awbDeductionId);
            update_post_meta($current_post_id, 'op_urgent_cargus_value', $RepaymentValue);
            mark_uc_order_complete($current_post_id, $awbStatus);
        } // end foreach		
    } // end else
}

function mark_uc_order_complete($post_id, $awb_status)
{
    if (get_option('uc_auto_mark_complete') !== "da") return;
    if (empty($post_id)) return;

    $order = wc_get_order($post_id);
    if (in_array($awb_status, ["Confirmat", "Rambursat"])) {
        $order->update_status('completed');
    }
}
