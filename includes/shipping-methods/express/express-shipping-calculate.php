<?php

include_once 'express-calculator.class.php';

// Check if WooCommerce is active
if (in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins')))) {
    function Express_Shipping_Method()
    {
        if (!class_exists('Express_Shipping_Method')) {
            class Express_Shipping_Method extends WC_Shipping_Method
            {
                // Constructor for your shipping class
                public function __construct()
                {
                    $this->id  = 'express';
                    $this->method_title = __('Express Shipping', 'express');
                    $this->method_description = __('Express Shipping Method for courier', 'express');

                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array('RO');

                    $this->init();

                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Express Shipping', 'express');
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
                            'title' => __('Denumire metoda livrare *', 'express'),
                            'type' => 'text',
                            'description' => __('Denumirea metodei de livrare in Cart si Checkout, vizibila de catre client.', 'express'),
                            'default' => __('Express', 'express'),
                            'desc_tip' => true,
                            'custom_attributes' => array('required' => 'required')
                        ),
                        'tarif_contract' => array(
                            'title' => __('Afisare tarif contract', 'express'),
                            'type' => 'select',
                            'default' => 'no',
                            'css' => 'width:400px;',
                            'description' => __('Pentru a activa aceasta optiune trebuie sa aveti si metoda Express - AWB activata si configurata.', 'express'),
                            'desc_tip' => true,
                            'options' => array(
                                'no' => __('Nu', 'express'),
                                'yes' => __('Da', 'express')
                            ),
                        ),
                        'prag_gratis_Bucuresti' => array(
                            'title' => __('Prag gratis Bucuresti', 'express'),
                            'type' => 'text',
                            'default' => __('250', 'express')
                        ),
                        'suma_fixa_Bucuresti' => array(
                            'title' => __('Suma fixa Bucuresti', 'express'),
                            'type' => 'text',
                            'default' => __('15', 'express')
                        ),
                        'prag_gratis_provincie' => array(
                            'title' => __('Prag gratis provincie', 'express'),
                            'type' => 'text',
                            'default' => __('250', 'express')
                        ),
                        'suma_fixa_provincie' => array(
                            'title' => __('Suma fixa provincie', 'express'),
                            'type' => 'text',
                            'default' => __('18', 'express')
                        ),
                        'tarif_implicit' => array(
                            'title' => __('Tarif implicit', 'express'),
                            'type' => 'number',
                            'default' => __('0', 'express'),
                            'desc_tip' => true,
                            'custom_attributes' => array('step' => '0.01', 'min' => '0'),
                            'description' => __('Tariful implicit pentru metoda de livrare, pentru a arata o suma atunci cand nu este introdusa adresa de livrare.', 'express')
                        ),
                        'tarif_maxim' => array(
                            'title' => __('Tarif maxim livrare', 'express'),
                            'type' => 'text',
                            'default' => __('40', 'express'),
                            'desc_tip' => true,
                            'description' => __('Tariful final nu poate depasi aceasta valoare.', 'express')
                        ),
                    );
                }

                public function admin_options()
                {
                    echo '<h1>SafeAlternative - Metoda de livrare Express</h1><br/><table class="form-table">';
                    $this->generate_settings_html();
                    echo '</table>';
                }

                private function fix_format($value)
                {
                    $value = str_replace(',', '.', $value);
                    return $value;
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

                    $orasdest = strtolower($package['destination']['city']);
                    $judetdest_abvr = $package['destination']['state'] ?? '';
                    $judetdest = safealternative_get_counties_list()[$judetdest_abvr];
                    $adresadest = $package['destination']['address'];
                    $postcodedest = $package['destination']['postcode'];
                    $tarif_contract = $this->get_option('tarif_contract');
                    $tarif_maxim = $this->get_option('tarif_maxim');
                    if (empty($tarif_maxim)) $tarif_maxim = 99999;

                    $ramburs = 0;
                    $greutate = 0;
                    $valoare_cos = 0;

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

                    $weight_type = get_option('woocommerce_weight_unit');
                    if ($weight_type == 'g') {
                        $greutate = $greutate / 1000;
                    }

                    if ($greutate < 1) {
                        $greutate = 1;
                    } else {
                        $greutate = round($greutate);
                    }

                    /////////////////////////////////////////////////////////
                    $label = $this->title;

                    if ($prag_gratis_Bucuresti == $prag_gratis_provincie && $ramburs >= $prag_gratis_Bucuresti) {
                        $transport = 0;
                        $label = $this->title . ': Gratis';
                    } else {
                        if ($tarif_contract == 'yes' && class_exists('ExpressAWB')) {
                            global $wpdb;
                            $first_zip = $wpdb->get_var("SELECT ZipCode FROM courier_zipcodes WHERE County='$judetdest' AND  City LIKE '%$orasdest%'");
                            $zip = $postcodedest ?? $first_zip;

                            if (isset($_REQUEST['payment_method']) && (addslashes($_REQUEST['payment_method']) != 'cod')) {
                                $ramburs = 0;
                            }

                            $req_vars = [
                                'type' => get_option('express_package_type'),
                                'service_type' => get_option('express_service'),
                                'cnt' => get_option('express_parcel_count'),
                                'retur' => get_option('express_return'),
                                'retur_type' => get_option('express_return_type'),
                                'ramburs' => $ramburs,
                                'ramburs_type' => 'cash',
                                'service_134' => get_option('express_retur_signed_doc_delivery'),
                                'service_135' => get_option('express_is_sat_delivery'),
                                'service_136' => get_option('express_18hr_20hr_package'),
                                'service_137' => get_option('express_printed_awb'),
                                'insurance' => get_option('express_insurance'),
                                'weight' =>  $greutate,
                                'content' => get_option('express_content'),
                                'fragile' => get_option('express_is_fragile'),
                                'payer' => get_option('express_payer'),
                                'from_county' => get_option('express_county'),
                                'from_city' => get_option('express_city'),
                                'from_address' => get_option('express_address'),
                                'from_zipcode' => get_option('express_postcode'),
                                'to_county' => $judetdest,
                                'to_city' => $orasdest,
                                'to_address' => $adresadest,
                                'to_zipcode' => $zip,
                            ];

                            $transport = (float) $this->get_option('tarif_implicit') ?: 0;

                            if (empty($judetdest_abvr) || empty($orasdest)) return;

                            try {
                                $bypass = false;
                                if ($judetdest_abvr == "B") {
                                    if ($valoare_cos >= $prag_gratis_Bucuresti) {
                                        $transport = 0;
                                        $bypass = true;
                                    }
                                } else {
                                    if ($valoare_cos >= $prag_gratis_provincie) {
                                        $transport = $bypass = 0;
                                        $bypass = true;
                                    }
                                }

                                if (!$bypass) {
                                    $transport = (new SafealternativeExpressShippingClass)->calculate($req_vars);
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
                        'id' => $this->id,
                        'label' => $label,
                        'cost' => $transport,
                        'taxes' => true
                    );

                    if ($transport !== 0 || (strpos($label, 'Gratis') !== false)) {
                        $args = apply_filters('safealternative_overwrite_express_shipping', $args, $judetdest, $orasdest);
                        $this->add_rate($args);
                    }
                } // end method
            } // end class
        } // end ifclass exist
    } // end function

    add_action('woocommerce_shipping_init', 'express_shipping_method');

    add_filter('woocommerce_shipping_methods', 'add_express_shipping_method');
    function add_express_shipping_method($methods)
    {
        $methods[] = 'Express_Shipping_Method';
        return $methods;
    }

    // activate city
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_true');

    ////INCLUDE JAVASCRIPT//////////////////////////////////////////////////////////
    add_filter('woocommerce_default_address_fields', 'bbloomer_move_checkout_fields_woo_express');
    function bbloomer_move_checkout_fields_woo_express($fields)
    {
        $fields['state']['priority'] = 70;
        $fields['city']['priority'] = 80;
        return $fields;
    }

    add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache_express');
    function clear_wc_shipping_rates_cache_express()
    {
        $packages = WC()->cart->get_shipping_packages();

        foreach ($packages as $key => $value) {
            $shipping_session = "shipping_for_package_$key";
            unset(WC()->session->$shipping_session);
        }
    }

    add_action('admin_menu', 'register_express_shipping_subpage');
    function register_express_shipping_subpage()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Express - Livrare',
            'Express - Livrare',
            'manage_woocommerce',
            'express_redirect',
            function () {
                wp_safe_redirect(safealternative_redirect_url('admin.php?page=wc-settings&tab=shipping&section=express'));
                exit;
            }
        );
    }
} // end if
