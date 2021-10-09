<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir.'courier.class.php');

class ExpressAWB 
{
    public function __construct() 
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page_settings' ) );
        add_action( 'add_meta_boxes', array( $this, 'express_add_meta_box' ) );
        add_action( 'admin_init', array( $this, 'add_register_setting' ));
        
        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action( 'woocommerce_order_status_changed', array(
            $this,
            'autogenerate_express_awb'
        ), 99, 3);    
    
        add_filter ( 'manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table') , 11 );
        add_action ( 'manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values') , 2 );	
    }

    function add_plugin_page_settings()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Express - AWB',
            'Express - AWB',
            'manage_woocommerce',
            'express-plugin-setting',
            array(
                $this,
                'express_plugin_page'
            )
        );     
    }

    function add_register_setting() 
    {
        add_option('express_package_type', 'package');
        add_option('express_key', '');
        add_option('express_retur', 'false');
        add_option('express_parcel_count', '1');
        add_option('express_retur_type', 'document');
        add_option('express_insurance', '');
        add_option('express_payer', 'expeditor');
        add_option('express_name', '');
        add_option('express_contact_person', '');
        add_option('express_phone', '');
        add_option('express_email', '');
        add_option('express_county', '');
        add_option('express_city', '');
        add_option('express_address', '');
        add_option('express_postcode', '');
        add_option('express_content', '');
        add_option('express_sender_id', '');
        add_option('express_service', 'Standard');
        add_option('express_parcel_count', '1');
        add_option('express_is_sat_delivery', 'false');
        add_option('express_is_fragile','false');
        add_option('express_retur_signed_doc_delivery', 'false');
        add_option('express_printed_awb', 'false');
        add_option('express_18hr_20hr_package', 'false');
        add_option('express_trimite_mail', 'nu');
        add_option('express_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('express_page_type', 'default');
        add_option('express_auto_generate_awb', 'nu');
        add_option('express_auto_mark_complete', 'nu');

        register_setting('express-plugin-settings', 'express_package_type' );
        register_setting('express-plugin-settings', 'express_key' );
        register_setting('express-plugin-settings', 'express_retur' );
        register_setting('express-plugin-settings', 'express_parcel_count' );
        register_setting('express-plugin-settings', 'express_retur_type' );
        register_setting('express-plugin-settings', 'express_insurance' );
        register_setting('express-plugin-settings', 'express_payer' );
        register_setting('express-plugin-settings', 'express_name' );
        register_setting('express-plugin-settings', 'express_contact_person' );
        register_setting('express-plugin-settings', 'express_phone' );
        register_setting('express-plugin-settings', 'express_email' );
        register_setting('express-plugin-settings', 'express_county' );
        register_setting('express-plugin-settings', 'express_city' );
        register_setting('express-plugin-settings', 'express_address' );
        register_setting('express-plugin-settings', 'express_postcode' );
        register_setting('express-plugin-settings', 'express_content' );
        register_setting('express-plugin-settings', 'express_sender_id' );
        register_setting('express-plugin-settings', 'express_service' );
        register_setting('express-plugin-settings', 'express_is_sat_delivery' );
        register_setting('express-plugin-settings', 'express_retur_signed_doc_delivery' );
        register_setting('express-plugin-settings', 'express_is_fragile' );
        register_setting('express-plugin-settings', 'express_printed_awb' );
        register_setting('express-plugin-settings', 'express_trimite_mail' );
        register_setting('express-plugin-settings', 'express_subiect_mail' );
        register_setting('express-plugin-settings', 'express_page_type' );
        register_setting('express-plugin-settings', 'express_18hr_20hr_package');
        register_setting('express-plugin-settings', 'express_auto_generate_awb' );
        register_setting('express-plugin-settings', 'express_auto_mark_complete' );

        require_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message_status = get_transient('express_account_status')) && $qv === "shop_order") {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_status, 'safealternative-express-woocommerce'); ?></p>
            </div>
            <?php
        }

        if (($message_settings = get_transient('express_account_settings'))) {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_settings, 'safealternative-express-woocommerce'); ?></p>
            </div>
            <?php
            delete_transient('express_account_settings');
        }    
    }

    function express_plugin_page() 
    {
        require_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            null,
            'Genereaza AWB Express',
            'Genereaza AWB Express',
            'manage_woocommerce',
            'generate-awb-express',
            array($this, 'create_admin_page'),
            null
        );        
    }

    public function create_admin_page()
    {       
        if (!isset($_GET['order_id'])) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }
        
        $awb_already_generated = get_post_meta($_GET['order_id'], 'awb_express', 1);
        if($awb_already_generated) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $order = wc_get_order($_GET['order_id']);
        $items = $order->get_items();
        $weight = 0;
        
        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
            }
        }

        if ($weight <= 1) $weight = 1;
        $weight = round($weight);

        if( empty(get_option('express_key')) ) { 
            echo '<div class="wrap"><h1>SafeAlternative Express AWB</h2><br><h2>Plugin-ul SafeAlternative Express AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=express-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();

        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $awb_info = [
            'type' => get_option('express_package_type'),
            'service_type' => get_option('express_service'),
            'cnt' => get_option('express_parcel_count'),
            'retur' => get_option('express_retur'),
            'retur_type' => get_option('express_retur_type'),
            'ramburs' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'ramburs_type' => 'cash',
            'service_135' => get_option('express_is_sat_delivery'),
            'service_134' => get_option('express_retur_signed_doc_delivery'),
            'service_137' => get_option('express_printed_awb'),
            'service_136' => get_option('express_18hr_20hr_package'),
            'insurance' => get_option('express_insurance'),
            'weight' =>  $weight,
            'content' => get_option('express_content'),
            'fragile' => get_option('express_is_fragile'),
            'payer' => get_option('express_payer'),
            'from_name' => get_option('express_name'),
            'from_contact' => get_option('express_contact_person'),
            'from_phone' => get_option('express_phone'),
            'from_email' => get_option('express_email'),
            'from_county' => get_option('express_county'),
            'from_city' => get_option('express_city'),
            'from_address' => get_option('express_address'),
            'from_zipcode' => get_option('express_postcode'),
            'to_name' =>  empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'to_contact' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : '',
            'to_phone' => $order->get_billing_phone(),
            'to_email' => $order->get_billing_email(),
            'to_county' => $recipient_address_state_id,
            'to_city' => $order->get_shipping_city(),
            'to_address' => $order->get_shipping_address_1().' '.$order->get_shipping_address_2(),
            'to_zipcode' => $postcode,
        ];        

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Express', $order);

        $_POST['awb'] = $awb_info;

        require_once(plugin_dir_path(__FILE__) . '/templates/generate-awb-page.php');
    }

    function express_add_meta_box() 
    {
        $screens = array( 'shop_order' );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'express_sectionid',
                __( 'Express - AWB', 'safealternative_express' ),
                array( $this, 'express_meta_box_callback' ),
                $screen,
                'side'
            );
        }
    }

    function express_meta_box_callback( $post ) 
    {
        $awb = get_post_meta($post->ID, 'awb_express', 'true');

        echo '<style>.express_secondary_button{border-color:#f44336!important;color:#f44336!important}.express_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://app.expressexpress.ro/express/Main?tracking=true&appcont=500&onlyCodes=false&awbno='.$awb.'" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button express_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-express&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
        }
    }
    
    /////////////////////////////////////////////////////////
    /////// ADD T&T COLUMNS  ////////////////////////////////
    /////////////////////////////////////////////////////////
           	 
	/////////////////////////////////////////////////////
	function add_custom_columns_to_orders_table($columns) 
    {
		$new_columns = $columns;
		
		if (! is_array ( $columns )) {
			$columns = array ();
		}
		
		$new_columns ['Express_AWB'] = 'Express Courier';
		
		return $new_columns;
	}
	
	////////////////////////////////////////////////
	function get_custom_columns_values($column) 
    {
		global $post;
		
		if ($column == 'Express_AWB') {
            $awb = get_post_meta($post->ID, 'awb_express', true);
            $status = get_post_meta($post->ID, 'awb_express_status', true);
            
            // avem awb 
			if (!empty($awb)) {
				$printing_link = plugin_dir_url( __FILE__ ).'download.php?&order_id='.$post->ID.'';
                echo '<a class="button tips downloadBtn" href="'.$printing_link.'" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">'.$awb.'</a></br>';
                echo '<div class="expressNoticeWrapper">';
                echo '<div class="expressNotice"><span class="dashicons dashicons-warning"></span>Status: '.$status.'</div>';
                echo '</div>';
			}
			// nu avem awb
			else {
                $current_url_generate = esc_url(add_query_arg(array(
                    'generate_awb_express' => absint($post->ID)
                )));

                echo '<p><a class="button generateBtn tips" data-tip="'.__('Genereaza AWB ExpressCourier', 'safealternative-plugin').'"
                        href="'.$current_url_generate.'"><img src="'.plugin_dir_url(__FILE__).'assets/images/logo_express.png'.'" style="height: 29px;"/></a>
                    </p>';   
			}
		}
	}
    
	// This method will generate a new awb
	static function generate_awb_express() 
    {
		$order_id = get_query_var ('generate_awb_express', NULL );
        if(empty($order_id))  {
            return null;
        }

        $awb_already_generated = get_post_meta($order_id, 'awb_express', true);
        if($awb_already_generated) {
            return null;
        } 

        $trimite_mail = get_option('express_trimite_mail');
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        $weight = 0;
        
        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
            }
        }

        if ($weight <= 1) $weight = 1;
        $weight = round($weight);

        if( empty(get_option('express_key')) ) { 
            echo '<div class="wrap"><h1>SafeAlternative Express AWB</h2><br><h2>Plugin-ul SafeAlternative Express AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=express-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }
        
        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();

        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $awb_info = [
            'type' => get_option('express_package_type'),
            'service_type' => get_option('express_service'),
            'cnt' => get_option('express_parcel_count'),
            'retur' => get_option('express_retur'),
            'retur_type' => get_option('express_retur_type'),
            'ramburs' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'ramburs_type' => 'cash',
            'insurance' => get_option('express_insurance'),
            'weight' =>  $weight,
            'service_135' => get_option('express_is_sat_delivery'),
            'service_134' => get_option('express_retur_signed_doc_delivery'),
            'service_137' => get_option('express_printed_awb'),
            'service_136' => get_option('express_18hr_20hr_package'),
            'content' => get_option('express_content'),
            'fragile' => get_option('express_is_fragile'),
            'payer' => get_option('express_payer'),
            'from_name' => get_option('express_name'),
            'from_contact' => get_option('express_contact_person'),
            'from_phone' => get_option('express_phone'),
            'from_email' => get_option('express_email'),
            'from_county' => get_option('express_county'),
            'from_city' => get_option('express_city'),
            'from_address' => get_option('express_address'),
            'from_zipcode' => get_option('express_postcode'),
            'to_name' =>  empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'to_contact' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : '',
            'to_phone' => $order->get_billing_phone(),
            'to_email' => $order->get_billing_email(),
            'to_county' => $recipient_address_state_id,
            'to_city' => $order->get_shipping_city(),
            'to_address' => $order->get_shipping_address_1().' '.$order->get_shipping_address_2(),
            'to_zipcode' => $postcode,
        ];        
        
        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Express', $order);

        $courier = new SafealternativeExpressClass();

        if($awb_info['retur'] == 'false'){
            unset($awb_info['retur_type']);
        }

        $result = $courier->callMethod("generateAwb", $awb_info, 'POST');
        
        if ($result['status']!="200") {
            set_transient('express_account_settings', json_decode($result['message']), MONTH_IN_SECONDS);
        } else {
            $message = json_decode($result['message']);
            
            if ( !empty($message->error) ) {
                set_transient('express_account_settings', $message->error->message, MONTH_IN_SECONDS);
            } else {
                $awb = $message->awb;
                
                if ($trimite_mail=='da') {
                    ExpressAWB::send_mails($order_id, $awb, $awb_info['recipient_email']);
                }
                
                update_post_meta($order_id, 'awb_express', $awb);
                update_post_meta($order_id, 'awb_express_status', 'Inregistrat');
                do_action('safealternative_awb_generated', 'express', $awb);

                $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');
                $account_status = json_decode($account_status_response['message']);

                if($account_status->show_message){
                    set_transient( 'express_account_status', $account_status->message, MONTH_IN_SECONDS );
                } else {
                    delete_transient( 'express_account_status' );
                }                       
            }
        }            
	}// end function
 
    static public function send_mails($idOrder, $awb, $receiver_email) 
    {
        $sender_email = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template = get_option('express_email_template');
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.$sender_email.'>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option ( 'express_subiect_mail' ), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_express_email', $email_content, $data);
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

    public function autogenerate_express_awb($order_id, $old_status, $new_status)
    {
        if(get_option('express_auto_generate_awb') != "da") return;
        if($new_status !== 'processing') return; 

        set_query_var('generate_awb_express', $order_id);
        ExpressAWB::generate_awb_express();
    }    
}
// end class

