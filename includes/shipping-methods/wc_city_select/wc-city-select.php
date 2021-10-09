<?php

if (!defined('ABSPATH')) {
    exit;
}
// Exit if accessed directly
// Check if WooCommerce is active
if (
    safealternative_is_woocommerce_active() &&
    !in_array('wc-city-select/wc-city-select.php', apply_filters('active_plugins', get_option('active_plugins'))) &&
    !class_exists('WC_City_Select')
) {
    class WC_City_Select
    {
        const VERSION = '1.0.6';

        private $plugin_path;
        private $plugin_url;

        private $cities;

        public function __construct()
        {
            add_filter('woocommerce_billing_fields', array($this, 'billing_fields'), 10, 2);
            add_filter('woocommerce_shipping_fields', array($this, 'shipping_fields'), 10, 2);
            add_filter('woocommerce_form_field_city', array($this, 'form_field_city'), 10, 4);

            //js scripts
            add_action('wp_enqueue_scripts', array($this, 'load_scripts'));
        }

        public function billing_fields($fields, $country)
        {
            $fields['billing_city']['type'] = 'city';

            return $fields;
        }

        public function shipping_fields($fields, $country)
        {
            $fields['shipping_city']['type'] = 'city';

            return $fields;
        }

        public function get_cities($cc = null)
        {
            if (empty($this->cities)) {
                $this->load_country_cities();
            }

            if (!is_null($cc)) {
                return isset($this->cities[$cc]) ? $this->cities[$cc] : false;
            } else {
                return $this->cities;
            }
        }

        public function load_country_cities()
        {
            global $cities;

            // Load only the city files the shop owner wants/needs.
            $allowed = array_merge(WC()->countries->get_allowed_countries(), WC()->countries->get_shipping_countries());

            if ($allowed) {
                foreach ($allowed as $code => $country) {
                    if (!isset($cities[$code]) && file_exists($this->get_plugin_path() . '/cities/' . $code . '.php')) {
                        include $this->get_plugin_path() . '/cities/' . $code . '.php';
                    }
                }
            }

            $this->cities = apply_filters('wc_city_select_cities', $cities);
        }

        public function form_field_city($field, $key, $args, $value)
        {
            // Do we need a clear div?
            if ((!empty($args['clear']))) {
                $after = '<div class="clear"></div>';
            } else {
                $after = '';
            }

            // Required markup
            if ($args['required']) {
                $args['class'][] = 'validate-required';
                $required = ' <abbr class="required" title="' . esc_attr__('required', 'woocommerce') . '">*</abbr>';
            } else {
                $required = '';
            }

            // Custom attribute handling
            $custom_attributes = array();

            if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
                foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
                    $custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
                }
            }

            // Validate classes
            if (!empty($args['validate'])) {
                foreach ($args['validate'] as $validate) {
                    $args['class'][] = 'validate-' . $validate;
                }
            }

            // field p and label
            $field = '<p class="form-row ' . esc_attr(implode(' ', $args['class'])) . '" id="' . esc_attr($args['id']) . '_field">';
            if ($args['label']) {
                $field .= '<label for="' . esc_attr($args['id']) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
            }

            // Get Country
            $subkey = substr($key, 0, strlen('billing')) === 'billing' ? 'billing' : 'shipping';
            $current_country = WC()->checkout->get_value("{$subkey}_country") ?: (WC()->session->get('customer')['country'] ?? null);
            $cart_selected_county = WC()->checkout->get_value("{$subkey}_state") ?: (WC()->session->get('customer')['state'] ?? null);

            // Get country cities
            $cities = $this->get_cities($current_country);

            if (is_array($cities)) {
                $field .= '<select name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" class="city_select ' . esc_attr(implode(' ', $args['input_class'])) . '" ' . implode(' ', $custom_attributes) . ' placeholder="' . esc_attr($args['placeholder']) . '"><option value="">' . __('Selectati localitatea&hellip;', 'woocommerce') . '</option>';

                if ($cart_selected_county && $cities[$cart_selected_county]) {
                    $dropdown_cities = $cities[$cart_selected_county];
                    foreach ($dropdown_cities as $city_name) {
                        $field .= '<option value="' . esc_attr($city_name) . '" ' . selected($value, $city_name, false) . '>' . $city_name . '</option>';
                    }
                }

                $field .= '</select>';

                if (!empty($cart_selected_county)) {
                    $field .= '<script>
                        jQuery("#' . $subkey . '_state:visible").val("' . $cart_selected_county . '").trigger("change");
                    </script>';
                }
            } else {
                $field .= '<input type="text" class="input-text ' . esc_attr(implode(' ', $args['input_class'])) . '" value="' . esc_attr($value) . '"  placeholder="' . esc_attr($args['placeholder']) . '" name="' . esc_attr($key) . '" id="' . esc_attr($args['id']) . '" ' . implode(' ', $custom_attributes) . ' />';
            }

            // field description and close wrapper
            if ($args['description']) {
                $field .= '<span class="description">' . esc_attr($args['description']) . '</span>';
            }

            $field .= '</p>' . $after;

            return $field;
        }

        public function load_scripts()
        {
            if (is_cart() || is_checkout() || is_wc_endpoint_url('edit-address')) {
                $city_select_path = $this->get_plugin_url() . 'assets/js/city-select.min.js';
                wp_enqueue_script('wc-city-select', $city_select_path, array('jquery', 'woocommerce'), self::VERSION, true);

                $cities = json_encode($this->get_cities());
                wp_localize_script('wc-city-select', 'wc_city_select_params', array(
                    'cities' => $cities,
                    'i18n_select_city_text' => esc_attr__('Select an option&hellip;', 'woocommerce'),
                ));
            }
        }

        public function get_plugin_path()
        {
            if ($this->plugin_path) {
                return $this->plugin_path;
            }

            return $this->plugin_path = plugin_dir_path(__FILE__);
        }

        public function get_plugin_url()
        {
            if ($this->plugin_url) {
                return $this->plugin_url;
            }

            return $this->plugin_url = plugin_dir_url(__FILE__);
        }
    }

    $GLOBALS['wc_city_select'] = new WC_City_Select();
}

