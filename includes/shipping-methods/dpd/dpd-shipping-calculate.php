<?php

include_once 'dpd-calculator.class.php';
include_once 'SafealternativeDPDClient.php';

// Check if WooCommerce is active
if (safealternative_is_woocommerce_active()) {
    function DPD_Shipping_Method()
    {
        if (!class_exists('DPD_Shipping_Method')) {
            class DPD_Shipping_Method extends WC_Shipping_Method
            {
                // Constructor for your shipping class
                public function __construct()
                {
                    $this->id                 = 'dpd';
                    $this->method_title       = __('DPD Shipping', 'dpd');
                    $this->method_description = __('DPD Shipping Method for courier', 'dpd');

                    // Availability & Countries
                    $this->availability = 'including';
                    $this->countries = array('RO');

                    $this->init();

                    $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('DPD Shipping', 'dpd');
                }

                // init
                function init()
                {
                    // Load the settings API
                    $this->init_form_fields();
                    $this->init_settings();

                    // Save settings in admin if you have any defined
                    add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));

                    add_action('woocommerce_review_order_after_shipping', array($this, 'add_collect_points_dropdown_section'));

                }

                // define settings field for this shipping
                function init_form_fields()
                {
                    $this->form_fields = array(
                        'title' => array(
                            'title' => __('Denumire metoda livrare *', 'dpd'),
                            'type' => 'text',
                            'description' => __('Denumirea metodei de livrare in Cart si Checkout, vizibila de catre client.', 'dpd'),
                            'default' => __('DPD', 'dpd'),
                            'desc_tip'      => true,
                            'custom_attributes' => array('required' => 'required')
                        ),
                        'tarif_contract' => array(
                            'title'         => __('Afisare tarif contract', 'dpd'),
                            'type'          => 'select',
                            'default'       => 'no',
                            'css'           => 'width:400px;',
                            'description' => __('Pentru a activa aceasta optiune trebuie sa aveti si metoda DPD - AWB activata si configurata.', 'dpd'),
                            'desc_tip'      => true,
                            'options'       => array(
                                'no'        => __('Nu', 'dpd'),
                                'yes'       => __('Da', 'dpd')
                            ),
                        ),
                        'prag_gratis_Bucuresti' => array(
                            'title'     => __('Prag gratis Bucuresti', 'dpd'),
                            'type'      => 'text',
                            'default'   => __('250', 'dpd')
                        ),
                        'suma_fixa_Bucuresti' => array(
                            'title'     => __('Suma fixa Bucuresti', 'dpd'),
                            'type'      => 'text',
                            'default'   => __('15', 'dpd')
                        ),
                        'prag_gratis_provincie' => array(
                            'title'     => __('Prag gratis provincie', 'dpd'),
                            'type'      => 'text',
                            'default'   => __('250', 'dpd')
                        ),
                        'suma_fixa_provincie' => array(
                            'title'     => __('Suma fixa provincie', 'dpd'),
                            'type'      => 'text',
                            'default'   => __('18', 'dpd')
                        ),
                        'tarif_implicit' => array(
                            'title'         => __('Tarif implicit', 'dpd'),
                            'type'          => 'number',
                            'default'       => __('0', 'dpd'),
                            'desc_tip'      => true,
                            'custom_attributes' => array('step' => '0.01', 'min' => '0'),
                            'description'   => __('Tariful implicit pentru metoda de livrare, pentru a arata o suma atunci cand nu este introdusa adresa de livrare.', 'dpd')
                        ),
                        'tarif_maxim' => array(
                            'title'         => __('Tarif maxim livrare', 'dpd'),
                            'type'          => 'text',
                            'default'       => __('40', 'dpd'),
                            'desc_tip'      => true,
                            'description'   => __('Tariful final nu poate depasi aceasta valoare.', 'dpd')
                        ),
                        'dpd_box' => array(
                            'title'         => __('Activeaza serviciul DPDBox  <span style="color: red">(functie premium)</span>', 'dpd'),
                            'type'          => 'select',
                            'default'       => 'Nu',
                            'css'           => 'width:400px;',
                            'options'       => array(
                                'no'        => __('Nu', 'fan'),
                                'yes'        => __('Da', 'fan')
                            ),
                            'desc_tip'      => true,
                            'description'   => __('Pe pagina de checkout clientii vor avea posibilitatea de a alege livrarea catre un Collect Point DPD.', 'dpd')
                        ),
                    );
                }

                public function admin_options()
                {
                    echo '<h1>SafeAlternative - Metoda de livrare DPD</h1><br/><table class="form-table">';
                    $this->generate_settings_html();
                    echo '</table>';
                }

                private function fix_format($value)
                {
                    $value = str_replace(',', '.', $value);
                    return $value;
                }

                public function get_dpd_client()
                {
                    return new SafealternativeDPDClient();
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
                        if ($tarif_contract == 'yes' && class_exists('DPDAWB')) {
                            global $wpdb;
                            $first_zip = $wpdb->get_var("SELECT ZipCode FROM courier_zipcodes WHERE County='$judetdest' AND  City LIKE '%$orasdest%' LIMIT 1");
                            
                            if (isset($_REQUEST['payment_method']) && (addslashes($_REQUEST['payment_method']) != 'cod')) {
                                $ramburs = 0;
                            }

                            $req_vars = [
                                'sender_id' => get_option('dpd_sender_id'),
                                'service_ids' => get_option('dpd_service_id'),
                                'recipient_private_person' => 'y',
                                'courier_service_payer' => get_option('dpd_courier_service_payer') ?: 'SENDER',
                                'parcels_count' => get_option('dpd_parcel_count') ?: 1,
                                'total_weight' => $greutate,
                                'recipient_address_site_name' => $orasdest,
                                'recipient_address_state_id' => $judetdest,
                                'recipient_address_postcode' => $first_zip,
                                'autoadjust_pickup_date' => 'y',
                                'cod_amount' => $ramburs,
                                'cod_currency' => get_woocommerce_currencies(),
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
                                        $transport = 0;
                                        $bypass = true;
                                    }
                                }
    
                                if (!$bypass) {
                                    $transport = (new SafealternativeDPDShippingClass)->calculate($req_vars);
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
                        $args = apply_filters('safealternative_overwrite_dpd_shipping', $args, $judetdest, $orasdest);
                        $this->add_rate($args);

                        if ($this->get_option('dpd_box') == "yes") {
                            //Gets the available collectpoint options in the selected city
                            if (is_numeric($transport)) $this->get_collect_points_rate($transport);
                        }
                    }
                } // end method

                public function get_collect_points_rate($transport)
                {
                    $shipping_city = strtoupper(WC()->session->get('customer')['shipping_city']);

                    if (!($collectPointsDPD = get_transient('safealternative_dpd_box'))) {
                        $dpd_client = $this->get_dpd_client();
                        $collectPointsDPD = $dpd_client->get_collect_points();
                        set_transient('safealternative_dpd_box', $collectPointsDPD, DAY_IN_SECONDS );
                    }
                    
                    $collect_points = collect($collectPointsDPD)->filter(function ($point) use ($shipping_city) {
                        return false !== stripos($point['address']['siteName'], $shipping_city);
                    });

                    if($collect_points->isNotEmpty())
                    {
                        $label = 'DPDBox';
                        if ($transport == 0) $label .= ": Gratis";

                        $args = array(
                            'id'    => 'safealternative_dpd_box',
                            'label' => $label,
                            'cost'  => $transport,
                            'taxes' => true
                        );
                        $args = apply_filters('safealternative_overwrite_dpd_box_shipping', $args);
                        $this->add_rate($args);
                    }
                }

                public function add_collect_points_dropdown_section()
                {
                    $chosen_shipping_methods = WC()->session->get('chosen_shipping_methods');

                    if (empty($chosen_shipping_methods) || !in_array('safealternative_dpd_box', $chosen_shipping_methods)) {
                        return;
                    }

                    $shipping_city = strtoupper(WC()->session->get('customer')['shipping_city']);
                    
                    if (!($collectPointsDPD = get_transient('safealternative_dpd_box'))) {
                        $dpd_client = $this->get_dpd_client();
                        $collectPointsDPD = $dpd_client->get_collect_points();
                        set_transient('safealternative_dpd_box', $collectPointsDPD, DAY_IN_SECONDS );
                    }
                     
                    $collect_points = collect($collectPointsDPD)->filter(function ($point) use ($shipping_city) {
                    return false !== stripos($point['address']['siteName'], $shipping_city);
                    });

                    $template_data['collect_points'] = $collect_points;
                    wc_get_template('/templates/checkout-collectpoint-select.php', $template_data, dirname(__FILE__),  dirname(__FILE__));
                }
            } // end class
        } // end ifclass exist
    } // end function

    add_action('woocommerce_shipping_init', 'dpd_shipping_method');

    add_filter('woocommerce_shipping_methods', 'add_dpd_shipping_method');
    function add_dpd_shipping_method($methods)
    {
        $methods[] = 'DPD_Shipping_Method';
        return $methods;
    }

    // activate city
    add_filter('woocommerce_shipping_calculator_enable_city', '__return_true');
    add_filter('woocommerce_shipping_calculator_enable_postcode', '__return_true');

    ////INCLUDE JAVASCRIPT//////////////////////////////////////////////////////////
    add_filter('woocommerce_default_address_fields', 'move_checkout_fields_woo_dpd');
    function move_checkout_fields_woo_dpd($fields)
    {
        $fields['state']['priority'] = 70;
        $fields['city']['priority'] = 80;
        return $fields;
    }

    add_filter('woocommerce_checkout_update_order_review', 'clear_wc_shipping_rates_cache_dpd');
    function clear_wc_shipping_rates_cache_dpd()
    {
        $packages = WC()->cart->get_shipping_packages();

        foreach ($packages as $key => $value) {
            $shipping_session = "shipping_for_package_$key";
            unset(WC()->session->$shipping_session);
        }
    }

    add_action('admin_menu', 'register_dpd_shipping_subpage');
    function register_dpd_shipping_subpage()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'DPD - Livrare',
            'DPD - Livrare',
            'manage_woocommerce',
            'dpd_redirect',
            function () {
                wp_safe_redirect(safealternative_redirect_url('admin.php?page=wc-settings&tab=shipping&section=dpd'));
                exit;
            }
        );
    }

    add_action( 'woocommerce_checkout_update_order_meta', function ( $order_id ) {
        if ( ! empty( $_POST['safealternative_dpd_box'] ) ) {
            update_post_meta( $order_id, 'safealternative_dpd_box', sanitize_text_field( $_POST['safealternative_dpd_box'] ) );
        }
    });
} // end if
