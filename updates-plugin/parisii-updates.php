<?php
/**
 * Plugin Name: Parisii Updates
 * Description: Detecte les mises a jour du theme et des plugins Parisii Optique. L'installation reste manuelle.
 * Update URI: https://updates.phangwilly.com/
 * Version: 1.0.0
 * Requires at least: 6.8
 * Requires PHP: 8.3
 * Text Domain: parisii-updates
 */

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/version-rules.php';

/**
 * Channel selected from wp-config.php, outside the theme directory.
 *
 * @return string
 */
function parisii_updates_channel() {
    if (!defined('WP_ENVIRONMENT_TYPE')) {
        return '';
    }

    return parisii_updates_channel_for_environment(WP_ENVIRONMENT_TYPE);
}

/**
 * @param string $channel beta or stable.
 * @return array<string,mixed>|null Null keeps the previous WordPress cache on network errors.
 */
function parisii_updates_catalog($channel) {
    static $cache = array();
    if (array_key_exists($channel, $cache)) {
        return $cache[$channel];
    }

    $response = wp_remote_get(
        'https://updates.phangwilly.com/' . $channel . '/latest.json',
        array(
            'timeout' => 5,
            'redirection' => 0,
            'sslverify' => true,
        )
    );
    if (is_wp_error($response) || (int) wp_remote_retrieve_response_code($response) !== 200) {
        $cache[$channel] = null;
        return null;
    }

    $metadata = json_decode(wp_remote_retrieve_body($response), true);
    $cache[$channel] = is_array($metadata) ? $metadata : null;
    return $cache[$channel];
}

/**
 * @param array|false          $update           Existing update payload.
 * @param array<string,string> $theme_data       Theme headers.
 * @param string               $theme_stylesheet Theme directory name.
 * @return array|false
 */
function parisii_updates_filter_theme($update, $theme_data, $theme_stylesheet) {
    if ($theme_stylesheet !== 'parisii-optique') {
        return $update;
    }

    $channel = parisii_updates_channel();
    if ($channel === '') {
        return false;
    }

    $catalog = parisii_updates_catalog($channel);
    if (!is_array($catalog) || !isset($catalog['theme']) || !is_array($catalog['theme'])) {
        return $update;
    }

    $item = $catalog['theme'];
    $installed = isset($theme_data['Version']) ? (string) $theme_data['Version'] : '';
    if (($item['slug'] ?? '') !== 'parisii-optique' || !parisii_updates_offer_is_installable($channel, $installed, $item)) {
        return false;
    }

    return array(
        'theme' => 'parisii-optique',
        'version' => (string) $item['version'],
        'new_version' => (string) $item['version'],
        'url' => 'https://updates.phangwilly.com/',
        'package' => (string) $item['package'],
        'requires' => '6.8',
        'requires_php' => '8.3',
    );
}
add_filter('update_themes_updates.phangwilly.com', 'parisii_updates_filter_theme', 10, 3);

/**
 * @param array|false          $update      Existing update payload.
 * @param array<string,string> $plugin_data Plugin headers.
 * @param string               $plugin_file Plugin basename.
 * @return array|false
 */
function parisii_updates_filter_plugin($update, $plugin_data, $plugin_file) {
    $channel = parisii_updates_channel();
    if ($channel === '') {
        return false;
    }

    $catalog = parisii_updates_catalog($channel);
    if (!is_array($catalog) || !isset($catalog['plugins']) || !is_array($catalog['plugins'])) {
        return $update;
    }

    $item = null;
    foreach ($catalog['plugins'] as $candidate) {
        if (is_array($candidate) && ($candidate['file'] ?? '') === $plugin_file) {
            $item = $candidate;
            break;
        }
    }
    if ($item === null) {
        return $update;
    }

    $installed = isset($plugin_data['Version']) ? (string) $plugin_data['Version'] : '';
    if (!parisii_updates_offer_is_installable($channel, $installed, $item)) {
        return false;
    }

    return array(
        'slug' => (string) $item['slug'],
        'plugin' => $plugin_file,
        'version' => (string) $item['version'],
        'new_version' => (string) $item['version'],
        'url' => 'https://updates.phangwilly.com/',
        'package' => (string) $item['package'],
        'requires' => '6.8',
        'requires_php' => '8.3',
    );
}
add_filter('update_plugins_updates.phangwilly.com', 'parisii_updates_filter_plugin', 10, 3);

