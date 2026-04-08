<?php
/**
 * Plugin Name: Parisii Optique Popup Manager
 * Description: Gestionnaire de popups avancé pour le thème Parisii Optique
 * Version: 1.0.0
 * Author: Parisii Optique
 * Text Domain: parisii-popup-manager
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('PARISII_POPUP_VERSION', '1.0.0');
define('PARISII_POPUP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('PARISII_POPUP_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main Popup Manager class
 */
class Parisii_Popup_Manager {
    
    /**
     * Constructor
     */
    public function __construct() {
        add_action('init', [$this, 'init']);
        add_action('wp_enqueue_scripts', [$this, 'enqueue_scripts']);
        add_action('wp_footer', [$this, 'render_popups']);
        add_action('wp_ajax_parisii_popup_action', [$this, 'handle_popup_action']);
        add_action('wp_ajax_nopriv_parisii_popup_action', [$this, 'handle_popup_action']);
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Register post type for popups
        $this->register_popup_post_type();
        
        // Add admin menu
        add_action('admin_menu', [$this, 'add_admin_menu']);
        
        // Add meta boxes
        add_action('add_meta_boxes', [$this, 'add_meta_boxes']);
        add_action('save_post', [$this, 'save_popup_meta']);
    }
    
    /**
     * Register popup post type
     */
    public function register_popup_post_type() {
        $args = [
            'label' => 'Popups',
            'labels' => [
                'name' => 'Popups',
                'singular_name' => 'Popup',
                'add_new' => 'Ajouter un popup',
                'add_new_item' => 'Ajouter un nouveau popup',
                'edit_item' => 'Modifier le popup',
                'new_item' => 'Nouveau popup',
                'view_item' => 'Voir le popup',
                'search_items' => 'Rechercher des popups',
                'not_found' => 'Aucun popup trouvé',
                'not_found_in_trash' => 'Aucun popup dans la corbeille',
            ],
            'public' => false,
            'show_ui' => true,
            'show_in_menu' => true,
            'menu_icon' => 'dashicons-megaphone',
            'supports' => ['title', 'editor'],
            'has_archive' => false,
        ];
        
        register_post_type('parisii_popup', $args);
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_submenu_page(
            'edit.php?post_type=parisii_popup',
            'Paramètres des Popups',
            'Paramètres',
            'manage_options',
            'popup-settings',
            [$this, 'settings_page']
        );
    }
    
    /**
     * Add meta boxes
     */
    public function add_meta_boxes() {
        add_meta_box(
            'popup_settings',
            'Paramètres du Popup',
            [$this, 'popup_settings_meta_box'],
            'parisii_popup',
            'normal',
            'high'
        );
    }
    
