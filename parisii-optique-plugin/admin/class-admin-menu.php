<?php
/**
 * Admin Menu class
 *
 * @package Parisii_Optique_Plugin
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

class Parisii_Optique_Admin_Menu {
    
    public function __construct() {
        add_action('admin_menu', array($this, 'add_menu'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
        add_action('admin_init', array($this, 'handle_brand_delete_confirmation'));
        add_action('admin_init', array($this, 'handle_plugin_update'));
    }
    
    /**
     * Enqueue admin scripts and styles
     */
    public function enqueue_admin_scripts($hook) {
        if (strpos($hook, 'parisii-optique') !== false) {
            wp_enqueue_style('parisii-optique-admin', plugin_dir_url(__FILE__) . '../admin/css/admin.css', array('wp-admin'), '1.0.0');
            wp_enqueue_script('parisii-optique-admin', plugin_dir_url(__FILE__) . '../admin/js/admin.js', array('jquery'), '1.0.0', true);
        }
    }
    
    /**
     * Add admin menu
     */
    public function add_menu() {
        // Main menu page "Parisii Optique" with Lucide eye icon
        add_menu_page(
            __('Parisii Optique', 'parisii-optique-plugin'),
            __('Parisii Optique', 'parisii-optique-plugin'),
            'manage_options',
            'parisii-optique',
            array($this, 'render_main_page'),
            'data:image/svg+xml;base64,' . base64_encode('<svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2.062 12.348a1 1 0 0 1 0-.696 10.75 10.75 0 0 1 19.876 0 1 1 0 0 1 0 .696 10.75 10.75 0 0 1-19.876 0"/><circle cx="12" cy="12" r="3"/></svg>'),
            30
        );
        
        // Submenu page "Contact" (first)
        add_submenu_page(
            'parisii-optique',
            __('Contact', 'parisii-optique-plugin'),
            __('Contact', 'parisii-optique-plugin'),
            'manage_options',
            'parisii-optique-contact',
            array($this, 'render_contact_page')
        );
        
        // Submenu page "Marques"
        add_submenu_page(
            'parisii-optique',
            __('Marques', 'parisii-optique-plugin'),
            __('Marques', 'parisii-optique-plugin'),
            'manage_options',
            'parisii-optique-brands',
            array($this, 'render_brands_page')
        );
    }
    
    /**
     * Render main page (Parisii Optique dashboard)
     */
    public function render_main_page() {
        ?>
        <div class="wrap">
            <h1><?php _e('Parisii Optique', 'parisii-optique-plugin'); ?></h1>
            
            <?php
            Parisii_Optique_Update_Plugin_Button::render(
                array(
                    'label' => __('Mise à jour du plugin', 'parisii-optique-plugin'),
                )
            );
            ?>
            
            <p><?php _e('Bienvenue dans le panneau de gestion Parisii Optique.', 'parisii-optique-plugin'); ?></p>
            
            <div class="parisii-dashboard-cards">
                <div class="parisii-dashboard-card">
                    <h2><?php _e('Contact', 'parisii-optique-plugin'); ?></h2>
                    <p><?php _e('Consultez les messages du formulaire de contact et gérez les réglages.', 'parisii-optique-plugin'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=parisii-optique-contact'); ?>" class="button button-primary">
                        <?php _e('Accéder au contact', 'parisii-optique-plugin'); ?>
                    </a>
                </div>
                <div class="parisii-dashboard-card">
                    <h2><?php _e('Marques', 'parisii-optique-plugin'); ?></h2>
                    <p><?php _e('Gérez vos marques de lunettes et accessoires.', 'parisii-optique-plugin'); ?></p>
                    <a href="<?php echo admin_url('admin.php?page=parisii-optique-brands'); ?>" class="button button-primary">
                        <?php _e('Accéder aux marques', 'parisii-optique-plugin'); ?>
                    </a>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render brands page with tabs
     */
    public function render_brands_page() {
        // Get current tab
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'list';
        
        // Define tabs
        $tabs = array(
            'list' => __('Liste', 'parisii-optique-plugin'),
            'add' => __('Ajouter', 'parisii-optique-plugin'),
            'edit' => __('Modifier', 'parisii-optique-plugin'),
            'delete' => __('Supprimer', 'parisii-optique-plugin'),
        );
        
        // Handle edit tab - check if we have an ID
        if ($current_tab === 'edit' && !isset($_GET['id'])) {
            $current_tab = 'list';
        }
        
        // Handle delete tab - check if we have an ID
        if ($current_tab === 'delete' && !isset($_GET['id'])) {
            $current_tab = 'list';
        }
        
        ?>
        <div class="wrap">
            <div class="wp-header-end"></div>
            <h1 class="wp-heading-inline"><?php _e('Marques', 'parisii-optique-plugin'); ?></h1>
            
            <?php Parisii_Optique_Update_Plugin_Button::render(); ?>
            
            <!-- Tab Navigation -->
            <nav class="nav-tab-wrapper wp-clearfix">
                <?php foreach ($tabs as $tab_key => $tab_label) : ?>
                    <?php
                    $tab_url = add_query_arg(array('page' => 'parisii-optique-brands', 'tab' => $tab_key), admin_url('admin.php'));
                    $active_class = ($current_tab === $tab_key) ? ' nav-tab-active' : '';
                    ?>
                    <a href="<?php echo esc_url($tab_url); ?>" class="nav-tab<?php echo $active_class; ?>">
                        <?php echo esc_html($tab_label); ?>
                    </a>
                <?php endforeach; ?>
            </nav>
            
            <!-- Tab Content -->
            <div class="tab-content">
                <?php
                switch ($current_tab) {
                    case 'list':
                        $this->render_list_tab();
                        break;
                    case 'add':
                        $this->render_add_tab();
                        break;
                    case 'edit':
                        $this->render_edit_tab();
                        break;
                    case 'delete':
                        $this->render_delete_tab();
                        break;
                    default:
                        $this->render_list_tab();
                        break;
                }
                ?>
            </div>
        </div>
        <?php
    }
    
    /**
     * Render list tab
     */
    private function render_list_tab() {
        $list_table = new Parisii_Optique_Brand_List();
        $list_table->render();
    }
    
    /**
     * Render add tab
     */
    private function render_add_tab() {
        $form = new Parisii_Optique_Brand_Form();
        $form->render(0); // 0 = new brand
    }
    
    /**
     * Render edit tab
     */
    private function render_edit_tab() {
        $brand_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        if ($brand_id) {
            $form = new Parisii_Optique_Brand_Form();
            $form->render($brand_id);
        } else {
            echo '<div class="notice notice-error"><p>' . __('ID de marque manquant.', 'parisii-optique-plugin') . '</p></div>';
        }
    }
    
    /**
     * Render delete tab
     */
    private function render_delete_tab() {
        $brand_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        if ($brand_id) {
            $form = new Parisii_Optique_Brand_Form();
            $form->delete($brand_id);
        } else {
            echo '<div class="notice notice-error"><p>' . __('ID de marque manquant.', 'parisii-optique-plugin') . '</p></div>';
        }
    }

    /**
     * Handle brand delete confirmation before the admin page starts rendering.
     */
    public function handle_brand_delete_confirmation() {
        if (
            !isset($_POST['confirm_delete'], $_GET['page'], $_GET['tab'], $_GET['id']) ||
            $_GET['page'] !== 'parisii-optique-brands' ||
            $_GET['tab'] !== 'delete'
        ) {
            return;
        }

        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'parisii-optique-plugin'));
        }

        $brand_id = absint($_GET['id']);
        if (!$brand_id) {
            return;
        }

        check_admin_referer('parisii_optique_delete_' . $brand_id);
        check_admin_referer('parisii_optique_confirm_delete', 'parisii_optique_delete_nonce');

        Parisii_Optique_Brand::delete($brand_id);

        wp_safe_redirect(add_query_arg(array('page' => 'parisii-optique-brands', 'tab' => 'list', 'deleted' => '1'), admin_url('admin.php')));
        exit;
    }

    /**
     * Render contact page with tabs (list, view, settings)
     */
    public function render_contact_page() {
        $current_tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : 'list';
        $view_id = isset($_GET['id']) ? absint($_GET['id']) : 0;
        if ($current_tab === 'view' && $view_id) {
            // View single message
        } elseif ($current_tab === 'settings') {
            // Settings
        } else {
            $current_tab = 'list';
        }
        
        $tabs = array(
            'list'     => __('Messages', 'parisii-optique-plugin'),
            'settings' => __('Réglages', 'parisii-optique-plugin'),
        );
        ?>
        <div class="wrap">
            <div class="wp-header-end"></div>
            <h1 class="wp-heading-inline"><?php esc_html_e('Contact', 'parisii-optique-plugin'); ?></h1>
            <?php if ($current_tab !== 'view' || !$view_id) : ?>
            <?php Parisii_Optique_Update_Plugin_Button::render(); ?>
            <?php endif; ?>
            
            <?php if ($current_tab === 'view' && $view_id) : ?>
                <?php
                $view = new Parisii_Optique_Contact_View();
                $view->render($view_id);
                ?>
            <?php else : ?>
                <nav class="nav-tab-wrapper wp-clearfix">
                    <?php foreach ($tabs as $tab_key => $tab_label) : ?>
                        <?php
                        $tab_url = add_query_arg(array('page' => 'parisii-optique-contact', 'tab' => $tab_key), admin_url('admin.php'));
                        $active_class = ($current_tab === $tab_key) ? ' nav-tab-active' : '';
                        ?>
                        <a href="<?php echo esc_url($tab_url); ?>" class="nav-tab<?php echo esc_attr($active_class); ?>">
                            <?php echo esc_html($tab_label); ?>
                        </a>
                    <?php endforeach; ?>
                </nav>
                <div class="tab-content">
                    <?php
                    if ($current_tab === 'settings') {
                        $settings = new Parisii_Optique_Contact_Settings();
                        $settings->render_page();
                    } else {
                        $list = new Parisii_Optique_Contact_List();
                        $list->render();
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
    }
    
    /**
     * Handle plugin update from theme
     */
    public function handle_plugin_update() {
        if (!isset($_POST['action']) || $_POST['action'] !== 'update_plugin') {
            return;
        }
        
        check_admin_referer('parisii_optique_update_plugin', 'parisii_optique_update_nonce');
        
        if (!current_user_can('manage_options')) {
            wp_die(__('Vous n\'avez pas les permissions nécessaires.', 'parisii-optique-plugin'));
        }
        
        $result = $this->sync_plugin_from_theme();
        
        if ($result['success']) {
            add_action('admin_notices', function() use ($result) {
                echo '<div class="notice notice-success is-dismissible"><p>' . 
                     sprintf(__('Plugin mis à jour avec succès ! %d fichiers synchronisés.', 'parisii-optique-plugin'), $result['files_count']) . 
                     '</p></div>';
            });
        } else {
            add_action('admin_notices', function() use ($result) {
                echo '<div class="notice notice-error is-dismissible"><p>' . 
                     sprintf(__('Erreur lors de la mise à jour : %s', 'parisii-optique-plugin'), $result['error']) . 
                     '</p></div>';
            });
        }
    }
    
    /**
     * Sync plugin files from theme to plugins directory
     */
    private function sync_plugin_from_theme() {
        $theme_dir = get_template_directory();
        $theme_plugin_dir = $theme_dir . '/parisii-optique-plugin';
        $wp_plugins_dir = WP_PLUGIN_DIR . '/parisii-optique-plugin';
        
        // Check if theme plugin directory exists
        if (!is_dir($theme_plugin_dir)) {
            return array(
                'success' => false,
                'error' => __('Répertoire du plugin dans le thème introuvable.', 'parisii-optique-plugin')
            );
        }
        
        // Create plugins directory if it doesn't exist
        if (!is_dir($wp_plugins_dir)) {
            if (!wp_mkdir_p($wp_plugins_dir)) {
                return array(
                    'success' => false,
                    'error' => __('Impossible de créer le répertoire des plugins.', 'parisii-optique-plugin')
                );
            }
        }
        
        $files_count = $this->copy_directory($theme_plugin_dir, $wp_plugins_dir);
        
        if ($files_count === false) {
            return array(
                'success' => false,
                'error' => __('Erreur lors de la copie des fichiers.', 'parisii-optique-plugin')
            );
        }
        
        return array(
            'success' => true,
            'files_count' => $files_count
        );
    }
    
    /**
     * Copy directory recursively
     */
    private function copy_directory($src, $dst) {
        $files_count = 0;
        
        if (!is_dir($dst)) {
            if (!wp_mkdir_p($dst)) {
                return false;
            }
        }
        
        $dir = opendir($src);
        if (!$dir) {
            return false;
        }
        
        while (($file = readdir($dir)) !== false) {
            if ($file != '.' && $file != '..') {
                $src_file = $src . '/' . $file;
                $dst_file = $dst . '/' . $file;
                
                if (is_dir($src_file)) {
                    $sub_count = $this->copy_directory($src_file, $dst_file);
                    if ($sub_count === false) {
                        closedir($dir);
                        return false;
                    }
                    $files_count += $sub_count;
                } else {
                    if (copy($src_file, $dst_file)) {
                        $files_count++;
                    } else {
                        closedir($dir);
                        return false;
                    }
                }
            }
        }
        
        closedir($dir);
        return $files_count;
    }
}

