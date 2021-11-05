<?php

/*******************************************************************************
 * Plugin Name: Safe Alternative
 * Plugin URI: https://safe-alternative.ro
 * Description: Plugin-ul Safe Alternative All-in-one - Generare AWB si Metode de livrare
 * Version: 2.15.5
 * Author: Safe Alternative
 * Author URI: https://safe-alternative.ro
 * WC requires at least: 3.0.0
 * WC tested up to: 5.3.0
 * Requires PHP: 7.1
 * Text Domain: safe-alternative-plugin
 *******************************************************************************/

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

// Constants
define('SAFEALTERNATIVE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SAFEALTERNATIVE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SAFEALTERNATIVE_PLUGIN_FILE', __FILE__);
define('SAFEALTERNATIVE_API_VERSION_JSON', 'http://localhost/safe-alternative/public/api/plugin/safe-alternative-plugin.json');
//define('SAFEALTERNATIVE_API_URL', 'http://proiect.curiersigur.ro/api/');
define('SAFEALTERNATIVE_API_URL', 'http://localhost/safe-alternative/public/api/');

define('SAFEALTERNATIVE_DB_VER', '1.1.2');

// Mandatory

include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/helper_functions.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/register_settings.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/handle_update_checking.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/admin/admin_menu.php';


// Plugin States
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/handle_plugin_old_version.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/handle_plugin_disable.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/handle_plugin_shipping_prereq.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/handle_plugin_db_update.php';

// Stop execution if not authenticaed in plugin
if (get_option('auth_validity') != '1') return;

// Shipping and Printing Methods
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/register_printing_methods.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/register_shipping_methods.php';
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/register_additional_hooks.php';

// Addons
include SAFEALTERNATIVE_PLUGIN_PATH . '/includes/register_addons.php';