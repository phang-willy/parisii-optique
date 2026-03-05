<?php
/**
 * Plugin Sync - Synchronisation automatique du plugin en développement
 * Synchronise parisii-optique-plugin du thème vers le dossier plugins
 *
 * @package Parisii_Optique
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Vérifier si on est en mode développement
 */
function parisii_optique_is_dev_mode() {
    // Vérifier WP_DEBUG ou WP_ENVIRONMENT_TYPE
    $is_dev = (defined('WP_DEBUG') && WP_DEBUG === true) || 
              (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'development') ||
              (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'local');
    
    return $is_dev;
}

/**
 * Synchroniser le plugin du thème vers le dossier plugins
 */
function parisii_optique_sync_plugin() {
    // Ne fonctionne qu'en mode développement
    if (!parisii_optique_is_dev_mode()) {
        return;
    }
    
    // Ne s'exécute que dans l'admin
    if (!is_admin()) {
        return;
    }
    
    $source = get_template_directory() . '/parisii-optique-plugin';
    $destination = WP_PLUGIN_DIR . '/parisii-optique-plugin';
    
    // Vérifier si les deux dossiers existent
    if (!file_exists($source) || !file_exists($destination)) {
        return;
    }
    
    // Vérifier si des fichiers ont été modifiés
    $needs_sync = parisii_optique_check_if_sync_needed($source, $destination);
    
    if ($needs_sync) {
        // Synchroniser les fichiers (sans afficher de notice : message uniquement au clic sur les boutons dédiés)
        parisii_optique_recursive_sync($source, $destination);
    }
}
add_action('admin_init', 'parisii_optique_sync_plugin');

/**
 * Vérifier si une synchronisation est nécessaire
 */
function parisii_optique_check_if_sync_needed($source, $destination) {
    // Utiliser un cache pour ne pas vérifier à chaque chargement
    $last_check = get_transient('parisii_optique_last_sync_check');
    
    // Vérifier toutes les 30 secondes
    if ($last_check !== false) {
        return false;
    }
    
    // Marquer qu'on a vérifié
    set_transient('parisii_optique_last_sync_check', time(), 30);
    
    // Comparer les timestamps des fichiers
    return parisii_optique_compare_directories($source, $destination);
}

/**
 * Comparer deux dossiers pour détecter des modifications
 */
function parisii_optique_compare_directories($source, $destination) {
    $source_files = parisii_optique_get_all_files($source);
    
    foreach ($source_files as $file) {
        $relative_path = str_replace($source, '', $file);
        $dest_file = $destination . $relative_path;
        
        // Si le fichier n'existe pas dans la destination, sync nécessaire
        if (!file_exists($dest_file)) {
            return true;
        }
        
        // Comparer les timestamps
        if (filemtime($file) > filemtime($dest_file)) {
            return true;
        }
    }
    
    return false;
}

/**
 * Obtenir tous les fichiers d'un dossier récursivement
 */
function parisii_optique_get_all_files($dir) {
    $files = array();
    
    if (!is_dir($dir)) {
        return $files;
    }
    
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
        RecursiveIteratorIterator::SELF_FIRST
    );
    
    foreach ($iterator as $file) {
        if ($file->isFile()) {
            $files[] = $file->getPathname();
        }
    }
    
    return $files;
}

/**
 * Synchroniser récursivement les fichiers
 */
function parisii_optique_recursive_sync($source, $destination) {
    if (!is_dir($source)) {
        return;
    }
    
    // Créer le dossier de destination s'il n'existe pas
    if (!is_dir($destination)) {
        wp_mkdir_p($destination);
    }
    
    $dir = opendir($source);
    
    while (false !== ($file = readdir($dir))) {
        if ($file === '.' || $file === '..') {
            continue;
        }
        
        $source_path = $source . '/' . $file;
        $dest_path = $destination . '/' . $file;
        
        if (is_dir($source_path)) {
            // Synchroniser le sous-dossier
            parisii_optique_recursive_sync($source_path, $dest_path);
        } else {
            // Copier le fichier si plus récent
            if (!file_exists($dest_path) || filemtime($source_path) > filemtime($dest_path)) {
                copy($source_path, $dest_path);
            }
        }
    }
    
    closedir($dir);
}

