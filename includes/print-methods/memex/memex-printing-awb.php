<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

class MemexAWB
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_menu', array($this, 'add_plugin_page_settings'));
        add_action('add_meta_boxes', array($this, 'memex_add_meta_box'));
        add_action('admin_init', array($this, 'add_register_setting'));

        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action('woocommerce_order_status_changed', array(
            $this,
            'autogenerate_memex_awb'
        ), 99, 3);

        add_filter('manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table'), 11);
        add_action('manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values'), 2);
    }

    function add_plugin_page_settings()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Memex - AWB',
            'Memex - AWB',
            'manage_woocommerce',
            'memex-plugin-setting',
            array(
                $this,
                'memex_plugin_page'
            )
        );
    }

    function add_register_setting()
    {
        add_option('memex_username', '');
        add_option('memex_password', '');
        add_option('memex_parcel_content', 'nu');
        add_option('memex_service_id', '38');
        add_option('memex_name', '');
        add_option('memex_address', '');
        add_option('memex_city', '');
        add_option('memex_postcode', '');
        add_option('memex_countrycode', 'RO');
        add_option('memex_person', '');
        add_option('memex_contact', '');
        add_option('memex_email', '');
        add_option('memex_is_private_person', 'false');
        add_option('memex_insurance', 'Nu');
        add_option('memex_additional_sms', 'Nu');
        add_option('memex_package_count', '1');
        add_option('memex_envelope_count', '0');
        add_option('memex_parcel_length', '');
        add_option('memex_parcel_height', '');
        add_option('memex_parcel_width', '');
        add_option('memex_parcel_weight', '');
        add_option('memex_envelope_length', '');
        add_option('memex_envelope_height', '');
        add_option('memex_envelope_width', '');
        add_option('memex_envelope_weight', '');
        add_option('memex_parcel_note', '');
        add_option('memex_trimite_mail', 'nu');
        add_option('memex_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('memex_label_format', 'PDF');
        add_option('memex_auto_generate_awb', 'nu');
        add_option('memex_auto_mark_complete', 'nu');
        add_option('memex_call_pickup', '');
        add_option('memex_pickup_date', '');
        add_option('memex_pickup_time', '');
        add_option('memex_max_pickup_date', '');
        add_option('memex_max_pickup_time', '');

        register_setting('memex-plugin-settings', 'memex_username');
        register_setting('memex-plugin-settings', 'memex_password');
        register_setting('memex-plugin-settings', 'memex_parcel_content');
        register_setting('memex-plugin-settings', 'memex_service_id');
        register_setting('memex-plugin-settings', 'memex_name');
        register_setting('memex-plugin-settings', 'memex_address');
        register_setting('memex-plugin-settings', 'memex_city');
        register_setting('memex-plugin-settings', 'memex_postcode');
        register_setting('memex-plugin-settings', 'memex_countrycode');
        register_setting('memex-plugin-settings', 'memex_person');
        register_setting('memex-plugin-settings', 'memex_email');
        register_setting('memex-plugin-settings', 'memex_contact');
        register_setting('memex-plugin-settings', 'memex_is_private_person');
        register_setting('memex-plugin-settings', 'memex_insurance');
        register_setting('memex-plugin-settings', 'memex_additional_sms');
        register_setting('memex-plugin-settings', 'memex_package_count');
        register_setting('memex-plugin-settings', 'memex_envelope_count');
        register_setting('memex-plugin-settings', 'memex_parcel_length');
        register_setting('memex-plugin-settings', 'memex_parcel_height');
        register_setting('memex-plugin-settings', 'memex_parcel_width');
        register_setting('memex-plugin-settings', 'memex_parcel_weight');
        register_setting('memex-plugin-settings', 'memex_envelope_length');
        register_setting('memex-plugin-settings', 'memex_envelope_height');
        register_setting('memex-plugin-settings', 'memex_envelope_width');
        register_setting('memex-plugin-settings', 'memex_envelope_weight');
        register_setting('memex-plugin-settings', 'memex_parcel_note');
        register_setting('memex-plugin-settings', 'memex_is_sat_delivery');
        register_setting('memex-plugin-settings', 'memex_is_fragile');
        register_setting('memex-plugin-settings', 'memex_trimite_mail');
        register_setting('memex-plugin-settings', 'memex_subiect_mail');
        register_setting('memex-plugin-settings', 'memex_label_format');
        register_setting('memex-plugin-settings', 'memex_auto_generate_awb');
        register_setting('memex-plugin-settings', 'memex_auto_mark_complete');
        register_setting('memex-plugin-settings', 'memex_call_pickup');
        register_setting('memex-plugin-settings', 'memex_pickup_date');
        register_setting('memex-plugin-settings', 'memex_pickup_time');
        register_setting('memex-plugin-settings', 'memex_max_pickup_date');
        register_setting('memex-plugin-settings', 'memex_max_pickup_time');
        
        require_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message_status = get_transient('memex_account_status')) && $qv === "shop_order") {
        ?>
            <div class="notice notice-warning">
                <p><?php _e($message_status, 'safealternative-memex-woocommerce'); ?></p>
            </div>
        <?php
        }

        if (($message_settings = get_transient('memex_account_settings'))) {
        ?>
            <div class="notice notice-warning">
                <p><?php _e($message_settings, 'safealternative-memex-woocommerce'); ?></p>
            </div>
        <?php
            delete_transient('memex_account_settings');
        }
    }

    function memex_plugin_page()
    {
        require_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            null,
            'Genereaza AWB Memex',
            'Genereaza AWB Memex',
            'manage_woocommerce',
            'generate-awb-memex',
            array($this, 'create_admin_page'),
            null
        );
    }

    public function create_admin_page()
    {
        if (!isset($_GET['order_id'])) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $awb_already_generated = get_post_meta($_GET['order_id'], 'awb_memex', 1);
        if ($awb_already_generated) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $order = wc_get_order($_GET['order_id']);
        $items = $order->get_items();

        $memex_parcel_content = get_option('memex_parcel_content');
        $package_count = (int)get_option('memex_package_count');
        $envelope_count = (int)get_option('memex_envelope_count');
        $asigurare = get_option('memex_insurance');
        $sms = get_option('memex_additional_sms');
        $length_env = get_option('memex_envelope_length');
        $height_env = get_option('memex_envelope_height');
        $width_env = get_option('memex_envelope_width');
        $weight_env = get_option('memex_envelope_weight');

        $length = 0;
        $height = 0;
        $width  = 0;
        $weight = 0;
        
        $heightList = array();
        $lengthList = array();
        $contents = '';

        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight      += (float) $_product->get_weight() * $item->get_quantity();
                $width       += (int) $_product->get_width() * $item->get_quantity();
                $height       = (int) $_product->get_height();
                $heightList[] = $height;
                $length       = (int) $_product->get_length();
                $lengthList[] = $length;
            }
            $cod_pro = $_product->get_sku();
            if (!$cod_pro) {
                $cod_pro = $item->get_product_id();
            } 
            switch($memex_parcel_content) {
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
        }
        $contents = ltrim($contents, ', ');

        $height = max($heightList);
        $length = max($lengthList);

        if ($height == 0) {
            $height = get_option('memex_parcel_height') ?: 10;
        } //$height == 0
        if ($width == 0) {
            $width  = get_option('memex_parcel_width') ?: 10;
        } //$width == 0
        if ($length == 0) {
            $length = get_option('memex_parcel_length') ?: 10;
        } //$length == 0
        
        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        } //$weight_type == 'g'
        
        if ($weight <= 1 ) {
            $weight = get_option('memex_parcel_weight') ?: 1;
        }
        $weight = round($weight);

        if (empty(get_option('memex_username'))) {
            echo '<div class="wrap"><h1>SafeAlternative Memex AWB</h2><br><h2>Plugin-ul SafeAlternative Memex AWB nu a fost configurat.</h2> Va rugam dati click <a href="' . safealternative_redirect_url('admin.php?page=memex-plugin-setting') . '"> aici</a> pentru a il configura.</div>';
            exit;
        }
        
        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);
 
        $colete_parcel = array();
        if (!empty($package_count)) {
            $colete_parcel[]= array(
                "Parcel" => array(
                    "Type" => 'Package',
                    "Weight" => (string) round($weight),
                    "D" => $length,
                    "W" => $height,
                    "S" => $width,
                    "IsNST" => 'true'
                )
            );
        }
            
        $plicuri_parcel = array();
        if (!empty($envelope_count)) {
            $plicuri_parcel[]= array(
                "Parcel" => array(
                    "Type" => 'Envelope',
                    "Weight" => (string) $weight_env,
                    "D" => $length_env,
                    "W" => $height_env,
                    "S" => $width_env,
                    "IsNST" => 'true'
                )
            );
        }

        $parcels = array_merge_recursive($colete_parcel,$plicuri_parcel);
        $cod_amount = $order->get_payment_method() == 'cod' ? $order->get_total() : 0;

        if($asigurare == 'Da') {
            $val_decl = $cod_amount;
        } else {
            $val_decl = 0;
        }

        $awb_info = [
            "shipmentRequest" => array(
                "ServiceId" => get_option('memex_service_id'),
                "ShipFrom" => array(
                    "PointId" => '',
                    "Name" => get_option('memex_name'),
                    "Address" => get_option('memex_address'),
                    "City" => get_option('memex_city'),
                    "PostCode" => get_option('memex_postcode'),
                    "CountryCode" => get_option('memex_countrycode'),
                    "Person" => get_option('memex_person'),
                    "Contact" => get_option('memex_contact'),
                    "Email" => get_option('memex_email'),
                    "IsPrivatePerson" => get_option('memex_is_private_person')
                ),
                "ShipTo" => array(
                    "PointId" => '',
                    "Name" => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
                    "Address" => $order->get_shipping_address_1().' '.$order->get_shipping_address_2(),
                    "City" => $order->get_shipping_city(),
                    "PostCode" => $postcode,
                    "CountryCode" => "RO",
                    "Person" => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
                    "Contact" => $order->get_billing_phone(),
                    "Email" => $order->get_billing_email(),
                    "IsPrivatePerson" => empty($order->get_shipping_company()) ? 'true' : 'false'
                ),
                "Parcels" => $parcels,
                "COD" => array(
                    "Amount" => $cod_amount,
                ),
                "InsuranceAmount" => $val_decl,
                "MPK" => "",
                "ContentDescription" => $contents,
                "RebateCoupon" => "",
                "LabelFormat" => get_option('memex_label_format')
            ),
            "Parcels" => (string) $package_count,
            "Envelopes" => (string) $envelope_count,
            "additional_sms" => $sms
        ];

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Memex', $order);

        $_POST['awb'] = $awb_info;

        require_once(plugin_dir_path(__FILE__) . '/templates/generate-awb-page.php');
    }

    function memex_add_meta_box()
    {

        $screens = array('shop_order');

        foreach ($screens as $screen) {
            add_meta_box(
                'memex_sectionid',
                __('Memex - AWB', 'safealternative_memex'),
                array($this, 'memex_meta_box_callback'),
                $screen,
                'side'
            );
        }
    }

    function memex_meta_box_callback($post)
    {
        $awb = get_post_meta($post->ID, 'awb_memex', true);

        echo '<style>.memex_secondary_button{border-color:#f44336!important;color:#f44336!important}.memex_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 100%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button memex_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-memex&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
        }
    }

    /////////////////////////////////////////////////////////
    /////// ADD T&T COLUMNS  ////////////////////////////////
    /////////////////////////////////////////////////////////

    /////////////////////////////////////////////////////
    function add_custom_columns_to_orders_table($columns)
    {
        $new_columns = $columns;

        if (!is_array($columns)) {
            $columns = array();
        }

        $new_columns['Memex_AWB'] = 'Memex';

        return $new_columns;
    }

    ////////////////////////////////////////////////
    function get_custom_columns_values($column)
    {
        global $post;

        if ($column == 'Memex_AWB') {
            $awb = get_post_meta($post->ID, 'awb_memex', true);
            $status = get_post_meta($post->ID, 'awb_memex_status', true);

            // avem awb 
            if (!empty($awb)) {
                $printing_link = plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '';
                echo '<a class="button tips downloadBtn" href="' . $printing_link . '" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">' . $awb . '</a></br>';
                echo '<div class="memexNoticeWrapper">';
                echo '<div class="memexNotice"><span class="dashicons dashicons-warning"></span>Status: ' . $status . '</div>';
                echo '</div>';
            }
            // nu avem awb
            else {
                if((int)get_option('memex_package_count')+(int)get_option('memex_envelope_count') <= 1) {
                    $current_url_generate = esc_url(add_query_arg(array(
                        'generate_awb_memex' => absint($post->ID)
                    )));
                } else {
                    $current_url_generate = '/wp-admin/admin.php?page=generate-awb-memex&order_id='.$post->ID;
                }
                
                echo '<p><a class="button generateBtn tips" data-tip="' . __('Genereaza AWB Memex', 'safealternative-plugin') . '"
                        href="' . $current_url_generate . '"><img src="' . plugin_dir_url(__FILE__) . 'assets/images/memex.svg' . '" style="height: 20px;"/></a>
                    </p>';
            }
        }
    }

    // This method will generate a new awb
    static function generate_awb_memex()
    {
        $order_id = get_query_var('generate_awb_memex', NULL);
        if (empty($order_id)) {
            return null;
        }

        $awb_already_generated = get_post_meta($order_id, 'awb_memex', true);
        if ($awb_already_generated) {
            return null;
        }

        $trimite_mail = get_option('memex_trimite_mail');
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        $memex_parcel_content = get_option('memex_parcel_content');
        $package_count = (int)get_option('memex_package_count');
        $envelope_count = (int)get_option('memex_envelope_count');
        $asigurare = get_option('memex_insurance');
        $sms = get_option('memex_additional_sms');
        $length_env = get_option('memex_envelope_length');
        $height_env = get_option('memex_envelope_height');
        $width_env = get_option('memex_envelope_width');
        $weight_env = get_option('memex_envelope_weight');

        $length = 0;
        $height = 0;
        $width  = 0;
        $weight = 0;
        
        $heightList = array();
        $lengthList = array();
        $contents = '';

        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight      += (float) $_product->get_weight() * $item->get_quantity();
                $width       += (int) $_product->get_width() * $item->get_quantity();
                $height       = (int) $_product->get_height();
                $heightList[] = $height;
                $length       = (int) $_product->get_length();
                $lengthList[] = $length;
            }
            $cod_pro = $_product->get_sku();
            if (!$cod_pro) {
                $cod_pro = $item->get_product_id();
            } 
            switch($memex_parcel_content) {
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
        }
        $contents = ltrim($contents, ', ');

        $height = max($heightList);
        $length = max($lengthList);

        if ($height == 0) {
            $height = get_option('memex_parcel_height') ?: 10;
        } //$height == 0
        if ($width == 0) {
            $width  = get_option('memex_parcel_width') ?: 10;
        } //$width == 0
        if ($length == 0) {
            $length = get_option('memex_parcel_length') ?: 10;
        } //$length == 0
        
        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        } //$weight_type == 'g'
        
        if ($weight <= 1 ) {
            $weight = get_option('memex_parcel_weight') ?: 1;
        }
        $weight = round($weight);

        if (empty(get_option('memex_username'))) {
            echo '<div class="wrap"><h1>SafeAlternative Memex AWB</h2><br><h2>Plugin-ul SafeAlternative Memex AWB nu a fost configurat.</h2> Va rugam dati click <a href="' . safealternative_redirect_url('admin.php?page=memex-plugin-setting') . '"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $colete_parcel = array();
        if (!empty($package_count)) {
            $colete_parcel[]= array(
                "Parcel" => array(
                    "Type" => 'Package',
                    "Weight" => (string) round($weight),
                    "D" => $length,
                    "W" => $height,
                    "S" => $width,
                    "IsNST" => 'true'
                )
            );
        }
            
        $plicuri_parcel = array();
        if (!empty($envelope_count)) {
            $plicuri_parcel[]= array(
                "Parcel" => array(
                    "Type" => 'Envelope',
                    "Weight" => (string) round($weight_env),
                    "D" => $length_env,
                    "W" => $height_env,
                    "S" => $width_env,
                    "IsNST" => 'true'
                )
            );
        }

        $parcels = array_merge_recursive($colete_parcel,$plicuri_parcel);
        $cod_amount = $order->get_payment_method() == 'cod' ? $order->get_total() : 0;

        if($asigurare == 'Da') {
            $val_decl = $cod_amount;
        } else {
            $val_decl = 0;
        }

        $awb_info = [
            "shipmentRequest" => array(
                "ServiceId" => get_option('memex_service_id'),
                "ShipFrom" => array(
                    "PointId" => '',
                    "Name" => get_option('memex_name'),
                    "Address" => get_option('memex_address'),
                    "City" => get_option('memex_city'),
                    "PostCode" => get_option('memex_postcode'),
                    "CountryCode" => get_option('memex_countrycode'),
                    "Person" => get_option('memex_person'),
                    "Contact" => get_option('memex_contact'),
                    "Email" => get_option('memex_email'),
                    "IsPrivatePerson" => get_option('memex_is_private_person')
                ),
                "ShipTo" => array(
                    "PointId" => '',
                    "Name" => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
                    "Address" => $order->get_shipping_address_1().' '.$order->get_shipping_address_2(),
                    "City" => $order->get_shipping_city(),
                    "PostCode" => $postcode,
                    "CountryCode" => "RO",
                    "Person" => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
                    "Contact" => $order->get_billing_phone(),
                    "Email" => $order->get_billing_email(),
                    "IsPrivatePerson" => empty($order->get_shipping_company()) ? 'true' : 'false'
                ),
                "Parcels" => $parcels,
                "COD" => array(
                    "Amount" => $cod_amount,
                ),
                "InsuranceAmount" => (string) $val_decl,
                "MPK" => "",
                "ContentDescription" => $contents,
                "RebateCoupon" => "",
                "LabelFormat" => get_option('memex_label_format')
            ),
            "Parcels" => (string) $package_count,
            "Envelopes" => (string) $envelope_count,
            "additional_sms" => $sms
        ];

        if($awb_info['additional_sms'] == 'Da') {
            $awb_info["shipmentRequest"]["AdditionalServices"]["AdditionalService"]["Code"] = 'SSMS';
        }

        //daca ambele coduri postale se afla in lista localitatilor pentru serviciul loco standard (ServiceID=121), atunci acesta este selectat automat
        $localities = array("077106","077040","077085","077041","077086","077145","077191","077160","077042","077190","077010");

        if((in_array($awb_info['shipmentRequest']['ShipFrom']['PostCode'], $localities) || strtolower($awb_info['shipmentRequest']['ShipFrom']['City']) == 'bucuresti') && (in_array($awb_info['shipmentRequest']['ShipTo']['PostCode'], $localities) || strtolower($awb_info['shipmentRequest']['ShipTo']['City']) == 'bucuresti')){
            $awb_info['shipmentRequest']['ServiceId'] = '121';
        } 
        

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Memex', $order);
        $courier = new SafealternativeMemexClass();
        $result = $courier->callMethod("generateAwb", $awb_info, 'POST');

        if ($result['status'] == "200") {
            $message = json_decode($result['message'], true);

            if (!empty($message->error)) {
                set_transient('memex_account_settings', $message->error, MONTH_IN_SECONDS);
            } else {
                $awb = $message['awb'];

                if ($trimite_mail == 'da') {
                    MemexAWB::send_mails($order_id, $awb, $awb_info['shipmentRequest']['ShipTo']['Email']);
                }

                foreach($parcels as $key => $parcel){
                    $parcels = $parcel;
                }

                update_post_meta($order_id, 'awb_memex', $awb);
                update_post_meta($order_id, 'awb_memex_status', 'Inregistrat');
                update_post_meta($order_id, 'memex_parcels', json_encode($parcels));
                update_post_meta($order_id, 'memex_awb_service_id', get_option('memex_service_id'));
                update_post_meta($order_id, 'memex_awb_generated_date', date('Y-m-d'));
                update_post_meta($order_id, 'memex_ship_from', json_encode($awb_info['shipmentRequest']['ShipFrom']));

                do_action('safealternative_awb_generated', 'Memex', $awb);

                $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');

                $account_status = json_decode($account_status_response['message']);
                if ($account_status->show_message) {
                    set_transient('memex_account_status', $account_status->message, MONTH_IN_SECONDS);
                } else {
                    delete_transient('memex_account_status');
                }
            }
        }
    } // end function


    static public function send_mails($idOrder, $awb, $receiver_email)
    {
        $sender_email    = get_option('memex_email') ?: get_bloginfo('admin_email');
        $email_template  = get_option('memex_email_template');

        $headers         = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo('name') . ' <' . $sender_email . '>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option('memex_subiect_mail'), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_memex_email', $email_content, $data);
        $email_content = nl2br($email_content);

        try {
            if (!wp_mail($receiver_email, $subiect_mail, $email_content, $headers)) {
                set_transient('email_sent_error', 'Nu am putut trimite email-ul catre ' . $receiver_email, 5);
            } else {
                set_transient('email_sent_success', 'Email-ul s-a trimis catre ' . $receiver_email, 5);
            }
        } catch (Exception $e) {}
    }

    static function handle_email_template($template, $data)
    {
        $tabel_produse = '<table><tr><th width="400" align="center">Produs</th><th width="50" align="center">Cantitate</th><th width="200" align="center">Pret</th></tr>';
        foreach ($data['produse'] as $item) {
            $tabel_produse .= '<tr>';
            $tabel_produse .= '<td align="center">' . $item->get_name() . '</td>';
            $tabel_produse .= '<td align="center">' . $item->get_quantity() . '</td>';
            $tabel_produse .= '<td align="center">' . wc_price($item->get_total() + $item->get_total_tax()) . '</td>';
            $tabel_produse .= '</tr>';
        }
        $tabel_produse .= '</table>';

        $template = str_replace('[nr_comanda]', $data['nr_comanda'], $template);
        $template = str_replace('[data_comanda]', $data['data_comanda'], $template);
        $template = str_replace('[nr_awb]', $data['awb'], $template);
        $template = str_replace('[tabel_produse]', $tabel_produse, $template);

        return $template;
    }

    public function autogenerate_memex_awb($order_id, $old_status, $new_status)
    {
        if (get_option('memex_auto_generate_awb') != "da") return;
        if ($new_status !== 'processing') return;

        set_query_var('generate_awb_memex', $order_id);
        MemexAWB::generate_awb_memex();
    }
}
// end class

