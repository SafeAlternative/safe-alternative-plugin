<?php

$dir = plugin_dir_path(__FILE__);
include_once($dir . 'courier.class.php');

class OptimusAWB
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_plugin_page'));
        add_action('admin_menu', array($this, 'add_plugin_page_settings'));
        add_action('add_meta_boxes', array($this, 'optimus_add_meta_box'));
        add_action('admin_init', array($this, 'add_register_setting'));

        add_action('admin_notices', array(
            $this,
            'show_account_status_nag'
        ), 10);

        add_action('woocommerce_order_status_changed', array(
            $this,
            'autogenerate_optimus_awb'
        ), 99, 3);

        add_filter('manage_edit-shop_order_columns', array($this, 'add_custom_columns_to_orders_table'), 11);
        add_action('manage_shop_order_posts_custom_column', array($this, 'get_custom_columns_values'), 2);
    }

    function add_plugin_page_settings()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Optimus - AWB',
            'Optimus - AWB',
            'manage_woocommerce',
            'optimus-plugin-setting',
            array(
                $this,
                'optimus_plugin_page'
            )
        );
    }

    function add_register_setting()
    {
        add_option('optimus_username', '');
        add_option('optimus_key', '');
        add_option('optimus_parcel_content', '');
        add_option('optimus_count', '1');
        add_option('optimus_parcel_weight', '');
        add_option('optimus_trimite_mail', 'nu');
        add_option('optimus_subiect_mail', 'Comanda dumneavoastra a fost expediata!');
        add_option('optimus_auto_generate_awb', 'nu');
        add_option('optimus_auto_mark_complete', 'nu');

        register_setting('optimus-plugin-settings', 'optimus_username');
        register_setting('optimus-plugin-settings', 'optimus_key');
        register_setting('optimus-plugin-settings', 'optimus_parcel_content');
        register_setting('optimus-plugin-settings', 'optimus_count');
        register_setting('optimus-plugin-settings', 'optimus_parcel_weight');
        register_setting('optimus-plugin-settings', 'optimus_trimite_mail');
        register_setting('optimus-plugin-settings', 'optimus_subiect_mail');
        register_setting('optimus-plugin-settings', 'optimus_auto_generate_awb');
        register_setting('optimus-plugin-settings', 'optimus_auto_mark_complete');
        
        require_once(plugin_dir_path(__FILE__) . '/templates/default-email-template.php');
    }

    function show_account_status_nag()
    {
        global $wp;
        $qv = $wp->query_vars['post_type'] ?? NULL;

        if (($message_status = get_transient('optimus_account_status')) && $qv === "shop_order") {
        ?>
            <div class="notice notice-warning">
                <p><?php _e($message_status, 'safealternative-optimus-woocommerce'); ?></p>
            </div>
        <?php
        }

        if (($message_settings = get_transient('optimus_account_settings'))) {
        ?>
            <div class="notice notice-warning">
                <p><?php _e($message_settings, 'safealternative-optimus-woocommerce'); ?></p>
            </div>
        <?php
            delete_transient('optimus_account_settings');
        }
    }

    function optimus_plugin_page()
    {
        require_once(plugin_dir_path(__FILE__) . '/templates/settings-page.php');
    }

    public function add_plugin_page()
    {
        add_submenu_page(
            null,
            'Genereaza AWB Optimus',
            'Genereaza AWB Optimus',
            'manage_woocommerce',
            'generate-awb-optimus',
            array($this, 'create_admin_page'),
            null
        );
    }

    public function create_admin_page()
    {
        if (!isset($_GET['order_id'])) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $awb_already_generated = get_post_meta($_GET['order_id'], 'awb_optimus', 1);
        if ($awb_already_generated) {
            wp_redirect(safealternative_redirect_url('/edit.php?post_type=shop_order'), 302);
        }

        $order = wc_get_order($_GET['order_id']);
        $items = $order->get_items();

        $weight = 0;
        $content = null;

        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
                $content .= $_product->get_name().', ';
            }
        }
        
        $content = rtrim($content, ', ');

        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        } //$weight_type == 'g'
        
        if ($weight <= 1 ) {
            $weight = get_option('optimus_parcel_weight') ?: 1;
        }
        $weight = round($weight);

        if (empty(get_option('optimus_username'))) {
            echo '<div class="wrap"><h1>SafeAlternative Optimus AWB</h2><br><h2>Plugin-ul SafeAlternative Optimus AWB nu a fost configurat.</h2> Va rugam dati click <a href="' . safealternative_redirect_url('admin.php?page=optimus-plugin-setting') . '"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);

        $destinatar = empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company();

        $awb_info = [
            'destinatar_nume' => $destinatar,
            'destinatar_adresa' => $order->get_shipping_address_1(),
            'destinatar_localitate' => $order->get_shipping_city(),
            'destinatar_judet' => $recipient_address_state_id,
            'destinatar_cod_postal' => $postcode,
            'destinatar_contact' => $destinatar,
            'destinatar_telefon' => $order->get_billing_phone(),
            'colet_buc' => get_option('optimus_count'),
            'colet_greutate' => $weight,
            'data_colectare' => date('Y-m-d'),
            'ramburs_valoare' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'ref_factura' => 'Comanda '.$_GET['order_id'],
            'colet_descriere' => $content ?? get_option('optimus_parcel_content')
        ];

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Optimus', $order);

        $_POST['awb'] = $awb_info;

        require_once(plugin_dir_path(__FILE__) . '/templates/generate-awb-page.php');
    }

    function optimus_add_meta_box()
    {
        $screens = array('shop_order');

        foreach ($screens as $screen) {
            add_meta_box(
                'optimus_sectionid',
                __('OptimusCourier - AWB', 'safealternative_optimus'),
                array($this, 'optimus_meta_box_callback'),
                $screen,
                'side'
            );
        }
    }

    function optimus_meta_box_callback($post)
    {
        $awb = get_post_meta($post->ID, 'awb_optimus', true);

        echo '<style>.optimus_secondary_button{border-color:#f44336!important;color:#f44336!important}.optimus_secondary_button:focus{box-shadow:0 0 0 1px #f44336!important}</style>';

        if ($awb) {
            echo '<p><input type="text" value="'.$awb.'" style="width: 80%; text-align: center; vertical-align: top;" readonly="true" autocomplete="false" /><a class="button" style="width: 19%; text-align: center;" href="https://optimuscourier.ro/search/?awb='.$awb.'" target="_blank"><i class="dashicons dashicons-clipboard" style="vertical-align: middle; font-size: 17px;" title="Tracking AWB"></i></a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '" class="add_note button" target="blank_" style="width: 100%; text-align: center;"><i class="dashicons dashicons-download" style="vertical-align: middle; font-size: 17px;"></i> Descarca AWB </a></p>';
            echo '<p><a href="' . plugin_dir_url(__FILE__) . 'delete.php?&order_id=' . $post->ID . '" onclick="return confirm(`Sunteți sigur(ă) că doriți să ștergeți AWB-ul?`)" class="add_note button optimus_secondary_button" style="width: 100%; text-align: center;"><i class="dashicons dashicons-trash" style="vertical-align: sub; font-size: 17px;"></i> Sterge AWB </a></p>';
        } else {
            echo '<p><a href="admin.php?page=generate-awb-optimus&order_id=' . $post->ID . '" class="add_note button button-primary" style="width: 100%; text-align: center;"><i class="dashicons dashicons-edit-page" style="vertical-align: middle; font-size: 16px;"></i> Genereaza AWB </a></p>';
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

        $new_columns['Optimus_AWB'] = 'Optimus';

        return $new_columns;
    }

    ////////////////////////////////////////////////
    function get_custom_columns_values($column)
    {
        global $post;

        if ($column == 'Optimus_AWB') {
            $awb = get_post_meta($post->ID, 'awb_optimus', true);
            $status = get_post_meta($post->ID, 'awb_optimus_status', true);

            // avem awb 
            if (!empty($awb)) {
                $printing_link = plugin_dir_url(__FILE__) . 'download.php?&order_id=' . $post->ID . '';
                echo '<a class="button tips downloadBtn" href="' . $printing_link . '" target="_blank" data-tip="Printeaza" style="background-color:#fffae6">' . $awb . '</a></br>';
                echo '<div class="optimusNoticeWrapper">';
                echo '<div class="optimusNotice"><span class="dashicons dashicons-warning"></span>Status: ' . $status . '</div>';
                echo '</div>';
            }
            // nu avem awb
            else {
                $current_url_generate = esc_url(add_query_arg(array(
                    'generate_awb_optimus' => absint($post->ID)
                )));

                echo '<p><a class="button generateBtn tips" data-tip="' . __('Genereaza AWB Optimus', 'safealternative-plugin') . '"
                        href="' . $current_url_generate . '"><img src="' . plugin_dir_url(__FILE__) . 'assets/images/optimuslogo.png' . '" style="height: 20px;"/></a>
                    </p>';
            }
        }
    }

    // This method will generate a new awb
    static function generate_awb_optimus()
    {
        $order_id = get_query_var('generate_awb_optimus', NULL);
        if (empty($order_id)) {
            return null;
        }

        $awb_already_generated = get_post_meta($order_id, 'awb_optimus', true);
        if ($awb_already_generated) {
            return null;
        }

        $trimite_mail = get_option('optimus_trimite_mail');
        $order = wc_get_order($order_id);
        $items = $order->get_items();

        $weight = 0;
        $content = null;
        
        foreach ( $items as $i => $item ) {
            $_product = $item->get_product();
            if ($_product && ! $_product->is_virtual() ) {
                $weight += (float) $_product->get_weight() * $item->get_quantity();
                $content .= $_product->get_name().', ';
            }
        }

        $content = rtrim($content, ', ');

        $weight_type = get_option('woocommerce_weight_unit');
        if ($weight_type == 'g') {
            $weight = $weight / 1000;
        } //$weight_type == 'g'
        
        if ($weight <= 1 ) {
            $weight = get_option('optimus_parcel_weight') ?: 1;
        }
        $weight = round($weight);

        if (empty(get_option('optimus_username')) ) {
            echo '<div class="wrap"><h1>SafeAlternative Optimus AWB</h2><br><h2>Plugin-ul SafeAlternative Optimus AWB nu a fost configurat.</h2> Va rugam dati click <a href="' . safealternative_redirect_url('admin.php?page=optimus-plugin-setting') . '"> aici</a> pentru a il configura.</div>';
            exit;
        }

        $recipient_address_state_id = safealternative_get_counties_list($order->get_billing_state());
        $recipient_address_city_id = $order->get_billing_city();
        $postcode = $order->get_shipping_postcode() ?: safealternative_get_post_code($recipient_address_state_id, $recipient_address_city_id);
        
        $awb_info = [
            'destinatar_nume' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'destinatar_adresa' => $order->get_shipping_address_1(),
            'destinatar_localitate' => $order->get_shipping_city(),
            'destinatar_judet' => $recipient_address_state_id,
            'destinatar_cod_postal' => $postcode,
            'destinatar_contact' => empty($order->get_shipping_company()) ? "{$order->get_shipping_first_name()} {$order->get_shipping_last_name()}" : $order->get_shipping_company(),
            'destinatar_telefon' => $order->get_billing_phone(),
            'colet_buc' => get_option('optimus_count'),
            'colet_greutate' => $weight,
            'data_colectare' => date('Y-m-d'),
            'ramburs_valoare' => $order->get_payment_method() == 'cod' ? $order->get_total() : 0,
            'ref_factura' => 'Comanda '.$order_id,
            'colet_descriere' => $content ?? get_option('optimus_parcel_content')
        ];

        $awb_info = apply_filters('safealternative_awb_details', $awb_info, 'Optimus', $order);
        
        $courier = new SafealternativeOptimusClass();
        $result = $courier->callMethod("generateAwb", $awb_info, 'POST');

        if ($result['status'] == "200") {
            $message = json_decode($result['message'], true);

            if (!empty($message->error)) {
                set_transient('optimus_account_settings', $message->error, MONTH_IN_SECONDS);
            } else {
                $awb = $message['awb'];
                $awb_id = $message['id'];

                if ($trimite_mail == 'da') {
                    OptimusAWB::send_mails($order_id, $awb, $order->get_billing_email());
                }

                update_post_meta($order_id, 'awb_optimus', $awb);
                update_post_meta($order_id, 'awb_optimus_status', 'Inregistrat');
                update_post_meta($order_id, 'awb_optimus_id', $awb_id);
                do_action('safealternative_awb_generated', 'Optimus', $awb);

                $account_status_response = $courier->callMethod("newAccountStatus", [], 'POST');

                $account_status = json_decode($account_status_response['message']);
                if ($account_status->show_message) {
                    set_transient('optimus_account_status', $account_status->message, MONTH_IN_SECONDS);
                } else {
                    delete_transient('optimus_account_status');
                }
            }
        }
    } // end function


    static public function send_mails($idOrder, $awb, $receiver_email)
    {
        $sender_email = get_option('optimus_email') ?: get_bloginfo('admin_email');
        $email_template = get_option('optimus_email_template');

        $headers = array('Content-Type: text/html; charset=UTF-8', 'From: ' . get_bloginfo('name') . ' <' . $sender_email . '>');

        $order = wc_get_order($idOrder);
        $data = array(
            'awb' => $awb,
            'nr_comanda' => $idOrder,
            'data_comanda' => $order->get_date_created()->format('d.m.Y H:i'),
            'comanda' => $order,
            'produse' => $order->get_items()
        );

        $subiect_mail = self::handle_email_template(get_option('optimus_subiect_mail'), $data);
        $email_content = self::handle_email_template($email_template, $data);
        $email_content = apply_filters('safealternative_overwrite_optimus_email', $email_content, $data);
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

    public function autogenerate_optimus_awb($order_id, $old_status, $new_status)
    {
        if (get_option('optimus_auto_generate_awb') != "da") return;
        if ($new_status !== 'processing') return;

        set_query_var('generate_awb_optimus', $order_id);
        OptimusAWB::generate_awb_optimus();
    }
}
// end class

require_once(plugin_dir_path(__FILE__) . 'cron.php');

//Bulk generate
add_action('admin_footer', 'add_bulk_action_optimus');
function add_bulk_action_optimus()
{
    global $post_type;

    if ('shop_order' == $post_type) {
        wp_enqueue_script('bulk_admin_js_optimus', plugin_dir_url(__FILE__) . 'assets/js/bulkGenerate.min.js', array('jquery'), '1.0.2');
    }
}

////////////////////////////////////////////////////////////////////////////////
// add info order
////////////////////////////////////////////////////////////////////////////////

add_action('woocommerce_order_details_after_order_table_items', function ($order) {
    $order_id = $order->get_order_number();
    $awb = get_post_meta($order_id, 'awb_optimus', true);
    if ($awb) echo 'Nota de transport (AWB) are numarul: ' . $awb . ' si poate fi urmarita aici: <a href="https://optimuscourier.ro/search/?awb=" ' .$awb. ' target="_blank">Status comanda</a><br/>';
});

// Add custom query vars
add_filter('query_vars', function ($vars) {
    $vars[] = "generate_awb_optimus";
    return $vars;
});

add_action('admin_notices', array('OptimusAWB', 'generate_awb_optimus'), true);

add_action('admin_head', function () {
    wp_enqueue_style('custom_admin_css_optimus', plugin_dir_url(__FILE__) . 'assets/css/custom.css');
}, 1);
