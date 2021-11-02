<?php

// Exit if accessed directly
if (!defined('ABSPATH')) exit;

if (!function_exists('get_plugin_data')) {
    include_once(ABSPATH . 'wp-admin/includes/plugin.php');
}

add_filter('plugin_row_meta', function ($plugin_meta, $pluginFile) {
    if (plugin_basename(SAFEALTERNATIVE_PLUGIN_FILE) === $pluginFile) {
        foreach ($plugin_meta as $existing_link) {
            if (strpos($existing_link, 'tab=plugin-information') !== false) {
                return $plugin_meta;
            }
        }

        $plugin_info = get_plugin_data(SAFEALTERNATIVE_PLUGIN_FILE);

        $plugin_meta[] = sprintf(
            '<a href="%s" class="open-plugin-settings" aria-label="%s" data-title="%s">%s</a>',
            esc_url(safealternative_redirect_url('admin.php?page=safealternative-menu-content')),
            esc_attr(sprintf(__('Setari plugin %s'), $plugin_info['Name'])),
            esc_attr($plugin_info['Name']),
            __('Configureaza plugin')
        );

        $plugin_meta[] = sprintf(
            '<a href="%s" class="thickbox open-plugin-details-modal" aria-label="%s" data-title="%s">%s</a>',
            esc_url(safealternative_redirect_url('plugin-install.php?tab=plugin-information&plugin=safe-alternative-plugin&TB_iframe=true&width=600&height=550')),
            esc_attr(sprintf(__('More information about %s'), $plugin_info['Name'])),
            esc_attr($plugin_info['Name']),
            __('View details')
        );

        $plugin_meta[] = sprintf(
            '<a href="%s" class="open-plugin-tickets" aria-label="%s" data-title="%s" target="_blank">%s</a>',
            esc_url('https://api.safe-alternative.ro/tickets'),
            esc_attr(sprintf(__('Cere ajutor'))),
            esc_attr($plugin_info['Name']),
            __('Cere ajutor')
        );
    }
    return $plugin_meta;
}, 10, 2);

add_filter('plugins_api', function ($res, $action, $args) {
    // do nothing if this is not about getting plugin information
    if ($action !== 'plugin_information')
        return false;

    // do nothing if it is not our plugin	
    if ('safe-alternative-plugin' !== $args->slug)
        return $res;

    if (false == $remote = get_transient('safealternative_update_plugin')) {
        $remote = wp_remote_get(
            SAFEALTERNATIVE_API_VERSION_JSON,
            array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json'
                )
            )
        );

        if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
            set_transient('safealternative_update_plugin', $remote, HOUR_IN_SECONDS);
        }
    }

    if (!is_wp_error($remote)) {
        $remote = json_decode($remote['body']);
        $res = new stdClass();
        $res->name = $remote->name;
        $res->slug = $remote->slug;
        $res->version = $remote->version;
        $res->tested = $remote->tested;
        $res->requires = $remote->requires;
        $res->author = $remote->author;
        $res->author_profile = $remote->author_homepage;
        $res->download_link = $remote->download_url;
        $res->trunk = $remote->download_url;
        $res->requires_php = '7.1';
        $res->sections = array(
            'description' => $remote->section->description,
            'changelog' => (new SafeAlternative\cebe\markdown\GithubMarkdown)->parse(file_get_contents($remote->section->changelog))
        );

        if (!empty($remote->section->screenshots)) {
            $res->sections['screenshots'] = $remote->section->screenshots;
        }

        $res->banners = array(
            'low' => SAFEALTERNATIVE_PLUGIN_URL . '/assets/images/banner-772x250.png',
            'high' => SAFEALTERNATIVE_PLUGIN_URL . '/assets/images/banner-1544x500.png'
        );

        return $res;
    }
    return false;
}, 20, 3);

add_filter('site_transient_update_plugins', function ($transient) {
    // a fost verificat update
    if (empty($transient->checked)) {
        return $transient;
    }

    // nu avem informatia stocata , atunci o obtinem
    if (false == $remote = get_transient('safealternative_update_plugin')) {
        $remote = wp_remote_get(
            SAFEALTERNATIVE_API_VERSION_JSON,
            array(
                'timeout' => 10,
                'headers' => array(
                    'Accept' => 'application/json'
                )
            )
        );

        if (!is_wp_error($remote) && isset($remote['response']['code']) && $remote['response']['code'] == 200 && !empty($remote['body'])) {
            set_transient('safealternative_update_plugin', $remote, HOUR_IN_SECONDS);
        }
    }

    // avem info deja stocata
    if (!empty($remote) && !is_wp_error($remote)) {
        $remote = json_decode($remote['body']);
        $current_version = get_plugin_data(SAFEALTERNATIVE_PLUGIN_FILE)['Version'];

        if (!empty($remote) && version_compare($current_version, $remote->version, '<') && version_compare($remote->requires, get_bloginfo('version'), '<')) {
            $res = new stdClass;
            $res->slug = $remote->slug;
            $res->plugin = 'safe-alternative-plugin/safe-alternative-plugin.php';
            $res->new_version = $remote->version;
            $res->tested = $remote->tested;
            $res->package = $remote->download_url;
            $res->requires_php = '7.1';

            $res->icons = array(
                'default' => SAFEALTERNATIVE_PLUGIN_URL . '/assets/images/icon-256x256.png'
            );

            $transient->response[$res->plugin] = $res;
        }
    }

    return $transient;
});

add_action('upgrader_process_complete', function ($upgrader_object, $options) {
    if ($options['action'] == 'update' && $options['type'] === 'plugin') {
        delete_transient('safealternative_update_plugin');
    }
}, 10, 2);

add_action('core_upgrade_preamble', function () {
    delete_transient('safealternative_update_plugin');
}, 10, 0);
