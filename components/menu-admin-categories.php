<?php
/**
 * Menu Admin Categories Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add product categories to menu admin
 */
function parisii_optique_add_product_categories_to_menu_admin() {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Add product categories to menu admin
    add_meta_box(
        'parisii-product-categories',
        __('Catégories de Produits', 'parisii-optique'),
        'parisii_optique_product_categories_meta_box',
        'nav-menus',
        'side',
        'default'
    );
}
add_action('admin_init', 'parisii_optique_add_product_categories_to_menu_admin');

/**
 * Product categories meta box
 */
function parisii_optique_product_categories_meta_box() {
    // Get product categories
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);
    
    if (is_wp_error($categories) || empty($categories)) {
        echo '<p>' . __('Aucune catégorie de produit trouvée', 'parisii-optique') . '</p>';
        return;
    }
    
    ?>
    <div id="parisii-product-categories" class="posttypediv">
        <div id="tabs-panel-parisii-product-categories" class="tabs-panel tabs-panel-active">
            <ul id="parisii-product-categories-checklist" class="categorychecklist form-no-clear">
                <?php
                $walker = new Walker_Nav_Menu_Checklist();
                echo walk_nav_menu_tree(array_map('wp_setup_nav_menu_item', $categories), 0, (object) ['walker' => $walker]);
                ?>
            </ul>
        </div>
        
        <p class="button-controls">
            <span class="list-controls">
                <a href="#" class="select-all" data-target="parisii-product-categories"><?php _e('Tout sélectionner', 'parisii-optique'); ?></a>
                <a href="#" class="select-none" data-target="parisii-product-categories"><?php _e('Tout désélectionner', 'parisii-optique'); ?></a>
            </span>
            <span class="add-to-menu">
                <input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Ajouter au menu', 'parisii-optique'); ?>" name="add-parisii-product-categories-menu-item" id="submit-parisii-product-categories" />
                <span class="spinner"></span>
            </span>
        </p>
    </div>
    <?php
}

/**
 * Add product categories to menu items
 */
function parisii_optique_add_product_categories_to_menu_items($items, $args) {
    // Only add to primary menu
    if ($args->theme_location !== 'primary') {
        return $items;
    }
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return $items;
    }
    
    // Get product categories
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0,
        'number' => 10,
        'orderby' => 'name',
        'order' => 'ASC',
    ]);
    
    if (is_wp_error($categories) || empty($categories)) {
        return $items;
    }
    
    // Add product categories to menu items
    foreach ($categories as $category) {
        $items[] = (object) [
            'ID' => 'product_cat_' . $category->term_id,
            'title' => $category->name,
            'url' => get_term_link($category),
            'object' => 'product_cat',
            'object_id' => $category->term_id,
            'type' => 'taxonomy',
            'type_label' => __('Catégorie de produit', 'parisii-optique'),
            'classes' => ['menu-item-product-category'],
            'xfn' => '',
            'target' => '',
            'attr_title' => '',
            'description' => $category->description,
            'count' => $category->count,
        ];
    }
    
    return $items;
}
add_filter('wp_nav_menu_objects', 'parisii_optique_add_product_categories_to_menu_items', 10, 2);

/**
 * Add product categories to menu walker
 */
function parisii_optique_add_product_categories_to_menu_walker($args) {
    if ($args['theme_location'] === 'primary') {
        $args['walker'] = new Parisii_Product_Categories_Admin_Walker();
    }
    return $args;
}
add_filter('wp_nav_menu_args', 'parisii_optique_add_product_categories_to_menu_walker');

/**
 * Custom walker for product categories
 */
class Parisii_Product_Categories_Admin_Walker extends Walker_Nav_Menu {
    
