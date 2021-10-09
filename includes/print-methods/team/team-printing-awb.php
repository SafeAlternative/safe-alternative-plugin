<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir.'courier.class.php');

class TeamAWB 
{
    public function __construct() 
    {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_menu', array( $this, 'add_plugin_page_settings' ) );
        add_action( 'add_meta_boxes', array( $this, 'team_add_meta_box' ) );
        add_action( 'admin_init', array( $this, 'add_register_setting' ));
        
        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action( 'woocommerce_order_status_changed', array(
            $this,
            'autogenerate_team_awb'
        ), 99, 3);    
    
        add_filter ( 'manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table') , 11 );
        add_action ( 'manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values') , 2 );	
    }

    function add_plugin_page_settings()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Team - AWB',
            'Team - AWB',
            'manage_woocommerce',
            'team-plugin-setting',
            array(
                $this,
                'team_plugin_page'
            )
        );     
    }

    function add_register_setting() 
    {
        add_option('team_package_type', 'package');
        add_option('team_key', '');
        add_option('team_retur', 'false');
        add_option('team_parcel_count', '1');
        add_option('team_retur_type', 'document');
        add_option('team_insurance', '');
        add_option('team_payer', 'expeditor');
        add_option('team_name', '');
        add_option('team_contact_person', '');
        add_option('team_phone', '');
        add_option('team_email', '');
        add_option('team_county', '');
        add_option('team_city', '');
        add_option('team_address', '');
        add_option('team_postcode', '');
        add_option('team_content', '');
        add_option('team_sender_id', '');
        add_option('team_service', 'Standard');
        add_option('team_parcel_count', '1');
        add_option('team_open_package', 'false');
        add_option('team_sat_delivery', 'false');
        add_option('team_tax_urgent_express', 'false');
        add_option('team_change_delivery_address', 'false');
        add_option('team_special_delivery_hour', 'false');
        add_option('team_swap_package', 'false');
        add_option('team_retur_delivery_confirmation', 'false');
        add_option('team_retur_documents', 'false');
        add_option('team_3rd_national_delivery', 'false');
        add_option('team_retur_expedition_undelivered_package', 'false');
        add_option('team_awb_by_delivery_agent', 'false');
        add_option('team_labeling_package_with_awb', 'false');
        add_option('team_multiple_packages', 'false');
        add_option('team_is_fragile','false');
        add_option('team_trimite_mail', 'nu');
        add_option('team_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('team_page_type', 'default');
        add_option('team_auto_generate_awb', 'nu');
        add_option('team_auto_mark_complete', 'nu');

        register_setting('team-plugin-settings', 'team_package_type' );
        register_setting('team-plugin-settings', 'team_key' );
        register_setting('team-plugin-settings', 'team_retur' );
        register_setting('team-plugin-settings', 'team_parcel_count' );
        register_setting('team-plugin-settings', 'team_retur_type' );
        register_setting('team-plugin-settings', 'team_insurance' );
        register_setting('team-plugin-settings', 'team_payer' );
        register_setting('team-plugin-settings', 'team_name' );
        register_setting('team-plugin-settings', 'team_contact_person' );
        register_setting('team-plugin-settings', 'team_phone' );
        register_setting('team-plugin-settings', 'team_email' );
        register_setting('team-plugin-settings', 'team_county' );
        register_setting('team-plugin-settings', 'team_city' );
        register_setting('team-plugin-settings', 'team_address' );
        register_setting('team-plugin-settings', 'team_postcode' );
        register_setting('team-plugin-settings', 'team_content' );
        register_setting('team-plugin-settings', 'team_sender_id' );
        register_setting('team-plugin-settings', 'team_service' );
        register_setting('team-plugin-settings', 'team_open_package' );
        register_setting('team-plugin-settings', 'team_sat_delivery' );
        register_setting('team-plugin-settings', 'team_tax_urgent_express' );
        register_setting('team-plugin-settings', 'team_change_delivery_address' );
        register_setting('team-plugin-settings', 'team_special_delivery_hour' );
        register_setting('team-plugin-settings', 'team_swap_package' );
        register_setting('team-plugin-settings', 'team_retur_delivery_confirmation' );
        register_setting('team-plugin-settings', 'team_retur_documents' );
        register_setting('team-plugin-settings', 'team_3rd_national_delivery' );
        register_setting('team-plugin-settings', 'team_retur_expedition_undelivered_package' );
        register_setting('team-plugin-settings', 'team_awb_by_delivery_agent' );
        register_setting('team-plugin-settings', 'team_labeling_package_with_awb' );
        register_setting('team-plugin-settings', 'team_multiple_packages' );
        register_setting('team-plugin-settings', 'team_trimite_mail' );
        register_setting('team-plugin-settings', 'team_subiect_mail' );
        register_setting('team-plugin-settings', 'team_page_type' );
        register_setting('team-plugin-settings', 'team_auto_generate_awb' );
        register_setting('team-plugin-settings', 'team_auto_mark_complete' );

        require_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message_status = get_transient('team_account_status')) && $qv === "shop_order") {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_status, 'safealternative-team-woocommerce'); ?></p>
            </div>
            <?php
        }

        if (($message_settings = get_transient('team_account_settings'))) {
            ?>
            <div class="notice notice-warning">
                <p><?php _e($message_settings, 'safealternative-team-woocommerce'); ?></p>
            </div>
            <?php
            delete_transient('team_account_settings');
        }    
    }

    function team_plugin_page() 
    {
        require_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            null,
            'Genereaza AWB Team',
            'Genereaza AWB Team',
            'manage_woocommerce',
            'generate-awb-team',
            array($this, 'create_admin_page'),
            null
        );        
    }

    public function create_admin_page()
    {       
        if (!isset($_GET['order_id'])) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }
        
        $awb_already_generated = get_post_meta($_GET['order_id'], 'awb_team', 1);
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

        if( empty(get_option('team_key')) ) { 
            echo '<div class="wrap"><h1>SafeAlternative Team AWB</h2><br><h2>Plugin-ul SafeAlternative Team AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=team-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();

        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $awb_info = [
            'type' => get_option('team_package_type'),
            'service_type' => get_option('team_service'),
            'cnt' => get_option('team_parcel_count'),
            'retur' => get_option('team_retur'),
            'retur_type' => get_option('team_retur_type'),
            'ramburs' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'ramburs_type' => 'cash',
            'service_41' => get_option('team_open_package'),
            'service_42' => get_option('team_sat_delivery'),
            'service_51' => get_option('team_tax_urgent_express'),
            'service_62' => get_option('team_change_delivery_address'),
            'service_63' => get_option('team_special_delivery_hour'),
            'service_64' => get_option('team_swap_package'),
            'service_66' => get_option('team_retur_delivery_confirmation'),
            'service_67' => get_option('team_retur_documents'),
            'service_73' => get_option('team_3rd_national_delivery'),
            'service_84' => get_option('team_retur_expedition_undelivered_package'),
            'service_104' => get_option('team_awb_by_delivery_agent'),
            'service_108' => get_option('team_labeling_package_with_awb'),
            'service_292' => get_option('team_multiple_packages'),
            'insurance' => get_option('team_insurance'),
            'weight' =>  $weight,
            'content' => get_option('team_content'),
            'fragile' => get_option('team_is_fragile'),
            'payer' => get_option('team_payer'),
            'from_name' => get_option('team_name'),
            'from_contact' => get_option('team_contact_person'),
            'from_phone' => get_option('team_phone'),
            'from_email' => get_option('team_email'),
            'from_county' => get_option('team_county'),
            'from_city' => get_option('team_city'),
            'from_address' => get_option('team_address'),
            'from_zipcode' => get_option('team_postcode'),
            'to_name' =>  empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'to_contact' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : '',
            'to_phone' => $order->get_billing_phone(),
            'to_email' => $order->get_billing_email(),
            'to_county' => $recipient_address_state_id,
            'to_city' => $order->get_shipping_city(),
            'to_address' => $order->get_shipping_address_1().' '.$order->get_shipping_address_2(),
            'to_zipcode' => $postcode,
        ];        

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Team', $order);

        $_POST['awb'] = $awb_info;

        require_once(plugin_dir_path(__FILE__) . '/templates/generate-awb-page.php');
    }

    function team_add_meta_box() 
    {
        $screens = array( 'shop_order' );

        foreach ( $screens as $screen ) {
            add_meta_box(
                'team_sectionid',
                __( 'Team - AWB', 'safealternative_team' ),
                array( $this, 'team_meta_box_callback' ),
                $screen,
                'side'
            );
        }
    }

    function team_meta_box_callback( $post ) 
    {
        $awb = get_post_meta($post->ID, 'awb_team', 'true');

        echo '<style>.team_secondary_button{border-color:#f44336!important;color:#f44336!important}.team_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://app.teamteam.ro/team/Main?tracking=true&appcont=500&onlyCodes=false&awbno='.$awb.'" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button team_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-team&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
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
		
		$new_columns ['Team_AWB'] = 'Team Courier';
		
		return $new_columns;
	}
	
	////////////////////////////////////////////////
	function get_custom_columns_values($column) 
    {
		global $post;
		
		if ($column == 'Team_AWB') {
            $awb = get_post_meta($post->ID, 'awb_team', true);
            $status = get_post_meta($post->ID, 'awb_team_status', true);
            
            // avem awb 
			if (!empty($awb)) {
				$printing_link = plugin_dir_url( __FILE__ ).'download.php?&order_id='.$post->ID.'';
                echo '<a class="button tips downloadBtn" href="'.$printing_link.'" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">'.$awb.'</a></br>';
                echo '<div class="teamNoticeWrapper">';
                echo '<div class="teamNotice"><span class="dashicons dashicons-warning"></span>Status: '.$status.'</div>';
                echo '</div>';
			}
			// nu avem awb
			else {
                $current_url_generate = esc_url(add_query_arg(array(
                    'generate_awb_team' => absint($post->ID)
                )));

                echo '<p><a class="button generateBtn tips" data-tip="'.__('Genereaza AWB TeamCourier', 'safealternative-plugin').'"
                        href="'.$current_url_generate.'"><img src="'.plugin_dir_url(__FILE__).'assets/images/logo_team.png'.'" style="height: 29px;"/></a>
                    </p>';   
			}
		}
	}
    
	// This method will generate a new awb
	static function generate_awb_team() 
    {
		$order_id = get_query_var ('generate_awb_team', NULL );
        if(empty($order_id))  {
            return null;
        }

        $awb_already_generated = get_post_meta($order_id, 'awb_team', true);
        if($awb_already_generated) {
            return null;
        } 

        $trimite_mail = get_option('team_trimite_mail');
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

        if( empty(get_option('team_key')) ) { 
            echo '<div class="wrap"><h1>SafeAlternative Team AWB</h2><br><h2>Plugin-ul SafeAlternative Team AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=team-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();

        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $awb_info = [
            'type' => get_option('team_package_type'),
            'service_type' => get_option('team_service'),
            'cnt' => get_option('team_parcel_count'),
            'retur' => get_option('team_retur'),
            'retur_type' => get_option('team_retur_type'),
            'ramburs' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'ramburs_type' => 'cash',
            'insurance' => get_option('team_insurance'),
            'weight' =>  $weight,
            'service_41' => get_option('team_open_package'),
            'service_42' => get_option('team_sat_delivery'),
            'service_51' => get_option('team_tax_urgent_express'),
            'service_62' => get_option('team_change_delivery_address'),
            'service_63' => get_option('team_special_delivery_hour'),
            'service_64' => get_option('team_swap_package'),
            'service_66' => get_option('team_retur_delivery_confirmation'),
            'service_67' => get_option('team_retur_documents'),
            'service_73' => get_option('team_3rd_national_delivery'),
            'service_84' => get_option('team_retur_expedition_undelivered_package'),
            'service_104' => get_option('team_awb_by_delivery_agent'),
            'service_108' => get_option('team_labeling_package_with_awb'),
            'service_292' => get_option('team_multiple_packages'),
            'content' => get_option('team_content'),
            'fragile' => get_option('team_is_fragile'),
            'payer' => get_option('team_payer'),
            'from_name' => get_option('team_name'),
            'from_contact' => get_option('team_contact_person'),
            'from_phone' => get_option('team_phone'),
            'from_email' => get_option('team_email'),
            'from_county' => get_option('team_county'),
            'from_city' => get_option('team_city'),
            'from_address' => get_option('team_address'),
            'from_zipcode' => get_option('team_postcode'),
            'to_name' =>  empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'to_contact' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : '',
            'to_phone' => $order->get_billing_phone(),
            'to_email' => $order->get_billing_email(),
            'to_county' => $recipient_address_state_id,
            'to_city' => $order->get_shipping_city(),
            'to_address' => $order->get_shipping_address_1().' '.$order->get_shipping_address_2(),
            'to_zipcode' => $postcode,
        ];        
        
        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Team', $order);

        $courier = new SafealternativeTeamClass();

        if($awb_info['retur'] == 'false'){
            unset($awb_info['retur_type']);
        }

        $result = $courier->callMethod("generateAwb", $awb_info, 'POST');

        if ($result['status']!="200") {
            set_transient('team_account_settings', json_decode($result['message']), MONTH_IN_SECONDS);
        } else {
            $message = json_decode($result['message']);
            
            if ( !empty($message->error) ) {
                set_transient('team_account_settings', $message->error->message, MONTH_IN_SECONDS);
            } else {
                $awb = $message->awb;
                
                if ($trimite_mail=='da') {
                    TeamAWB::send_mails($order_id, $awb, $awb_info['recipient_email']);
                }
                
                update_post_meta($order_id, 'awb_team', $awb);
                update_post_meta($order_id, 'awb_team_status', 'Inregistrat');
                do_action('safealternative_awb_generated', 'team', $awb);

                $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');
                $account_status = json_decode($account_status_response['message']);

                if($account_status->show_message){
                    set_transient( 'team_account_status', $account_status->message, MONTH_IN_SECONDS );
                } else {
                    delete_transient( 'team_account_status' );
                }                       
            }
        }            
	}// end function
 
    static public function send_mails($idOrder, $awb, $receiver_email) 
    {
        $sender_email = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template = get_option('team_email_template');
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.$sender_email.'>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option ( 'team_subiect_mail' ), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_team_email', $email_content, $data);
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

    public function autogenerate_team_awb($order_id, $old_status, $new_status)
    {
        if(get_option('team_auto_generate_awb') != "da") return;
        if($new_status !== 'processing') return; 

        set_query_var('generate_awb_team', $order_id);
        TeamAWB::generate_awb_team();
    }    
}
// end class

