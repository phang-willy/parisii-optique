<?php
/**
 * Advanced Menu Categories Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Advanced Menu Categories Walker
 */
class Parisii_Advanced_Menu_Categories_Walker extends Walker_Nav_Menu {
    
    private $category_options = [];
    
    public function __construct($category_options = []) {
        $this->category_options = wp_parse_args($category_options, [
            'show_icons' => true,
            'show_counts' => true,
            'show_descriptions' => false,
            'max_depth' => 2,
            'exclude_categories' => [],
            'include_categories' => [],
        ]);
    }
    
    /**
     * Start the list before the elements are added
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"sub-menu sub-menu-depth-{$depth}\">\n";
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
            
            // Add depth class
            $classes[] = 'menu-item-depth-' . $depth;
            
            // Add count class if has products
            if (isset($item->count) && $item->count > 0) {
                $classes[] = 'menu-item-has-products';
            }
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
        
        // Add product category icon if enabled
        if ($item->object === 'product_cat' && $this->category_options['show_icons']) {
            $icon = $this->get_category_icon($item->object_id);
            $item_output .= '<span class="menu-item-icon ' . esc_attr($icon) . '"></span>';
        }
        
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
        
        // Add product count if enabled
        if ($item->object === 'product_cat' && $this->category_options['show_counts'] && isset($item->count)) {
            $item_output .= ' <span class="menu-item-count">(' . $item->count . ')</span>';
        }
        
        $item_output .= '</a>';
        
        // Add description if enabled
        if ($item->object === 'product_cat' && $this->category_options['show_descriptions'] && !empty($item->description)) {
            $item_output .= '<div class="menu-item-description">' . esc_html($item->description) . '</div>';
        }
        
        $item_output .= isset($args->after) ? $args->after : '';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    /**
     * End the element output
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
    
    /**
     * Get category icon
     */
    private function get_category_icon($category_id) {
        $icons = [
            'dashicons-tag',
            'dashicons-category',
            'dashicons-products',
            'dashicons-store',
            'dashicons-cart',
        ];
        
        // Use category ID to determine icon
        $icon_index = $category_id % count($icons);
        return $icons[$icon_index];
    }
}

/**
 * Get product categories for menu
 */
function parisii_optique_get_menu_product_categories($options = []) {
    $defaults = [
        'hide_empty' => true,
        'parent' => 0,
        'number' => 10,
        'orderby' => 'name',
        'order' => 'ASC',
        'exclude' => [],
        'include' => [],
        'show_counts' => true,
        'show_descriptions' => false,
    ];
    
    $options = wp_parse_args($options, $defaults);
    
    // Get categories
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => $options['hide_empty'],
        'parent' => $options['parent'],
        'number' => $options['number'],
        'orderby' => $options['orderby'],
        'order' => $options['order'],
        'exclude' => $options['exclude'],
        'include' => $options['include'],
    ]);
    
    if (is_wp_error($categories) || empty($categories)) {
        return [];
    }
    
    // Convert to menu items
    $menu_items = [];
    foreach ($categories as $category) {
        $menu_item = (object) [
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
            'count' => $options['show_counts'] ? $category->count : 0,
            'parent' => $category->parent,
        ];
        
        $menu_items[] = $menu_item;
    }
    
    return $menu_items;
}

/**
 * Add product categories to menu with advanced options
 */
