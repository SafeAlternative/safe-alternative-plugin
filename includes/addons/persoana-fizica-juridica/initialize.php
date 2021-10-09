<?php

class InitializePFPJ
{
    public function __construct()
    {
        define('SAFEALTERNATIVE_PF_PJ_VERSION', '1.1.1');
        define('SAFEALTERNATIVE_PF_PJ_SLUG', plugin_basename(__FILE__));
        define('SAFEALTERNATIVE_PF_PJ_PATH', plugin_dir_path(__FILE__));
        define('SAFEALTERNATIVE_PF_PJ_ASSETS', plugin_dir_url(__FILE__) . '/assets/');

        add_action('admin_menu', array($this, 'add_plugin_safealternative_pf_pj'));
        add_action('init', array($this, 'choose_pers'));
    }

    //verific daca e permisa afisarea in checkout a campului pers fizica/juridica
    public function choose_pers()
    {
        $enable_pers_fiz_jurid = get_option('enable_pers_fiz_jurid');
        if ($enable_pers_fiz_jurid == 1) {
            require plugin_dir_path(__FILE__) . 'includes/safealternative-pf-pj-class.php';
            (new Safealternative_PF_PJ_Class)->run();
        }
    }

    public function add_plugin_safealternative_pf_pj()
    {
        add_submenu_page(
            'safealternative-menu-content',
            'Persoana fizica/juridica',
            'Persoana fizica/juridica',
            'manage_woocommerce',
            'pf_pj_submenu_content',
            function () {
                wp_safe_redirect(safealternative_redirect_url('admin.php?page=wc-settings&tab=safealternative-pf-pj')); //redirectionez din safealternative catre woocommerce settings/pf_pj 
            }
        );
    }
}

new InitializePFPJ;