/**
 * @param bool|null $update Existing auto-update decision.
 * @param object    $item   Theme update item.
 * @return bool|null
 */
function parisii_updates_disable_auto_update($update, $item) {
    if (isset($item->theme) && $item->theme === 'parisii-optique') {
        return false;
    }

    return $update;
}
add_filter('auto_update_theme', 'parisii_updates_disable_auto_update', 10, 2);

/**
 * @param bool|null $update Existing auto-update decision.
 * @param object    $item   Plugin update item.
 * @return bool|null
 */
function parisii_updates_disable_plugin_auto_update($update, $item) {
    $plugin = isset($item->plugin) ? (string) $item->plugin : '';
    $manual = array(
        'parisii-optique-plugin/parisii-optique-plugin.php',
        'updates-plugin/parisii-updates.php',
    );
    if (in_array($plugin, $manual, true)) {
        return false;
    }

    return $update;
}
add_filter('auto_update_plugin', 'parisii_updates_disable_plugin_auto_update', 10, 2);

/**
 * Reject a package whose root folder would create a second theme.
 *
 * @param string      $source        Extracted source path.
 * @param string      $remote_source Remote source path.
 * @param WP_Upgrader $upgrader      Upgrader instance.
 * @param array       $hook_extra    Upgrader context.
 * @return string|WP_Error
 */
function parisii_updates_check_package_root($source, $remote_source, $upgrader, $hook_extra) {
    unset($remote_source, $upgrader);
    $expected = '';
    if (($hook_extra['theme'] ?? '') === 'parisii-optique') {
        $expected = 'parisii-optique';
    }
    $plugin = isset($hook_extra['plugin']) ? (string) $hook_extra['plugin'] : '';
    if (in_array($plugin, array('parisii-optique-plugin/parisii-optique-plugin.php', 'updates-plugin/parisii-updates.php'), true)) {
        $expected = dirname($plugin);
    }
    if ($expected === '' || is_wp_error($source)) {
        return $source;
    }

    if (basename(untrailingslashit($source)) !== $expected) {
        return new WP_Error(
            'parisii_updates_bad_root',
            "Le dossier racine de l'archive doit s'appeler " . $expected . '.'
        );
    }

    return $source;
}
add_filter('upgrader_source_selection', 'parisii_updates_check_package_root', 10, 4);

/**
 * Manual check from the themes screen. It never installs an update.
 */
function parisii_updates_manual_check() {
    if (!current_user_can('update_themes') && !current_user_can('update_plugins')) {
        wp_die(esc_html('Permission refusee.'));
    }

    check_admin_referer('parisii_updates_check');
    delete_site_transient('update_themes');
    delete_site_transient('update_plugins');
    wp_update_themes();
    wp_update_plugins();
    wp_safe_redirect(admin_url('themes.php?parisii_updates_checked=1'));
    exit;
}
add_action('admin_post_parisii_updates_check', 'parisii_updates_manual_check');

/**
 * Show the manual check on the themes screen.
 */
function parisii_updates_admin_notice() {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || !in_array($screen->id, array('themes', 'plugins'), true)) {
        return;
    }
    if (!current_user_can('update_themes') && !current_user_can('update_plugins')) {
        return;
    }

    $channel = parisii_updates_channel();
    if ($channel === '') {
        echo '<div class="notice notice-warning"><p>Parisii Updates : WP_ENVIRONMENT_TYPE ne selectionne aucun canal. Aucune mise a jour ne sera proposee.</p></div>';
        return;
    }

    $url = wp_nonce_url(admin_url('admin-post.php?action=parisii_updates_check'), 'parisii_updates_check');
    echo '<div class="notice notice-info"><p>Parisii Updates consulte le canal ' . esc_html($channel) . '. ';
    echo '<a class="button" href="' . esc_url($url) . '">Verifier les mises a jour</a></p></div>';
}
add_action('admin_notices', 'parisii_updates_admin_notice');