function parisii_optique_add_advanced_product_categories_to_menu($items, $args) {
    // Only add to primary menu
    if ($args->theme_location !== 'primary') {
        return $items;
    }
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return $items;
    }
    
    // Get options
    $options = get_option('parisii_menu_categories_options', []);
    $show_categories = isset($options['enabled']) ? $options['enabled'] : true;
    
    if (!$show_categories) {
        return $items;
    }
    
    // Get product categories
    $category_options = [
        'hide_empty' => isset($options['hide_empty']) ? $options['hide_empty'] : true,
        'parent' => isset($options['parent']) ? $options['parent'] : 0,
        'number' => isset($options['number']) ? $options['number'] : 10,
        'orderby' => isset($options['orderby']) ? $options['orderby'] : 'name',
        'order' => isset($options['order']) ? $options['order'] : 'ASC',
        'exclude' => isset($options['exclude']) ? $options['exclude'] : [],
        'include' => isset($options['include']) ? $options['include'] : [],
        'show_counts' => isset($options['show_counts']) ? $options['show_counts'] : true,
        'show_descriptions' => isset($options['show_descriptions']) ? $options['show_descriptions'] : false,
    ];
    
    $product_categories = parisii_optique_get_menu_product_categories($category_options);
    
    if (empty($product_categories)) {
        return $items;
    }
    
    // Find the position to insert product categories
    $insert_position = false;
    $items_array = explode('</li>', $items);
    
    foreach ($items_array as $index => $item) {
        if (strpos($item, 'menu-item-') !== false) {
            $insert_position = $index;
            break;
        }
    }
    
    if ($insert_position === false) {
        $insert_position = count($items_array) - 1;
    }
    
    // Create product categories menu items
    $product_categories_html = '';
    foreach ($product_categories as $category) {
        $product_categories_html .= '<li class="menu-item menu-item-product-category menu-item-' . $category->object_id . '">';
        $product_categories_html .= '<a href="' . esc_url($category->url) . '">';
        
        // Add icon if enabled
        if (isset($options['show_icons']) && $options['show_icons']) {
            $icon = parisii_optique_get_category_icon($category->object_id);
            $product_categories_html .= '<span class="menu-item-icon ' . esc_attr($icon) . '"></span>';
        }
        
        $product_categories_html .= esc_html($category->title);
        
        // Add count if enabled
        if ($category_options['show_counts'] && $category->count > 0) {
            $product_categories_html .= ' <span class="menu-item-count">(' . $category->count . ')</span>';
        }
        
        $product_categories_html .= '</a>';
        
        // Add description if enabled
        if ($category_options['show_descriptions'] && !empty($category->description)) {
            $product_categories_html .= '<div class="menu-item-description">' . esc_html($category->description) . '</div>';
        }
        
        $product_categories_html .= '</li>';
    }
    
    // Insert product categories
    $items_array[$insert_position] .= $product_categories_html;
    $items = implode('</li>', $items_array);
    
    return $items;
}
add_filter('wp_nav_menu_items', 'parisii_optique_add_advanced_product_categories_to_menu', 10, 2);

/**
 * Get category icon
 */
