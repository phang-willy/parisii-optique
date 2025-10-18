<?php
/**
 * Script pour diagnostiquer et corriger les permaliens WordPress
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator to run this script.');
}

echo "<h2>🔧 Diagnostic et correction des permaliens</h2>";

// Vérifier la configuration actuelle
$permalink_structure = get_option('permalink_structure');
$rewrite_rules = get_option('rewrite_rules');

echo "<h3>Configuration actuelle :</h3>";
echo "<p><strong>Structure des permaliens :</strong> " . ($permalink_structure ?: 'Non configurée') . "</p>";
echo "<p><strong>Règles de réécriture :</strong> " . (empty($rewrite_rules) ? 'Aucune' : count($rewrite_rules) . ' règles') . "</p>";

// Vérifier si mod_rewrite est activé
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $mod_rewrite_enabled = in_array('mod_rewrite', $modules);
    echo "<p><strong>mod_rewrite :</strong> " . ($mod_rewrite_enabled ? '✅ Activé' : '❌ Désactivé') . "</p>";
} else {
    echo "<p><strong>mod_rewrite :</strong> Impossible de vérifier (pas Apache)</p>";
}

// Vérifier le fichier .htaccess
$htaccess_file = ABSPATH . '.htaccess';
$htaccess_exists = file_exists($htaccess_file);
$htaccess_writable = $htaccess_exists ? is_writable($htaccess_file) : is_writable(ABSPATH);

echo "<p><strong>Fichier .htaccess :</strong> " . ($htaccess_exists ? '✅ Existe' : '❌ Manquant') . "</p>";
echo "<p><strong>Droits d'écriture :</strong> " . ($htaccess_writable ? '✅ Écriture autorisée' : '❌ Pas d\'écriture') . "</p>";

// Configuration recommandée
echo "<h3>Configuration recommandée :</h3>";

if (!$permalink_structure) {
    echo "<p style='color: orange;'>⚠️ Aucune structure de permaliens configurée</p>";
    echo "<p>Configuration recommandée : <code>/%postname%/</code></p>";
    
    // Configurer automatiquement
    if (isset($_GET['auto_configure'])) {
        update_option('permalink_structure', '/%postname%/');
        echo "<p style='color: green;'>✅ Structure des permaliens configurée automatiquement</p>";
    } else {
        echo "<p><a href='?auto_configure=1' class='button button-primary'>Configurer automatiquement</a></p>";
    }
} else {
    echo "<p style='color: green;'>✅ Structure des permaliens configurée : <code>$permalink_structure</code></p>";
}

// Vérifier les règles de réécriture pour les catégories de produits
echo "<h3>Règles de réécriture pour les catégories de produits :</h3>";

$product_cat_rules = array_filter($rewrite_rules, function($rule, $pattern) {
    return strpos($pattern, 'product_cat') !== false;
}, ARRAY_FILTER_USE_BOTH);

if (empty($product_cat_rules)) {
    echo "<p style='color: orange;'>⚠️ Aucune règle de réécriture pour les catégories de produits</p>";
    
    // Ajouter les règles manuellement
    if (isset($_GET['add_rules'])) {
        // Ajouter les règles de réécriture
        add_rewrite_rule(
            '^([^/]+)/?$',
            'index.php?product_cat=$matches[1]',
            'top'
        );
        
        add_rewrite_rule(
            '^([^/]+)/page/([0-9]+)/?$',
            'index.php?product_cat=$matches[1]&paged=$matches[2]',
            'top'
        );
        
        flush_rewrite_rules();
        echo "<p style='color: green;'>✅ Règles de réécriture ajoutées</p>";
    } else {
        echo "<p><a href='?add_rules=1' class='button button-primary'>Ajouter les règles</a></p>";
    }
} else {
    echo "<p style='color: green;'>✅ Règles de réécriture trouvées :</p>";
    foreach ($product_cat_rules as $pattern => $replacement) {
        echo "<p><code>$pattern</code> → <code>$replacement</code></p>";
    }
}

// Créer/mettre à jour le fichier .htaccess
if (isset($_GET['create_htaccess'])) {
    $htaccess_content = "# BEGIN WordPress
<IfModule mod_rewrite.c>
RewriteEngine On
RewriteRule .* - [E=HTTP_AUTHORIZATION:%{HTTP:Authorization}]
RewriteBase /parisii-optique/
RewriteRule ^index\.php$ - [L]
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule . /parisii-optique/index.php [L]
</IfModule>
# END WordPress";

    if (file_put_contents($htaccess_file, $htaccess_content)) {
        echo "<p style='color: green;'>✅ Fichier .htaccess créé/mis à jour</p>";
    } else {
        echo "<p style='color: red;'>❌ Impossible de créer le fichier .htaccess</p>";
    }
} else {
    if (!$htaccess_exists || !$htaccess_writable) {
        echo "<p><a href='?create_htaccess=1' class='button button-primary'>Créer/Mettre à jour .htaccess</a></p>";
    }
}

// Test des URLs
echo "<h3>Test des URLs :</h3>";
$test_urls = [
    home_url('/marques/'),
    home_url('/lunettes/'),
    home_url('/verres/'),
];

foreach ($test_urls as $url) {
    echo "<p><a href='$url' target='_blank'>$url</a></p>";
}

echo "<hr>";
echo "<p><a href='" . home_url() . "'>Retour au site</a> | <a href='" . admin_url('options-permalink.php') . "'>Configuration des permaliens</a></p>";
?>
