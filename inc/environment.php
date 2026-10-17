<?php
/**
 * Environment helpers.
 *
 * Production and pre-production are selected only with the native
 * WP_ENVIRONMENT_TYPE constant in wp-config.php (never committed):
 *
 * Production (OVH):
 * define('WP_ENVIRONMENT_TYPE', 'production');
 *
 * Pre-production (Hostinger):
 * define('WP_ENVIRONMENT_TYPE', 'staging');
 *
 * If the constant is missing, the theme does not assume production.
 * Public pages are marked noindex and an admin notice is shown.
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Official production origin used for canonical URLs in production.
 *
 * @return string
 */
function parisii_optique_official_origin() {
    return 'https://parisii-optique.fr';
}

/**
 * Raw WP_ENVIRONMENT_TYPE value, or an empty string when it is not defined.
 *
 * @return string
 */
function parisii_optique_environment_type() {
    if (!defined('WP_ENVIRONMENT_TYPE')) {
        return '';
    }

    $type = strtolower(trim((string) WP_ENVIRONMENT_TYPE));
    $allowed = ['local', 'development', 'staging', 'production'];

    return in_array($type, $allowed, true) ? $type : '';
}

/**
 * Whether the environment constant is missing or invalid.
 *
 * @return bool
 */
function parisii_optique_environment_is_configured() {
    return parisii_optique_environment_type() !== '';
}

/**
 * Whether the current site is production.
 *
 * @return bool
 */
function parisii_is_production() {
    return parisii_optique_environment_type() === 'production';
}

/**
 * Whether the current site is pre-production.
 *
 * @return bool
 */
function parisii_is_staging() {
    return parisii_optique_environment_type() === 'staging';
}

/**
 * Explain the missing environment in the admin and in Site Health.
 */
function parisii_optique_environment_admin_notice() {
    if (!current_user_can('manage_options') || parisii_optique_environment_is_configured()) {
        return;
    }

    echo '<div class="notice notice-error"><p><strong>Parisii Optique :</strong> ';
    echo esc_html("WP_ENVIRONMENT_TYPE n'est pas défini dans wp-config.php. Le site est traité comme non indexable. En local : define('WP_ENVIRONMENT_TYPE', 'local'); en préproduction : define('WP_ENVIRONMENT_TYPE', 'staging'); en production : define('WP_ENVIRONMENT_TYPE', 'production');.");
    echo '</p></div>';
}
add_action('admin_notices', 'parisii_optique_environment_admin_notice');

/**
 * Site Health check for the environment constant.
 *
 * @param array<string,array<string,mixed>> $tests Site Health tests.
 * @return array<string,array<string,mixed>>
 */
function parisii_optique_site_status_tests($tests) {
    $tests['direct']['parisii_environment'] = [
        'label' => 'Environnement WordPress Parisii Optique',
        'test'  => 'parisii_optique_site_status_environment',
    ];

    return $tests;
}
add_filter('site_status_tests', 'parisii_optique_site_status_tests');

/**
 * Site Health result for WP_ENVIRONMENT_TYPE.
 *
 * @return array<string,mixed>
 */
function parisii_optique_site_status_environment() {
    $type = parisii_optique_environment_type();
    $result = [
        'label'       => 'Environnement WordPress configuré',
        'status'      => 'good',
        'badge'       => [
            'label' => 'SEO',
            'color' => 'blue',
        ],
        'description' => '<p>WP_ENVIRONMENT_TYPE = ' . esc_html($type) . '.</p>',
        'actions'     => '',
        'test'        => 'parisii_environment',
    ];

    if ($type === '') {
        $result['status'] = 'critical';
        $result['label'] = 'WP_ENVIRONMENT_TYPE est absent ou invalide';
        $result['description'] = "<p>Ajoutez dans wp-config.php, sans le committer : define('WP_ENVIRONMENT_TYPE', 'local'); en local, define('WP_ENVIRONMENT_TYPE', 'staging'); en préproduction, ou define('WP_ENVIRONMENT_TYPE', 'production'); en production. Tant que cette constante manque, les pages publiques restent en noindex.</p>";
    }

    return $result;
}
