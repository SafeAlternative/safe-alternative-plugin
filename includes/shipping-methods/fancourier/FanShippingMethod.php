<?php

class Fan_Shipping_Method extends WC_Shipping_Method
{
    // Constructor for your shipping class
    public function __construct()
    {
        $this->id                 = 'fan';
        $this->method_title       = __('Fan Courier Shipping', 'fan');
        $this->method_description = __('Fan Shipping Method for courier', 'fan');

        // Availability & Countries
        $this->availability = 'including';
        $this->countries = array('RO');

        $this->init();

        $this->title = isset($this->settings['title']) ? $this->settings['title'] : __('Fan Courier Shipping', 'fan');
    }

    // init
    function init()
    {
        // Load the settings API
        $this->init_form_fields();
        $this->init_settings();

        // Add collectpoints in review order
        add_action('woocommerce_review_order_after_shipping', array($this, 'review_order_after_shipping'));

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    // define settings field for this shipping
    function init_form_fields()
    {
        $shipping_method_settings = include 'shipping-method-settings.php';
        $this->form_fields = $shipping_method_settings;
    }

    public function admin_options()
    {
        echo '<style>.woocommerce table.form-table th{vertical-align:middle;}</style>';
        echo '<h1>SafeAlternative - Metoda de livrare FanCourier</h1><br/><table class="form-table">';
        $this->generate_settings_html();
        echo '</table>';
    }

    private function fix_format($value)
    {
        $value = str_replace(',', '.', $value);
        return $value;
    }

    private function getCountyNameByCode($code)
    {
        return safealternative_get_counties_list($code);
    }

    public function get_fan_obj()
    {
        return new SafealternativeFanCalcApi(
            get_option('fan_user'), 
            get_option('fan_password'), 
            get_option('fan_clientID')
        );
    }

    public function calculate_shipping($package = array())
    {
        global $wpdb;

        $tarif_contract = $this->get_option('tarif_contract');
        $tarif_contract_tva = $this->get_option('tarif_contract_tva');
        $prag_gratis_Bucuresti = $this->get_option('prag_gratis_Bucuresti');
        $suma_fixa_Bucuresti = $this->get_option('suma_fixa_Bucuresti');
        $prag_gratis_provincie = $this->get_option('prag_gratis_provincie');
        $suma_fixa_provincie = $this->get_option('suma_fixa_provincie');
        $pret_km_suplimentar = $this->get_option('pret_km_suplimentar');
        $numar_colete = $this->get_option('numar_colete');
        $numar_plicuri = $this->get_option('numar_plicuri');
        $plata_transportului = $this->get_option('plata_transportului');
        $tarif_maxim = $this->get_option('tarif_maxim');
        $deschidere_la_livrare = get_option('fan_deschidere');
        $livrare_sambata = get_option('fan_sambata');
        if (empty($tarif_maxim)) $tarif_maxim = 99999;

        $orasdest = ucfirst(strtolower($package['destination']['city']));
        $judetdest = $package['destination']['state'];

        $valoare_declarata = 0;
        $ramburs = 0;
        $valoare_cos = 0;

        $greutate = 0;
        $latime = 0;
        $heightList = array();
        $lengthList = array();

        foreach ($package['contents'] as $product) {
            $valoare_declarata += ($product['line_total'] + $product['line_tax']);
            $ramburs += ($product['line_total'] + $product['line_tax']);
            $valoare_cos += ($product['line_total'] + $product['line_tax']);

            // WooCommerce 3.0 or later.
            if (method_exists($product['data'], 'get_height')) {
                $greutate += $this->fix_format($product['data']->get_weight() ?: 1) * $product['quantity'];
                $latime += wc_get_dimension($this->fix_format(null != $product['data']->get_width() ? $product['data']->get_width() : 10), 'cm') * $product['quantity'];
                $inaltime = wc_get_dimension($this->fix_format(null != $product['data']->get_height() ? $product['data']->get_height() : 10), 'cm');
                $heightList[] = $inaltime;
                $lungime = wc_get_dimension($this->fix_format(null != $product['data']->get_length() ? $product['data']->get_length()  : 10), 'cm');
                $lengthList[] = $lungime;
            } else {
                $greutate += $this->fix_format($product['data']->weight ?: 1) * $product['quantity'];
                $latime += wc_get_dimension($this->fix_format(null != $product['data']->width ? $product['data']->width : 10), 'cm') * $product['quantity'];
                $inaltime = wc_get_dimension($this->fix_format(null != $product['data']->height ? $product['data']->height : 10), 'cm');
                $heightList[] = $inaltime;
                $lungime = wc_get_dimension($this->fix_format(null != $product['data']->length ? $product['data']->length : 10), 'cm');
                $lengthList[] = $lungime;
            }
        }

        $inaltime = max($heightList);
        $lungime = max($lengthList);

        if ($inaltime == 0) {
            $inaltime = 10;
        }
        if ($latime == 0) {
            $latime = 10;
        }
        if ($lungime == 0) {
            $lungime = 10;
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

        $asigurare = $this->get_option('asigurare');
        if ($asigurare == 'fara') {
            $valoare_declarata = 0;
        }

        $options = array();
        if($deschidere_la_livrare == 'da'){
            $options[] = 'A';
        }
        if($livrare_sambata == 'da'){
            $options[] = 'S';
        }
        $options = implode(',', $options);

        $localityList = $wpdb->get_results("SELECT * FROM courier_localities where fan_locality_id IS NOT NULL AND county_initials='" . $judetdest . "' AND  locality_name='" . $orasdest . "' ");
        
        if ($prag_gratis_Bucuresti == $prag_gratis_provincie && $valoare_cos >= $prag_gratis_Bucuresti) {
            $transport = 0;
            $label = $this->title . ': Gratis';
        } else {
            // a facut o selectie buna din nomenclator
            if (!empty($localityList)) {
                /////// tarif contract /////////////////////////////////
                if ($tarif_contract == 'yes' && class_exists('FanGenereazaAWB')) {

                    $obj_fan = $this->get_fan_obj();

                    $numele_serviciului = 'Cont Colector';
                    if ($ramburs == 0) {
                        $numele_serviciului = 'Standard';
                    }

                    if (isset($_REQUEST['payment_method']) && (addslashes($_REQUEST['payment_method']) != 'cod')) {
                        $numele_serviciului = 'Standard';
                        $ramburs = 0;
                    }

                    $plata_la   = $plata_transportului;
                    $localitate_dest = $localityList[0]->fan_locality_name;
                    $judet_dest = $this->getCountyNameByCode($judetdest);
                    $plicuri    = $numar_plicuri;
                    $colete     = $numar_colete;
                    $greutate   = (int) $greutate;

                    $parameters = array(
                        'serviciu'  => $numele_serviciului,
                        'plata_la'  => $plata_la,
                        'localitate_dest' => $localitate_dest,
                        'judet_dest' => $judet_dest,
                        'plicuri'   => $plicuri,
                        'colete'    => $colete,
                        'greutate'  => $greutate,
                        'lungime'   => $lungime,
                        'latime'    => $latime,
                        'inaltime'  => $inaltime,
                        'ramburs'   => $ramburs,
                        'optiuni'   => $options
                    );

                    $parameters = apply_filters('safealternative_overwrite_fan_shipping_parameters', $parameters ?? null);
                    $transport = $obj_fan->getTarif($parameters);

                    // praguri dupa valoarea cosului
                    if ($judetdest == "B") {
                        if ($valoare_cos >= $prag_gratis_Bucuresti) $transport = 0;
                    } else {
                        if ($valoare_cos >= $prag_gratis_provincie) $transport = 0;
                    }

                    $label = $this->title;

                    if (strpos($transport, 'Error nume localitate') !== false)
                        return;

                    if ($transport == 0) {
                        $label = $this->title . ': Gratis';
                    } else {
                        if (!empty($tarif_contract_tva)) {
                            $transport =  $transport + ($transport * $tarif_contract_tva / 100);
                        }
                    }
                } else {
                    $extra_km = $wpdb->get_var("SELECT fan_extra_km FROM courier_localities where fan_locality_id IS NOT NULL AND county_initials='" . $judetdest . "' AND  locality_name='" . $orasdest . "' LIMIT 1 ") ?: 0;

                    if ($judetdest == "B") {
                        if ($valoare_cos < $prag_gratis_Bucuresti) $transport = $suma_fixa_Bucuresti;
                        if ($valoare_cos >= $prag_gratis_Bucuresti) $transport = 0;
                    } else {
                        if ($valoare_cos < $prag_gratis_provincie) $transport = $suma_fixa_provincie + $pret_km_suplimentar * $extra_km;
                        if ($valoare_cos >= $prag_gratis_provincie) $transport = 0 + $pret_km_suplimentar * $extra_km;
                    }

                    $label = $this->title;
                    if ($transport == 0) $label = $this->title . ': Gratis';
                }
            }

            // Daca nu a selectat o localitate punem tarif implicit //////////
            else {
                $label = $this->title;
                $transport = (float) $this->get_option('tarif_implicit') ?: 0;
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
            $args = apply_filters('safealternative_overwrite_fan_shipping', $args, $judetdest, $extra_km ?? null, $orasdest);
            $this->add_rate($args);

            if ($this->get_option('collectpoint_activ') == "yes") {
                if (empty($obj_fan)) {
                    $obj_fan = $this->get_fan_obj();
                }

                //Gets the available collectpoint options in the selected city
                if (is_numeric($transport)) $this->get_collect_point_rate($obj_fan, $package, $transport, $valoare_cos);
            }
        }
    }

    public function get_collect_point_rate($obj_fan, $package, $transport_collectpoint, $valoare_cos = null)
    {
        $params['number'] = $this->get_option('numar_collectpoints') + 1;
        $params['radius'] = 10;
        $params['address'] = ucfirst(strtolower($package['destination']['city'])) . ', ' . ucfirst(strtolower($package['destination']['address']));

        $collect_points = $obj_fan->getCollectPoints($params);

        $tarif_fix_collectpoint = $this->get_option('tarif_collectpoints');
        if (!empty($tarif_fix_collectpoint))
            $transport_collectpoint = $tarif_fix_collectpoint;

        if (!count($collect_points)) return;

        $prag_gratis_collectpoints = $this->get_option('prag_gratis_collectpoints');
        if ($prag_gratis_collectpoints && $valoare_cos >= $prag_gratis_collectpoints) {
            $transport_collectpoint = 0;
        }

        $label = 'Fan Courier CollectPoint';
        if ($transport_collectpoint == 0) $label .= ": Gratis";

        $args = array(
            'id'    => 'safealternative_fan_collectpoint',
            'label' => $label,
            'cost'  => $transport_collectpoint,
            'taxes' => true
        );
        $args = apply_filters('safealternative_overwrite_fan_collect_point_shipping', $args);
        $this->add_rate($args);
    }

    function review_order_after_shipping()
    {
        $chosen_shipping_methods = WC()->session->get('chosen_shipping_methods');

        if (empty($chosen_shipping_methods) || !in_array('safealternative_fan_collectpoint', $chosen_shipping_methods)) {
            return;
        }

        $shipping_city = WC()->session->get('customer')['shipping_city'];
        $shipping_address = WC()->session->get('customer')['address'];

        $obj_fan = $this->get_fan_obj();

        $params['number'] = $this->get_option('numar_collectpoints') + 1;
        $params['radius'] = 10;
        $params['address'] = ucfirst(strtolower($shipping_city)) . ', ' . ucfirst(strtolower($shipping_address));

        $template_data['collectpoints'] = $obj_fan->getCollectPoints($params);
        wc_get_template('/templates/checkout-collectpoint-select.php', $template_data, dirname(__FILE__),  dirname(__FILE__));
    }
}