    /**
     * Popup settings meta box
     */
    public function popup_settings_meta_box($post) {
        wp_nonce_field('parisii_popup_meta', 'parisii_popup_meta_nonce');
        
        $popup_type = get_post_meta($post->ID, '_popup_type', true) ?: 'modal';
        $trigger_type = get_post_meta($post->ID, '_trigger_type', true) ?: 'page_load';
        $delay = get_post_meta($post->ID, '_popup_delay', true) ?: '3';
        $pages = get_post_meta($post->ID, '_popup_pages', true) ?: [];
        $categories = get_post_meta($post->ID, '_popup_categories', true) ?: [];
        $products = get_post_meta($post->ID, '_popup_products', true) ?: [];
        $start_date = get_post_meta($post->ID, '_popup_start_date', true) ?: '';
        $end_date = get_post_meta($post->ID, '_popup_end_date', true) ?: '';
        $page_starts_with = get_post_meta($post->ID, '_popup_page_starts_with', true) ?: '';
        $show_once = get_post_meta($post->ID, '_popup_show_once', true) ?: '1';
        $close_button = get_post_meta($post->ID, '_popup_close_button', true) ?: '1';
        $overlay_close = get_post_meta($post->ID, '_popup_overlay_close', true) ?: '1';
        $size = get_post_meta($post->ID, '_popup_size', true) ?: 'medium';
        $position = get_post_meta($post->ID, '_popup_position', true) ?: 'center';
        
        ?>
        <table class="form-table">
            <tr>
                <th><label for="popup_type">Type de popup</label></th>
                <td>
                    <select name="popup_type" id="popup_type">
                        <option value="modal" <?php selected($popup_type, 'modal'); ?>>Modal</option>
                        <option value="banner" <?php selected($popup_type, 'banner'); ?>>Bannière</option>
                        <option value="slide_in" <?php selected($popup_type, 'slide_in'); ?>>Slide-in</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="trigger_type">Déclencheur</label></th>
                <td>
                    <select name="trigger_type" id="trigger_type">
                        <option value="page_load" <?php selected($trigger_type, 'page_load'); ?>>Chargement de page</option>
                        <option value="scroll" <?php selected($trigger_type, 'scroll'); ?>>Scroll</option>
                        <option value="click" <?php selected($trigger_type, 'click'); ?>>Clic</option>
                        <option value="exit_intent" <?php selected($trigger_type, 'exit_intent'); ?>>Intention de sortie</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="popup_delay">Délai (secondes)</label></th>
                <td>
                    <input type="number" name="popup_delay" id="popup_delay" value="<?php echo esc_attr($delay); ?>" min="0" max="60">
                </td>
            </tr>
            
            <tr>
                <th><label>Pages d'affichage</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="popup_pages[]" value="all" <?php checked(in_array('all', $pages)); ?>>
                        Toutes les pages
                    </label><br>
                    <?php
                    $all_pages = get_pages();
                    foreach ($all_pages as $page) {
                        $checked = in_array($page->ID, $pages) ? 'checked' : '';
                        echo '<label><input type="checkbox" name="popup_pages[]" value="' . esc_attr($page->ID) . '" ' . esc_attr($checked) . '> ' . esc_html($page->post_title) . '</label><br>';
                    }
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label>Catégories de blog</label></th>
                <td>
                    <?php
                    $blog_categories = get_categories();
                    foreach ($blog_categories as $category) {
                        $checked = in_array($category->term_id, $categories) ? 'checked' : '';
                        echo '<label><input type="checkbox" name="popup_categories[]" value="' . esc_attr($category->term_id) . '" ' . esc_attr($checked) . '> ' . esc_html($category->name) . '</label><br>';
                    }
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label>Produits WooCommerce</label></th>
                <td>
                    <?php
                    if (class_exists('WooCommerce')) {
                        $woo_products = get_posts(['post_type' => 'product', 'numberposts' => -1]);
                        foreach ($woo_products as $product) {
                            $checked = in_array($product->ID, $products) ? 'checked' : '';
                            echo '<label><input type="checkbox" name="popup_products[]" value="' . esc_attr($product->ID) . '" ' . esc_attr($checked) . '> ' . esc_html($product->post_title) . '</label><br>';
                        }
                    } else {
                        echo esc_html__('WooCommerce n\'est pas installé', 'parisii-popup-manager');
                    }
                    ?>
                </td>
            </tr>
            
            <tr>
                <th><label for="popup_page_starts_with">Page commence par</label></th>
                <td>
                    <input type="text" name="popup_page_starts_with" id="popup_page_starts_with" value="<?php echo esc_attr($page_starts_with); ?>" placeholder="ex: /offres">
                </td>
            </tr>
            
            <tr>
                <th><label for="popup_start_date">Date de début</label></th>
                <td>
                    <input type="datetime-local" name="popup_start_date" id="popup_start_date" value="<?php echo esc_attr($start_date); ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="popup_end_date">Date de fin</label></th>
                <td>
                    <input type="datetime-local" name="popup_end_date" id="popup_end_date" value="<?php echo esc_attr($end_date); ?>">
                </td>
            </tr>
            
            <tr>
                <th><label for="popup_size">Taille</label></th>
                <td>
                    <select name="popup_size" id="popup_size">
                        <option value="small" <?php selected($size, 'small'); ?>>Petit</option>
                        <option value="medium" <?php selected($size, 'medium'); ?>>Moyen</option>
                        <option value="large" <?php selected($size, 'large'); ?>>Grand</option>
                        <option value="fullscreen" <?php selected($size, 'fullscreen'); ?>>Plein écran</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label for="popup_position">Position</label></th>
                <td>
                    <select name="popup_position" id="popup_position">
                        <option value="center" <?php selected($position, 'center'); ?>>Centre</option>
                        <option value="top" <?php selected($position, 'top'); ?>>Haut</option>
                        <option value="bottom" <?php selected($position, 'bottom'); ?>>Bas</option>
                        <option value="top-left" <?php selected($position, 'top-left'); ?>>Haut gauche</option>
                        <option value="top-right" <?php selected($position, 'top-right'); ?>>Haut droite</option>
                        <option value="bottom-left" <?php selected($position, 'bottom-left'); ?>>Bas gauche</option>
                        <option value="bottom-right" <?php selected($position, 'bottom-right'); ?>>Bas droite</option>
                    </select>
                </td>
            </tr>
            
            <tr>
                <th><label>Options</label></th>
                <td>
                    <label>
                        <input type="checkbox" name="popup_show_once" value="1" <?php checked($show_once, '1'); ?>>
                        Afficher une seule fois par visiteur
                    </label><br>
                    <label>
                        <input type="checkbox" name="popup_close_button" value="1" <?php checked($close_button, '1'); ?>>
                        Bouton de fermeture
                    </label><br>
                    <label>
                        <input type="checkbox" name="popup_overlay_close" value="1" <?php checked($overlay_close, '1'); ?>>
                        Fermer en cliquant sur l'overlay
                    </label>
                </td>
            </tr>
        </table>
        <?php
    }
    
