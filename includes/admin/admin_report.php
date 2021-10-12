<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

function CR_generate_report($options = null)
{
    global $wp_version, $_SERVER;
    $frontend_htaccess = CR_htaccess_search(CR_frontend_path());
    $paths = array($frontend_htaccess);

    if (is_multisite()) {
        $active_plugins = get_site_option('active_sitewide_plugins');
        if (!empty($active_plugins)) {
            $active_plugins = array_keys($active_plugins);
        }
    } else {
        $active_plugins = get_option('active_plugins');
    }

    if (function_exists('wp_get_theme')) {
        $theme_obj = wp_get_theme();
        $active_theme = $theme_obj->get('Name');
    } else {
        $active_theme = get_current_theme();
    }

    $extras = array(
        'wordpress version' => $wp_version,
        'woocommerce version' => CR_get_WC_version_number(),
        'php version' => phpversion(),
        'siteurl' => get_option('siteurl'),
        'home' => get_option('home'),
        'home_url' => home_url(),
        'locale' => get_locale(),
        'active theme' => $active_theme
    );

    $extras['active plugins'] = $active_plugins;

    $item_options = array(
        'safealternative_plugin_version', 'safealternative_db_ver', 'auth_validity', 'user_safealternative', 'enable_fan_print', 'enable_fan_shipping', 'enable_cargus_print', 'enable_cargus_shipping', 'enable_gls_print', 'enable_gls_shipping', 'enable_dpd_print', 'enable_dpd_shipping', 'enable_sameday_print', 'enable_sameday_shipping', 'enable_bookurier_print', 'enable_bookurier_shipping','enable_memex_print', 'enable_memex_shipping','enable_nemo_print', 'enable_nemo_shipping', 'enable_optimus_print', 'enable_optimus_shipping', 'enable_express_print', 'enable_express_shipping', 'enable_team_print', 'enable_team_shipping', 'enable_checkout_city_select', 'safealternative_is_multisite', 'courier_email_from'
    );

    foreach ($item_options as $v) {
        if ($v === 'safealternative_plugin_version') {
            $options[$v] = get_file_data(SAFEALTERNATIVE_PLUGIN_FILE, ['Version' => 'Version'], 'plugin')['Version'];
        } else {
            $options[$v] = get_option($v, '0');
        }
    }

    return CR_build_report($_SERVER, $options, $extras, $paths);
}

function CR_build_report($server, $options, $extras = array(), $htaccess_paths = array())
{
    $server_keys = array(
        'DOCUMENT_ROOT' => '',
        'SERVER_SOFTWARE' => '',
        'REQUEST_URI' => ''
    );
    $server_vars = array_intersect_key($server, $server_keys);

    $buf = CR_format_report_section('Server Variables:', $server_vars);
    $buf .= CR_format_report_section('Wordpress Specific Extras:', $extras);
    $buf .= CR_format_report_section('SafeAlternative Plugin Options:', $options);

    if (empty($htaccess_paths)) {
        return $buf;
    }

    $buf .= "HTAccess below this point:\n";
    foreach ($htaccess_paths as $path) {
        if (!file_exists($path) || !is_readable($path)) {
            $buf .= $path . "File does not exist or is not readable.\n";
            continue;
        }

        $content = file_get_contents($path);
        if ($content === false) {
            $buf .= $path . "File returned false for file_get_contents.\n";
            continue;
        }
        $buf .= "Path: ";
        $buf .= $path . "\n" . $content . "\n\n";
    }
    return trim($buf);
}

function CR_format_report_section($section_header, $section)
{
    if (empty($section)) {
        return 'No matching ' . $section_header . "\n\n";
    }
    $buf = $section_header;

    foreach ($section as $k => $v) {
        $buf .= "\n" . '    ';

        if (!is_numeric($k)) {
            $buf .= $k . ' = ';
        }

        if (!is_string($v)) {
            $v = var_export($v, true);
        }

        $buf .= $v;
    }
    return $buf . "\n\n";
}

function CR_frontend_path()
{
    $frontend = rtrim(ABSPATH, '/');
    if (!$frontend) {
        $frontend = parse_url(get_option('home'));
        $frontend = !empty($frontend['path']) ? $frontend['path'] : '';
        $frontend = $_SERVER['DOCUMENT_ROOT'] . $frontend;
    }
    return realpath($frontend);
}

function CR_htaccess_search($start_path)
{
    $max_depth = 0;

    while (!file_exists($start_path . '/.htaccess') && $max_depth < 10) {
        if ($start_path === '/' || !$start_path) {
            return false;
        }
        if (!empty($_SERVER['DOCUMENT_ROOT']) && $start_path === $_SERVER['DOCUMENT_ROOT']) {
            return false;
        }
        $start_path = dirname($start_path);
        $max_depth++;
    }

    return "$start_path/.htaccess";
}

function CR_get_WC_version_number()
{
    if (!function_exists('get_plugins'))
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');

    $plugin_folder = get_plugins('/' . 'woocommerce');
    $plugin_file = 'woocommerce.php';

    return $plugin_folder[$plugin_file]['Version'] ?? 'MISSING';
}

add_action('wp_ajax_CR_send_report', 'CR_send_report');
function CR_send_report($ignore_die = false)
{
    $curl = curl_init();

    curl_setopt($curl, CURLOPT_POST, 1);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 100);
    curl_setopt($curl, CURLOPT_TIMEOUT, 100);
    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);

    $parameters = array(
        'user' => get_option('user_safealternative'),
        'report' => CR_generate_report()
    );

    curl_setopt($curl, CURLOPT_POSTFIELDS, $parameters);
    curl_setopt($curl, CURLOPT_URL, SAFEALTERNATIVE_API_URL . '/api/getUserReport');

    $response = curl_exec($curl);
    curl_close($curl);

    if ($ignore_die) {
        return $response;
    } else {
        wp_send_json($response);
        wp_die();
    }
}

// Send initial report after the first authentication
add_action('admin_init', function () {
    if ((get_option('auth_validity', '0') == '1') && (get_option('safealternative_initial_user_report', '0') == '0')) {
        //CR_send_report(true);
        //update_option('safealternative_initial_user_report', '1');
        //wp_safe_redirect(wp_get_referer(), 302);
        return true;
    }
}, 100);
