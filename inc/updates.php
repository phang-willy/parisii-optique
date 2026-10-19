<?php
/**
 * Theme and companion-plugin updates. Shipped with the theme, not as a third plugin.
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Turn off the former standalone updater once the theme provides it.
 */
function parisii_updates_retire_legacy_plugin() {
    $legacy = 'updates-plugin/parisii-updates.php';
    if (!function_exists('is_plugin_active') || !function_exists('deactivate_plugins')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if (!is_plugin_active($legacy)) {
        return;
    }

    deactivate_plugins($legacy, true);
}
add_action('admin_init', 'parisii_updates_retire_legacy_plugin');

if (!function_exists('parisii_updates_filter_theme')) {
    require_once __DIR__ . '/updates-register.php';
}