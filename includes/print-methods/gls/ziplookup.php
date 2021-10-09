<?php

add_action('add_meta_boxes', 'gls_tracking_box');
function gls_tracking_box()
{
	add_meta_box(
		'GLSawb_sectionid2',
		'GLS - Cod postal',
		'gls_meta_box_callback',
		'shop_order',
		'side'
	);
}

// Callback
function gls_meta_box_callback($post)
{
	$order = wc_get_order($post->ID);
	include_once(plugin_dir_path(__FILE__) . 'templates/ziplookup_metabox_template.php');
}

/**
 * Saving data
 */
add_action('save_post', 'gls_save_meta_box_data');
function gls_save_meta_box_data($order_id)
{
	if (isset($_POST['UpdateZipOrder'])) {
		$order = wc_get_order($order_id);

		if ($order->get_formatted_shipping_address()) {
			$order->set_shipping_postcode($_POST['zip_code_val']);
			$order->set_shipping_country('RO');
		} else {
			$order->set_billing_postcode($_POST['zip_code_val']);
			$order->set_billing_country('RO');
		}

		$order->save();
		wp_safe_redirect($_POST['_wp_http_referer']);
		exit();
	}
}

add_action("wp_ajax_safealternative_fetch_zipcode", function () {
	global $wpdb;

	$query = 'SELECT ZipCode,City,Street,County FROM courier_zipcodes';
	$keyword = $_POST['keyword'];

	if (is_numeric($keyword)) {
		$query = "$query WHERE ZipCode=$keyword";
	} else {
		$convertKeyword = explode(',', remove_accents(strtolower($keyword)));
		$convertedCity = remove_accents(trim($convertKeyword[0]));
		$convertedStreet = remove_accents(trim($convertKeyword[1] ?? ''));

		$query = "$query WHERE (City LIKE '%$convertedCity%' OR Street LIKE '%$convertedCity%')";

		if (!empty($convertedStreet)) {
			$query = "$query AND (City LIKE '%$convertedStreet%' OR Street LIKE '%$convertedStreet%')";
		}
	}

	$search = collect(
		$wpdb->get_results("$query LIMIT 25")
	);

	wp_send_json_success(
		$search->transform(function ($item) {
			return array(
				'zip_code' => $item->ZipCode,
				'city' => $item->City,
				'street' => $item->Street,
				'county' => $item->County
			);
		})
	);
});

function safealternative_icmp_strings($string1, $string2)
{
	return false !== stripos($string1, $string2);
}
