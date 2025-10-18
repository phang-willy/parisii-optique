<?php
/**
 * Script pour corriger la page d'accueil
 */

// Load WordPress
require_once('../../../wp-load.php');

// Check if user is admin
if (!current_user_can('manage_options')) {
    die('Access denied. You must be an administrator to run this script.');
}

echo "<h2>🏠 Correction de la page d'accueil</h2>";

// 1. Vérifier la configuration de la page d'accueil
echo "<h3>1. Configuration de la page d'accueil :</h3>";
$show_on_front = get_option('show_on_front');
$page_on_front = get_option('page_on_front');

echo "<p><strong>Type d'affichage :</strong> " . $show_on_front . "</p>";

if ($show_on_front === 'page' && $page_on_front) {
    $front_page = get_post($page_on_front);
    echo "<p><strong>Page d'accueil :</strong> " . ($front_page ? $front_page->post_title : 'Page introuvable') . "</p>";
} else {
    echo "<p><strong>Page d'accueil :</strong> Articles récents</p>";
}

// 2. Nettoyer les règles de réécriture problématiques
echo "<h3>2. Nettoyage des règles de réécriture :</h3>";

$rewrite_rules = get_option('rewrite_rules', []);
$original_count = count($rewrite_rules);

// Supprimer les règles trop génériques qui interfèrent avec la page d'accueil
$clean_rules = array_filter($rewrite_rules, function($rule, $pattern) {
    // Supprimer les règles qui matchent tout
    if ($pattern === '^([^/]+)/?$') {
        return false;
    }
    if ($pattern === '^([^/]+)/$') {
        return false;
    }
    return true;
}, ARRAY_FILTER_USE_BOTH);

update_option('rewrite_rules', $clean_rules);
$new_count = count($clean_rules);

echo "<p>Règles supprimées : " . ($original_count - $new_count) . "</p>";
echo "<p style='color: green;'>✅ Règles nettoyées</p>";

// 3. Configurer les permaliens si nécessaire
echo "<h3>3. Configuration des permaliens :</h3>";
$permalink_structure = get_option('permalink_structure');

if (!$permalink_structure) {
    update_option('permalink_structure', '/%postname%/');
    echo "<p style='color: green;'>✅ Structure des permaliens configurée : /%postname%/</p>";
} else {
    echo "<p style='color: green;'>✅ Structure des permaliens : $permalink_structure</p>";
}

// 4. Forcer la mise à jour des règles de réécriture
echo "<h3>4. Mise à jour des règles de réécriture :</h3>";
flush_rewrite_rules(true);
echo "<p style='color: green;'>✅ Règles de réécriture mises à jour</p>";

// 5. Ajouter des règles spécifiques pour les catégories de produits
echo "<h3>5. Ajout des règles spécifiques pour les catégories :</h3>";

if (class_exists('WooCommerce')) {
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'fields' => 'slugs'
    ]);
    
    if (!empty($categories) && !is_wp_error($categories)) {
        $category_slugs = implode('|', array_map('preg_quote', $categories));
        
        // Ajouter des règles spécifiques
        add_rewrite_rule(
            '^(' . $category_slugs . ')/?$',
            'index.php?product_cat=$matches[1]',
            'top'
        );
        
        add_rewrite_rule(
            '^(' . $category_slugs . ')/page/([0-9]+)/?$',
            'index.php?product_cat=$matches[1]&paged=$matches[2]',
            'top'
        );
        
        flush_rewrite_rules(true);
        
        echo "<p style='color: green;'>✅ Règles spécifiques ajoutées pour " . count($categories) . " catégories</p>";
        echo "<p>Catégories : " . implode(', ', $categories) . "</p>";
    } else {
        echo "<p style='color: orange;'>⚠️ Aucune catégorie de produit trouvée</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ WooCommerce n'est pas installé</p>";
}

// 6. Test de la page d'accueil
echo "<h3>6. Test de la page d'accueil :</h3>";

$home_url = home_url('/');
echo "<p><strong>URL de la page d'accueil :</strong> <a href='$home_url' target='_blank'>$home_url</a></p>";

// Vérifier si la page d'accueil est accessible
$response = wp_remote_get($home_url);
if (!is_wp_error($response)) {
    $status_code = wp_remote_retrieve_response_code($response);
    if ($status_code === 200) {
        echo "<p style='color: green;'>✅ Page d'accueil accessible (HTTP 200)</p>";
    } else {
        echo "<p style='color: red;'>❌ Page d'accueil non accessible (HTTP $status_code)</p>";
    }
} else {
    echo "<p style='color: orange;'>⚠️ Impossible de tester la page d'accueil</p>";
}

// 7. Vérifier les articles récents
echo "<h3>7. Vérification du contenu :</h3>";
$recent_posts = get_posts(['numberposts' => 3]);
echo "<p><strong>Articles récents :</strong> " . count($recent_posts) . "</p>";

if (!empty($recent_posts)) {
    echo "<ul>";
    foreach ($recent_posts as $post) {
        echo "<li><a href='" . get_permalink($post) . "' target='_blank'>" . $post->post_title . "</a></li>";
    }
    echo "</ul>";
} else {
    echo "<p style='color: orange;'>⚠️ Aucun article trouvé</p>";
}

echo "<hr>";
echo "<p><strong>✅ Correction terminée !</strong></p>";
echo "<p>Vous pouvez maintenant supprimer ce fichier (fix-homepage.php) car il n'est plus nécessaire.</p>";
echo "<p><a href='" . home_url() . "' target='_blank'>Tester la page d'accueil</a> | <a href='" . admin_url() . "'>Administration</a></p>";
?>