add_filter('wc_city_select_cities', 'my_cities');
if (!function_exists('my_cities')) {
    function my_cities($cities)
    {
        global $wpdb;

        $query = 'SELECT * FROM courier_localities WHERE 1=1 ';
        if (class_exists('WC_Urgent_Cargus_Shipping_Method') && !class_exists('Fan_Shipping_Method')) {
            $query .= 'AND cargus_locality_id IS NOT NULL';
        }

        if (class_exists('Fan_Shipping_Method') && !class_exists('WC_Urgent_Cargus_Shipping_Method')) {
            $query .= 'AND fan_locality_id IS NOT NULL';
        }

        $cityList = $wpdb->get_results($query);
        $cities['RO'] = array();

        foreach ($cityList as $city) {
            $cities['RO'][$city->county_initials][] = $city->locality_name;
        }

        return $cities;
    }
}

add_filter('woocommerce_states', 'custom_woocommerce_states');
if (!function_exists('custom_woocommerce_states')) {
    function custom_woocommerce_states($states)
    {
        $states['RO'] = safealternative_get_counties_list();
        return $states;
    }
}

add_filter('woocommerce_default_address_fields', 'safealternative_woocommerce_field_order');
if (!function_exists('safealternative_woocommerce_field_order')) {
    function safealternative_woocommerce_field_order($fields)
    {
        $fields['state']['priority'] = 80;
        $fields['city']['priority'] = 85;
        return $fields;
    }
}

add_filter('default_checkout_billing_country', 'change_default_checkout_country_billing');
add_filter('default_checkout_shipping_country', 'change_default_checkout_country_shipping');

add_filter('default_checkout_billing_state', 'change_default_checkout_state_billing');
add_filter('default_checkout_shipping_state', 'change_default_checkout_state_shipping');

if (!function_exists('change_default_checkout_country_billing')) {
    function change_default_checkout_country_billing()
    {
        if (!in_array(get_option('woocommerce_default_customer_address'), ['geolocation_ajax', 'geolocation']))
            return 'RO'; // country code
    }
}

if (!function_exists('change_default_checkout_state_billing')) {
    function change_default_checkout_state_billing($state)
    {
        if (!in_array(get_option('woocommerce_default_customer_address'), ['geolocation_ajax', 'geolocation']))
            return $state ?: 'B'; // state code
    }
}

if (!function_exists('change_default_checkout_country_shipping')) {
    function change_default_checkout_country_shipping()
    {
        if (!in_array(get_option('woocommerce_default_customer_address'), ['geolocation_ajax', 'geolocation']))
            return 'RO'; // country code
    }
}

if (!function_exists('change_default_checkout_state_shipping')) {
    function change_default_checkout_state_shipping($state)
    {
        if (!in_array(get_option('woocommerce_default_customer_address'), ['geolocation_ajax', 'geolocation']))
            return $state ?: 'B'; // state code
    }
}