function parisii_optique_get_category_icon($category_id) {
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
 * Add menu categories admin page
 */
function parisii_optique_add_menu_categories_admin_page() {
    add_theme_page(
        __('Catégories de Produits dans le Menu', 'parisii-optique'),
        __('Menu Catégories', 'parisii-optique'),
        'manage_options',
        'parisii-menu-categories',
        'parisii_optique_menu_categories_admin_page'
    );
}
add_action('admin_menu', 'parisii_optique_add_menu_categories_admin_page');

/**
 * Menu categories admin page
 */
function parisii_optique_menu_categories_admin_page() {
    if (isset($_POST['submit'])) {
        $options = [
            'enabled' => isset($_POST['enabled']),
            'hide_empty' => isset($_POST['hide_empty']),
            'parent' => intval($_POST['parent']),
            'number' => intval($_POST['number']),
            'orderby' => sanitize_text_field($_POST['orderby']),
            'order' => sanitize_text_field($_POST['order']),
            'exclude' => array_map('intval', explode(',', sanitize_text_field($_POST['exclude']))),
            'include' => array_map('intval', explode(',', sanitize_text_field($_POST['include']))),
            'show_icons' => isset($_POST['show_icons']),
            'show_counts' => isset($_POST['show_counts']),
            'show_descriptions' => isset($_POST['show_descriptions']),
        ];
        
        update_option('parisii_menu_categories_options', $options);
        echo '<div class="notice notice-success"><p>' . __('Options sauvegardées avec succès !', 'parisii-optique') . '</p></div>';
    }
    
    $options = get_option('parisii_menu_categories_options', []);
    $defaults = [
        'enabled' => true,
        'hide_empty' => true,
        'parent' => 0,
        'number' => 10,
        'orderby' => 'name',
        'order' => 'ASC',
        'exclude' => [],
        'include' => [],
        'show_icons' => true,
        'show_counts' => true,
        'show_descriptions' => false,
    ];
    $options = wp_parse_args($options, $defaults);
    
    ?>
    <div class="wrap">
        <h1><?php _e('Catégories de Produits dans le Menu', 'parisii-optique'); ?></h1>
        
        <form method="post" action="">
            <table class="form-table">
                <tr>
                    <th scope="row">
                        <label for="enabled"><?php _e('Activer les catégories de produits', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="enabled" id="enabled" value="1" <?php checked($options['enabled'], true); ?>>
                            <?php _e('Afficher les catégories de produits dans le menu principal', 'parisii-optique'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="hide_empty"><?php _e('Masquer les catégories vides', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="hide_empty" id="hide_empty" value="1" <?php checked($options['hide_empty'], true); ?>>
                            <?php _e('Ne pas afficher les catégories sans produits', 'parisii-optique'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="parent"><?php _e('Catégorie parente', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <select name="parent" id="parent">
                            <option value="0" <?php selected($options['parent'], 0); ?>><?php _e('Toutes les catégories', 'parisii-optique'); ?></option>
                            <?php
                            $categories = get_terms([
                                'taxonomy' => 'product_cat',
                                'hide_empty' => false,
                                'parent' => 0,
                            ]);
                            
                            foreach ($categories as $category) {
                                echo '<option value="' . $category->term_id . '" ' . selected($options['parent'], $category->term_id, false) . '>' . esc_html($category->name) . '</option>';
                            }
                            ?>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="number"><?php _e('Nombre de catégories', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <input type="number" name="number" id="number" value="<?php echo esc_attr($options['number']); ?>" min="1" max="50">
                        <p class="description"><?php _e('Nombre maximum de catégories à afficher', 'parisii-optique'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="orderby"><?php _e('Trier par', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <select name="orderby" id="orderby">
                            <option value="name" <?php selected($options['orderby'], 'name'); ?>><?php _e('Nom', 'parisii-optique'); ?></option>
                            <option value="count" <?php selected($options['orderby'], 'count'); ?>><?php _e('Nombre de produits', 'parisii-optique'); ?></option>
                            <option value="slug" <?php selected($options['orderby'], 'slug'); ?>><?php _e('Slug', 'parisii-optique'); ?></option>
                            <option value="term_id" <?php selected($options['orderby'], 'term_id'); ?>><?php _e('ID', 'parisii-optique'); ?></option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="order"><?php _e('Ordre', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <select name="order" id="order">
                            <option value="ASC" <?php selected($options['order'], 'ASC'); ?>><?php _e('Croissant', 'parisii-optique'); ?></option>
                            <option value="DESC" <?php selected($options['order'], 'DESC'); ?>><?php _e('Décroissant', 'parisii-optique'); ?></option>
                        </select>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="exclude"><?php _e('Exclure les catégories', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="exclude" id="exclude" value="<?php echo esc_attr(implode(',', $options['exclude'])); ?>" placeholder="1,2,3">
                        <p class="description"><?php _e('IDs des catégories à exclure (séparés par des virgules)', 'parisii-optique'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="include"><?php _e('Inclure uniquement', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <input type="text" name="include" id="include" value="<?php echo esc_attr(implode(',', $options['include'])); ?>" placeholder="1,2,3">
                        <p class="description"><?php _e('IDs des catégories à inclure uniquement (séparés par des virgules)', 'parisii-optique'); ?></p>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="show_icons"><?php _e('Afficher les icônes', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="show_icons" id="show_icons" value="1" <?php checked($options['show_icons'], true); ?>>
                            <?php _e('Afficher des icônes à côté des catégories', 'parisii-optique'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="show_counts"><?php _e('Afficher les compteurs', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="show_counts" id="show_counts" value="1" <?php checked($options['show_counts'], true); ?>>
                            <?php _e('Afficher le nombre de produits par catégorie', 'parisii-optique'); ?>
                        </label>
                    </td>
                </tr>
                
                <tr>
                    <th scope="row">
                        <label for="show_descriptions"><?php _e('Afficher les descriptions', 'parisii-optique'); ?></label>
                    </th>
                    <td>
                        <label>
                            <input type="checkbox" name="show_descriptions" id="show_descriptions" value="1" <?php checked($options['show_descriptions'], true); ?>>
                            <?php _e('Afficher les descriptions des catégories', 'parisii-optique'); ?>
                        </label>
                    </td>
                </tr>
            </table>
            
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}
