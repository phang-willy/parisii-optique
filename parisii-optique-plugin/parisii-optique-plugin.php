<?php
/**
 * Plugin Name: Parisii Optique
 * Plugin URI: https://parisii-optique.fr
 * Description: Plugin de gestion des marques pour Parisii Optique
 * Version: 1.0.0
 * Author: Parisii Optique
 * Author URI: https://parisii-optique.fr
 * Text Domain: parisii-optique-plugin
 * Domain Path: /languages
 * Requires at least: 6.8
 * Requires PHP: 8.3
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PARISII_OPTIQUE_PLUGIN_VERSION', '1.0.0');
define('PARISII_OPTIQUE_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('PARISII_OPTIQUE_PLUGIN_URL', plugin_dir_url(__FILE__));

/**
 * Plugin activation hook
 */
function parisii_optique_plugin_activate() {
    require_once PARISII_OPTIQUE_PLUGIN_PATH . 'includes/class-database.php';
    Parisii_Optique_Database::create_tables();
    
    // Flush rewrite rules for custom templates
    flush_rewrite_rules();
}
register_activation_hook(__FILE__, 'parisii_optique_plugin_activate');

/**
 * Plugin deactivation hook
 */
function parisii_optique_plugin_deactivate() {
    flush_rewrite_rules();
}
register_deactivation_hook(__FILE__, 'parisii_optique_plugin_deactivate');

/**
 * Load plugin text domain
 */
function parisii_optique_plugin_load_textdomain() {
    load_plugin_textdomain('parisii-optique-plugin', false, dirname(plugin_basename(__FILE__)) . '/languages');
}
add_action('plugins_loaded', 'parisii_optique_plugin_load_textdomain');

/**
 * Include required files
 */
require_once PARISII_OPTIQUE_PLUGIN_PATH . 'includes/class-database.php';
require_once PARISII_OPTIQUE_PLUGIN_PATH . 'includes/class-brand.php';
require_once PARISII_OPTIQUE_PLUGIN_PATH . 'admin/class-admin-menu.php';
require_once PARISII_OPTIQUE_PLUGIN_PATH . 'admin/class-brand-list.php';
require_once PARISII_OPTIQUE_PLUGIN_PATH . 'admin/class-brand-form.php';
require_once PARISII_OPTIQUE_PLUGIN_PATH . 'public/class-template-loader.php';
require_once PARISII_OPTIQUE_PLUGIN_PATH . 'public/class-brand-display.php';

/**
 * Initialize the plugin
 */
function parisii_optique_plugin_init() {
    // Initialize admin menu
    if (is_admin()) {
        new Parisii_Optique_Admin_Menu();
    }
    
    // Initialize template loader
    new Parisii_Optique_Template_Loader();
    
    // Initialize brand display
    new Parisii_Optique_Brand_Display();
}
add_action('init', 'parisii_optique_plugin_init');

/**
 * Enqueue admin styles and scripts
 */
function parisii_optique_plugin_admin_enqueue_scripts($hook) {
    // Only load on plugin pages
    if (strpos($hook, 'parisii-optique') === false) {
        return;
    }
    
    wp_enqueue_style('parisii-optique-admin', PARISII_OPTIQUE_PLUGIN_URL . 'admin/css/admin.css', array(), PARISII_OPTIQUE_PLUGIN_VERSION);
    wp_enqueue_media();
    wp_enqueue_script('parisii-optique-admin', PARISII_OPTIQUE_PLUGIN_URL . 'admin/js/admin.js', array('jquery'), PARISII_OPTIQUE_PLUGIN_VERSION, true);
    
    // WordPress color picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
}
add_action('admin_enqueue_scripts', 'parisii_optique_plugin_admin_enqueue_scripts');

/**
 * Enqueue public styles and scripts
 */
function parisii_optique_plugin_public_enqueue_scripts() {
    wp_enqueue_style('parisii-optique-public', PARISII_OPTIQUE_PLUGIN_URL . 'public/css/public.css', array(), PARISII_OPTIQUE_PLUGIN_VERSION);
    wp_enqueue_script('parisii-optique-public', PARISII_OPTIQUE_PLUGIN_URL . 'public/js/public.js', array('jquery'), PARISII_OPTIQUE_PLUGIN_VERSION, true);
}
add_action('wp_enqueue_scripts', 'parisii_optique_plugin_public_enqueue_scripts');

