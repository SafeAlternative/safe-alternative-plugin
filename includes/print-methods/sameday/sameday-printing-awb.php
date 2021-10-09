<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir.'courier.class.php');

class SamedayAWB 
{
    private static $countiesList;

    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page_settings' ) );
        add_action( 'add_meta_boxes', array( $this, 'sameday_add_meta_box' ) );
        add_action( 'admin_init', array( $this, 'add_register_setting' ));
        
        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action( 'woocommerce_order_status_changed', array(
            $this,
            'autogenerate_sameday_awb'
        ), 99, 3);    
    
        add_filter ( 'manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table') , 11 );
        add_action ( 'manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values') , 2 );	

        self::$countiesList = safealternative_get_counties_list();
    }

    function add_plugin_page_settings(){
        add_submenu_page(
            'safealternative-menu-content',
            'Sameday - AWB',
            'Sameday - AWB',
            'manage_woocommerce',
            'sameday-plugin-setting',
            array(
                $this,
                'sameday_plugin_page'
            )
        );     
    }

    function add_register_setting() {
        add_option('sameday_username', '');
        add_option('sameday_password', '');
        add_option('sameday_valid_auth', '0');

        add_option('sameday_package_type', '');
        add_option('sameday_pickup_point', '');
        add_option('sameday_service_id', '');
        add_option('sameday_declared_value', '');
        add_option('sameday_observation', '');
        add_option('sameday_descriere_continut', 'nu');

        add_option('sameday_default_weight', '');
        add_option('sameday_default_width', '');
        add_option('sameday_default_height', '');
        add_option('sameday_default_length', '');

        add_option('sameday_trimite_mail', 'nu');
        add_option('sameday_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('sameday_page_type', 'A4');
        add_option('sameday_auto_generate_awb', 'nu');
        add_option('sameday_auto_mark_complete', 'nu');

        register_setting('sameday-plugin-settings', 'sameday_username' );
        register_setting('sameday-plugin-settings', 'sameday_password' );
        register_setting('sameday-plugin-settings', 'sameday_valid_auth' );
        register_setting('sameday-plugin-settings', 'sameday_pickup_point' );
        register_setting('sameday-plugin-settings', 'sameday_package_type' );
        register_setting('sameday-plugin-settings', 'sameday_service_id' );
        register_setting('sameday-plugin-settings', 'sameday_declared_value' );

        register_setting('sameday-plugin-settings', 'sameday_default_weight' );
        register_setting('sameday-plugin-settings', 'sameday_default_width' );
        register_setting('sameday-plugin-settings', 'sameday_default_height' );
        register_setting('sameday-plugin-settings', 'sameday_default_length' );
        register_setting('sameday-plugin-settings', 'sameday_observation');
        register_setting('sameday-plugin-settings', 'sameday_descriere_continut');

        register_setting('sameday-plugin-settings', 'sameday_trimite_mail' );
        register_setting('sameday-plugin-settings', 'sameday_subiect_mail' );
        register_setting('sameday-plugin-settings', 'sameday_page_type' );
        register_setting('sameday-plugin-settings', 'sameday_auto_generate_awb' );
        register_setting('sameday-plugin-settings', 'sameday_auto_mark_complete' );

        require_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message_status = get_transient('sameday_account_status')) && $qv === "shop_order") {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_status, 'safealternative-sameday-woocommerce'); ?></p>
            </div>
            <?php
        }

        if (($message_settings = get_transient('sameday_account_settings'))) {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_settings, 'safealternative-sameday-woocommerce'); ?></p>
            </div>
            <?php
            delete_transient('sameday_account_settings');
        }    
    }

    function sameday_plugin_page() 
    {
        require_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            null,
            'Genereaza AWB Sameday',
            'Genereaza AWB Sameday',
            'manage_woocommerce',
            'generate-awb-sameday',
            array($this, 'create_admin_page'),
            null
        );        
    }


    public function create_admin_page()
    {
        if (!isset($_GET['order_id'])) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }
        
        $awb_already_generated = get_post_meta($_GET['order_id'], 'awb_sameday', 1);
        if($awb_already_generated) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $order = wc_get_order($_GET['order_id']);
        $items = $order->get_items();

        $package_type = get_option('sameday_package_type');
        $pickup_point = get_option('sameday_pickup_point');
        $service_id = get_option('sameday_service_id');
        $observation = get_option('sameday_observation');
        $sameday_descriere_continut = get_option('sameday_descriere_continut');

        $weight = 0;
        $height = 0;
        $width  = 0;
        $length = 0;
        
        $heightList = array();
        $lengthList = array();
        $contents = '';

        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
                $width += (int) $_product->get_width() * $item->get_quantity();
                $height = (int) $_product->get_height();
                $heightList[] = $height;
                $length = (int) $_product->get_length();
                $lengthList[] = $length;
            }
            $cod_pro = $_product->get_sku();
            if (!$cod_pro) {
                $cod_pro = $item->get_product_id();
            } 
            switch($sameday_descriere_continut) {
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
            $height = get_option('sameday_default_height') ?: 10;
        } //$height == 0
        if ($width == 0) {
            $width  = get_option('sameday_default_width') ?: 10;
        } //$width == 0
        if ($length == 0) {
            $length = get_option('sameday_default_length') ?: 10;
        } //$length == 0
        
        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        } //$weight_type == 'g'
        
        if ($weight <= 1 ) {
            $weight = get_option('sameday_default_weight') ?: 1;
        }
        $weight = round($weight);

        if( empty(get_option('sameday_username')) ) { 
            echo '<div class="wrap"><h1>SafeAlternative Sameday AWB</h2><br><h2>Plugin-ul SafeAlternative Sameday AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=sameday-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $cod_value = ($order->get_payment_method() == 'cod') ? $order->get_total() : 0;
        
        $awb_info = [
            'pickup_point' => $pickup_point,
            'package_type' => $package_type,
            'service_id' => $service_id,
            'observation' => $observation,
            'priceObservation' => $contents,
            'width' => $width,
            'height' => $height,
            'length' => $length,
            'weight' => $weight,
            'city' => $order->get_shipping_city(),
            'state' => self::$countiesList[$order->get_shipping_state()],
            'address' => "{$order->get_shipping_address_1()} {$order->get_shipping_address_2()}",
            'name' => "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}",
            'phone' => $order->get_billing_phone(),
            'email' => $order->get_billing_email(),
            'company' => $order->get_shipping_company() ?? '',
            'declared_value' => get_option('sameday_declared_value') ?: 0,
            'cod_value' => $cod_value,
            'lockerId' => get_post_meta($order->get_id(), 'safealternative_sameday_lockers', true)
        ];

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Sameday', $order);

        $_POST['awb'] = $awb_info;
            
        require_once(plugin_dir_path(__FILE__) . '/templates/generate-awb-page.php');
    }

    function sameday_add_meta_box() {

        $screens = array( 'shop_order' );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'sameday_sectionid',
                __( 'Sameday - AWB', 'safealternative_sameday' ),
                array( $this, 'sameday_meta_box_callback' ),
                $screen,
                'side'
            );
        }
    }

    function sameday_meta_box_callback( $post ) {
        $awb = get_post_meta($post->ID, 'awb_sameday', true);

        echo '<style>.sameday_secondary_button{border-color:#f44336!important;color:#f44336!important}.sameday_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://sameday.ro/#awb='.$awb.'" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button sameday_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-sameday&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
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
		
		$new_columns ['Sameday_AWB'] = 'Sameday';
		
		return $new_columns;
	}
	
	////////////////////////////////////////////////
	function get_custom_columns_values($column) {
		global $post;
		
		if ($column == 'Sameday_AWB') {
            $awb = get_post_meta($post->ID, 'awb_sameday', true);
            $status = get_post_meta($post->ID, 'awb_sameday_status', true);
            
            // avem awb 
			if (!empty($awb)) {
				$printing_link = plugin_dir_url( __FILE__ ).'download.php?&order_id='.$post->ID.'';
                echo '<a class="button tips downloadBtn" href="'.$printing_link.'" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">'.$awb.'</a></br>';
                echo '<div class="samedayNoticeWrapper">';
                echo '<div class="samedayNotice"><span class="dashicons dashicons-warning"></span>Status: '.$status.'</div>';
                echo '</div>';
			}
			// nu avem awb
			else {
                $current_url_generate = esc_url(add_query_arg(array(
                    'generate_awb_sameday' => absint($post->ID)
                )));

                echo '<p><a class="button generateBtn tips" data-tip="'.__('Genereaza AWB Sameday', 'safealternative-plugin').'"
                        href="'.$current_url_generate.'"><img src="'.plugin_dir_url(__FILE__).'assets/images/sameday.png'.'" style="height: 31px;"/></a>
                    </p>';   
			}
		}
	}
    
	// This method will generate a new awb
	static function generate_awb_sameday() {
		$order_id = get_query_var ('generate_awb_sameday', NULL );
        if(empty($order_id))  {
            return null;
        }

        $awb_already_generated = get_post_meta($order_id, 'awb_sameday', true);
        if($awb_already_generated) {
            return null;
        } 

        $trimite_mail = get_option('sameday_trimite_mail');
        $order = wc_get_order($order_id);
        $items = $order->get_items();
        $package_type = get_option('sameday_package_type');
        $pickup_point = get_option('sameday_pickup_point');
        $service_id = get_option('sameday_service_id');
        $observation = get_option('sameday_observation');
        $sameday_descriere_continut = get_option('sameday_descriere_continut');

        $weight = 0;
        $height = 0;
        $width  = 0;
        $length = 0;
        
        $heightList = array();
        $lengthList = array();
        $contents = '';

        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
                $width += (int) $_product->get_width() * $item->get_quantity();
                $height = (int) $_product->get_height();
                $heightList[] = $height;
                $length = (int) $_product->get_length();
                $lengthList[] = $length;
            }
            $cod_pro = $_product->get_sku();
            if (!$cod_pro) {
                $cod_pro = $item->get_product_id();
            } 
            switch($sameday_descriere_continut) {
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
            $height = get_option('sameday_default_height') ?: 10;
        } //$height == 0
        if ($width == 0) {
            $width  = get_option('sameday_default_width') ?: 10;
        } //$width == 0
        if ($length == 0) {
            $length = get_option('sameday_default_length') ?: 10;
        } //$length == 0
        
        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        } //$weight_type == 'g'
        
        if ($weight <= 1 ) {
            $weight = get_option('sameday_default_weight') ?: 1;
        }
        $weight = round($weight);

        if( empty(get_option('sameday_username')) ) { 
            echo '<div class="notice notice-error"><h2>Plugin-ul SafeAlternative Sameday AWB nu a fost configurat.</h2> <p>Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=sameday-plugin-setting').'"> aici</a> pentru a il configura.</p></div>';
            return;
        }

        $cod_value = ($order->get_payment_method() == 'cod') ? $order->get_total() : 0;
        $lockers = get_post_meta($order->get_id(),'safealternative_sameday_lockers',true);
        
        $awb_info = [
            'pickup_point' => $pickup_point,
            'package_type' => $package_type,
            'service_id' => empty($lockers) ? $service_id : '15',
            'observation' => $observation,
            'priceObservation' => $contents,
            'width' => $width,
            'height' => $height,
            'length' => $length,
            'weight' => $weight,
            'city' => $order->get_shipping_city(),
            'state' => self::$countiesList[$order->get_shipping_state()],
            'address' => "{$order->get_shipping_address_1()} {$order->get_shipping_address_2()}",
            'name' => "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}",
            'phone' => $order->get_billing_phone(),
            'email' => $order->get_billing_email(),
            'company' => $order->get_shipping_company() ?? '',
            'declared_value' => get_option('sameday_declared_value') ?: 0,
            'cod_value' => $cod_value,
            'lockerId' => $lockers
        ];

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Sameday', $order);
        
        $courier = new SafealternativeSamedayClass();
        $result = $courier->callMethod("generateAwb", $awb_info, 'POST');

        if ($result['status']!="200") {
            set_transient('sameday_account_settings', json_decode($result['message']), MONTH_IN_SECONDS);
        } else {
            $message = json_decode($result['message']);
            
            if ( !empty($message->error) ) {
                set_transient('sameday_account_settings', $message->error, MONTH_IN_SECONDS);
            } else {
                $awb = $message->id;
                if ($trimite_mail=='da' && !empty($awb_info['email'])) {
                    SamedayAWB::send_mails($order_id, $awb, $awb_info['email']);
                }
                
                update_post_meta($order_id, 'awb_sameday', $awb);
                update_post_meta($order_id, 'awb_sameday_status', 'Inregistrat');
                do_action('safealternative_awb_generated', 'Sameday', $awb, $order_id);

                $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');
                $account_status = json_decode($account_status_response['message']);

                if($account_status->show_message){
                    set_transient( 'sameday_account_status', $account_status->message, MONTH_IN_SECONDS );
                } else {
                    delete_transient( 'sameday_account_status' );
                }                       
            }
        }            
	}// end function

 
    static public function send_mails($idOrder, $awb, $receiver_email) {
        $sender_email    = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template  = get_option('sameday_email_template');
        $headers         = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.$sender_email.'>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option ( 'sameday_subiect_mail' ), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_sameday_email', $email_content, $data);
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

    public function autogenerate_sameday_awb($order_id, $old_status, $new_status){
        if(get_option('sameday_auto_generate_awb') != "da") return;
        if($new_status !== 'processing') return; 

        set_query_var('generate_awb_sameday', $order_id);
        SamedayAWB::generate_awb_sameday();
    }    
}
// end class

