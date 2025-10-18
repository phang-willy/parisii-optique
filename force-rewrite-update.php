<?php
/**
 * Script pour forcer la mise à jour des règles de réécriture WordPress
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator to run this script.');
}

echo "<h2>🔄 Mise à jour forcée des règles de réécriture</h2>";

// Vérifier la configuration actuelle
$permalink_structure = get_option('permalink_structure');
echo "<p><strong>Structure des permaliens actuelle :</strong> " . ($permalink_structure ?: 'Non configurée') . "</p>";

// Si pas de structure configurée, en configurer une
if (!$permalink_structure) {
    echo "<p style='color: orange;'>⚠️ Configuration des permaliens...</p>";
    update_option('permalink_structure', '/%postname%/');
    echo "<p style='color: green;'>✅ Structure configurée : /%postname%/</p>";
}

// Ajouter nos règles de réécriture personnalisées
echo "<p>🔧 Ajout des règles de réécriture personnalisées...</p>";

// Supprimer les anciennes règles si elles existent
$rewrite_rules = get_option('rewrite_rules', []);
$custom_rules = [
    '^([^/]+)/?$' => 'index.php?product_cat=$matches[1]',
    '^([^/]+)/page/([0-9]+)/?$' => 'index.php?product_cat=$matches[1]&paged=$matches[2]',
];

// Ajouter nos règles
foreach ($custom_rules as $pattern => $replacement) {
    $rewrite_rules[$pattern] = $replacement;
}

// Sauvegarder les règles
update_option('rewrite_rules', $rewrite_rules);

// Forcer la mise à jour des règles de réécriture
flush_rewrite_rules(true);

echo "<p style='color: green;'>✅ Règles de réécriture mises à jour</p>";

// Vérifier les catégories de produits existantes
echo "<h3>📋 Catégories de produits disponibles :</h3>";

if (class_exists('WooCommerce')) {
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);
    
    if (!empty($categories) && !is_wp_error($categories)) {
        echo "<ul>";
        foreach ($categories as $category) {
            $old_url = home_url('/categorie-produit/' . $category->slug . '/');
            $new_url = home_url('/' . $category->slug . '/');
            echo "<li><strong>{$category->name}</strong> ({$category->slug})</li>";
            echo "<li style='margin-left: 20px; color: #666;'>Ancienne URL : <a href='$old_url' target='_blank'>$old_url</a></li>";
            echo "<li style='margin-left: 20px; color: #666;'>Nouvelle URL : <a href='$new_url' target='_blank'>$new_url</a></li>";
            echo "<br>";
        }
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'>⚠️ Aucune catégorie de produit trouvée</p>";
    }
} else {
    echo "<p style='color: red;'>❌ WooCommerce n'est pas installé</p>";
}

// Test de redirection
echo "<h3>🧪 Test de redirection :</h3>";
echo "<p>Testez ces URLs :</p>";
echo "<ul>";
echo "<li><a href='" . home_url('/marques/') . "' target='_blank'>" . home_url('/marques/') . "</a></li>";
echo "<li><a href='" . home_url('/lunettes/') . "' target='_blank'>" . home_url('/lunettes/') . "</a></li>";
echo "<li><a href='" . home_url('/verres/') . "' target='_blank'>" . home_url('/verres/') . "</a></li>";
echo "</ul>";

// Vérifier si les URLs avec index.php redirigent correctement
echo "<h3>🔄 Test de redirection depuis index.php :</h3>";
echo "<p>Ces URLs devraient rediriger automatiquement :</p>";
echo "<ul>";
echo "<li><a href='" . home_url('/index.php/marques/') . "' target='_blank'>" . home_url('/index.php/marques/') . "</a> → devrait rediriger vers " . home_url('/marques/') . "</li>";
echo "</ul>";

echo "<hr>";
echo "<p><strong>✅ Mise à jour terminée !</strong></p>";
echo "<p>Vous pouvez maintenant supprimer ce fichier (force-rewrite-update.php) car il n'est plus nécessaire.</p>";
echo "<p><a href='" . home_url() . "'>Retour au site</a> | <a href='" . admin_url() . "'>Administration</a></p>";

// Marquer comme terminé
update_option('parisii_optique_flush_rewrite_rules', '1');
?>
