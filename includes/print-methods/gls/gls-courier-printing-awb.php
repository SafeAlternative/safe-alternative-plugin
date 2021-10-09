<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

class GLSGenereazaAWB
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page_generate_GLS'));
        add_action('admin_menu', array($this, 'add_plugin_page_settings_GLS'));
        add_action('add_meta_boxes', array($this, 'add_meta_box_awb_GLS'));
        add_action('admin_init', array($this, 'add_register_setting_GLS'));

        add_action( 'admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action( 'woocommerce_order_status_changed', array(
            $this,
            'autogenerate_gls_awb'
        ), 99, 3);    

        ////INCLUDE TRACK&TRACE COLUMS /////////////////////////////////////////////////////
        add_filter('manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table_GLS'), 10);
        add_action('manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values_GLS'), 6);


        add_action( 'admin_init', array($this, 'add_other_gls_sender' ));
        add_action( 'admin_init', array($this, 'remove_other_gls_sender' ));
    }

    public function add_plugin_page_settings_GLS()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'GLS - AWB',
            'GLS - AWB',
            'manage_woocommerce',
            'GLS-plugin-setting',
            array(
                $this,
                'GLS_plugin_page'
            )
        );
    }

    public function add_plugin_page_generate_GLS()
    {
        add_submenu_page(
            null,
            'SafeAlternative GLS',
            'SafeAlternative GLS',
            'manage_woocommerce',
            'generate-awb-GLS',
            array($this, 'create_admin_page'),
            null
        );
    }

    public function add_register_setting_GLS()
    {
        add_option('GLS_user', '');
        add_option('GLS_password', '');
        add_option('GLS_senderid', '');

        add_option('GLS_sender_name', '');
        add_option('GLS_sender_address', '');
        add_option('GLS_sender_city', '');
        add_option('GLS_sender_zipcode', '');
        add_option('GLS_sender_contact', '');
        add_option('GLS_sender_phone', '');
        add_option('GLS_sender_email', '');
        add_option('GLS_trimite_mail', 'nu');
        add_option('GLS_show_content', 'nu');
        add_option('GLS_show_client_note', '0');
        add_option('GLS_show_order_id', '0');

        add_option('GLS_observatii', '');
        add_option('GLS_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('GLS_printertemplate', 'A4_2x2');

        add_option('GLS_pcount', '1');
        add_option('GLS_services', '');
        add_option('GLS_other_senders', '');
        add_option('GLS_auto_generate_awb', 'nu');
        add_option('GLS_auto_mark_complete', 'nu');

        register_setting('GLS-plugin-settings', 'GLS_user');
        register_setting('GLS-plugin-settings', 'GLS_password');
        register_setting('GLS-plugin-settings', 'GLS_senderid');

        register_setting('GLS-plugin-settings', 'GLS_sender_name');
        register_setting('GLS-plugin-settings', 'GLS_sender_address');
        register_setting('GLS-plugin-settings', 'GLS_sender_city');
        register_setting('GLS-plugin-settings', 'GLS_sender_zipcode');
        register_setting('GLS-plugin-settings', 'GLS_sender_contact');
        register_setting('GLS-plugin-settings', 'GLS_sender_phone');
        register_setting('GLS-plugin-settings', 'GLS_sender_email');
        register_setting('GLS-plugin-settings', 'GLS_observatii');
        register_setting('GLS-plugin-settings', 'GLS_trimite_mail');
        register_setting('GLS-plugin-settings', 'GLS_show_content');
        register_setting('GLS-plugin-settings', 'GLS_show_client_note');
        register_setting('GLS-plugin-settings', 'GLS_show_order_id');
        register_setting('GLS-plugin-settings', 'GLS_subiect_mail');
        register_setting('GLS-plugin-settings', 'GLS_printertemplate');
        register_setting('GLS-plugin-settings', 'GLS_pcount');
        register_setting('GLS-plugin-settings', 'GLS_services');
        register_setting('GLS-plugin-settings', 'GLS_auto_generate_awb');
        register_setting('GLS-plugin-settings', 'GLS_auto_mark_complete');

        include_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    public function GLS_plugin_page()
    {
        include_once(plugin_dir_path(__FILE__) . '/templates/settings_page.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if( ($message = get_transient( 'gls_account_status' )) && $qv === "shop_order" ){
            ?>
            <div class="notice notice-warning">
                <p><?php _e( $message, 'safealternative-gls-woocommerce' ); ?></p>
            </div>
            <?php
        }
    }

    /**
     * Options page callback
     */
    public function create_admin_page()
    {

        if (!isset($_GET['order_id'])) {
            echo 'Generarea awb-ulul se face din Woocommerce => Orders => View Orders';
        }

        $GLS_user = get_option('GLS_user');
        if (empty($GLS_user)) {
            echo '<div class="wrap"><h1>SafeAlternative GLS AWB</h2><br><h2>Plugin-ul SafeAlternative GLS AWB nu a fost configurat.</h2> Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=GLS-plugin-setting').'"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $dir = plugin_dir_path(__FILE__);
        include_once($dir . 'courier.class.php');

        $awb_details = self::getAwbDetails(1);

        include_once(plugin_dir_path(__FILE__) . '/templates/generate_awb_page.php');
    }


    ///////////////////////////////////////////////
    public function add_meta_box_awb_GLS()
    {
        $screens = array('shop_order');

        foreach ($screens as $screen) {
            add_meta_box(
                'GLSawb_sectionid',
                __('GLS - AWB', 'safealternative-plugin'),
                array($this, 'GLSawb_meta_box_callback'),
                $screen,
                'side'
            );
        }
    }

    public function GLSawb_meta_box_callback($post)
    {
        $awb = get_post_meta($post->ID, 'awb_GLS', true);

        echo '<style>.gls_secondary_button{border-color:#f44336!important;color:#f44336!important}.gls_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://gls-group.eu/RO/ro/urmarire-colet?match='.$awb.'" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button gls_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-GLS&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
        }
    }


    /////////////////////////////////////////////////////                
    /////////CURIER FIELDS//////////////// //////////////                       	
    /////////////////////////////////////////////////////
    public function add_custom_columns_to_orders_table_GLS($columns)
    {
        $new_columns = $columns;

        if (!is_array($columns)) {
            $columns = array();
        }

        $new_columns['GLS_AWB'] = 'GLS';

        return $new_columns;
    }

    ////////////////////////////////////////////////
    public function get_custom_columns_values_GLS($column)
    {
        global $post;

        if ($column == 'GLS_AWB') {
            $awb        = get_post_meta($post->ID, 'awb_GLS');
            $status     = get_post_meta($post->ID, 'awb_GLS_status');

            if (!empty($awb[0]) && !is_array($awb[0])) {
                $printing_link = plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '';
                echo '<a class="button tips downloadBtn" href="' . $printing_link . '" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">' . $awb[0] . '</a></br>';

                if (!empty($status[0])) {
                    echo '<div class="glsNoticeWrapper">';
                    echo '<div class="glsNotice"><span class="dashicons dashicons-yes"></span>Status: '. ( (strpos($status[0], '-') !== false) ? substr($status[0], 3 ) : $status[0] ) .'</br></div>';
                    echo '</div>';
				}
            } else {
                $current_url_generate = esc_url(add_query_arg(array('generate_awb_GLS' => absint($post->ID))));
                echo    '<p><div class="GLS_AWB"><a class="button tips  generateBtn" data-tip="' . __('Genereaza AWB GLS', 'safealternative-plugin') . '"
                                    href="' . $current_url_generate . '"><img src="' . plugin_dir_url(__FILE__) . 'assets/images/gls-button.png"' . ' style="height: 28px;"/></a>
                        </div></p>';
            }
        }
    }


    ////////////////////////////////////////////////////////////////////////////
    // This method will generate a new awb /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    static public function getAwbDetails($sender = 2)
    {
        $idExpeditor           = get_option('GLS_senderid');
        $numeExpeditor         = get_option('GLS_sender_name');
        $localitateExpeditor   = get_option('GLS_sender_city');
        $adresaExpeditor       = get_option('GLS_sender_address');
        $codPostalExpeditor    = get_option('GLS_sender_zipcode');
        $telefonExpeditor      = get_option('GLS_sender_phone');
        $emailExpeditor        = get_option('GLS_sender_email');
        $persoanaDeContact     = get_option('GLS_sender_contact');
        $observatii            = get_option('GLS_observatii');
        $printertemplate       = get_option('GLS_printertemplate');
        $show_contents         = get_option('GLS_show_content');
        $show_client_note      = get_option('GLS_show_client_note');
        $show_order_id         = get_option('GLS_show_order_id');
        $services              = get_option('GLS_services');
        $pcount                = get_option('GLS_pcount');

        if ($sender == 2) {
            $order_id = get_query_var('generate_awb_GLS', null);
            $order = wc_get_order($order_id);
        }

        if ($sender == 1) {
            $order = wc_get_order($_GET['order_id']);
        }

        if (!$order) {
            die('Eroare la generarea awb-ului');
        }

        $items = $order->get_items();

        $weight = 0;
        $contents = '';
        $packing = '';

        $height = 0;
        $width = 0;
        $length = 0;

        foreach ($items as $i => $item) {
            $product = $item->get_product();

            $desc_pro = $product->get_short_description();
            if (!$desc_pro) $desc_pro = $product->get_name();
            $cod_pro = $product->get_sku();
            if (!$cod_pro) $cod_pro = $item->get_product_id();

            $name_pro = $product->get_name();
            $val_dec_pro = $item->get_total();

            $packing .= ($name_pro . "/" . $desc_pro . "/" . $cod_pro . "/" . $item->get_quantity() . "/" . $val_dec_pro . "|");
            switch($show_contents) {
                case 'nu': 
                    break;
                case 'name':
                    $contents .= ', ' . $item->get_quantity() . ' x ' . $item->get_name();
                    break;
                case 'sku':
                    $contents .= ', ' . $item->get_quantity() . ' x ' . $cod_pro;
                    break;
            }

            $_product = $item->get_product();
            if ($_product && !$_product->is_virtual()) {
                $weight += (float)$_product->get_weight() * $item->get_quantity();

                $height += (int)$product->get_height();
                $width  += (int)$product->get_width();
                $length += (int)$product->get_length();
            }
        }

        if ($height == 0) {
            $height = 10;
        }
        if ($width == 0) {
            $width = 10;
        }
        if ($length == 0) {
            $length = 10;
        }

        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        }

        if ($weight == 0) $weight = 1;

        $ramburs = $order->get_total();

        if ($order->get_payment_method() == 'cod') {
            $ramburs = $order->get_total();
        } else {
            $ramburs = null;
        }

        $client_notes = '';
        if ($show_client_note == '1' && $order->get_customer_note() != '') {
            $client_notes = 'Nota client: ' . $order->get_customer_note();
            $observatii = $observatii .' '. $client_notes;
        }

        if ($show_order_id == '1') {
            $observatii = $observatii .' #'. $order->ID;
        }        

        if(!empty($contents)) {
            $contents = 'Continut: '. ltrim($contents, ', ');
        }
        
        if(!empty($observatii)) {
            $contents = $observatii .' '. $contents;
        }

        $dataPickUp = date('Y-m-d');

        $persoana_juridica = $order->get_shipping_company();
        if (!empty($persoana_juridica)) {
            $consig_name = $persoana_juridica;
        } else {
            $consig_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
        }

        $district = $order->get_shipping_state();
        $city = $order->get_shipping_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($district, $city);

        $awb_details = array(
            "senderid"            => $idExpeditor,
            "sender_name"         => $numeExpeditor,
            "sender_contact"      => $persoanaDeContact,
            "sender_country"      => WC()->countries->get_base_country(),
            "sender_city"         => $localitateExpeditor,
            "sender_address"      => $adresaExpeditor,
            "sender_zipcode"      => $codPostalExpeditor,
            "sender_phone"        => $telefonExpeditor,
            "sender_email"        => $emailExpeditor,

            "consig_name"       => $consig_name,
            "consig_county"     => $district,
            "consig_country"    => $order->get_shipping_country(),
            "consig_contact"    => $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name(),
            "consig_city"       => $city,
            "consig_address"    => $order->get_shipping_address_1() . ' ' . $order->get_shipping_address_2(),
            "consig_zipcode"    => $postcode,
            "consig_phone"      => $order->get_billing_phone(),
            "consig_email"      => $order->get_billing_email(),

            'pcount' => $pcount,
            'pickupdate' => $dataPickUp,
            'content' => $contents,
            'clientref' => '',
            'codamount' => $ramburs,
            'codref' => '',
            'services' => $services,
            'printertemplate' => $printertemplate,
            'printit' => true,
            'timestamp' => time(),
            'domain' => site_url()
        );

        $awb_details = apply_filters('safealternative_awb_details', $awb_details, 'GLS', $order);

        array_walk($awb_details, function (&$item) {
            if (is_string($item)) $item = remove_accents($item);
        });

        return $awb_details;
    }

    static public function generate_awb_GLS()
    {
        $order_id = get_query_var('generate_awb_GLS', null);

        if ($order_id) {
            // don't make another awb with same order if refresh page  
            if (get_post_meta($order_id, 'awb_GLS')) return;

            try {
                $GLS_usr = get_option('GLS_user');
                if (empty($GLS_usr)) {
                    echo '<div class="notice notice-error"><h2>Plugin-ul SafeAlternative GLS AWB nu a fost configurat.</h2><p>Va rugam dati click <a href="'.safealternative_redirect_url('admin.php?page=GLS-plugin-setting').'"> aici</a> pentru a il configura.</p></div>';
                    return;
                }

                $dir = plugin_dir_path(__FILE__);
                include_once($dir . 'courier.class.php');

                $awb_details = self::getAwbDetails(2);
                $json_parameters = json_encode($awb_details);

                $trimite_mail = get_option('GLS_trimite_mail');
                $courier = new SafealternativeGLSClass();
                $response = $courier->callMethod("generateAwb", $json_parameters, 'POST');

                if ($response['status'] == 200) {

                    $mesage = json_decode($response['message'], true);

                    $successfull = $mesage['successfull'];
                    if ($successfull) {
                        $awb = $mesage['pcls'][0];

                        if ($trimite_mail == 'da') {
                            GLSGenereazaAWB::send_mails($order_id, $awb, $awb_details['consig_email']);
                        }
                        update_post_meta($order_id, 'awb_GLS', $awb);
                        update_post_meta($order_id, 'awb_GLS_all_pcls', $mesage['all_pcls']);
                        update_post_meta($order_id, 'awb_GLS_status', 'Inregistrat');

                        do_action( 'safealternative_awb_generated', 'GLS', $awb, $order_id );

                        $account_status_response = $courier->callMethod("newAccountStatus", '', 'POST');
                        $account_status = json_decode($account_status_response['message']);

                        if($account_status->show_message){
                            set_transient( 'gls_account_status', $account_status->message, MONTH_IN_SECONDS );
                        } else {
                            delete_transient( 'gls_account_status' );
                        }
                    } else {
                        $errdesc = $mesage['errdesc'];
                        echo 'Eroare la generare :' . $errdesc;
                    }
                } else {
                    var_dump($response);
                }
            } catch (Exception $e) {
                $e->getMessage();
            }
        }
    } // end function


    ////////////////////////////////////////////////////
    /////////// send mail ////////////////////
    /////////////////////////////////////////////////// 
    static function send_mails($idOrder, $awb, $receiver_email)
    {
        $sender_email = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template  = get_option('GLS_email_template');
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: '.get_bloginfo('name').' <'.$sender_email.'>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option('GLS_subiect_mail'), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_GLS_email', $email_content, $data);
        $email_content = nl2br($email_content);

        try {
            if (!wp_mail($receiver_email, $subiect_mail, $email_content, $headers)) {
                set_transient('email_sent_error', 'Nu am putut trimite email-ul catre ' . $receiver_email, 5);
            } else {
                set_transient('email_sent_success', 'Email-ul s-a trimis catre ' . $receiver_email, 5);
            }
        } catch (Exception $e) { }
    } // function mail

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

    function add_other_gls_sender() {
        if( isset( $_POST['GLS_other_sender'] )) {
            $current_other_senders = maybe_unserialize(get_option('GLS_other_senders'));
            if(empty($current_other_senders)) $current_other_senders = array();

            $current_other_senders[mt_rand()] =  $_POST['GLS_other_sender'];

            update_option('GLS_other_senders', maybe_serialize($current_other_senders));
        }
    }    

    function remove_other_gls_sender() {
        if( isset( $_POST['remove_GLS_other_sender'] )) {
            $remove_key = $_POST['remove_GLS_other_sender'];
            $current_other_senders = maybe_unserialize(get_option('GLS_other_senders'));

            if(isset($current_other_senders[$remove_key])) unset($current_other_senders[$remove_key]);
            update_option('GLS_other_senders', maybe_serialize($current_other_senders));
        }
    }    


    public function autogenerate_gls_awb($order_id, $old_status, $new_status){
        if(get_option('GLS_auto_generate_awb') != "da") return;
        if($new_status !== 'processing') return; 
        
        set_query_var('generate_awb_GLS', $order_id);
        GLSGenereazaAWB::generate_awb_GLS();
    }       
}


////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////
add_action('woocommerce_order_details_after_order_table_items', function ($order) {
    $order_id = $order->get_order_number();
    $awb = get_post_meta($order_id, 'awb_GLS', true);
    if ($awb) echo "Nota de transport (AWB) are numarul: {$awb} si poate fi urmarita aici: <a href='https://gls-group.eu/RO/ro/urmarire-colet?match={$awb}' target='_blank'>Status comanda</a><br/>";
});

// Add custom query vars
add_filter('query_vars', function ($vars) {
    $vars[] = "generate_awb_GLS";
    return $vars;
});

add_action('admin_notices', array('GLSGenereazaAWB', 'generate_awb_GLS'), 1);

//Bulk
add_action( 'admin_footer', function () {
	global $post_type;

	if ( 'shop_order' == $post_type ) {	
        wp_enqueue_script( 'bulk_admin_js', plugin_dir_url(__FILE__).'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2' );    
	}
});

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_gls', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);

//Zip lookup feature
include_once(plugin_dir_path(__FILE__) . '/ziplookup.php');

//Cron
include_once(plugin_dir_path(__FILE__) . '/cron.php');
