<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!function_exists('safealternative_redirect_url')) {
    function safealternative_redirect_url(string $path = null): string
    {
        return (bool) esc_attr(get_option('safealternative_is_multisite'))
            ? network_admin_url($path)
            : admin_url($path);
    }
}

if (!function_exists('safealternative_get_counties_list')) {
    function safealternative_get_counties_list(string $county_code = null)
    {
        $counties = [
            "AB" => "Alba", "AR" => "Arad", "AG" => "Arges", "BC" => "Bacau", "BH" => "Bihor", "BN" => "Bistrita-Nasaud", "BT" => "Botosani", "BR" => "Braila", "BV" => "Brasov", "B" => "Bucuresti", "BZ" => "Buzau", "CL" => "Calarasi", "CS" => "Caras-Severin", "CJ" => "Cluj", "CT" => "Constanta", "CV" => "Covasna", "DB" => "Dambovita", "DJ" => "Dolj", "GL" => "Galati", "GJ" => "Gorj", "GR" => "Giurgiu", "HR" => "Harghita", "HD" => "Hunedoara", "IL" => "Ialomita", "IS" => "Iasi", "IF" => "Ilfov", "MM" => "Maramures", "MH" => "Mehedinti", "MS" => "Mures", "NT" => "Neamt", "OT" => "Olt", "PH" => "Prahova", "SJ" => "Salaj", "SM" => "Satu Mare", "SB" => "Sibiu", "SV" => "Suceava", "TR" => "Teleorman", "TM" => "Timis", "TL" => "Tulcea", "VS" => "Vaslui", "VL" => "Valcea", "VN" => "Vrancea"
        ];
        return ($county_code && array_key_exists($county_code, $counties))
            ? $counties[$county_code]
            : $counties;
    }
}

if (!function_exists('safealternative_get_post_code')) {
    function safealternative_get_post_code(string $judet = null, string $oras = null): string
    {
        if (empty($judet) || empty($oras)) return '';
        if (strlen($judet) <= 2) $judet = safealternative_get_counties_list($judet);
        global $wpdb;
        return $wpdb->get_var("SELECT ZipCode FROM courier_zipcodes WHERE County LIKE '%$judet%' AND City='$oras' LIMIT 1") ?:
            $wpdb->get_var("SELECT ZipCode FROM courier_zipcodes WHERE County LIKE '%$judet%' AND City LIKE '%$oras%' LIMIT 1") ?: '';
    }
}

if (!function_exists('safealternative_is_woocommerce_active')) {
    function safealternative_is_woocommerce_active()
    {
        $is_active = in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')));
        if (!$is_active && is_multisite()) {
            $is_active = array_key_exists(
                'woocommerce/woocommerce.php',
                apply_filters('active_plugins', get_site_option('active_sitewide_plugins'))
            );
        }
        return $is_active;
    }
}

if (!function_exists('safealternative_get_post_id_by_meta')) {
    function safealternative_get_post_id_by_meta($key, $value)
    {
        global $wpdb;
        return $wpdb->get_var(
            $wpdb->prepare("SELECT post_id FROM $wpdb->postmeta WHERE meta_key=%s AND meta_value=%s LIMIT 1", $key, $value)
        );
    }
}

if (!function_exists('safealternative_get_url_contents')) {
    function safealternative_get_url_contents($url)
    {
        if (ini_get('allow_url_fopen')) {
            $response = file_get_contents($url);
        } else {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $response = curl_exec($ch);
            curl_close($ch);
        }
        return $response;
    }
}