<?php

$dir = plugin_dir_path(__FILE__);

include_once($dir.'courierCargusSafe.class.php');
include_once($dir.'courierCargus.class.php');

class CargusAWB {

    private $urgent;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page_settings' ) );
        add_action( 'add_meta_boxes', array( $this, 'cargusawb_add_meta_box' ) );
        add_action( 'admin_init', array( $this, 'add_register_setting' ));
        
        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action( 'woocommerce_order_status_changed', array(
            $this,
            'autogenerate_urgent_awb'
        ), 99, 3);    
    
        add_filter ( 'manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table') , 11 );
        add_action ( 'manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values') , 2 );	
                
    	$url = get_option ( 'uc_url' );
    	$key = get_option ( 'uc_key' );
    	$this->urgent = new CourierCargus( $url, $key );
    }

    function add_plugin_page_settings(){
        add_submenu_page(
            'safealternative-menu-content',
            'Cargus - AWB',
            'Cargus - AWB',
            'manage_woocommerce',
            'urgent-cargus-setting',
            array(
                $this,
                'urgent_cargus_plugin_page'
            )
        );     
    }

    function add_register_setting() {
        add_option( 'uc_url', 'https://urgentcargus.azure-api.net/api');
        add_option( 'uc_key', 'c76c9992055e4e419ff7fa953c3e4569');
        add_option( 'uc_username', '');
        add_option( 'uc_password', '');
        add_option( 'uc_token', '');
        add_option( 'uc_punct_ridicare', '0');    
        add_option( 'uc_price_table_id', '0');    
        add_option( 'uc_nr_colete', '1');
        add_option( 'uc_nr_plicuri', '0');
        add_option( 'uc_observatii', '');
        add_option( 'uc_serie_client', '');
        add_option( 'uc_plata_transport', '1');
        add_option( 'uc_plata_ramburs', '1');
        add_option( 'uc_asigurare', '0');
        add_option( 'uc_deschidere', '0');
        add_option( 'uc_matinal', '0');
        add_option( 'uc_sambata', '0');
        add_option( 'uc_tip_serviciu', '34');
        add_option( 'uc_descrie_continut', '0');
        add_option( 'uc_trimite_mail', '0');
        add_option( 'uc_print_format', '0');
        add_option( 'uc_print_once', '0');
        add_option( 'uc_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option( 'uc_auto_generate_awb', 'nu');
        add_option( 'uc_auto_mark_complete', 'nu');
        add_option( 'uc_force_width', '');
        add_option( 'uc_force_height', '');
        add_option( 'uc_force_length', '');
        add_option( 'uc_force_weight', '');

        register_setting( 'urgent-cargus-plugin-settings', 'uc_url' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_key' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_username' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_password' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_token' );        
        register_setting( 'urgent-cargus-plugin-settings', 'uc_punct_ridicare' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_price_table_id' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_nr_colete' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_nr_plicuri' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_observatii' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_serie_client');
        register_setting( 'urgent-cargus-plugin-settings', 'uc_plata_transport' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_plata_ramburs' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_asigurare' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_deschidere' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_matinal' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_sambata' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_tip_serviciu' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_descrie_continut' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_print_format' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_print_once' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_trimite_mail' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_subiect_mail' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_auto_generate_awb' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_auto_mark_complete' );
        register_setting( 'urgent-cargus-plugin-settings', 'uc_force_width'); 
        register_setting( 'urgent-cargus-plugin-settings', 'uc_force_height'); 
        register_setting( 'urgent-cargus-plugin-settings', 'uc_force_length'); 
        register_setting( 'urgent-cargus-plugin-settings', 'uc_force_weight'); 

        include_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message_status = get_transient('urgent_account_status')) && $qv === "shop_order") {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_status, 'safealternative-urgentcargus-woocommerce'); ?></p>
            </div>
            <?php
        }

        if (($message_settings = get_transient('urgent_account_settings'))) {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_settings, 'safealternative-urgentcargus-woocommerce'); ?></p>
            </div>
            <?php
            delete_transient('urgent_account_settings');
        }    
    }

    function urgent_cargus_plugin_page() 
    {
        include_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            null,
            'Genereaza AWB UrgentCargus',
            'Genereaza AWB UrgentCargus',
            'manage_woocommerce',
            'generate-awb-urgent-cargus',
            array($this, 'create_admin_page'),
            null
        );        
    }

    public function create_admin_page()
    {
        global $wpdb;
        
        if (!isset($_GET['order_id'])) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }
        
        $awb_already_generated = get_post_meta($_GET['order_id'], 'awb_urgent_cargus', 1);
        if($awb_already_generated) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $descrie_continut = get_option ( 'uc_descrie_continut' );
        $serie_client = get_option ( 'uc_serie_client' );
        $order = wc_get_order($_GET['order_id']);
        $items = $order->get_items();
        $weight = 0;
        $height = 0;
        $width  = 0;
        $length = 0;
        $contents = '';
        
        $heightList = array();
        $lengthList = array();
        
        foreach ( $items as $i => $item ) {
            if($descrie_continut) {
                $contents .= ', '.$item->get_quantity().' x '.str_replace('"', '', $item->get_name());
            }
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();

                $width += (int) $_product->get_width() * $item->get_quantity();
                
                $height       = (int) $_product->get_height();
                $heightList[] = $height;
                $length       = (int) $_product->get_length();
                $lengthList[] = $length;
            }

        }
        $contents = ltrim($contents, ', ');

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

        if ($weight <= 1 ) $weight = 1;
        $weight = round($weight);
        
        $ramburs = $order->get_total();
        if($order->get_payment_method()=='cod') {
            $ramburs = $order->get_total();
        } else {
            $ramburs = 0;
        }

    	$UserName           = rawurlencode(get_option('uc_username'));
    	$Password           = rawurlencode(get_option('uc_password'));
    	$punct_ridicare     = get_option('uc_punct_ridicare');
    	$price_table_id     = get_option('uc_price_table_id');
    	$numar_plicuri      = get_option('uc_nr_plicuri');
        $numar_colete       = get_option('uc_nr_colete');
        $plata_transport    = get_option('uc_plata_transport');
        $plata_ramburs      = get_option('uc_plata_ramburs');
        $asigurare          = get_option('uc_asigurare');
        $deschidere         = get_option('uc_deschidere');
        $matinal            = get_option('uc_matinal');
        $service_type_id    = get_option('uc_tip_serviciu');
        $sambata            = get_option('uc_sambata');
        $obs                = get_option('uc_observatii');
        $force_width        = get_option('uc_force_width');
        $force_height       = get_option('uc_force_height');
        $force_length       = get_option('uc_force_length');
        $force_weight       = get_option('uc_force_weight');                

        if( empty($UserName) ) { 
            echo '<div class="wrap"><h1>SafeAlternative UrgentCargus AWB</h2><br><h2>Plugin-ul SafeAlternative UrgentCargus AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=urgent-cargus-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

		if ($order->get_customer_note() != '') {
		    $obs = $order->get_customer_note();
		}
		
		// asigurare
		if ($asigurare=='1') $val_decl=$ramburs;
		if ($asigurare=='0' OR $asigurare=='') $val_decl='0';	           
        
        if (!empty($numar_colete)) $parcel_type_code = 1;
        if (!empty($numar_plicuri)) $parcel_type_code = 0;


        if($force_height) $height = $force_height;
        if($force_width) $width = $force_width;
        if($force_length) $length = $force_length;
        if($force_weight) $weight = $force_weight;

        $district = safealternative_get_counties_list($order->get_shipping_state());
        $city = $wpdb->get_var("SELECT cargus_locality_name FROM courier_localities where locality_name='" . $order->get_shipping_city() . "' ") ?: $order->get_shipping_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($district, $city);

        $shipping_address = array (
            'first_name' => remove_accents($order->get_shipping_first_name()),
            'last_name' => remove_accents($order->get_shipping_last_name()),
            'company' => remove_accents($order->get_shipping_company()),
            'address_1' => remove_accents($order->get_shipping_address_1()),
            'address_2' => remove_accents($order->get_shipping_address_2()),
            'city' => remove_accents($city),
            'state' => remove_accents($order->get_shipping_state()),
            'postcode' => $order->get_shipping_postcode(),
            'country' => $order->get_shipping_country(),
            'phone' => $order->get_billing_phone(),
            'email' => $order->get_billing_email(),
            'cod_postal' => $postcode,
        );

        if($shipping_address['state']=='Municipiul Bucuresti' || $shipping_address['state']=="S1" ||
            $shipping_address['state']=="S2" ||
            $shipping_address['state']=="S3" ||
            $shipping_address['state']=="S4" ||
            $shipping_address['state']=="S5" ||
            $shipping_address['state']=="S6" ||
            $shipping_address['state']=="Sectorul 1" ||
            $shipping_address['state']=="Sectorul 2" ||
            $shipping_address['state']=="Sectorul 3" ||
            $shipping_address['state']=="Sectorul 4" ||
            $shipping_address['state']=="Sectorul 5" ||
            $shipping_address['state']=="Sectorul 6") {
            $shipping_address['state']='B'; 
        }
    
        if ($shipping_address['city']=="Bucuresti" && $shipping_address['state']=="") $shipping_address['state']='B';

        $_POST['awb'] = array(
            'Sender' => array(
                'LocationId' => $punct_ridicare,
            ),
            'Recipient' => array(
                'Name' => empty($shipping_address['company']) ? $shipping_address ['first_name'] . ' ' . $shipping_address ['last_name'] : $shipping_address['company'],
                'CountyName' => $shipping_address ['state'],
                'LocalityName' => $shipping_address ['city'],
                'AddressText' => $shipping_address ['address_1'].' '.$shipping_address ['address_2'],
                'StreetName' => $shipping_address ['address_1'],
                'BuildingNumber' => '',
                'ContactPerson' => $shipping_address ['first_name'] . ' ' . $shipping_address ['last_name'],
                'PhoneNumber' => $shipping_address ['phone'],
                'Email' => $shipping_address ['email'],
                'CodPostal' => $shipping_address ['cod_postal']
            ),
            'ParcelCodes' => array(
                array(
                    'Code' => 0,
                    'Type' => $parcel_type_code,
                    'Length' => (int)$length,
                    'Width' => (int)$width,
                    'Height' => (int)$height,
                    'Weight' => (int)$weight,
                )
            ),
            'Parcels' => (int)$numar_colete,
            'Envelopes' => (int)$numar_plicuri,
            'TotalWeight' => (int)$weight,
            'DeclaredValue' => (float)$val_decl,
            'CashRepayment' => 0,
            'BankRepayment' => (float)$ramburs,
            'OtherRepayment' => '',
            'ServiceId' => (int)$service_type_id,
            'PriceTableId' => (int)$price_table_id,
            'ShipmentPayer' => (int)$plata_transport,
            'ShippingRepayment'  => (int)$plata_ramburs,
            'OpenPackage' => filter_var($deschidere, FILTER_VALIDATE_BOOLEAN),
            'SaturdayDelivery' => filter_var($sambata, FILTER_VALIDATE_BOOLEAN),
            'MorningDelivery'   => filter_var($matinal, FILTER_VALIDATE_BOOLEAN),
            'Observations' => $obs,
            'PackageContent' => $contents,
            'CustomString' => $serie_client,
            'SenderReference1' => '',
            'RecipientReference1' => '',
            'RecipientReference2' => '',
            'InvoiceReference' => 'Comanda nr. '.$_GET['order_id'],
        );

        $_POST['awb'] = apply_filters('safealternative_awb_details', $_POST['awb'], 'UrgentCargus', $order);
            
        include_once(plugin_dir_path(__FILE__) . '/templates/generate-awb-page.php');
    }

    function cargusawb_add_meta_box() {

        $screens = array( 'shop_order' );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'cargusawb_sectionid',
                __( 'UrgentCargus - AWB', 'cargusawb_textdomain' ),
                array( $this, 'cargusawb_meta_box_callback' ),
                $screen,
                'side'
            );
        }
    }

    function cargusawb_meta_box_callback( $post ) {
        $awb = get_post_meta($post->ID, 'awb_urgent_cargus', true);

        echo '<style>.cargus_secondary_button{border-color:#f44336!important;color:#f44336!important}.cargus_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://cargus.ro/tracking-romanian/?t='.$awb.'" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button cargus_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-urgent-cargus&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
        }
    }
    
    /////////////////////////////////////////////////////////
    /////// ADD T&T COLUMNS  ////////////////////////////////
    /////////////////////////////////////////////////////////
           	 
	/////////////////////////////////////////////////////
	function add_custom_columns_to_orders_table($columns) {
		$new_columns = $columns;
		
		if (! is_array ( $columns )) {
			$columns = array ();
		}
		
		$new_columns ['Urgent_Cargus_AWB'] = 'Urgent Cargus';
		
		return $new_columns;
	}
	
	////////////////////////////////////////////////
	function get_custom_columns_values($column) {
		global $post;
		
		if ($column == 'Urgent_Cargus_AWB') {

            $awb = get_post_meta($post->ID, 'awb_urgent_cargus');
            $status = get_post_meta($post->ID, 'awb_urgent_cargus_trace_status');
            $op = get_post_meta($post->ID, 'op_urgent_cargus');
            $op_value =get_post_meta($post->ID, 'op_urgent_cargus_value');
            
            // avem awb 
			if (!empty($awb[0])) {
				$printing_link = plugin_dir_url( __FILE__ ).'download.php?&order_id='.$post->ID.'';
                echo '<a class="button tips downloadBtn" href="'.$printing_link.'" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">'.$awb[0].'</a></br>';
                echo '<div class="urgentNoticeWrapper">';
				
				if (!empty($status[0])) {
					if ($status[0] == 'Confirmat') echo '<div class="urgentNotice"><span class="dashicons dashicons-yes"></span>Status: '.$status[0].'</br></div>';
					elseif ($status[0] == 'Preluat') echo '<div class="urgentNotice"><span class="dashicons dashicons dashicons-migrate"></span>Status: '.$status[0].'</br></div>';
					elseif ($status[0] == 'Rambursat') echo '<div class="urgentNotice"><span class="dashicons dashicons-images-alt"></span>Status: '.$status[0].'</br></div>';
					elseif ($status[0] == 'Returnat') echo '<div class="urgentNotice"><span class="dashicons dashicons-image-rotate"></span>Status: '.$status[0].'</br></div>';
					else echo '<div class="urgentNotice"><span class="dashicons dashicons-dismiss"></span>Status: '.$status[0].'</br></div>';
				} else {
					echo '<div class="urgentNotice"><span class="dashicons dashicons-warning"></span>Status: Nescanat</div>';
                    if (!empty($op[0])) {
                        echo 'OP: '.$op[0].'</br>';
                        echo 'OP value: '.$op_value[0];
                    }
                }
                echo '</div>';
			}
			// nu avem awb
			else {
                if(get_option('uc_nr_colete') <= 1){
                    $current_url_generate = esc_url ( add_query_arg ( array ('generate_awb_cargus' => absint ( $post->ID ) ) ) );
                } else {
                    $current_url_generate = '/wp-admin/?page=generate-awb-urgent-cargus&order_id='.$post->ID;
                }
				
                echo '<p><a class="button generateBtn tips" data-tip="'.__('Genereaza AWB UrgentCargus', 'safealternative-plugin').'"
                        href="'.$current_url_generate.'"><img src="'.plugin_dir_url(__FILE__).'assets/images/urgentcargus.png'.'" style="height: 29px;"/></a>
                    </p>';   
			}
		}
	}
    
	// This method will generate a new awb
	static function generate_awb() {
        global $wpdb;

		$order_id = get_query_var ( 'generate_awb_cargus', NULL );
        if(!$order_id)  {
            return null;
        }

        $awb_already_generated = get_post_meta($order_id, 'awb_urgent_cargus', 1);
        if($awb_already_generated) {
            return null;
        }

        $UserName = rawurlencode(get_option ( 'uc_username' ));
        $Password = rawurlencode(get_option ( 'uc_password' ));
        $punct_ridicare = get_option ( 'uc_punct_ridicare' );
        $price_table_id = get_option ( 'uc_price_table_id');
        $numar_plicuri  = get_option('uc_nr_plicuri');
        $numar_colete   = get_option('uc_nr_colete');
        $plata_transport= get_option('uc_plata_transport');
        $plata_ramburs  = get_option ( 'uc_plata_ramburs' );
        $asigurare      = get_option ( 'uc_asigurare' );
        $deschidere     = get_option ( 'uc_deschidere' );
        $service_type_id = get_option('uc_tip_serviciu');
        $matinal        = get_option ( 'uc_matinal' );
        $sambata        = get_option ( 'uc_sambata' );
        $descrie_continut = get_option ( 'uc_descrie_continut' );
        $observatii     = get_option ( 'uc_observatii' );
        $serie_client = get_option('uc_serie_client');
        $force_width        = get_option('uc_force_width');
        $force_height       = get_option('uc_force_height');
        $force_length       = get_option('uc_force_length');
        $force_weight       = get_option('uc_force_weight');   
    
        if( empty($UserName) ) {
            echo '<div class="notice notice-error"><h2>Plugin-ul SafeAlternative UrgentCargus AWB nu a fost configurat.</h2><p>Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=urgent-cargus-setting').'"> aici</a> pentru a il configura.</p></div>';
            return;
        }
        
        try {
            $order = new WC_Order ( $order_id );
            $district = $order->get_shipping_state();
            $city = $wpdb->get_var("SELECT cargus_locality_name FROM courier_localities where locality_name='" . $order->get_shipping_city() . "' ") ?: $order->get_shipping_city();
            $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($district, $city);
            
            $shipping_address = array (
                'first_name' => remove_accents($order->get_shipping_first_name()),
                'last_name' => remove_accents($order->get_shipping_last_name()),
                'company' => remove_accents($order->get_shipping_company()),
                'address_1' => remove_accents($order->get_shipping_address_1()),
                'address_2' => remove_accents($order->get_shipping_address_2()),
                'city' => remove_accents($city),
                'state' => remove_accents($order->get_shipping_state()),
                'postcode' => $order->get_shipping_postcode(),
                'country' => $order->get_shipping_country(),
                'phone' => $order->get_billing_phone(),
                'email' => $order->get_billing_email(),
                'cod_postal' => $postcode,
            );

            if($shipping_address['state']=='Municipiul Bucuresti' || $shipping_address['state']=="S1" ||
                $shipping_address['state']=="S2" ||
                $shipping_address['state']=="S3" ||
                $shipping_address['state']=="S4" ||
                $shipping_address['state']=="S5" ||
                $shipping_address['state']=="S6" ||
                $shipping_address['state']=="Sectorul 1" ||
                $shipping_address['state']=="Sectorul 2" ||
                $shipping_address['state']=="Sectorul 3" ||
                $shipping_address['state']=="Sectorul 4" ||
                $shipping_address['state']=="Sectorul 5" ||
                $shipping_address['state']=="Sectorul 6") {
                $shipping_address['state']='B'; 
            }
        
            if ($shipping_address['city']=="Bucuresti" && $shipping_address['state']=="") $shipping_address['state']='B';

            $items = $order->get_items();
            $weight = 0;
            $height = 0;
            $width  = 0;
            $length = 0;
            $contents = '';
            
            $heightList = array();
            $lengthList = array();
            
            foreach ( $items as $i => $item ) {
                if($descrie_continut) {
                    $contents .= ', '.$item->get_quantity().' x '.str_replace('"', '', $item->get_name());
                }
                $_product = $item->get_product();
                if ($_product && ! $_product->is_virtual() ) {
                    $weight += (float) $_product->get_weight() * $item->get_quantity();

                    $width += (int) $_product->get_width() * $item->get_quantity();
                    
                    $height       = (int) $_product->get_height();
                    $heightList[] = $height;
                    $length       = (int) $_product->get_length();
                    $lengthList[] = $length;
                }
    
            }
            $contents = ltrim($contents, ', ');

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
            
            if ($weight <= 1 ) $weight=1;
            $weight = round($weight);
            
            $ramburs = $order->get_total();
            
            if($order->get_payment_method()=='cod') {
                $ramburs = $order->get_total();
            } else {
                $ramburs = 0;
            }
                    
            $obs = $observatii;
            if ($order->get_customer_note() != '') {
                $obs = $order->get_customer_note();
            }
            
            // asigurare
            if ($asigurare=='1') $val_decl=$order->get_total();
            if ($asigurare=='0' OR $asigurare=='') $val_decl='0';	

            if($force_height) $height = $force_height;
            if($force_width) $width = $force_width;
            if($force_length) $length = $force_length;
            if($force_weight) $weight = $force_weight;

            $colete_parcel_codes = array();
            if (!empty($numar_colete)) {
                $colete_parcel_codes[] = array(
                    'Code' => 0,
                    'Type' => 1,
                    'Length' => (int) $length,
                    'Width' => (int) $width,
                    'Height' => (int) $height,
                    'Weight' => (int) $weight,
                );
            }
            
            $plicuri_parcel_codes = array();
            if (!empty($numar_plicuri)) {
                for ($i = 0; $i < $numar_plicuri; $i++) {
                    $plicuri_parcel_codes[] = array(
                        'Code' => $i + count($colete_parcel_codes ?? array()),
                        'Type' => 0,
                    );
                }
            }

            if (empty($numar_colete) && !empty($numar_plicuri)) {
                $weight = 1;
            }

            // ///////////////////////////////////////////
            $awbsDetails = array(
                'Sender' => array(
                    'LocationId' => $punct_ridicare,
                ),
                'Recipient' => array(
                    "Name" => empty($shipping_address['company']) ? $shipping_address ['first_name'] . ' ' . $shipping_address ['last_name'] : $shipping_address['company'],
                    "CountyName" => $shipping_address ['state'],
                    "LocalityName" => $shipping_address ['city'],
                    "AddressText" => $shipping_address ['address_1'].' '.$shipping_address ['address_2'],
                    "StreetName" => $shipping_address ['address_1'],
                    "BuildingNumber" => '',
                    "ContactPerson" => $shipping_address ['first_name'] . ' ' . $shipping_address ['last_name'],
                    "PhoneNumber" => $shipping_address ['phone'],
                    "Email" => $shipping_address ['email'],
                    "CodPostal" => $shipping_address ['cod_postal']
                ),
                'ParcelCodes' => array_merge($colete_parcel_codes, $plicuri_parcel_codes),
                'Parcels' => (int)$numar_colete,
                'Envelopes' => (int)$numar_plicuri,
                'TotalWeight' => (int)$weight,
                'DeclaredValue' => (float)$val_decl,
                'CashRepayment' => 0,
                'BankRepayment' => (float)$ramburs,
                'OtherRepayment' => '',
                'ServiceId' => (int)$service_type_id,
                'OpenPackage' => filter_var($deschidere, FILTER_VALIDATE_BOOLEAN),
                'PriceTableId' => ($price_table_id == 1) ? 0 : $price_table_id,
                'ShipmentPayer' => (int)$plata_transport,
                'ShippingRepayment'  => (int)$plata_ramburs,
                'SaturdayDelivery' => filter_var($sambata, FILTER_VALIDATE_BOOLEAN),
                'MorningDelivery' => filter_var($matinal, FILTER_VALIDATE_BOOLEAN),
                'Observations' => $obs,
                'PackageContent' => $contents,
                'CustomString' => $serie_client,
                'SenderReference1' => '',
                'RecipientReference1' => '',
                'RecipientReference2' => '',
                'InvoiceReference' => 'Comanda nr. '.$order_id,
                'SenderClientId' => '',
                'domain' => site_url()
            );           

            $awbsDetails = apply_filters('safealternative_awb_details', $awbsDetails, 'UrgentCargus', $order);
    
            $trimite_mail = get_option ( 'uc_trimite_mail');
            

            
            $awbsDetails['token'] =  get_option('token');
            $awbsDetails['token_cargus'] =  get_option('uc_token');
            $awbsDetails['subscriptionKey']  = get_option('uc_key');

            $courier  = new CourierCargusSafe();
            $response = $courier->callMethod("generateAwb", $awbsDetails, 'POST'); 

            if ($response['success']) {
                if ( !is_numeric(json_decode($response['message'])) ) {
                    
                } 
                
                else {

                    $awb=json_decode($response['message']);

                    if ($trimite_mail=='1') {
                        CargusAWB::send_mails($order_id, $awb, $awbsDetails['Recipient']['Email']);
                    }
                    
                    update_post_meta($order_id, 'awb_urgent_cargus', $awb);
                    do_action( 'safealternative_awb_generated', 'UrgentCargus', $awb , $order_id);
                     
                }
            }            
        } 
        catch ( Exception $e ) {
            echo "<p>Nu a putut fi generat awb-ul!</p>";
        }
        
	}// end function

 
    static public function send_mails($idOrder, $awb, $receiver_email) {
        $sender_email    = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template  = get_option('uc_email_template');
        $headers         = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.$sender_email.'>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option ( 'uc_subiect_mail' ), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_cargus_email', $email_content, $data);
        $email_content = nl2br($email_content);

        try {
            if (!wp_mail($receiver_email, $subiect_mail, $email_content, $headers)) {
                set_transient('email_sent_error', 'Nu am putut trimite email-ul catre ' . $receiver_email, 5);
            } else {
                set_transient('email_sent_success', 'Email-ul s-a trimis catre ' . $receiver_email, 5);
            }
        } catch (Exception $e) { }
    }

    static function handle_email_template($template, $data)
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

    public function autogenerate_urgent_awb($order_id, $old_status, $new_status){
        if(get_option('uc_auto_generate_awb') != "da") return;
        if($new_status !== 'processing') return; 

        set_query_var('generate_awb_cargus', $order_id);
        CargusAWB::generate_awb();
    }    
}
// end class

include_once(plugin_dir_path(__FILE__) . '/cron.php');

//Bulk generate
add_action( 'admin_footer', 'add_bulk_action_cargus');
function add_bulk_action_cargus() {
	global $post_type;

	if ( 'shop_order' == $post_type ) {	
        wp_enqueue_script( 'bulk_admin_js_cargus', plugin_dir_url(__FILE__).'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');    
	}
}
    

////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action ( 'woocommerce_order_details_after_order_table_items', function ( $order ) {
	$order_id = $order->get_order_number();
	$awb = get_post_meta($order_id, 'awb_urgent_cargus', true);	
	if($awb) echo 'Nota de transport (AWB) are numarul: '.$awb.' si poate fi urmarita aici: <a href="https://cargus.ro/tracking-romanian/?t='.$awb.'" target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter ( 'query_vars', function ($vars) {
	$vars [] = "generate_awb_cargus";
	return $vars;
});

add_action ( 'admin_notices', array ('CargusAWB','generate_awb' ), 1 );

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_cargus', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);