require_once(plugin_dir_path(__FILE__) . '/team.class.php');
require_once(plugin_dir_path(__FILE__) . '/cron.php');

//Bulk generate
add_action( 'admin_footer', 'add_bulk_action_team');
function add_bulk_action_team()
{
	global $post_type;

	if ( 'shop_order' == $post_type ) {	
        wp_enqueue_script( 'bulk_admin_js_team', plugin_dir_url(__FILE__).'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');    
	}
}
    
////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action ( 'woocommerce_order_details_after_order_table_items', function ( $order ) 
{
	$order_id = $order->get_order_number();
    $awb = get_post_meta($order_id, 'awb_team', true);
    if ($awb) echo 'Nota de transport (AWB) are numarul: ' . $awb . ' si poate fi urmarita aici: <a href="https://app.couriermanager.eu/cscourier/Main?tracking=true&appcont=1311&onlyCodes=false&awbno='.$awb.'" target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter ( 'query_vars', function ($vars) {
	$vars [] = "generate_awb_team";
	return $vars;
});

add_action('admin_notices', array('TeamAWB', 'generate_awb_team'), true);

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_team', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);

add_action( 'init', function() {
    add_filter( 'pre_update_option_team_username', function($new_val, $old_val) {
        if ($old_val != $new_val) {
            delete_transient('team_sender_list');
        }
        return $new_val;
    }, 10, 2 );
} );