    /**
     * Save popup meta
     */
    public function save_popup_meta($post_id) {
        if (!isset($_POST['parisii_popup_meta_nonce']) || !wp_verify_nonce($_POST['parisii_popup_meta_nonce'], 'parisii_popup_meta')) {
            return;
        }
        
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }
        
        $fields = [
            'popup_type', 'trigger_type', 'popup_delay', 'popup_pages', 'popup_categories',
            'popup_products', 'popup_page_starts_with', 'popup_start_date', 'popup_end_date',
            'popup_size', 'popup_position', 'popup_show_once', 'popup_close_button', 'popup_overlay_close'
        ];
        
        foreach ($fields as $field) {
            if (isset($_POST[$field])) {
                $value = wp_unslash($_POST[$field]);
                if (is_array($value)) {
                    $value = array_map('sanitize_text_field', $value);
                } else {
                    $value = sanitize_text_field($value);
                }
                update_post_meta($post_id, '_' . $field, $value);
            }
        }
    }
    
    /**
     * Settings page
     */
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Paramètres des Popups</h1>
            <p>Configurez les paramètres globaux pour les popups.</p>
        </div>
        <?php
    }
    
    /**
     * Enqueue scripts
     */
    public function enqueue_scripts() {
        wp_enqueue_script('parisii-popup', PARISII_POPUP_PLUGIN_URL . 'js/popup.js', ['jquery'], PARISII_POPUP_VERSION, true);
        wp_enqueue_style('parisii-popup', PARISII_POPUP_PLUGIN_URL . 'css/popup.css', [], PARISII_POPUP_VERSION);
        
        wp_localize_script('parisii-popup', 'parisii_popup_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('parisii_popup_nonce'),
        ]);
    }
    
    /**
     * Render popups
     */
    public function render_popups() {
        $popups = get_posts([
            'post_type' => 'parisii_popup',
            'post_status' => 'publish',
            'numberposts' => -1,
        ]);
        
        foreach ($popups as $popup) {
            if ($this->should_show_popup($popup)) {
                $this->render_popup($popup);
            }
        }
    }
    
    /**
     * Check if popup should be shown
     */
    public function should_show_popup($popup) {
        $popup_id = $popup->ID;
        
        // Check if already shown (show once)
        $show_once = get_post_meta($popup_id, '_popup_show_once', true);
        if ($show_once && isset($_COOKIE['parisii_popup_' . $popup_id])) {
            return false;
        }
        
        // Check date range
        $start_date = get_post_meta($popup_id, '_popup_start_date', true);
        $end_date = get_post_meta($popup_id, '_popup_end_date', true);
        
        if ($start_date && strtotime($start_date) > time()) {
            return false;
        }
        
        if ($end_date && strtotime($end_date) < time()) {
            return false;
        }
        
        // Check pages
        $pages = get_post_meta($popup_id, '_popup_pages', true);
        if (!empty($pages) && !in_array('all', $pages)) {
            if (!in_array(get_the_ID(), $pages)) {
                return false;
            }
        }
        
        // Check categories
        $categories = get_post_meta($popup_id, '_popup_categories', true);
        if (!empty($categories)) {
            $post_categories = wp_get_post_categories(get_the_ID());
            if (!array_intersect($categories, $post_categories)) {
                return false;
            }
        }
        
        // Check products
        $products = get_post_meta($popup_id, '_popup_products', true);
        if (!empty($products) && is_product()) {
            if (!in_array(get_the_ID(), $products)) {
                return false;
            }
        }
        
        // Check page starts with
        $page_starts_with = get_post_meta($popup_id, '_popup_page_starts_with', true);
        if ($page_starts_with) {
            $current_path = isset($_SERVER['REQUEST_URI']) ? sanitize_text_field(wp_unslash($_SERVER['REQUEST_URI'])) : '';
            if (strpos($current_path, $page_starts_with) !== 0) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Render popup
     */
    public function render_popup($popup) {
        $popup_id = $popup->ID;
        $popup_type = get_post_meta($popup_id, '_popup_type', true) ?: 'modal';
        $trigger_type = get_post_meta($popup_id, '_trigger_type', true) ?: 'page_load';
        $delay = get_post_meta($popup_id, '_popup_delay', true) ?: '3';
        $size = get_post_meta($popup_id, '_popup_size', true) ?: 'medium';
        $position = get_post_meta($popup_id, '_popup_position', true) ?: 'center';
        $close_button = get_post_meta($popup_id, '_popup_close_button', true) ?: '1';
        $overlay_close = get_post_meta($popup_id, '_popup_overlay_close', true) ?: '1';
        
        $size_classes = [
            'small' => 'max-w-sm',
            'medium' => 'max-w-md',
            'large' => 'max-w-2xl',
            'fullscreen' => 'max-w-full h-full',
        ];
        
        $position_classes = [
            'center' => 'top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2',
            'top' => 'top-4 left-1/2 transform -translate-x-1/2',
            'bottom' => 'bottom-4 left-1/2 transform -translate-x-1/2',
            'top-left' => 'top-4 left-4',
            'top-right' => 'top-4 right-4',
            'bottom-left' => 'bottom-4 left-4',
            'bottom-right' => 'bottom-4 right-4',
        ];
        
        ?>
        <div id="parisii-popup-<?php echo $popup_id; ?>" 
             class="parisii-popup fixed inset-0 z-50 hidden" 
             data-trigger="<?php echo esc_attr($trigger_type); ?>"
             data-delay="<?php echo esc_attr($delay); ?>"
             data-popup-id="<?php echo $popup_id; ?>">
            
            <div class="popup-overlay absolute inset-0 bg-black bg-opacity-50 <?php echo $overlay_close ? 'popup-overlay-close' : ''; ?>"></div>
            
            <div class="popup-content absolute <?php echo esc_attr($position_classes[$position]); ?> <?php echo esc_attr($size_classes[$size]); ?> w-full mx-4">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-2xl overflow-hidden">
                    <?php if ($close_button) : ?>
                        <button class="popup-close absolute top-4 right-4 z-10 p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    <?php endif; ?>
                    
                    <div class="popup-body p-6">
                        <h3 class="text-2xl font-heading font-bold text-gray-900 dark:text-white mb-4">
                            <?php echo esc_html($popup->post_title); ?>
                        </h3>
                        <div class="prose prose-lg max-w-none text-gray-700 dark:text-gray-300">
                            <?php echo apply_filters('the_content', $popup->post_content); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    
    /**
     * Handle popup actions
     */
    public function handle_popup_action() {
        check_ajax_referer('parisii_popup_nonce', 'nonce');
        
        $action = sanitize_text_field($_POST['action_type']);
        $popup_id = intval($_POST['popup_id']);
        
        switch ($action) {
            case 'close':
                $show_once = get_post_meta($popup_id, '_popup_show_once', true);
                if ($show_once) {
                    setcookie('parisii_popup_' . $popup_id, '1', time() + (30 * DAY_IN_SECONDS), '/');
                }
                break;
        }
        
        wp_die();
    }
}

// Initialize the plugin
new Parisii_Popup_Manager();
