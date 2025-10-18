<?php
/**
 * Plugin Auto-Installation - Parisii Optique
 * Copie et active automatiquement le plugin du thème
 *
 * @package Parisii_Optique
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Copier le plugin dans le dossier plugins de WordPress au lieu d'utiliser un zip
 */
function parisii_optique_copy_plugin_to_plugins_dir() {
    $source = get_template_directory() . '/parisii-optique-plugin';
    $destination = WP_PLUGIN_DIR . '/parisii-optique-plugin';
    
    // Vérifier si le plugin existe dans le thème
    if (!file_exists($source)) {
        return;
    }
    
    // Vérifier si le plugin n'est pas déjà dans le dossier plugins
    if (file_exists($destination)) {
        return;
    }
    
    // Créer le dossier de destination
    if (!file_exists($destination)) {
        wp_mkdir_p($destination);
    }
    
    // Copier récursivement tous les fichiers
    parisii_optique_recursive_copy($source, $destination);
}

/**
 * Fonction helper pour copier récursivement un dossier
 */
function parisii_optique_recursive_copy($src, $dst) {
    $dir = opendir($src);
    @mkdir($dst);
    
    while(false !== ($file = readdir($dir))) {
        if (($file != '.') && ($file != '..')) {
            if (is_dir($src . '/' . $file)) {
                parisii_optique_recursive_copy($src . '/' . $file, $dst . '/' . $file);
            } else {
                copy($src . '/' . $file, $dst . '/' . $file);
            }
        }
    }
    
    closedir($dir);
}

/**
 * Activer automatiquement le plugin après l'activation du thème
 */
function parisii_optique_auto_activate_plugin() {
    // Copier le plugin dans le dossier plugins
    parisii_optique_copy_plugin_to_plugins_dir();
    
    // Vérifier si le plugin existe
    $plugin_file = 'parisii-optique-plugin/parisii-optique-plugin.php';
    $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
    
    if (!file_exists($plugin_path)) {
        return;
    }
    
    // Vérifier si le plugin n'est pas déjà activé
    if (!is_plugin_active($plugin_file)) {
        // Activer le plugin
        activate_plugin($plugin_file);
    }
}
add_action('after_switch_theme', 'parisii_optique_auto_activate_plugin');

/**
 * Vérifier et installer le plugin même si le thème est déjà actif
 * S'exécute une seule fois après l'installation/mise à jour
 */
function parisii_optique_check_plugin_on_init() {
    // Vérifier si on a déjà fait la vérification
    if (get_option('parisii_optique_plugin_checked')) {
        return;
    }
    
    // Vérifier si on est en admin
    if (!is_admin()) {
        return;
    }
    
    $plugin_file = 'parisii-optique-plugin/parisii-optique-plugin.php';
    $plugin_path = WP_PLUGIN_DIR . '/' . $plugin_file;
    
    // Si le plugin n'existe pas dans le dossier plugins, le copier
    if (!file_exists($plugin_path)) {
        parisii_optique_copy_plugin_to_plugins_dir();
    }
    
    // Si le plugin existe mais n'est pas activé, l'activer
    if (file_exists($plugin_path) && !is_plugin_active($plugin_file)) {
        activate_plugin($plugin_file);
    }
    
    // Marquer comme vérifié (se réinitialisera à chaque mise à jour du thème)
    update_option('parisii_optique_plugin_checked', true);
}
add_action('admin_init', 'parisii_optique_check_plugin_on_init');

/**
 * Réinitialiser la vérification lors de la mise à jour du thème
 */
function parisii_optique_reset_plugin_check_on_upgrade($upgrader_object, $options) {
    // Vérifier si c'est une mise à jour de thème
    if ($options['type'] !== 'theme') {
        return;
    }
    
    // Vérifier si c'est notre thème
    if (isset($options['themes']) && in_array('parisii-optique', $options['themes'])) {
        delete_option('parisii_optique_plugin_checked');
    }
}
add_action('upgrader_process_complete', 'parisii_optique_reset_plugin_check_on_upgrade', 10, 2);

/**
 * Afficher une notice si le plugin n'est pas activé
 */
function parisii_optique_plugin_notice() {
    $plugin_file = 'parisii-optique-plugin/parisii-optique-plugin.php';
    
    // Si le plugin n'est pas activé, afficher une notice
    if (!is_plugin_active($plugin_file)) {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong><?php _e('Parisii Optique:', 'parisii-optique'); ?></strong>
                <?php _e('Le plugin "Parisii Optique" est requis pour ce thème. Veuillez l\'activer.', 'parisii-optique'); ?>
                <?php if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_file)) : ?>
                    <a href="<?php echo admin_url('plugins.php'); ?>" class="button button-primary">
                        <?php _e('Activer le plugin', 'parisii-optique'); ?>
                    </a>
                <?php endif; ?>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'parisii_optique_plugin_notice');

