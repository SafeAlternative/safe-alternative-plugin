<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'SafealternativeFanCalcApi.php');

// Check if WooCommerce is active
if (safealternative_is_woocommerce_active()) {
    function Fan_Shipping_Method()
    {
        if (!class_exists('Fan_Shipping_Method')) {
            include_once('FanShippingMethod.php');
        }
    }
    add_action('woocommerce_shipping_init', 'fan_shipping_method');

    add_filter('woocommerce_shipping_methods', 'add_fan_shipping_method');
    function add_fan_shipping_method($methods)
    {
        $methods[] = 'Fan_Shipping_Method';
        return $methods;
    }

    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    // add_filter( 'woocommerce_shipping_calculator_enable_postcode', '__return_false' );

    add_filter('woocommerce_default_address_fields', 'safealternative_move_checkout_fields_woo_fan');
    function safealternative_move_checkout_fields_woo_fan($fields)
    {
        $fields['state']['priority'] = 70;
        $fields['city']['priority'] = 80;
        return $fields;
    }

    add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache');
    function clear_wc_shipping_rates_cache()
    {
        $packages = WC()->cart->get_shipping_packages();

        foreach ($packages as $key => $value) {
            $shipping_session = "shipping_for_package_$key";
            unset(WC()->session->$shipping_session);
        }
    }

    add_action('admin_menu', 'register_fan_shipping_subpage');
    function register_fan_shipping_subpage()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'FanCourier - Livrare',
            'FanCourier - Livrare',
            'manage_woocommerce',
            'fan_redirect',
            function () {
                wp_safe_redirect( safealternative_redirect_url('admin.php?page=wc-settings&tab=shipping&section=fan' ) );
                exit;
            }
        );
    }

    add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
        if ( ! empty( $_POST['safealternative_fan_collectpoint'] ) ) {
            update_post_meta( $order_id, 'safealternative_fan_collectpoint', sanitize_text_field( $_POST['safealternative_fan_collectpoint'] ) );
        }
    });
}