require_once(plugin_dir_path(__FILE__) . '/express.class.php');
require_once(plugin_dir_path(__FILE__) . '/cron.php');

//Bulk generate
add_action( 'admin_footer', 'add_bulk_action_express');
function add_bulk_action_express()
{
	global $post_type;

	if ( 'shop_order' == $post_type ) {	
        wp_enqueue_script( 'bulk_admin_js_express', plugin_dir_url(__FILE__).'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');    
	}
}
    
////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action ( 'woocommerce_order_details_after_order_table_items', function ( $order ) 
{
	$order_id = $order->get_order_number();
    $awb = get_post_meta($order_id, 'awb_express', true);
    if ($awb) echo 'Nota de transport (AWB) are numarul: ' . $awb . ' si poate fi urmarita aici: <a href="https://app.couriermanager.eu/cscourier/Main?tracking=true&appcont=1311&onlyCodes=false&awbno='.$awb.'" target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter ( 'query_vars', function ($vars) {
	$vars [] = "generate_awb_express";
	return $vars;
});

add_action('admin_notices', array('ExpressAWB', 'generate_awb_express'), true);

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_express', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);

add_action( 'init', function() {
    add_filter( 'pre_update_option_express_username', function($new_val, $old_val) {
        if ($old_val != $new_val) {
            delete_transient('express_sender_list');
        }
        return $new_val;
    }, 10, 2 );
} );