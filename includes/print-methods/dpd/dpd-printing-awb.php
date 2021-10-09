<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir.'courier.class.php');

class DPDAWB 
{
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page_settings' ) );
        add_action( 'add_meta_boxes', array( $this, 'dpd_add_meta_box' ) );
        add_action( 'admin_init', array( $this, 'add_register_setting' ));
        
        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action( 'woocommerce_order_status_changed', array(
            $this,
            'autogenerate_dpd_awb'
        ), 99, 3);    
    
        add_filter ( 'manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table') , 11 );
        add_action ( 'manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values') , 2 );
    }

    function add_plugin_page_settings(){
        add_submenu_page(
            'safealternative-menu-content',
            'DPD - AWB',
            'DPD - AWB',
            'manage_woocommerce',
            'dpd-plugin-setting',
            array(
                $this,
                'dpd_plugin_page'
            )
        );     
    }

    function add_register_setting() {
        add_option('dpd_username', '');
        add_option('dpd_password', '');
        add_option('dpd_content_type', '');
        add_option('dpd_sender_id', '');
        add_option('dpd_service_id', '2505');
        add_option('dpd_parcel_count', '1');
        add_option('dpd_is_sat_delivery', '0');
        add_option('dpd_is_fragile', '0');
        add_option('dpd_parcel_note', '');
        add_option('dpd_trimite_mail', 'nu');
        add_option('dpd_courier_service_payer', 'SENDER');
        add_option('dpd_courier_package_payer', 'SENDER');
        add_option('dpd_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('dpd_page_type', 'A4');
        add_option('dpd_auto_generate_awb', 'nu');
        add_option('dpd_auto_mark_complete', 'nu');
        add_option('dpd_force_weight', '');

        register_setting('dpd-plugin-settings', 'dpd_username' );
        register_setting('dpd-plugin-settings', 'dpd_password' );
        register_setting('dpd-plugin-settings', 'dpd_content_type' );
        register_setting('dpd-plugin-settings', 'dpd_sender_id' );
        register_setting('dpd-plugin-settings', 'dpd_service_id' );
        register_setting('dpd-plugin-settings', 'dpd_parcel_count' );
        register_setting('dpd-plugin-settings', 'dpd_parcel_note' );
        register_setting('dpd-plugin-settings', 'dpd_is_sat_delivery' );
        register_setting('dpd-plugin-settings', 'dpd_is_fragile' );
        register_setting('dpd-plugin-settings', 'dpd_trimite_mail' );
        register_setting('dpd-plugin-settings', 'dpd_subiect_mail' );
        register_setting('dpd-plugin-settings', 'dpd_page_type' );
        register_setting('dpd-plugin-settings', 'dpd_courier_service_payer' );
        register_setting('dpd-plugin-settings', 'dpd_courier_package_payer' );
        register_setting('dpd-plugin-settings', 'dpd_auto_generate_awb' );
        register_setting('dpd-plugin-settings', 'dpd_auto_mark_complete' );
        register_setting('dpd-plugin-settings', 'dpd_force_weight' );

        require_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message_status = get_transient('dpd_account_status')) && $qv === "shop_order") {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_status, 'safealternative-dpd-woocommerce'); ?></p>
            </div>
            <?php
        }

        if (($message_settings = get_transient('dpd_account_settings'))) {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_settings, 'safealternative-dpd-woocommerce'); ?></p>
            </div>
            <?php
            delete_transient('dpd_account_settings');
        }    
    }

    function dpd_plugin_page() 
    {
        require_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            null,
            'Genereaza AWB DPD',
            'Genereaza AWB DPD',
            'manage_woocommerce',
            'generate-awb-dpd',
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
        
        $awb_already_generated = get_post_meta($_GET['order_id'], 'awb_dpd', 1);
        if($awb_already_generated) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $order = wc_get_order($_GET['order_id']);
        $items = $order->get_items();
        $weight = 0;
        $force_weight = get_option('dpd_force_weight');
        
        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
            }
        }

        if ($weight <= 1) $weight = 1;
        $weight = round($weight);

        if($force_weight) $weight = $force_weight;

        if( empty(get_option('dpd_username')) ) { 
            echo '<div class="wrap"><h1>SafeAlternative DPD AWB</h2><br><h2>Plugin-ul SafeAlternative DPD AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=dpd-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_shipping_state());
        $recipient_address_city_id = $order->get_shipping_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $awb_info = [
            'sender_id' => get_option('dpd_sender_id'),
            'service_id' => get_option('dpd_service_id'),
            'language' => 'RO',
            'courier_service_payer' => get_option('dpd_courier_service_payer'),
            'third_party_client_id' => get_option('dpd_sender_id'),
            'package_payer' => get_option('dpd_courier_package_payer'),
            'package' => 'BOX',
            'contents' => get_option('dpd_content_type'),
            'recipient_name' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'recipient_contact' => !empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : '',
            'recipient_private_person' => empty($order->get_shipping_company()) ? 'y' : 'n',
            'recipient_phone' => $order->get_billing_phone(),
            'recipient_address_state_id' => $recipient_address_state_id,
            'recipient_address_site_name' => $order->get_shipping_city(),
            'recipient_address_postcode' => $postcode,
            'recipient_address_note' => "{$order->get_shipping_address_1()} {$order->get_shipping_address_2()}",
            'recipient_address_line1' => $order->get_shipping_address_1(),
            'recipient_address_line2' => $order->get_shipping_address_2(),
            'recipient_pickup_office_id' => get_post_meta($order->get_id(), 'safealternative_dpd_box', true),
            'recipient_email' => $order->get_billing_email(),
            'declared_value_amount' => '',
            'saturday_delivery' => get_option('dpd_is_sat_delivery'),
            'declared_value_fragile' => get_option('dpd_is_fragile'),
            'cod_amount' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'cod_currency' => 'RON',
            'parcels_count' => get_option('dpd_parcel_count'),
            'total_weight' => $weight,
            'autoadjust_pickup_date' => 'y',
            'shipmentNote' => get_option('dpd_parcel_note'),
            'ref1' => ''
        ];

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'DPD', $order);

        $_POST['awb'] = $awb_info;
            
        require_once(plugin_dir_path(__FILE__) . '/templates/generate-awb-page.php');
    }

    function dpd_add_meta_box() {

        $screens = array( 'shop_order' );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'dpd_sectionid',
                __( 'DPD - AWB', 'safealternative_dpd' ),
                array( $this, 'dpd_meta_box_callback' ),
                $screen,
                'side'
            );
        }
    }

    function dpd_meta_box_callback( $post ) {
        $awb = get_post_meta($post->ID, 'awb_dpd', true);

        echo '<style>.dpd_secondary_button{border-color:#f44336!important;color:#f44336!important}.dpd_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://tracking.dpd.ro/?shipmentNumber='.$awb.'&language=ro" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button dpd_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-dpd&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
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
		
		$new_columns ['DPD_AWB'] = 'DPD';
		
		return $new_columns;
	}
	
	////////////////////////////////////////////////
	function get_custom_columns_values($column) {
		global $post;
		
		if ($column == 'DPD_AWB') {
            $awb = get_post_meta($post->ID, 'awb_dpd', true);
            $status = get_post_meta($post->ID, 'awb_dpd_status', true);
            
            // avem awb 
			if (!empty($awb)) {
				$printing_link = plugin_dir_url( __FILE__ ).'download.php?&order_id='.$post->ID.'';
                echo '<a class="button tips downloadBtn" href="'.$printing_link.'" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">'.$awb.'</a></br>';
                echo '<div class="dpdNoticeWrapper">';
                echo '<div class="dpdNotice"><span class="dashicons dashicons-warning"></span>Status: '.$status.'</div>';
                echo '</div>';
			}
			// nu avem awb
			else {
                $current_url_generate = esc_url(add_query_arg(array(
                    'generate_awb_dpd' => absint($post->ID)
                )));

                echo '<p><a class="button generateBtn tips" data-tip="'.__('Genereaza AWB DPD', 'safealternative-plugin').'"
                        href="'.$current_url_generate.'"><img src="'.plugin_dir_url(__FILE__).'assets/images/dpd.svg'.'" style="height: 29px;"/></a>
                    </p>';   
			}
		}
	}
    
	// This method will generate a new awb
	static function generate_awb_dpd() {
        global $wpdb;

		$order_id = get_query_var ('generate_awb_dpd', NULL );
        if(empty($order_id))  {
            return null;
        }

        $awb_already_generated = get_post_meta($order_id, 'awb_dpd', true);
        if($awb_already_generated) {
            return null;
        } 

        $trimite_mail = get_option('dpd_trimite_mail');
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        $weight = 0;
        $force_weight = get_option('dpd_force_weight');

        
        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
            }
        }

        if ($weight <= 1) $weight = 1;
        $weight = round($weight);

        if($force_weight) $weight = $force_weight;

        if( empty(get_option('dpd_username')) ) { 
            echo '<div class="notice notice-error"><h2>Plugin-ul SafeAlternative DPD AWB nu a fost configurat.</h2><p>Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=dpd-plugin-setting').'"> aici</a> pentru a il configura.</p></div>';
            return;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_shipping_state());
        $recipient_address_city_id = $order->get_shipping_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $awb_info = [
            'sender_id' => get_option('dpd_sender_id'),
            'service_id' => get_option('dpd_service_id'),
            'language' => 'RO',
            'courier_service_payer' => get_option('dpd_courier_service_payer'),
            'package_payer' => get_option('dpd_courier_package_payer'),
            'package' => 'BOX',
            'contents' => get_option('dpd_content_type'),
            'recipient_name' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'recipient_contact' => !empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : '',
            'recipient_private_person' => empty($order->get_shipping_company()) ? 'y' : 'n',
            'recipient_phone' => $order->get_billing_phone(),
            'recipient_address_state_id' => $recipient_address_state_id,
            'recipient_address_site_name' => $order->get_shipping_city(),
            'recipient_address_postcode' => $postcode,
            'recipient_address_note' => "{$order->get_shipping_address_1()} {$order->get_shipping_address_2()}",
            'recipient_address_line1' => $order->get_shipping_address_1(),
            'recipient_address_line2' => $order->get_shipping_address_2(),
            'recipient_pickup_office_id' => get_post_meta($order->get_id(), 'safealternative_dpd_box', true),
            'recipient_email' => $order->get_billing_email(),
            'declared_value_amount' => '',
            'saturday_delivery' => get_option('dpd_is_sat_delivery'),
            'declared_value_fragile' => get_option('dpd_is_fragile'),
            'cod_amount' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'cod_currency' => 'RON',
            'parcels_count' => get_option('dpd_parcel_count'),
            'total_weight' => $weight,
            'autoadjust_pickup_date' => 'y',
            'shipmentNote' => get_option('dpd_parcel_note'),
            'ref1' => ''
        ];        
        
        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'DPD', $order);
        
        $courier = new SafealternativeDPDClass();
        $result = $courier->callMethod("generateAwb", $awb_info, 'POST');

        if ($result['status']!="200") {
            set_transient('dpd_account_settings', json_decode($result['message']), MONTH_IN_SECONDS);
        } else {
            $message = json_decode($result['message']);
            
            if ( !empty($message->error) ) {
                set_transient('dpd_account_settings', $message->error->message, MONTH_IN_SECONDS);
            } else {
                $awb = $message->id;
                if ($trimite_mail=='da') {
                    DPDAWB::send_mails($order_id, $awb, $awb_info['recipient_email']);
                }
                
                update_post_meta($order_id, 'awb_dpd', $awb);
                update_post_meta($order_id, 'awb_dpd_status', 'Inregistrat');
                do_action('safealternative_awb_generated', 'DPD', $awb, $order_id);

                $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');
                $account_status = json_decode($account_status_response['message']);

                if($account_status->show_message){
                    set_transient( 'dpd_account_status', $account_status->message, MONTH_IN_SECONDS );
                } else {
                    delete_transient( 'dpd_account_status' );
                }                       
            }
        }            
	}// end function

 
    static public function send_mails($idOrder, $awb, $receiver_email) {
        $sender_email    = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template  = get_option('dpd_email_template');
        $headers         = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.$sender_email.'>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option ( 'dpd_subiect_mail' ), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_dpd_email', $email_content, $data);
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

    public function autogenerate_dpd_awb($order_id, $old_status, $new_status){
        if(get_option('dpd_auto_generate_awb') != "da") return;
        if($new_status !== 'processing') return; 

        set_query_var('generate_awb_dpd', $order_id);
        DPDAWB::generate_awb_dpd();
    }    
}
// end class

