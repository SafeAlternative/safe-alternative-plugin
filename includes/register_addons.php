<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (get_option('enable_pers_fiz_jurid') == '1') {
    include_once SAFEALTERNATIVE_PLUGIN_PATH . 'includes/addons/persoana-fizica-juridica/initialize.php';
}