require_once(plugin_dir_path(__FILE__) . 'cron.php');

//Bulk generate
add_action('admin_footer', 'add_bulk_action_memex');
function add_bulk_action_memex()
{
    global $post_type;

    if ('shop_order' == $post_type) {
        wp_enqueue_script('bulk_admin_js_memex', plugin_dir_url(__FILE__) . 'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');
    }
}


////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action('woocommerce_order_details_after_order_table_items', function ($order) {
    $order_id = $order->get_order_number();
    $awb = get_post_meta($order_id, 'awb_memex', true);
    if ($awb) echo 'Nota de transport (AWB) are numarul: ' . $awb . ' si poate fi urmarita aici: <a href="https://memex.ro/urmareste-livrarea" target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter('query_vars', function ($vars) {
    $vars[] = "generate_awb_memex";
    return $vars;
});

add_action('admin_notices', array('MemexAWB', 'generate_awb_memex'), true);

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_memex', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);

add_action('init', function () {
    add_filter('pre_update_option_memex_username', function ($new_val, $old_val) {
        if ($old_val != $new_val) {
            delete_transient('memex_sender_list');
        }
        return $new_val;
    }, 10, 2);

    add_filter( 'pre_update_option_memex_pickup_time', function($new_val, $old_val) {
        if(esc_attr(get_option('memex_call_pickup')) == '0') return;
        date_default_timezone_set('Europe/Bucharest');

        if (!wp_next_scheduled('safealternative_memex_call_pickup')) {
            wp_schedule_event(strtotime(get_option('memex_pickup_time')), 'safealternative_memex_daily_pickup', 'safealternative_memex_call_pickup');
        }
        
        if (wp_next_scheduled('safealternative_memex_call_pickup') && $old_val != $new_val) {
            wp_unschedule_event(wp_next_scheduled('safealternative_memex_call_pickup'), 'safealternative_memex_call_pickup');
            wp_schedule_event(strtotime($new_val), 'safealternative_memex_daily_pickup', 'safealternative_memex_call_pickup');
        }
        
        return $new_val;
    }, 10, 2 );
});
