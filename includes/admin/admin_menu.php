<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

/**
 * Register the SafeAlternative admin page
 */
add_action('admin_menu', 'safealternative_admin_menu');
function safealternative_admin_menu()
{
    add_menu_page(
        __('SafeAlternative', 'safealternative-plugin'),
        'SafeAlternative',
        'manage_woocommerce',
        'safealternative-menu-content',
        'safealternative_menu_content',
        'data:image/svg+xml;base64,' . base64_encode('<svg width="20" height="20" viewBox="0 0 1792 1792" xmlns="http://www.w3.org/2000/svg"><path fill="#a0a5aa" d="M640 1408q0-52-38-90t-90-38-90 38-38 90 38 90 90 38 90-38 38-90zm-384-512h384v-256h-158q-13 0-22 9l-195 195q-9 9-9 22v30zm1280 512q0-52-38-90t-90-38-90 38-38 90 38 90 90 38 90-38 38-90zm256-1088v1024q0 15-4 26.5t-13.5 18.5-16.5 11.5-23.5 6-22.5 2-25.5 0-22.5-.5q0 106-75 181t-181 75-181-75-75-181h-384q0 106-75 181t-181 75-181-75-75-181h-64q-3 0-22.5.5t-25.5 0-22.5-2-23.5-6-16.5-11.5-13.5-18.5-4-26.5q0-26 19-45t45-19v-320q0-8-.5-35t0-38 2.5-34.5 6.5-37 14-30.5 22.5-30l198-198q19-19 50.5-32t58.5-13h160v-192q0-26 19-45t45-19h1024q26 0 45 19t19 45z"/></svg>'),
        99
    );

    add_submenu_page(
        'safealternative-menu-content',
        __('Setari Generale', 'safealternative-plugin'),
        __('Setari Generale', 'safealternative-plugin'),
        'manage_woocommerce',
        'safealternative-menu-content',
        'safealternative_menu_content'
    );

    $avail_shipping_methods = function_exists('WC') ? WC()->shipping->get_shipping_methods() : array();
    unset($avail_shipping_methods['flat_rate'], $avail_shipping_methods['free_shipping'], $avail_shipping_methods['local_pickup']);

    if (count($avail_shipping_methods) >= 2) {
        add_submenu_page(
            'safealternative-menu-content',
            __('Livrare - Sortare', 'safealternative-plugin'),
            __('Livrare - Sortare', 'safealternative-plugin'),
            'manage_woocommerce',
            'safealternative-shipping-reorder',
            'safealternative_shipping_reorder_content'
        );
    }
}

function safealternative_menu_content()
{
    include 'admin_template.php';
}

function safealternative_shipping_reorder_content()
{
    include 'admin_shipping_reorder_template.php';
}