require_once(plugin_dir_path(__FILE__) . '/dpd.class.php');
require_once(plugin_dir_path(__FILE__) . '/cron.php');

//Bulk generate
add_action( 'admin_footer', 'add_bulk_action_dpd');
function add_bulk_action_dpd() {
	global $post_type;

	if ( 'shop_order' == $post_type ) {	
        wp_enqueue_script( 'bulk_admin_js_dpd', plugin_dir_url(__FILE__).'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');    
	}
}
    

////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action ( 'woocommerce_order_details_after_order_table_items', function ( $order ) {
	$order_id = $order->get_order_number();
	$awb = get_post_meta($order_id, 'awb_dpd', true);	
    if($awb) echo 'Nota de transport (AWB) are numarul: '.$awb.' si poate fi urmarita aici: <a href="https://tracking.dpd.ro/?shipmentNumber='.$awb.'&language=ro" target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter ( 'query_vars', function ($vars) {
	$vars [] = "generate_awb_dpd";
	return $vars;
});

add_action ( 'admin_notices', array ('DPDAWB', 'generate_awb_dpd'), 1 );

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_dpd', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);

add_action( 'init', function() {
    add_filter( 'pre_update_option_dpd_username', function($new_val, $old_val) {
        if ($old_val != $new_val) {
            delete_transient('dpd_sender_list');
        }
        return $new_val;
    }, 10, 2 );
} );