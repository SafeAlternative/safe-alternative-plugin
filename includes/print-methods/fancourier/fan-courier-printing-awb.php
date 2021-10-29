<?php

include_once(plugin_dir_path(__FILE__) .'/courierFanSafe.class.php');
include_once(plugin_dir_path(__FILE__) .'/courierFan.class.php');

class FanGenereazaAWB
{
    static public $countiesList;

    public function __construct()
    {
        add_action('admin_menu', array(
            $this,
            'add_plugin_page_generate_fan'
        ));
        add_action('admin_menu', array(
            $this,
            'add_plugin_page_settings_fan'
        ));
        add_action('add_meta_boxes', array(
            $this,
            'add_meta_box_awb_fan'
        ));
        add_action('admin_init', array(
            $this,
            'add_register_setting_fan'
        ));
        
        add_filter('manage_edit-shop_order_columns', array(
            $this,
            'add_custom_columns_to_orders_table_fan'
        ), 10);
        add_action('manage_shop_order_posts_custom_column', array(
            $this,
            'get_custom_columns_values_fan'
        ), 6);  
        add_action( 'admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action( 'woocommerce_order_status_changed', array(
            $this,
            'autogenerate_fan_awb'
        ), 99, 3);       

        self::$countiesList = safealternative_get_counties_list();
    }

    public function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if( ($message = get_transient( 'fan_account_status' )) && $qv === "shop_order" ){
            ?>
            <div class="notice notice-warning">
                <p><?php _e( $message, 'safealternative-fancourier-woocommerce' ); ?></p>
            </div>
            <?php
        }
    }

    public function add_plugin_page_settings_fan()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'FanCourier - AWB',
            'FanCourier - AWB',
            'manage_woocommerce',
            'fan-plugin-setting', 
            array(
            $this,
            'fan_plugin_page'
        ));
    }

    public function add_register_setting_fan()
    {
        add_option('fan_clientID', '');
        add_option('fan_user', '');
        add_option('fan_password', '');
        add_option('fan_nr_colete', '1');
        add_option('fan_nr_plicuri', '0');
        add_option('fan_observatii', 'A se contacta telefonic.');
        add_option('fan_plata_transport', 'nu');
        add_option('fan_plata_ramburs', 'nu');
        add_option('fan_asigurare', 'nu');
        add_option('fan_deschidere', '');
        add_option('fan_sambata', '');
        add_option('fan_contact_exp', 'Nume contact exp');
        add_option('fan_descriere_continut', 'nu');
        add_option('fan_trimite_mail', 'nu');
        add_option('fan_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('fan_personal_data', '');
        add_option('fan_epod_opod', 'nu');
        add_option('fan_page_type', 'A4');
        add_option('fan_force_width', '');
        add_option('fan_force_height', '');
        add_option('fan_force_length', '');
        add_option('fan_force_weight', '');
        add_option('fan_auto_generate_awb', 'nu');
        add_option('fan_auto_mark_complete', 'nu');
        
        register_setting('fan-plugin-settings', 'fan_clientID');
        register_setting('fan-plugin-settings', 'fan_user');
        register_setting('fan-plugin-settings', 'fan_password');
        register_setting('fan-plugin-settings', 'fan_nr_colete');
        register_setting('fan-plugin-settings', 'fan_nr_plicuri');
        register_setting('fan-plugin-settings', 'fan_observatii');
        register_setting('fan-plugin-settings', 'fan_plata_transport');
        register_setting('fan-plugin-settings', 'fan_plata_ramburs');
        register_setting('fan-plugin-settings', 'fan_asigurare');
        register_setting('fan-plugin-settings', 'fan_deschidere');
        register_setting('fan-plugin-settings', 'fan_sambata');
        register_setting('fan-plugin-settings', 'fan_contact_exp');
        register_setting('fan-plugin-settings', 'fan_descriere_continut');
        register_setting('fan-plugin-settings', 'fan_trimite_mail');
        register_setting('fan-plugin-settings', 'fan_subiect_mail');
        register_setting('fan-plugin-settings', 'fan_personal_data'); 
        register_setting('fan-plugin-settings', 'fan_epod_opod'); 
        register_setting('fan-plugin-settings', 'fan_page_type'); 
        register_setting('fan-plugin-settings', 'fan_force_width'); 
        register_setting('fan-plugin-settings', 'fan_force_height'); 
        register_setting('fan-plugin-settings', 'fan_force_length'); 
        register_setting('fan-plugin-settings', 'fan_force_weight'); 
        register_setting('fan-plugin-settings', 'fan_auto_generate_awb'); 
        register_setting('fan-plugin-settings', 'fan_auto_mark_complete'); 
        
        include_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    public function fan_plugin_page() 
    {
        include_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    /**
     * Add options page
     */
    public function add_plugin_page_generate_fan()
    {
        add_submenu_page(
            null, 
            'Genereaza AWB Fan Curier', 
            'Genereaza AWB Fan Curier', 
            'manage_woocommerce', 
            'generate-awb-fan', 
            array( $this, 'create_admin_page'), 
            null
        );
    }
    
    /**
     * Options page callback
     */
    public function create_admin_page()
    {
        global $wpdb;
        $countiesList = self::$countiesList;

        $fan_usr = get_option('fan_user');
        if (empty($fan_usr)) {
            echo '<div class="wrap"><h1>Safe Alternative Fan Courier AWB</h2><br><h2>Plugin-ul Safe Alternative Fan Courier AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=fan-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $user          = rawurlencode(get_option('fan_user'));
        $password      = rawurlencode(get_option('fan_password'));
        $clientID      = rawurlencode(get_option('fan_clientID'));

        $contact_exp     = get_option('fan_contact_exp');
        $val_declarata   = get_option('fan_asigurare');
        $plata_transport = get_option('fan_plata_transport');
        $plata_ramburs   = get_option('fan_plata_ramburs');
        $numar_colete    = get_option('fan_nr_colete');
        $numar_plicuri   = get_option('fan_nr_plicuri');
        $deschidere      = get_option('fan_deschidere');
        $sambata         = get_option('fan_sambata');
        $observatii      = get_option('fan_observatii');
        $personal_data   = get_option('fan_personal_data');
        $fan_descriere_continut = get_option('fan_descriere_continut');
        $fan_epod_opod = get_option('fan_epod_opod');

        $force_width = get_option('fan_force_width');
        $force_height = get_option('fan_force_height');
        $force_length = get_option('fan_force_length');
        $force_weight = get_option('fan_force_weight');        
        
        $order = wc_get_order($_GET['order_id']);
        
        if (!$order) {
            die('Invalid order id');
        } //!$order

        $weight           = 0;
        $contents         = '';
        $packing          = '';
        
        $height = 0;
        $width  = 0;
        $length = 0;
        
        $heightList = array();
        $lengthList = array();
        
        $items = $order->get_items();
        foreach ($items as $i => $item) {
            
            $product  = $item->get_product();
            $desc_pro = $product->get_short_description();
            if (!$desc_pro) {
                $desc_pro = $product->get_name();
            } //!$desc_pro
            
            $desc_pro = str_replace(",", ";", $desc_pro);
            $desc_pro = str_replace("|", "-", $desc_pro);
            $desc_pro = str_replace("\\", "-", $desc_pro);
            $desc_pro = str_replace("/", "-", $desc_pro);
            
            $cod_pro = $product->get_sku();
            if (!$cod_pro) {
                $cod_pro = $item->get_product_id();
            } //!$cod_pro
            
            $name_pro = $product->get_name();
            $name_pro = str_replace(",", ";", $name_pro);
            $name_pro = str_replace("|", "-", $name_pro);
            $name_pro = str_replace("\\", "-", $name_pro);
            $name_pro = str_replace("/", "-", $name_pro);
            
            $val_dec_pro = $item->get_total();
            $val_dec_pro = str_replace(",", ".", $val_dec_pro);
            
            $packing .= strip_tags($name_pro . "/" . $desc_pro . "/" . $cod_pro . "/" . $item->get_quantity() . "/" . $val_dec_pro . "|");

            switch($fan_descriere_continut) {
                case 'nu': 
                    break;
                case 'name':
                    $contents .= ', ' . $item->get_quantity() . ' x ' . $item->get_name();
                    break;
                case 'sku':
                    $contents .= ', ' . $item->get_quantity() . ' x ' . $cod_pro;
                    break;
                case 'both':
                    $contents .= ', ' . $item->get_quantity() . ' x ' . $item->get_name() . '/' . $cod_pro;
                    break;
            }

            $_product = $item->get_product();
            $heightList = array();
            $lengthList = array();
            if ($_product && !$_product->is_virtual()) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
                
                $width += (int) $product->get_width() * $item->get_quantity();
                
                $height       = (int) $product->get_height();
                $heightList[] = $height;
                $length       = (int) $product->get_length();
                $lengthList[] = $length;
            } //!$_product->is_virtual()
            
        } //$items as $i => $item
        if (count($heightList))
        $height = max($heightList);
        if (count($lengthList))
        $length = max($lengthList);
        
        if ($height == 0) {
            $height = 10;
        } //$height == 0
        if ($width == 0) {
            $width = 10;
        } //$width == 0
        if ($length == 0) {
            $length = 10;
        } //$length == 0
        
        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        } //$weight_type == 'g'
        
        if ($weight <= 1 ) $weight = 1;
        $weight = round($weight);

        if($force_height) $height = $force_height;
        if($force_width) $width = $force_width;
        if($force_length) $length = $force_length;
        if($force_weight) $weight = $force_weight;            
        
        $contents = ltrim($contents, ', ');
        
        if ($plata_transport == 'expeditor') {
            $ramburs = $order->get_total();
        } //$plata_transport == 'expeditor'
        
        if ($plata_transport == 'destinatar') {
            $ramburs = number_format((float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
        } //$plata_transport == 'destinatar'
        
        // plata la curier
        if ($order->get_payment_method() == 'cod') { 
            if ($plata_transport == 'expeditor') {
                
                $ramburs = $order->get_total();
            } //$plata_transport == 'expeditor'
            
            if ($plata_transport == 'destinatar') {
                $ramburs = number_format((float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
            } //$plata_transport == 'destinatar'
            $tip_serv = 'Cont Colector';
        } //$order->get_payment_method() == 'cod'
        // plata cu card
        else {
            $ramburs  = 0;
            $tip_serv = 'Standard';
        }
        
        if ($deschidere == '')
            $deschidere_la_livrare = array(
                '' => 'Nu',
                'da' => 'Da'
            );
        if ($deschidere == 'da')
            $deschidere_la_livrare = array(
                'da' => 'Da',
                '' => 'Nu'
            );

        if ($sambata == '')
            $livrare_sambata = array(
                '' => 'Nu',
                'da' => 'Da'
            );
        if ($sambata == 'da')
            $livrare_sambata = array(
                'da' => 'Da',
                '' => 'Nu'
            );
        
        $packing = substr($packing, 0, -1);
        
        //$courier         = new SafealternativeFanClass();
        //$response        = $courier->callMethod("getServices/" . $api_user . "/" . $api_pass . "/" . $user . "/" . $password . "/" . $clientID, $json_parameters = '', 'POST');
        //$servicesListFan = json_decode($response['message']);

        if (!isset($_POST['generate_awb'])) { 

            $obs = $observatii;
            if ($order->get_customer_note() != '') {
                $obs = $order->get_customer_note();
            } //$order->get_customer_note() != ''

            // asigurare
            if ($val_declarata == 'da')
                $val_decl = $ramburs;
            if ($val_declarata == 'nu')
                $val_decl = '';

            $district = safealternative_get_counties_list($order->get_shipping_state());
            $city = $wpdb->get_var("SELECT fan_locality_name FROM courier_localities where locality_name='" . $order->get_shipping_city() . "' ") ?: $order->get_shipping_city();
            $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($district, $city);

            $awb_details = array(
                'tip_serviciu' => $tip_serv,
                'nr_plicuri' => $numar_plicuri,
                'nr_colete' => $numar_colete,
                'greutate' => ceil($weight),
                'plata_expeditie' => $plata_transport,
                'ramburs' => $ramburs,
                'plata_ramburs_la' => $plata_ramburs,
                'nume_destinatar' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                'companie' => $order->get_shipping_company(),
                'telefon' => $order->get_billing_phone(),
                'mail' => $order->get_billing_email(),
                'judet' => $district,
                'localitate' => $city,
                'strada' => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
                'adresa_collectpoint' => implode('', get_post_meta($order->get_id(), 'safealternative_fan_collectpoint')),
                'cod_postal' => $postcode,
                'val_decl' => $val_decl,
                'observatii' => $obs,
                'continut' => $contents,
                'deschidere_la_livrare' => $deschidere,
                'livrare_sambata' => $sambata,
                'packing' => $packing,
                'personal_data' => $personal_data,
                'pers_contact' => $contact_exp,
                'epod_opod' => $fan_epod_opod,
                'height' => $height,
                'width' => $width,
                'length' => $length,
            );
            
            $awb_details = apply_filters('safealternative_awb_details', $awb_details, 'FanCourier', $order);

            $_POST['awb'] = $awb_details;
        }


        $fan_obj = new CourierFan($user, $password, $clientID);
        
        $clientIds = $fan_obj->getClientIds();

        $servicesListFan = $fan_obj->getServices();

        include_once(plugin_dir_path(__FILE__) . '/templates/new-awb-page.php');
    }

    /**
     * Adds a box to the main column on the Post and Page edit screens.
     */
    public function add_meta_box_awb_fan()
    {
        global $pagenow;
        $screens = array();

        if(!in_array( $pagenow, array( 'post-new.php' ))){
            array_push($screens,'shop_order');
        }
        
        foreach ($screens as $screen) {
            add_meta_box('fanawb_sectionid', __('FanCourier - AWB', 'safealternative-plugin'), array(
                $this,
                'fanawb_meta_box_callback'
            ), $screen, 'side');
        } //$screens as $screen
    }
    
    /**
     * Prints the box content.
     *
     * @param WP_Post $post The object for the current post/page.
     */
    public function fanawb_meta_box_callback($post)
    {
        $awb = get_post_meta($post->ID, 'awb_fan', true);

        echo '<style>.fan_secondary_button{border-color:#f44336!important;color:#f44336!important}.fan_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';
        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://www.fancourier.ro/awb-tracking/?xawb='.$awb.'" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button fan_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-fan&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
        }
    }

    /////////////////////////////////////////////////////
    /////////CURIER FIELDS//////////////// //////////////
    /////////////////////////////////////////////////////
    public function add_custom_columns_to_orders_table_fan($columns)
    {
        $new_columns = $columns;
        
        if (!is_array($columns)) {
            $columns = array();
        }
        
        $new_columns['FAN_AWB'] = 'FAN Courier';
        
        return $new_columns;
    }
    
    ////////////////////////////////////////////////
    public function get_custom_columns_values_fan($column)
    {
        global $post;

        if ($column == 'FAN_AWB') {
            
            $awb                       = get_post_meta($post->ID, 'awb_fan', true);
            $status                    = get_post_meta($post->ID, 'awb_fan_status', true);
            $ordin_plata_ramburs       = get_post_meta($post->ID, 'ordin_plata_ramburs', true);
            $ordin_plata_ramburs_value = get_post_meta($post->ID, 'ordin_plata_ramburs_value', true);

            if (!empty($awb)) {
                
                $printing_link = plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '';
                echo '<a class="button tips downloadBtn" href="' . $printing_link . '" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">' . $awb . '</a></br></span>';
                echo '<div class="fanNoticeWrapper">';                

                if (!empty($status)) {
                    if ($status == 'Livrat')
                        echo '<div class="fanNotice"><span class="dashicons dashicons-yes"></span>Status: ' . $status . '</br></div>';
                    elseif ($status == 'Expeditie in livrare')
                        echo '<div class="fanNotice"><span class="dashicons dashicons dashicons-migrate"></span>Status: ' . $status . '</br></div>';
                    elseif ($status == 'Avizat')
                        echo '<div class="fanNotice"><span class="dashicons dashicons-images-alt"></span>Status: ' . $status . '</br></div>';
                    elseif ($status == 'Adresa incompleta')
                        echo '<div class="fanNotice"><span class="dashicons dashicons-image-rotate"></span>Status: ' . $status . '</br></div>';
                    else
                        echo '<div class="fanNotice"><span class="dashicons dashicons-info"></span>Status: ' . $status . '</br></div>';
                    
                    if ($ordin_plata_ramburs ?? null)
                        echo '<div class="fanNotice"><span class="dashicons dashicons-yes"></span>Ramburs: ' . $ordin_plata_ramburs . '</br></div>';
                    if ($ordin_plata_ramburs_value ?? null)
                        echo '<div class="fanNotice"><span class="dashicons dashicons-yes"></span>Valoare ramburs: ' . $ordin_plata_ramburs_value . '</br></div>';
                } //!empty($status)
                else
                    echo '<div class="fanNotice"><span class="dashicons dashicons-warning"></span>Status: Nescanat</div>';
                
                echo '</div>';
            } //!empty($awb)
            
            // nu avem awb
            else {
                $current_url_generate = esc_url(add_query_arg(array(
                    'generate_awb_fan' => absint($post->ID)
                )));

                echo '<p><a class="button tips generateBtn" data-tip="' . __('Genereaza AWB FanCourier', 'safealternative-plugin') . '"
                        href="' . $current_url_generate . '"><img src="' . plugin_dir_url(__FILE__) . 'assets/images/fancourier.png' . '" style="height: 29px;"/></a>
                      </p>';
            }
            
        } //$column == 'FAN_AWB'
    }
    
    ////////////////////////////////////////////////////////////////////////////
    // This method will generate a new awb /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////

    public static function generate_awb_fan()
    {
        global $wpdb;
        $order_id = get_query_var('generate_awb_fan', NULL);

        if ($order_id) {
            // don't make another awb with same order if refresh page  
            if (get_post_meta($order_id, 'awb_fan'))
                return;

            try {
                $fan_usr = get_option('fan_user');
                if (empty($fan_usr)) {
                    echo '<div class="notice notice-error"><h2>Plugin-ul SafeAlternative FanCourier AWB nu a fost configurat.</h2><p>Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=fan-plugin-setting').'"> aici</a> pentru a il configura.</p></div>';
                    return;
                }
                $token           = get_option('token');
                $username        = rawurlencode(get_option('fan_user'));
                $password        = rawurlencode(get_option('fan_password'));
                $clientID        = rawurlencode(get_option('fan_clientID'));
                $contact_exp     = get_option('fan_contact_exp');
                $val_declarata   = get_option('fan_asigurare');
                $plata_transport = get_option('fan_plata_transport');
                $plata_ramburs   = get_option('fan_plata_ramburs');
                $numar_colete    = get_option('fan_nr_colete');
                $numar_plicuri   = get_option('fan_nr_plicuri');
                $deschidere      = get_option('fan_deschidere');
                $sambata         = get_option('fan_sambata');
                $observatii      = get_option('fan_observatii');
                $trimite_mail    = get_option('fan_trimite_mail');
                $personal_data   = get_option('fan_personal_data');
                $fan_descriere_continut = get_option('fan_descriere_continut');
                $fan_epod_opod = get_option('fan_epod_opod');
                $force_width = get_option('fan_force_width');
                $force_height = get_option('fan_force_height');
                $force_length = get_option('fan_force_length');
                $force_weight = get_option('fan_force_weight');
                
                $order = wc_get_order($order_id);
                
                $items            = $order->get_items();
                $weight           = 0;
                $contents         = '';
                $packing          = '';
                
                $height = 0;
                $width  = 0;
                $length = 0;
                
                $heightList = array();
                $lengthList = array();
                
                foreach ($items as $i => $item) {
                    
                    $product = $item->get_product();
                    
                    $desc_pro = $product->get_short_description();
                    if (!$desc_pro)
                        $desc_pro = $product->get_name();
                    
                    $desc_pro = str_replace(",", ";", $desc_pro);
                    $desc_pro = str_replace("|", "-", $desc_pro);
                    $desc_pro = str_replace("\\", "-", $desc_pro);
                    $desc_pro = str_replace("/", "-", $desc_pro);
                    
                    $cod_pro = $product->get_sku();
                    if (!$cod_pro)
                        $cod_pro = $item->get_product_id();
                    
                    $name_pro = $product->get_name();
                    $name_pro = str_replace(",", ";", $name_pro);
                    $name_pro = str_replace("|", "-", $name_pro);
                    $name_pro = str_replace("\\", "-", $name_pro);
                    $name_pro = str_replace("/", "-", $name_pro);
                    
                    $val_dec_pro = $item->get_total();
                    $val_dec_pro = str_replace(",", ".", $val_dec_pro);
                    
                    $packing .= strip_tags($name_pro . "/" . $desc_pro . "/" . $cod_pro . "/" . $item->get_quantity() . "/" . $val_dec_pro . "|");
                    switch($fan_descriere_continut) {
                        case 'nu': 
                            break;
                        case 'name':
                            $contents .= ', ' . $item->get_quantity() . ' x ' . $item->get_name();
                            break;
                        case 'sku':
                            $contents .= ', ' . $item->get_quantity() . ' x ' . $cod_pro;
                            break;
                        case 'both':
                            $contents .= ', ' . $item->get_quantity() . ' x ' . $item->get_name() . '/' . $cod_pro;
                            break;
                    }
                    
                    $_product = $item->get_product();
                    if (!$_product->is_virtual()) {
                        $weight += (float) $_product->get_weight() * $item->get_quantity();
                        
                        $width += (int) $product->get_width() * $item->get_quantity();
                        
                        $height       = (int) $product->get_height();
                        $heightList[] = $height;
                        $length       = (int) $product->get_length();
                        $lengthList[] = $length;
                    } //!$_product->is_virtual()
                    
                } //$items as $i => $item
                
                $height = max($heightList);
                $length = max($lengthList);
                
                if ($height == 0) {
                    $height = 10;
                } //$height == 0
                if ($width == 0) {
                    $width = 10;
                } //$width == 0
                if ($length == 0) {
                    $length = 10;
                } //$length == 0
                
                $weight_type = get_option('woocommerce_weight_unit');
                if ($weight_type == 'g') {
                    $weight = $weight / 1000;
                } //$weight_type == 'g'
                        
                if ($weight <= 1 ) $weight = 1;
                $weight = round($weight);

                if($force_height) $height = $force_height;
                if($force_width) $width = $force_width;
                if($force_length) $length = $force_length;
                if($force_weight) $weight = $force_weight;
                
                $contents = ltrim($contents, ', ');
                
                if ($plata_transport == 'expeditor') {
                    $ramburs = $order->get_total();
                } //$plata_transport == 'expeditor'
                
                if ($plata_transport == 'destinatar') {
                    $ramburs = number_format((float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
                } //$plata_transport == 'destinatar'
                
                if ($order->get_payment_method() == 'cod') {
                    
                    if ($plata_transport == 'expeditor') {
                        $ramburs = $order->get_total();
                    } //$plata_transport == 'expeditor'
                    
                    if ($plata_transport == 'destinatar') {
                        $ramburs = number_format((float) $order->get_total() - $order->get_shipping_total() - $order->get_shipping_tax(), wc_get_price_decimals(), '.', '');
                    } //$plata_transport == 'destinatar'
                    $tip_serv = 'Cont Colector';
                } //$order->get_payment_method() == 'cod'
                else {
                    $ramburs  = 0;
                    $tip_serv = 'Standard';
                }
                
                if ($deschidere == '') {
                    $deschidere_la_livrare = '';
                } //$deschidere == ''
                if ($deschidere == 'da') {
                    $deschidere_la_livrare = 'A';
                } //$deschidere == 'da'

                if ($sambata == '') {
                    $livrare_sambata = '';
                } //$deschidere == ''
                if ($sambata == 'da') {
                    $livrare_sambata = 'S';
                } //$deschidere == 'da'

                $packing = substr($packing, 0, -1);
                $obs     = $observatii;
                if ($order->get_customer_note() != '') {
                    $obs = $order->get_customer_note();
                } //$order->get_customer_note() != ''

                // asigurare
                if ($val_declarata == 'da')
                    $val_decl = $ramburs;
                if ($val_declarata == 'nu')
                    $val_decl = '';

                $district = safealternative_get_counties_list($order->get_shipping_state());
                $city = $wpdb->get_var("SELECT fan_locality_name FROM courier_localities where locality_name='" . $order->get_shipping_city() . "' ") ?: $order->get_shipping_city();
                $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($district, $city);

                $awb_details = array(
                    'tip_serviciu' => $tip_serv,
                    'nr_plicuri' => $numar_plicuri,
                    'nr_colete' => $numar_colete,
                    'greutate' => ceil($weight),
                    'plata_expeditie' => $plata_transport,
                    'ramburs' => $ramburs,
                    'plata_ramburs_la' => $plata_ramburs,
                    'nume_destinatar' => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
                    'companie' => $order->get_shipping_company(),
                    'telefon' => $order->get_billing_phone(),
                    'mail' => $order->get_billing_email(),
                    'judet' => $district,
                    'localitate' => $city,
                    'strada' => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
                    'adresa_collectpoint' => implode('', get_post_meta($order->get_id(), 'safealternative_fan_collectpoint')),
                    'cod_postal' => $postcode,
                    'val_decl' => $val_decl,
                    'observatii' => $obs,
                    'continut' => $contents,
                    'deschidere_la_livrare' => $deschidere_la_livrare,
                    'livrare_sambata' => $livrare_sambata,
                    'packing' => $packing,
                    'personal_data' => $personal_data,
                    'pers_contact' => $contact_exp,
                    'height' => $height,
                    'width' => $width,
                    'length' => $length,
                    'epod_opod' => $fan_epod_opod
                );
                                
                $awb_details['domain'] = site_url();    
                $awb_details = apply_filters('safealternative_awb_details', $awb_details, 'FanCourier', $order);
                //$parameters      = $awb_details;
                //$json_parameters = json_encode($parameters);

                
                $awb_details['username'] = $username;
                $awb_details['client_id'] = $clientID;
                $awb_details['user_pass'] = $password;
                $awb_details['token'] = $token;
                
                $courier  = new CourierFanSafe();
                $response = $courier->callMethod("generateAwb", $awb_details, 'POST');

                if ($response['status'] == 200) {
                    $id = json_decode($response['message']);
                    if (is_numeric($id)) {
                        if ($trimite_mail == 'da') {
                            FanGenereazaAWB::send_mails($order_id, $id, $awb_details['mail']);
                        } //$trimite_mail == 'da'
                        update_post_meta($order_id, 'awb_fan', $id);
                        update_post_meta($order_id, 'awb_fan_client_id', $clientID);
                        update_post_meta($order_id, 'awb_fan_status_id', '0');
                        update_post_meta($order_id, 'awb_fan_status', 'AWB-ul a fost inregistrat de catre clientul expeditor.');

                        do_action( 'safealternative_awb_generated', 'FanCourier', $id, $order_id );

                        //$account_status_response = $courier->callMethod("newAccountStatus/" . $api_user . "/" . $api_pass . "/" . $user . "/" . $password . "/" . $clientID, '', 'POST');
                        //$account_status = json_decode($account_status_response['message']);

                        //if($account_status->show_message){
                        //    set_transient( 'fan_account_status', $account_status->message, MONTH_IN_SECONDS );
                        //} else {
                        //    delete_transient( 'fan_account_status' );
                        //}
                    } //is_numeric($id)
                    else {
                        echo "<br>Eroare la raspuns!<br>";
                        echo $response['message'];
                    }
                } //$response['status'] == 200
                
                else {
                    echo "<br>Va rugam sa verificati datele din comanda!<br>";
                    echo $response['message'];
                }
            }
            catch (Exception $e) {
                $e->getMessage();
            }
        } //$order_id
    } // end function

    ////////////////////////////////////////////////////
    /////////// send mail ////////////////////
    /////////////////////////////////////////////////// 
    public static function send_mails($idOrder, $awb, $receiver_email)
    {
        $sender_email    = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template  = get_option('fan_email_template');
        $headers         = array(
            'Content-Type: text/html; charset=UTF-8',
            'From: ' . get_bloginfo('name') . ' <' . $sender_email . '>'
        );

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail   = self::handle_email_template(get_option('fan_subiect_mail'), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_fan_email', $email_content, $data);
        $email_content = nl2br($email_content);

        try {
            if (!wp_mail($receiver_email, $subiect_mail, $email_content, $headers)) {
                set_transient('email_sent_error', 'Nu am putut trimite email-ul catre ' . $receiver_email, 5);
            } else {
                set_transient('email_sent_success', 'Email-ul s-a trimis catre ' . $receiver_email, 5);
            }
        } catch (Exception $e) { }
    }

    public static function handle_email_template($template, $data)
    {
        $tabel_produse = '<table><tr><th width="400" align="center">Produs</th><th width="50" align="center">Cantitate</th><th width="200" align="center">Pret</th></tr>';
        foreach ($data['produse'] as $item) {
            $tabel_produse .= '<tr>';
            $tabel_produse .= '<td align="center">' . $item->get_name() . '</td>';
            $tabel_produse .= '<td align="center">' . $item->get_quantity() . '</td>';
            $tabel_produse .= '<td align="center">' . wc_price($item->get_total()+$item->get_total_tax()) . '</td>';
            $tabel_produse .= '</tr>';
        }
        $tabel_produse .= '</table>';

        $template = str_replace('[nr_comanda]', $data['nr_comanda'], $template);
        $template = str_replace('[data_comanda]', $data['data_comanda'], $template);
        $template = str_replace('[nr_awb]', $data['awb'], $template);
        $template = str_replace('[tabel_produse]', $tabel_produse, $template);

        return $template;
    }

    public function autogenerate_fan_awb($order_id, $old_status, $new_status){
        if(get_option('fan_auto_generate_awb') != "da") return;
        if($new_status !== 'processing') return; 
        
        set_query_var('generate_awb_fan', $order_id);
        FanGenereazaAWB::generate_awb_fan();
    }
}


include_once(plugin_dir_path(__FILE__) .'/cron.php');

////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action('woocommerce_order_details_after_order_table_items', function ($order) {
    $order_id = $order->get_order_number();
    $awb = get_post_meta($order_id, 'awb_fan', true);
    if ($awb) echo 'Nota de transport (AWB) are numarul: ' . $awb . ' si poate fi urmarita aici: <a href="https://www.fancourier.ro/awb-tracking/?xawb=' . $awb . '" target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter('query_vars', function ($vars) {
    $vars[] = "generate_awb_fan";
    return $vars;
});

add_action('admin_notices', array(
    'FanGenereazaAWB',
    'generate_awb_fan'
), 1);

add_action('admin_head', function () {
    wp_enqueue_style( 'custom_admin_css_fan', plugin_dir_url(__FILE__).'assets/css/custom.css' );
}, 1);

//Bulk
add_action( 'admin_footer', 'add_bulk_action_fan');
function add_bulk_action_fan() {
	global $post_type;

	if ( 'shop_order' == $post_type ) {	
        wp_enqueue_script( 'bulk_admin_js_fan', plugin_dir_url(__FILE__).'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');    
	}
}