    /**
     * Start the list before the elements are added
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu\">\n";
    }
    
    /**
     * End the list after the elements are added
     */
    function end_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "$indent</ul>\n";
    }
    
    /**
     * Start the element output
     */
    function start_el(&$output, $item, $depth = 0, $args = null, $id = 0) {
        $indent = ($depth) ? str_repeat("\t", $depth) : '';
        
        $classes = empty($item->classes) ? array() : (array) $item->classes;
        $classes[] = 'menu-item-' . $item->ID;
        
        // Add specific classes for product categories
        if ($item->object === 'product_cat') {
            $classes[] = 'menu-item-product-category';
            $classes[] = 'menu-item-category-' . $item->object_id;
        }
        
        $class_names = join(' ', apply_filters('nav_menu_css_class', array_filter($classes), $item, $args));
        $class_names = $class_names ? ' class="' . esc_attr($class_names) . '"' : '';
        
        $id = apply_filters('nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args);
        $id = $id ? ' id="' . esc_attr($id) . '"' : '';
        
        $output .= $indent . '<li' . $id . $class_names .'>';
        
        $attributes = ! empty($item->attr_title) ? ' title="'  . esc_attr($item->attr_title) .'"' : '';
        $attributes .= ! empty($item->target)     ? ' target="' . esc_attr($item->target     ) .'"' : '';
        $attributes .= ! empty($item->xfn)        ? ' rel="'    . esc_attr($item->xfn        ) .'"' : '';
        $attributes .= ! empty($item->url)        ? ' href="'   . esc_attr($item->url        ) .'"' : '';
        
        $item_output = isset($args->before) ? $args->before : '';
        $item_output .= '<a' . $attributes .'>';
        
        // Add product category icon
        if ($item->object === 'product_cat') {
            $icon = parisii_optique_get_category_icon_admin($item->object_id);
            $item_output .= '<span class="menu-item-icon ' . esc_attr($icon) . '"></span>';
        }
        
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        
        // Add product count
        if ($item->object === 'product_cat' && isset($item->count) && $item->count > 0) {
            $item_output .= ' <span class="menu-item-count">(' . $item->count . ')</span>';
        }
        
        $item_output .= '</a>';
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    /**
     * End the element output
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
}

/**
 * Get category icon
 */
function parisii_optique_get_category_icon_admin($category_id) {
    $icons = [
        'dashicons-tag',
        'dashicons-category',
        'dashicons-products',
        'dashicons-store',
        'dashicons-cart',
        'dashicons-star-filled',
        'dashicons-heart',
    ];
    
    // Use category ID to determine icon
    $icon_index = $category_id % count($icons);
    return $icons[$icon_index];
}

/**
 * Add product categories to menu admin CSS
 */
function parisii_optique_menu_admin_css() {
    ?>
    <style>
    #parisii-product-categories .categorychecklist {
        max-height: 200px;
        overflow-y: auto;
    }
    
    #parisii-product-categories .categorychecklist li {
        margin: 0;
        padding: 0;
    }
    
    #parisii-product-categories .categorychecklist label {
        display: block;
        padding: 5px 10px;
        margin: 0;
        cursor: pointer;
    }
    
    #parisii-product-categories .categorychecklist label:hover {
        background-color: #f0f0f0;
    }
    
    #parisii-product-categories .categorychecklist input[type="checkbox"] {
        margin-right: 8px;
    }
    
    .menu-item-product-category {
        background-color: #f0f8ff;
        border-left: 3px solid #0073aa;
    }
    
    .menu-item-product-category .item-title {
        font-weight: 600;
    }
    
    .menu-item-product-category .item-type {
        color: #0073aa;
        font-style: italic;
    }
    
    .menu-item-product-category .dashicons {
        color: #0073aa;
        margin-right: 5px;
    }
    
    .menu-item-product-category .menu-item-count {
        color: #666;
        font-size: 12px;
        margin-left: 5px;
    }
    </style>
    <?php
}
add_action('admin_head', 'parisii_optique_menu_admin_css');

/**
 * Add product categories to menu admin JavaScript
 */
function parisii_optique_menu_admin_js() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Add product category indicator
        $('.menu-item-product-category').each(function() {
            var $item = $(this);
            var $title = $item.find('.item-title');
            var $type = $item.find('.item-type');
            
            // Add icon
            $title.prepend('<span class="dashicons dashicons-tag" style="font-size: 16px; vertical-align: middle; margin-right: 5px; color: #0073aa;"></span>');
            
            // Add description
            if ($type.length) {
                $type.text('Catégorie de produit');
            }
        });
        
        // Add drag and drop support
        $('.menu-item-product-category').draggable({
            helper: 'clone',
            opacity: 0.8
        });
        
        // Add select all/none functionality
        $('.select-all').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target + ' input[type="checkbox"]').prop('checked', true);
        });
        
        $('.select-none').on('click', function(e) {
            e.preventDefault();
            var target = $(this).data('target');
            $('#' + target + ' input[type="checkbox"]').prop('checked', false);
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'parisii_optique_menu_admin_js');

/**
 * Add product categories to menu admin AJAX
 */
function parisii_optique_menu_admin_ajax() {
    if (!wp_verify_nonce($_POST['nonce'], 'parisii_menu_admin_nonce')) {
        wp_die('Nonce invalide');
    }
    
    if (!current_user_can('edit_theme_options')) {
        wp_die('Permissions insuffisantes');
    }
    
    $category_ids = array_map('intval', $_POST['category_ids']);
    $menu_id = intval($_POST['menu_id']);
    
    $added_items = [];
    
    foreach ($category_ids as $category_id) {
        $category = get_term($category_id, 'product_cat');
        
        if (is_wp_error($category) || !$category) {
            continue;
        }
        
        $menu_item_id = wp_update_nav_menu_item($menu_id, 0, [
            'menu-item-title' => $category->name,
            'menu-item-url' => get_term_link($category),
            'menu-item-object' => 'product_cat',
            'menu-item-object-id' => $category->term_id,
            'menu-item-type' => 'taxonomy',
            'menu-item-status' => 'publish',
        ]);
        
        if (!is_wp_error($menu_item_id)) {
            $added_items[] = $menu_item_id;
        }
    }
    
    wp_send_json_success([
        'message' => sprintf(__('%d catégories ajoutées au menu', 'parisii-optique'), count($added_items)),
        'added_items' => $added_items
    ]);
}
add_action('wp_ajax_parisii_add_product_categories_to_menu', 'parisii_optique_menu_admin_ajax');

/**
 * Add product categories to menu admin settings
 */
function parisii_optique_menu_admin_settings() {
    add_settings_field(
        'parisii_show_product_categories_in_menu_admin',
        __('Afficher les catégories de produits dans l\'admin des menus', 'parisii-optique'),
        'parisii_optique_menu_admin_settings_callback',
        'general',
        'default'
    );
    
    register_setting('general', 'parisii_show_product_categories_in_menu_admin');
}
add_action('admin_init', 'parisii_optique_menu_admin_settings');

/**
 * Menu admin settings callback
 */
function parisii_optique_menu_admin_settings_callback() {
    $value = get_option('parisii_show_product_categories_in_menu_admin', '1');
    ?>
    <label>
        <input type="checkbox" name="parisii_show_product_categories_in_menu_admin" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Afficher les catégories de produits dans l\'interface de gestion des menus', 'parisii-optique'); ?>
    </label>
    <p class="description">
        <?php _e('Les catégories de produits seront disponibles dans la boîte "Catégories de Produits" lors de l\'édition des menus.', 'parisii-optique'); ?>
    </p>
    <?php
}
