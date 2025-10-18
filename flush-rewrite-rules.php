<?php
/**
 * Temporary script to flush rewrite rules
 * Run this once to update the permalink structure
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator to run this script.');
}

echo "<h2>Mise à jour des règles de réécriture</h2>";

// Add the rewrite rules
function parisii_optique_customize_product_category_urls() {
    // Only run if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Add rewrite rules for product categories
    add_rewrite_rule(
        '^([^/]+)/?$',
        'index.php?product_cat=$matches[1]',
        'top'
    );
    
    // Add rewrite rules for product category pages with pagination
    add_rewrite_rule(
        '^([^/]+)/page/([0-9]+)/?$',
        'index.php?product_cat=$matches[1]&paged=$matches[2]',
        'top'
    );
}

// Apply the rewrite rules
parisii_optique_customize_product_category_urls();

// Flush rewrite rules
flush_rewrite_rules();

echo "<p style='color: green;'>✅ Les règles de réécriture ont été mises à jour avec succès !</p>";
echo "<p>Vous pouvez maintenant supprimer ce fichier (flush-rewrite-rules.php) car il n'est plus nécessaire.</p>";
echo "<p><a href='" . home_url() . "'>Retour au site</a> | <a href='" . admin_url() . "'>Administration</a></p>";

// Mark as completed
update_option('parisii_optique_flush_rewrite_rules', '1');
?>
