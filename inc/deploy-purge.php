<?php
/**
 * Deploy purge endpoint - clears cache after theme deployment
 *
 * Call (POST): https://site.com/ with parisii_deploy_purge=1&token=YOUR_SECRET_TOKEN
 * Define PARISII_DEPLOY_PURGE_TOKEN in wp-config.php
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'parisii_optique_deploy_purge_handler', 1);

function parisii_optique_deploy_purge_handler() {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        return;
    }

    if (!isset($_POST['parisii_deploy_purge']) || !isset($_POST['token']) || !defined('PARISII_DEPLOY_PURGE_TOKEN')) {
        return;
    }

    $token = sanitize_text_field(wp_unslash($_POST['token']));
    if (!hash_equals(PARISII_DEPLOY_PURGE_TOKEN, $token)) {
        status_header(403);
        exit('Invalid token');
    }

    $purged = [];

    // WordPress object cache
    wp_cache_flush();
    $purged[] = 'wp_cache';

    // WP Rocket
    if (function_exists('rocket_clean_domain')) {
        rocket_clean_domain();
        $purged[] = 'wp_rocket';
    }

    // LiteSpeed Cache
    if (class_exists('LiteSpeed\Purge')) {
        do_action('litespeed_purge_all');
        $purged[] = 'litespeed';
    }

    // W3 Total Cache
    if (function_exists('w3tc_flush_all')) {
        w3tc_flush_all();
        $purged[] = 'w3tc';
    }

    // WP Super Cache
    if (function_exists('wp_cache_clear_cache')) {
        wp_cache_clear_cache();
        $purged[] = 'wp_super_cache';
    }

    // Autoptimize
    if (class_exists('autoptimizeCache')) {
        autoptimizeCache::clearall();
        $purged[] = 'autoptimize';
    }

    // Generic action for other plugins
    do_action('parisii_optique_deploy_purge');

    status_header(200);
    header('Content-Type: application/json');
    echo wp_json_encode(['success' => true, 'purged' => $purged]);
    exit;
}
