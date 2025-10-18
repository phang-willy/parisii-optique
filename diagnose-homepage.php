<?php
/**
 * Script de diagnostic pour la page d'accueil
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator to run this script.');
}

echo "<h2>🔍 Diagnostic de la page d'accueil</h2>";

// Vérifier la configuration de la page d'accueil
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');
$page_for_posts = get_option('page_for_posts');

echo "<h3>Configuration de la page d'accueil :</h3>";
echo "<p><strong>Type d'affichage :</strong> " . $show_on_front . "</p>";
echo "<p><strong>Page d'accueil :</strong> " . ($page_on_front ? get_the_title($page_on_front) : 'Aucune') . "</p>";
echo "<p><strong>Page des articles :</strong> " . ($page_for_posts ? get_the_title($page_for_posts) : 'Aucune') . "</p>";

// Vérifier les règles de réécriture
$rewrite_rules = get_option('rewrite_rules', []);
echo "<h3>Règles de réécriture :</h3>";
echo "<p><strong>Nombre de règles :</strong> " . count($rewrite_rules) . "</p>";

// Vérifier les règles problématiques
$problematic_rules = array_filter($rewrite_rules, function($rule, $pattern) {
    return strpos($pattern, '^([^/]+)/?$') !== false;
}, ARRAY_FILTER_USE_BOTH);

if (!empty($problematic_rules)) {
    echo "<p style='color: orange;'>⚠️ Règles problématiques détectées :</p>";
    foreach ($problematic_rules as $pattern => $replacement) {
        echo "<p><code>$pattern</code> → <code>$replacement</code></p>";
    }
}

// Vérifier la structure des permaliens
$permalink_structure = get_option('permalink_structure');
echo "<p><strong>Structure des permaliens :</strong> " . ($permalink_structure ?: 'Non configurée') . "</p>";

// Vérifier si mod_rewrite fonctionne
echo "<h3>Test de mod_rewrite :</h3>";
if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    $mod_rewrite_enabled = in_array('mod_rewrite', $modules);
    echo "<p><strong>mod_rewrite :</strong> " . ($mod_rewrite_enabled ? '✅ Activé' : '❌ Désactivé') . "</p>";
} else {
    echo "<p><strong>mod_rewrite :</strong> Impossible de vérifier</p>";
}

// Vérifier le fichier .htaccess
$htaccess_file = ABSPATH . '.htaccess';
$htaccess_content = file_exists($htaccess_file) ? file_get_contents($htaccess_file) : '';
echo "<p><strong>Fichier .htaccess :</strong> " . (file_exists($htaccess_file) ? '✅ Existe' : '❌ Manquant') . "</p>";

if (strpos($htaccess_content, 'RewriteEngine On') !== false) {
    echo "<p style='color: green;'>✅ RewriteEngine activé dans .htaccess</p>";
} else {
    echo "<p style='color: red;'>❌ RewriteEngine non trouvé dans .htaccess</p>";
}

// Test de la page d'accueil
echo "<h3>Test de la page d'accueil :</h3>";

// Simuler une requête vers la page d'accueil
$_SERVER['REQUEST_URI'] = '/parisii-optique/';
$_SERVER['REQUEST_METHOD'] = 'GET';

// Vérifier si la page d'accueil est accessible
$home_url = home_url('/');
echo "<p><strong>URL de la page d'accueil :</strong> <a href='$home_url' target='_blank'>$home_url</a></p>";

// Vérifier les posts récents
$recent_posts = get_posts(['numberposts' => 5]);
echo "<p><strong>Articles récents :</strong> " . count($recent_posts) . "</p>";

// Vérifier les pages
$pages = get_pages();
echo "<p><strong>Pages :</strong> " . count($pages) . "</p>";

// Solution recommandée
echo "<h3>🔧 Solution recommandée :</h3>";

if (empty($rewrite_rules)) {
    echo "<p style='color: red;'>❌ Aucune règle de réécriture trouvée</p>";
    echo "<p><a href='?fix_rewrite=1' class='button button-primary'>Corriger les règles de réécriture</a></p>";
} else {
    echo "<p style='color: green;'>✅ Règles de réécriture présentes</p>";
}

if (!$permalink_structure) {
    echo "<p style='color: orange;'>⚠️ Structure des permaliens non configurée</p>";
    echo "<p><a href='?fix_permalinks=1' class='button button-primary'>Configurer les permaliens</a></p>";
}

// Actions de correction
if (isset($_GET['fix_rewrite'])) {
    echo "<h3>🔧 Correction des règles de réécriture...</h3>";
    
    // Supprimer les règles problématiques
    $clean_rules = array_filter($rewrite_rules, function($rule, $pattern) {
        return strpos($pattern, '^([^/]+)/?$') === false;
    }, ARRAY_FILTER_USE_BOTH);
    
    update_option('rewrite_rules', $clean_rules);
    flush_rewrite_rules(true);
    
    echo "<p style='color: green;'>✅ Règles problématiques supprimées</p>";
    echo "<p><a href='?'>Recharger le diagnostic</a></p>";
}

if (isset($_GET['fix_permalinks'])) {
    echo "<h3>🔧 Configuration des permaliens...</h3>";
    
    update_option('permalink_structure', '/%postname%/');
    flush_rewrite_rules(true);
    
    echo "<p style='color: green;'>✅ Permaliens configurés</p>";
    echo "<p><a href='?'>Recharger le diagnostic</a></p>";
}

echo "<hr>";
echo "<p><a href='" . home_url() . "'>Tester la page d'accueil</a> | <a href='" . admin_url() . "'>Administration</a></p>";
?>
