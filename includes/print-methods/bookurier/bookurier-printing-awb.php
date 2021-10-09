<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

class BookurierGenereazaAWB
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page_generate_bookurier'));
        add_action('admin_menu', array($this, 'add_plugin_page_settings_bookurier'));
        add_action('add_meta_boxes', array($this, 'add_meta_box_awb_bookurier'));
        add_action('admin_init', array($this, 'add_register_setting_bookurier'));

        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action('woocommerce_order_status_changed', array(
            $this,
            'autogenerate_bookurier_awb'
        ), 99, 3);

        ////INCLUDE TRACK&TRACE COLUMS /////////////////////////////////////////////////////
        add_filter('manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table_bookurier'), 10);
        add_action('manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values_bookurier'), 6);
    }

    public function add_plugin_page_settings_bookurier()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Bookurier - AWB',
            'Bookurier - AWB',
            'manage_woocommerce',
            'bookurier-plugin-setting',
            array(
                $this,
                'bookurier_plugin_page'
            )
        );
    }

    public function add_plugin_page_generate_bookurier()
    {
        add_submenu_page(
            null,
            'SafeAlternative Bookurier',
            'SafeAlternative Bookurier',
            'manage_woocommerce',
            'generate-awb-bookurier',
            array($this, 'create_admin_page'),
            null
        );
    }

    public function add_register_setting_bookurier()
    {
        add_option('bookurier_user', '');
        add_option('bookurier_password', '');
        add_option('bookurier_senderid', '');

        add_option('bookurier_trimite_mail', 'nu');
        add_option('bookurier_observatii', '');
        add_option('bookurier_subiect_mail', 'Comanda dumneavoastra a fost expediata!');

        add_option('bookurier_pcount', '1');
        add_option('bookurier_services', '9');
        add_option('bookurier_insurance_val', '0');
        add_option('bookurier_auto_generate_awb', 'nu');
        add_option('bookurier_auto_mark_complete', 'nu');

        register_setting('bookurier-plugin-settings', 'bookurier_user');
        register_setting('bookurier-plugin-settings', 'bookurier_password');
        register_setting('bookurier-plugin-settings', 'bookurier_senderid');

        register_setting('bookurier-plugin-settings', 'bookurier_observatii');
        register_setting('bookurier-plugin-settings', 'bookurier_trimite_mail');
        register_setting('bookurier-plugin-settings', 'bookurier_subiect_mail');

        register_setting('bookurier-plugin-settings', 'bookurier_pcount');
        register_setting('bookurier-plugin-settings', 'bookurier_services');
        register_setting('bookurier-plugin-settings', 'bookurier_insurance_val');
        register_setting('bookurier-plugin-settings', 'bookurier_auto_generate_awb');
        register_setting('bookurier-plugin-settings', 'bookurier_auto_mark_complete');

        include_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    public function bookurier_plugin_page()
    {
        include_once(plugin_dir_path(__FILE__) . '/templates/settings_page.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message = get_transient('bookurier_account_status')) && $qv === "shop_order") {
?>
            <div class="notice notice-warning">
                <p><?php _e($message, 'safealternative-bookurier-woocommerce'); ?></p>
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

        $bookurier_user = get_option('bookurier_user');
        if (empty($bookurier_user)) {
            echo '<div class="wrap"><h1>SafeAlternative Bookurier AWB</h2><br><h2>Plugin-ul SafeAlternative Bookurier AWB nu a fost configurat.</h2> Va rugam dati click <a href="' . safealternative_redirect_url('admin.php?page=bookurier-plugin-setting') . '"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $dir = plugin_dir_path(__FILE__);
        include_once($dir . 'courier.class.php');

        $awb_details = self::getAwbDetails(1);

        include_once(plugin_dir_path(__FILE__) . '/templates/generate_awb_page.php');
    }


    ///////////////////////////////////////////////
    public function add_meta_box_awb_bookurier()
    {
        $screens = array('shop_order');

        foreach ($screens as $screen) {
            add_meta_box(
                'bookurierawb_sectionid',
                __('Bookurier - AWB', 'safealternative-plugin'),
                array($this, 'bookurierawb_meta_box_callback'),
                $screen,
                'side'
            );
        }
    }

    public function bookurierawb_meta_box_callback($post)
    {
        $awb = get_post_meta($post->ID, 'awb_bookurier', true);

        echo '<style>.bookurier_secondary_button{border-color:#f44336!important;color:#f44336!important}.bookurier_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';
        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 100%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button bookurier_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-bookurier&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
        }
    }


    /////////////////////////////////////////////////////                
    /////////CURIER FIELDS//////////////// //////////////                       	
    /////////////////////////////////////////////////////
    public function add_custom_columns_to_orders_table_bookurier($columns)
    {
        $new_columns = $columns;

        if (!is_array($columns)) {
            $columns = array();
        }

        $new_columns['bookurier_AWB'] = 'Bookurier';

        return $new_columns;
    }

    ////////////////////////////////////////////////
    public function get_custom_columns_values_bookurier($column)
    {
        global $post;

        if ($column == 'bookurier_AWB') {
            $awb        = get_post_meta($post->ID, 'awb_bookurier', true);
            $single_awb = explode(',', $awb);
            $status     = get_post_meta($post->ID, 'awb_bookurier_status', true);

            if (!empty($single_awb[0]) && is_array($single_awb)) {
                $printing_link = plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '';
                echo '<a class="button tips downloadBtn" href="' . $printing_link . '" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">' . $single_awb[0] . '</a></br>';

                if (!empty($status)) {
                    echo '<div class="bookurierNoticeWrapper">';
                    echo '<div class="bookurierNotice"><span class="dashicons dashicons-yes"></span>Status: ' . $status . '</br></div>';
                    echo '</div>';
                }
            } else {
                $current_url_generate = esc_url(add_query_arg(array('generate_awb_bookurier' => absint($post->ID))));
                echo    '<p><div class="bookurier_AWB"><a class="button tips  generateBtn" data-tip="' . __('Genereaza AWB Bookurier', 'safealternative-plugin') . '"
                                    href="' . $current_url_generate . '"><img src="' . plugin_dir_url(__FILE__) . 'assets/images/logo_bookurier.svg"' . ' style="height: 31px; width: 100%;"/></a>
                        </div></p>';
            }
        }
    }


    ////////////////////////////////////////////////////////////////////////////
    // This method will generate a new awb /////////////////////////////////////
    ////////////////////////////////////////////////////////////////////////////
    static public function getAwbDetails($sender = 2)
    {
        $client                = get_option('bookurier_senderid');
        $service               = get_option('bookurier_services');
        $pcount                = get_option('bookurier_pcount');
        $notes                 = get_option('bookurier_observatii');
        $insurance_val         = get_option('bookurier_insurance_val');

        if ($sender == 2) {
            $order_id = get_query_var('generate_awb_bookurier', null);
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

        foreach ($items as $i => $item) {
            $product = $item->get_product();
            if ($product && !$product->is_virtual()) {
                $weight += (float)$product->get_weight() * $item->get_quantity();
            }
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

        $persoana_juridica = $order->get_shipping_company();
        if (!empty($persoana_juridica)) {
            $consig_name = $persoana_juridica;
        } else {
            $consig_name = $order->get_shipping_first_name() . ' ' . $order->get_shipping_last_name();
        }

        $district = safealternative_get_counties_list($order->get_billing_state());
        $city = $order->get_billing_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($district, $city);

        $awb_details = array(
            'client' => $client,
            'unq' => $order->get_id(),
            'recv' => $consig_name,
            'phone' => $order->get_billing_phone(),
            'email' => $order->get_billing_email(),
            'country' => WC()->countries->countries[$order->get_billing_country()],
            'district' => $district,
            'city' => $city,
            'zip' => $postcode,
            'street' => "{$order->get_shipping_address_1()} {$order->get_shipping_address_2()}",
            'service' => $service,
            'packs' => $pcount,
            'exchange_pack' => '0',
            'weight' => $weight,
            'rbs_val' => $ramburs,
            'confirmation' => '0',
            'insurance_val' => $insurance_val,
            'notes' => $notes,
            'domain' => site_url()
        );

        $awb_details = apply_filters('safealternative_awb_details', $awb_details, 'Bookurier', $order);

        array_walk($awb_details, function (&$item) {
            if (is_string($item)) $item = remove_accents($item);
        });

        return $awb_details;
    }

    static public function generate_awb_bookurier()
    {
        $order_id = get_query_var('generate_awb_bookurier', null);

        if ($order_id) {
            // don't make another awb with same order if refresh page  
            if (get_post_meta($order_id, 'awb_bookurier')) return;

            try {
                $bookurier_usr = get_option('bookurier_user');
                if (empty($bookurier_usr)) {
                    echo '<div class="notice notice-error"><h2>Plugin-ul SafeAlternative Bookurier AWB nu a fost configurat.</h2><p>Va rugam dati click <a href="' . safealternative_redirect_url('admin.php?page=bookurier-plugin-setting') . '"> aici</a> pentru a il configura.</p></div>';
                    return;
                }

                $dir = plugin_dir_path(__FILE__);
                include_once($dir . 'courier.class.php');

                $awb_details = self::getAwbDetails(2);

                $trimite_mail = get_option('bookurier_trimite_mail');
                $courier = new SafealternativebookurierClass();
                $response = $courier->callMethod("generateAwb", $awb_details, 'POST');

                if ($response['status'] == 200) {
                    $mesage = json_decode($response['message'], true);

                    if ($mesage['success']) {
                        $awb = $mesage['awb'];

                        if ($trimite_mail == 'da') {
                            BookurierGenereazaAWB::send_mails($order_id, $awb, $awb_details['email']);
                        }

                        update_post_meta($order_id, 'awb_bookurier', $awb);
                        update_post_meta($order_id, 'awb_bookurier_status_id', '1');
                        update_post_meta($order_id, 'awb_bookurier_status', 'Inregistrat');

                        do_action('safealternative_awb_generated', 'Bookurier', $awb, $order_id);

                        $account_status_response = $courier->callMethod("newAccountStatus", array(), 'POST');
                        $account_status = json_decode($account_status_response['message']);

                        if ($account_status->show_message) {
                            set_transient('bookurier_account_status', $account_status->message, MONTH_IN_SECONDS);
                        } else {
                            delete_transient('bookurier_account_status');
                        }
                    } else {
                        wp_die("<b class='bad'>Bookurier API: Eroare la generare AWB.</b>");
                    }
                } else {
                    var_dump($response);
                }
            } catch (Exception $e) {
            }
        }
    } // end function


    ////////////////////////////////////////////////////
    /////////// send mail ////////////////////
    /////////////////////////////////////////////////// 
    static function send_mails($idOrder, $awb, $receiver_email)
    {
        $sender_email = get_option('courier_email_from') ?: get_bloginfo('admin_email');
        $email_template  = get_option('bookurier_email_template');
        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo('name') . ' <' . $sender_email . '>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option('bookurier_subiect_mail'), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_bookurier_email', $email_content, $data);
        $email_content = nl2br($email_content);

        try {
            if (!wp_mail($receiver_email, $subiect_mail, $email_content, $headers)) {
                set_transient('email_sent_error', 'Nu am putut trimite email-ul catre ' . $receiver_email, 5);
            } else {
                set_transient('email_sent_success', 'Email-ul s-a trimis catre ' . $receiver_email, 5);
            }
        } catch (Exception $e) {
        }
    } // function mail

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

    public function autogenerate_bookurier_awb($order_id, $old_status, $new_status)
    {
        if (get_option('bookurier_auto_generate_awb') != "da") return;
        if ($new_status !== 'processing') return;

        set_query_var('generate_awb_bookurier', $order_id);
        BookurierGenereazaAWB::generate_awb_bookurier();
    }
}


////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////
add_action('woocommerce_order_details_after_order_table_items', function ($order) {
    $order_id = $order->get_order_number();
    $awb = get_post_meta($order_id, 'awb_bookurier');

    if (!empty($awb) && is_array($awb))
        echo "Nota de transport (AWB) are numarul: {$awb[0]} si poate fi urmarita aici: <a href='https://bookurier-group.eu/RO/ro/urmarire-colet?match={$awb[0]}' target='_blank'>Status comanda</a><br/>";
});

// Add custom query vars
add_filter('query_vars', function ($vars) {
    $vars[] = "generate_awb_bookurier";
    return $vars;
});

add_action('admin_notices', array('BookurierGenereazaAWB', 'generate_awb_bookurier'), 1);

//Bulk
add_action('admin_footer', function () {
    global $post_type;

    if ('shop_order' == $post_type) {
        wp_enqueue_script('bulk_admin_js', plugin_dir_url(__FILE__) . 'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');
    }
});

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_bookurier', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);

//Cron
include_once(plugin_dir_path(__FILE__) . '/cron.php');