/**
 * Notice de succès après synchronisation
 */
function parisii_optique_sync_success_notice() {
    ?>
    <div class="notice notice-success is-dismissible">
        <p>
            <strong><?php _e('🔄 Synchronisation:', 'parisii-optique'); ?></strong>
            <?php _e('Le plugin Parisii Optique a été synchronisé depuis le thème.', 'parisii-optique'); ?>
        </p>
    </div>
    <?php
}

/**
 * Ajouter une notice indiquant qu'on est en mode développement
 */
function parisii_optique_dev_mode_notice() {
    if (!parisii_optique_is_dev_mode()) {
        return;
    }
    
    // Ne montrer que sur la page des plugins
    $screen = get_current_screen();
    if ($screen && $screen->id !== 'plugins') {
        return;
    }
    
    ?>
    <div class="notice notice-info">
        <p>
            <strong><?php _e('⚙️ Mode Développement:', 'parisii-optique'); ?></strong>
            <?php _e('La synchronisation automatique du plugin Parisii Optique est active. Les modifications dans le thème seront copiées automatiquement.', 'parisii-optique'); ?>
        </p>
    </div>
    <?php
}
add_action('admin_notices', 'parisii_optique_dev_mode_notice');

/**
 * Ajouter un bouton de synchronisation manuelle dans l'admin
 */
function parisii_optique_add_sync_button() {
    if (!parisii_optique_is_dev_mode()) {
        return;
    }
    
    // Vérifier si on a cliqué sur le bouton
    if (isset($_GET['parisii_sync_plugin']) && check_admin_referer('parisii_sync_plugin')) {
        $source = get_template_directory() . '/parisii-optique-plugin';
        $destination = WP_PLUGIN_DIR . '/parisii-optique-plugin';
        
        if (file_exists($source) && file_exists($destination)) {
            // Forcer la synchronisation
            delete_transient('parisii_optique_last_sync_check');
            parisii_optique_recursive_sync($source, $destination);
            
            wp_redirect(admin_url('plugins.php?parisii_sync_success=1'));
            exit;
        }
    }
    
    // Afficher le message de succès
    if (isset($_GET['parisii_sync_success'])) {
        add_action('admin_notices', function() {
            ?>
            <div class="notice notice-success is-dismissible">
                <p>
                    <strong><?php _e('✅ Synchronisation forcée:', 'parisii-optique'); ?></strong>
                    <?php _e('Le plugin Parisii Optique a été synchronisé avec succès !', 'parisii-optique'); ?>
                </p>
            </div>
            <?php
        });
    }
}
add_action('admin_init', 'parisii_optique_add_sync_button');

/**
 * Ajouter un lien de synchronisation dans la liste des plugins
 */
function parisii_optique_plugin_action_links($actions, $plugin_file) {
    if (!parisii_optique_is_dev_mode()) {
        return $actions;
    }
    
    if ($plugin_file === 'parisii-optique-plugin/parisii-optique-plugin.php') {
        $sync_url = wp_nonce_url(
            admin_url('plugins.php?parisii_sync_plugin=1'),
            'parisii_sync_plugin'
        );
        
        $sync_link = '<a href="' . esc_url($sync_url) . '" style="color: #2271b1; font-weight: 600;">🔄 ' . __('Synchroniser', 'parisii-optique') . '</a>';
        
        $actions = array_merge(array('sync' => $sync_link), $actions);
    }
    
    return $actions;
}
add_filter('plugin_action_links', 'parisii_optique_plugin_action_links', 10, 2);