require_once(plugin_dir_path(__FILE__) . '/sameday.class.php');
require_once(plugin_dir_path(__FILE__) . '/cron.php');

//Bulk generate
add_action( 'admin_footer', 'add_bulk_action_sameday');
function add_bulk_action_sameday() {
	global $post_type;

	if ( 'shop_order' == $post_type ) {	
        wp_enqueue_script( 'bulk_admin_js_sameday', plugin_dir_url(__FILE__).'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');    
	}
}
    

////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action ( 'woocommerce_order_details_after_order_table_items', function ( $order ) {
	$order_id = $order->get_order_number();
	$awb = get_post_meta($order_id, 'awb_sameday', true);	
	if($awb) echo 'Nota de transport (AWB) are numarul: '.$awb.' si poate fi urmarita aici: <a href="https://sameday.ro/#awb='.$awb.'" target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter ( 'query_vars', function ($vars) {
	$vars [] = "generate_awb_sameday";
	return $vars;
});

add_action ( 'admin_notices', array ('SamedayAWB', 'generate_awb_sameday'), 1 );

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_sameday', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);

add_action( 'init', function() {
    add_filter( 'pre_update_option_sameday_username', function($new_val, $old_val) {
        if ($old_val != $new_val) {
            delete_transient('sameday_pickup_points');
            delete_transient('sameday_services');
        }
        return $new_val;
    }, 10, 2 );

    add_filter( 'pre_update_option_sameday_password', function($new_val, $old_val) {
        if ($old_val != $new_val) {
            delete_transient('sameday_pickup_points');
            delete_transient('sameday_services');
        }
        return $new_val;
    }, 10, 2 );
} );