<?php

// Check if WooCommerce is active
if (safealternative_is_woocommerce_active()) {
    function Bookurier_Shipping_Method()
    {
        if (!class_exists('Bookurier_Shipping_Method')) {
            class Bookurier_Shipping_Method extends WC_Shipping_Method
            {
                // Constructor for your shipping class
                public function __construct()
                {
                    $this->id                 = 'bookurier';
                    $this->method_title       = __('Bookurier Shipping', 'bookurier');
                    $this->method_description = __('Bookurier Shipping Method for courier', 'bookurier');

                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array('RO');

                    $this->init();

                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Bookurier Shipping', 'bookurier');
                }

                // init
                function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                // define settings field for this shipping
                function init_form_fields()
                {
                    $this->form_fields = array(
                        'title' => array(
                            'title' => __('Denumire metoda livrare *', 'bookurier'),
                            'type' => 'text',
                            'description' => __('Denumirea metodei de livrare in Cart si Checkout, vizibila de catre client.', 'bookurier'),
                            'default' => __('Bookurier', 'bookurier'),
                            'desc_tip'      => true,
                            'custom_attributes' => array('required' => 'required')
                        ),
                        'prag_gratis_Bucuresti' => array(
                            'title'     => __('Prag gratis Bucuresti', 'bookurier'),
                            'type'      => 'text',
                            'default'   => __('250', 'bookurier')
                        ),
                        'suma_fixa_Bucuresti' => array(
                            'title'     => __('Suma fixa Bucuresti', 'bookurier'),
                            'type'      => 'text',
                            'default'   => __('15', 'bookurier')
                        ),
                        'prag_gratis_provincie' => array(
                            'title'     => __('Prag gratis provincie', 'bookurier'),
                            'type'      => 'text',
                            'default'   => __('250', 'bookurier')
                        ),
                        'suma_fixa_provincie' => array(
                            'title'     => __('Suma fixa provincie', 'bookurier'),
                            'type'      => 'text',
                            'default'   => __('18', 'bookurier')
                        ),
                        'tarif_implicit' => array(
                            'title'         => __('Tarif implicit', 'bookurier'),
                            'type'          => 'number',
                            'default'       => __('0', 'bookurier'),
                            'desc_tip'      => true,
                            'custom_attributes' => array('step' => '0.01', 'min' => '0'),
                            'description'   => __('Tariful implicit pentru metoda de livrare, pentru a arata o suma atunci cand nu este introdusa adresa de livrare. ', 'bookurier')
                        ),
                    );
                }

                public function admin_options()
                {
                    echo '<h1>SafeAlternative - Metoda de livrare Bookurier</h1><br/><table class="form-table">';
                    $this->generate_settings_html();
                    echo '</table>';
                }

                ////////////////////////////////////////////////////////////////
                //  Calculate //////////////////////////////////////////////////
                ////////////////////////////////////////////////////////////////

                public function calculate_shipping($package = array())
                {
                    $prag_gratis_Bucuresti = $this->get_option('prag_gratis_Bucuresti');
                    $suma_fixa_Bucuresti = $this->get_option('suma_fixa_Bucuresti');
                    $prag_gratis_provincie = $this->get_option('prag_gratis_provincie');
                    $suma_fixa_provincie = $this->get_option('suma_fixa_provincie');

                    $orasdest = ucfirst(strtolower($package['destination']['city']));
                    $judetdest = $package['destination']['state'];
                    $valoare_cos = 0;

                    foreach ($package['contents'] as $product) {
                        $valoare_cos += ($product['line_total'] + $product['line_tax']);
                    }

                    /////////////////////////////////////////////////////////
                    $label = $this->title;

                    if ($prag_gratis_Bucuresti == $prag_gratis_provincie && $valoare_cos >= $prag_gratis_Bucuresti) {
                        $transport = 0;
                        $label = $this->title . ': Gratis';
                    } else {
                        if ($judetdest && $orasdest) {
                            if ($judetdest == "B") {
                                if ($valoare_cos < $prag_gratis_Bucuresti) $transport = $suma_fixa_Bucuresti;
                                if ($valoare_cos >= $prag_gratis_Bucuresti) $transport = 0;
                            } else {
                                if ($valoare_cos < $prag_gratis_provincie) $transport = $suma_fixa_provincie;
                                if ($valoare_cos >= $prag_gratis_provincie) $transport = 0;
                            }
    
                            if ($transport == 0) $label = $this->title . ': Gratis';
                        } else {
                            $transport = (float) $this->get_option('tarif_implicit') ?: 0;
                        }
                    }

                    $args = array(
                        'id'    => $this->id,
                        'label' => $label,
                        'cost'  => $transport,
                        'taxes' => true
                    );

                    if ($transport !== 0 || (strpos($label, 'Gratis') !== false)) {
                        $args = apply_filters('safealternative_overwrite_bookurier_shipping', $args, $judetdest, $orasdest);
                        $this->add_rate($args);
                    }
                } // end method
            } // end class
        } // end ifclass exist
    } // end function

    add_action('woocommerce_shipping_init', 'bookurier_shipping_method');

    add_filter('woocommerce_shipping_methods', 'add_bookurier_shipping_method');
    function add_bookurier_shipping_method($methods)
    {
        $methods[] = 'Bookurier_Shipping_Method';
        return $methods;
    }

    // activate city
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_true');

    ////INCLUDE JAVASCRIPT//////////////////////////////////////////////////////////
    add_filter('woocommerce_default_address_fields', 'move_checkout_fields_woo_bookurier');
    function move_checkout_fields_woo_bookurier($fields)
    {
        $fields['state']['priority'] = 70;
        $fields['city']['priority'] = 80;
        return $fields;
    }

    add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache_bookurier');
    function clear_wc_shipping_rates_cache_bookurier()
    {
        $packages = WC()->cart->get_shipping_packages();

        foreach ($packages as $key => $value) {
            $shipping_session = "shipping_for_package_$key";
            unset(WC()->session->$shipping_session);
        }
    }

    add_action('admin_menu', 'register_bookurier_shipping_subpage');
    function register_bookurier_shipping_subpage()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Bookurier - Livrare',
            'Bookurier - Livrare',
            'manage_woocommerce',
            'bookurier_redirect',
            function () {
                wp_safe_redirect(safealternative_redirect_url('admin.php?page=wc-settings&tab=shipping&section=bookurier'));
                exit;
            }
        );
    }
} // end if
