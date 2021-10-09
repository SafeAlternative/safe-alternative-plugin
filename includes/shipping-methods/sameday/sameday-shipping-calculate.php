<?php

include_once 'sameday-calculator.class.php';
include_once 'sameday-lockers.class.php';

// Check if WooCommerce is active
if (safealternative_is_woocommerce_active()) {
    function Sameday_Shipping_Method()
    {
        if (!class_exists('Sameday_Shipping_Method')) {
            class Sameday_Shipping_Method extends WC_Shipping_Method
            {
                // Constructor for your shipping class
                public function __construct()
                {
                    $this->id                 = 'sameday';
                    $this->method_title       = __('Sameday Shipping', 'sameday');
                    $this->method_description = __('Sameday Shipping Method for courier', 'sameday');

                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array('RO');

                    $this->init();

                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Sameday Shipping', 'sameday');
                }

                // init
                function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    add_action('woocommerce_review_order_after_shipping', array($this, 'add_lockers_dropdown_section'));

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
                }

                // define settings field for this shipping
                function init_form_fields()
                {
                    $this->form_fields = array(
                        'title' => array(
                            'title' => __('Denumire metoda livrare *', 'sameday'),
                            'type' => 'text',
                            'description' => __('Denumirea metodei de livrare in Cart si Checkout, vizibila de catre client.', 'sameday'),
                            'default' => __('Sameday', 'sameday'),
                            'desc_tip'      => true,
                            'custom_attributes' => array('required' => 'required')
                        ),
                        'tarif_contract' => array(
                            'title'         => __('Afisare tarif contract', 'sameday'),
                            'type'          => 'select',
                            'default'       => 'no',
                            'css'           => 'width:400px;',
                            'description' => __('Pentru a activa aceasta optiune trebuie sa aveti si metoda Sameday - AWB activata si configurata.', 'sameday'),
                            'desc_tip'      => true,
                            'options'       => array(
                                'no'        => __('Nu', 'sameday'),
                                'yes'       => __('Da', 'sameday')
                            ),
                        ),
                        'prag_gratis_Bucuresti' => array(
                            'title'     => __('Prag gratis Bucuresti', 'sameday'),
                            'type'      => 'text',
                            'default'   => __('250', 'sameday')
                        ),
                        'suma_fixa_Bucuresti' => array(
                            'title'     => __('Suma fixa Bucuresti', 'sameday'),
                            'type'      => 'text',
                            'default'   => __('15', 'sameday')
                        ),
                        'prag_gratis_provincie' => array(
                            'title'     => __('Prag gratis provincie', 'sameday'),
                            'type'      => 'text',
                            'default'   => __('250', 'sameday')
                        ),
                        'suma_fixa_provincie' => array(
                            'title'     => __('Suma fixa provincie', 'sameday'),
                            'type'      => 'text',
                            'default'   => __('18', 'sameday')
                        ),
                        'tarif_implicit' => array(
                            'title'         => __('Tarif implicit', 'sameday'),
                            'type'          => 'number',
                            'default'       => __('0', 'sameday'),
                            'desc_tip'      => true,
                            'custom_attributes' => array('step' => '0.01', 'min' => '0'),
                            'description'   => __('Tariful implicit pentru metoda de livrare, pentru a arata o suma atunci cand nu este introdusa adresa de livrare. ', 'sameday')
                        ),
                        'tarif_maxim' => array(
                            'title'         => __('Tarif maxim livrare', 'sameday'),
                            'type'          => 'text',
                            'default'       => __('40', 'sameday'),
                            'desc_tip'      => true,
                            'description'   => __('Tariful final nu poate depasi aceasta valoare.', 'sameday')
                        ),
                        'lockers_activ' => array(
                            'title'         => __('Activeaza serviciul EasyBox <span style="color: red">(functie premium)</span>', 'sameday'),
                            'type'          => 'select',
                            'default'       => 'Nu',
                            'css'           => 'width:400px;',
                            'options'       => array(
                                'no'        => __('Nu', 'sameday'),
                                'yes'        => __('Da', 'sameday')
                            ),
                            'desc_tip'      => true,
                            'description'       => __('Pe pagina de checkout clientii vor avea posibilitatea de a alege optiunea EasyBox Sameday.  Coletul va fi trimis la adresa introdusa daca folositi un cont SafeAlternative FanCourier gratuit.', 'sameday')
                        ),
                    );
                }

                public function admin_options()
                {
                    echo '<h1>SafeAlternative - Metoda de livrare Sameday</h1><br/><table class="form-table">';
                    $this->generate_settings_html();
                    echo '</table>';
                }

                private function fix_format($value)
                {
                    $value = str_replace(',', '.', $value);
                    return $value;
                }

                public function get_sameday_obj()
                {
                    return new SafealternativeSamedayLockersClass();
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
                    $judetdest_abvr = $package['destination']['state'] ?? '';
                    $judetdest = safealternative_get_counties_list($judetdest_abvr);

                    $tarif_contract = $this->get_option('tarif_contract');
                    $tarif_maxim = $this->get_option('tarif_maxim');
                    if (empty($tarif_maxim)) $tarif_maxim = 99999;

                    $ramburs = 0;
                    $valoare_cos = 0;
                    $greutate = 0;

                    foreach ($package['contents'] as $product) {
                        $ramburs += ($product['line_total'] + $product['line_tax']);
                        $valoare_cos += ($product['line_total'] + $product['line_tax']);

                        // WooCommerce 3.0 or later.
                        if (method_exists($product['data'], 'get_height')) {
                            $greutate += $this->fix_format($product['data']->get_weight() ?: 1) * $product['quantity'];
                        } else {
                            $greutate += $this->fix_format($product['data']->weight ?: 1) * $product['quantity'];
                        }
                    }

                    /////////////////////////////////////////////////////////
                    $label = $this->title;
                    if ($prag_gratis_Bucuresti == $prag_gratis_provincie && $ramburs >= $prag_gratis_Bucuresti) {
                        $transport = 0;
                        $label = $this->title . ': Gratis';
                    } else {
                        if ($tarif_contract == 'yes' && class_exists('SamedayAWB')) {
                            if (isset($_REQUEST['payment_method']) && (addslashes($_REQUEST['payment_method']) != 'cod')) {
                                $ramburs = 0;
                            }

                            $parameters = [
                                'weight' => (int) $greutate,
                                'city' => $orasdest,
                                'state' => $judetdest,
                                'address' => $package['destination']['address'],
                                'declared_value' => get_option('sameday_declared_value') ?: 0,
                                'cod_value' => $ramburs
                            ];

                            $transport = (float) $this->get_option('tarif_implicit') ?: 0;

                            try {
                                $bypass = false;
                                if ($judetdest_abvr == "B") {
                                    if ($valoare_cos >= $prag_gratis_Bucuresti) {
                                        $transport = 0;
                                        $bypass = true;
                                    }
                                } else {
                                    if ($valoare_cos >= $prag_gratis_provincie) {
                                        $transport = 0;
                                        $bypass = true;
                                    }
                                }

                                if (!$bypass) {
                                    $transport = (new SafealternativeSamedayShippingClass)->calculate($parameters);
                                }

                                if ($transport == 0) $label = $this->title . ': Gratis';
                            } catch (\Exception $e) {
                            }
                        } else {
                            if ($judetdest_abvr && $orasdest) {
                                if ($judetdest_abvr == "B") {
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
                    }

                    $transport = min($transport, $tarif_maxim);

                    $args = array(
                        'id'    => $this->id,
                        'label' => $label,
                        'cost'  => $transport,
                        'taxes' => true
                    );

                    if ($transport !== 0 || (strpos($label, 'Gratis') !== false)) {
                        $args = apply_filters('safealternative_overwrite_sameday_shipping', $args, $judetdest, $orasdest);
                        $this->add_rate($args);

                        if ($this->get_option('lockers_activ') == "yes") {
                            //Gets the available collectpoint options in the selected city
                            if (is_numeric($transport)) $this->get_lockers_rate($transport);
                        }
                    }
                } // end method

                public function get_lockers_rate($transport)
                {
                    $label = 'Sameday EasyBox';
                    if ($transport == 0) $label .= ": Gratis";

                    $args = array(
                        'id'    => 'safealternative_sameday_lockers',
                        'label' => $label,
                        'cost'  => $transport,
                        'taxes' => true
                    );
                    $args = apply_filters('safealternative_overwrite_sameday_easybox_shipping', $args);
                    $this->add_rate($args);
                }

                public function add_lockers_dropdown_section()
                {
                    $chosen_shipping_methods = WC()->session->get('chosen_shipping_methods');

                    if (empty($chosen_shipping_methods) || !in_array('safealternative_sameday_lockers', $chosen_shipping_methods)) {
                        return;
                    }

                    $shipping_city = WC()->session->get('customer')['shipping_city'];
                    $shipping_county = WC()->session->get('customer')['state'];
                    $recipient_address_county = safealternative_get_counties_list($shipping_county);
                    
                    if (!($lockers = get_transient('safealternative_sameday_lockers'))) {
                        $obj_sameday = $this->get_sameday_obj();
                        $lockers = json_decode($obj_sameday->CallMethod('lockers', array(), 'GET')['message']);
                        set_transient('safealternative_sameday_lockers', $lockers, DAY_IN_SECONDS );
                    }

                    $gasit = 0;
                    $collection = collect($lockers)
                        ->where('county', $recipient_address_county)
                        ->where('city', $shipping_city);

                    if (!count($collection)) {
                        $gasit = 1;
                        $collection = collect($lockers)->where('county', $recipient_address_county);
                    }

                    $template_data['lockers'] = $collection->sortBy('city');
                    $template_data['gasit'] = $gasit;
                    wc_get_template('/templates/checkout-lockers-select.php', $template_data, dirname(__FILE__),  dirname(__FILE__));
                }
            } // end class
        } // end ifclass exist
    } // end function

    add_action('woocommerce_shipping_init', 'sameday_shipping_method');

    add_filter('woocommerce_shipping_methods', 'add_sameday_shipping_method');
    function add_sameday_shipping_method($methods)
    {
        $methods[] = 'Sameday_Shipping_Method';
        return $methods;
    }

    // activate city
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_true');

    ////INCLUDE JAVASCRIPT//////////////////////////////////////////////////////////
    add_filter('woocommerce_default_address_fields', 'move_checkout_fields_woo_sameday');
    function move_checkout_fields_woo_sameday($fields)
    {
        $fields['state']['priority'] = 70;
        $fields['city']['priority'] = 80;
        return $fields;
    }

    add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache_sameday');
    function clear_wc_shipping_rates_cache_sameday()
    {
        $packages = WC()->cart->get_shipping_packages();

        foreach ($packages as $key => $value) {
            $shipping_session = "shipping_for_package_$key";
            unset(WC()->session->$shipping_session);
        }
    }

    add_action('admin_menu', 'register_sameday_shipping_subpage');
    function register_sameday_shipping_subpage()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Sameday - Livrare',
            'Sameday - Livrare',
            'manage_woocommerce',
            'sameday_redirect',
            function () {
                wp_safe_redirect(safealternative_redirect_url('admin.php?page=wc-settings&tab=shipping&section=sameday'));
                exit;
            }
        );
    }

    add_action('woocommerce_checkout_update_order_meta', function ($order_id) {
        if (!empty($_POST['safealternative_sameday_lockers'])) {
            update_post_meta($order_id, 'safealternative_sameday_lockers', sanitize_text_field($_POST['safealternative_sameday_lockers']));
        }
    });
} // end if
