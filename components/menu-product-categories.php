<?php
/**
 * Menu Product Categories Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add product categories to menu walker
 */
class Parisii_Product_Categories_Walker extends Walker_Nav_Menu {
    
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
        
        // Check if this is a product category item
        if ($item->object === 'product_cat') {
            $classes[] = 'menu-item-product-category';
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
        
        // Add product category icon if it's a product category
        if ($item->object === 'product_cat') {
            $item_output .= '<span class="menu-item-icon dashicons dashicons-tag"></span>';
        }
        
        $item_output .= (isset($args->link_before) ? $args->link_before : '') . apply_filters('the_title', $item->title, $item->ID) . (isset($args->link_after) ? $args->link_after : '');
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
 * Add product categories to menu items
 */
function parisii_optique_add_product_categories_to_menu($items, $args) {
    // Only add to primary menu
    if ($args->theme_location !== 'primary') {
        return $items;
    }
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return $items;
    }
    
    // Get product categories
    $product_categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0, // Only top-level categories
        'number' => 5, // Limit to 5 categories
    ]);
    
    if (is_wp_error($product_categories) || empty($product_categories)) {
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
        $product_categories_html .= '<li class="menu-item menu-item-product-category menu-item-' . $category->term_id . '">';
        $product_categories_html .= '<a href="' . get_term_link($category) . '">';
        $product_categories_html .= '<span class="menu-item-icon dashicons dashicons-tag"></span>';
        $product_categories_html .= $category->name;
        $product_categories_html .= '</a>';
        $product_categories_html .= '</li>';
    }
    
    // Insert product categories
    $items_array[$insert_position] .= $product_categories_html;
    $items = implode('</li>', $items_array);
    
    return $items;
}
add_filter('wp_nav_menu_items', 'parisii_optique_add_product_categories_to_menu', 10, 2);

/**
 * Add product categories to menu admin
 */
function parisii_optique_add_product_categories_to_menu_objects($items, $args) {
    // Only add to primary menu
    if ($args->theme_location !== 'primary') {
        return $items;
    }
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return $items;
    }
    
    // Get product categories
    $product_categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => true,
        'parent' => 0,
        'number' => 5,
    ]);
    
    if (is_wp_error($product_categories) || empty($product_categories)) {
        return $items;
    }
    
    // Add product categories to menu items
    foreach ($product_categories as $category) {
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
        ];
    }
    
    return $items;
}
add_filter('wp_nav_menu_objects', 'parisii_optique_add_product_categories_to_menu_objects', 10, 2);

/**
 * Add product categories to menu walker
 */
function parisii_optique_menu_walker($args) {
    if ($args['theme_location'] === 'primary') {
        $args['walker'] = new Parisii_Product_Categories_Walker();
    }
    return $args;
}
add_filter('wp_nav_menu_args', 'parisii_optique_menu_walker');

/**
 * Add product categories to menu CSS
 */
function parisii_optique_menu_product_categories_css() {
    ?>
    <style>
    .menu-item-product-category .menu-item-icon {
        font-size: 16px;
        vertical-align: middle;
        margin-right: 5px;
    }
    
    .menu-item-product-category a {
        display: flex;
        align-items: center;
    }
    
    .menu-item-product-category:hover .menu-item-icon {
        color: var(--color-main-500);
    }
    
    .menu-item-product-category.current-menu-item .menu-item-icon {
        color: var(--color-main-500);
    }
    </style>
    <?php
}
add_action('wp_head', 'parisii_optique_menu_product_categories_css');

/**
 * Add product categories to menu JavaScript
 */
function parisii_optique_menu_product_categories_js() {
    ?>
    <script>
    jQuery(document).ready(function($) {
        // Add click tracking for product categories
        $('.menu-item-product-category a').on('click', function() {
            var categoryName = $(this).text().trim();
            console.log('Product category clicked:', categoryName);
            
            // You can add analytics tracking here
            if (typeof gtag !== 'undefined') {
                gtag('event', 'click', {
                    'event_category': 'Menu',
                    'event_label': 'Product Category: ' + categoryName
                });
            }
        });
        
        // Add hover effects
        $('.menu-item-product-category').hover(
            function() {
                $(this).addClass('hover');
            },
            function() {
                $(this).removeClass('hover');
            }
        );
    });
    </script>
    <?php
}
add_action('wp_footer', 'parisii_optique_menu_product_categories_js');

/**
 * Add product categories to menu admin CSS
 */
function parisii_optique_menu_product_categories_admin_css() {
    ?>
    <style>
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
    </style>
    <?php
}
add_action('admin_head', 'parisii_optique_menu_product_categories_admin_css');

/**
 * Add product categories to menu admin JavaScript
 */
function parisii_optique_menu_product_categories_admin_js() {
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
    });
    </script>
    <?php
}
add_action('admin_footer', 'parisii_optique_menu_product_categories_admin_js');

/**
 * Add product categories to menu settings
 */
function parisii_optique_menu_settings() {
    add_settings_field(
        'parisii_product_categories_in_menu',
        __('Afficher les catégories de produits dans le menu', 'parisii-optique'),
        'parisii_optique_menu_settings_callback',
        'general',
        'default'
    );
    
    register_setting('general', 'parisii_product_categories_in_menu');
}
add_action('admin_init', 'parisii_optique_menu_settings');

/**
 * Menu settings callback
 */
function parisii_optique_menu_settings_callback() {
    $value = get_option('parisii_product_categories_in_menu', '1');
    ?>
    <label>
        <input type="checkbox" name="parisii_product_categories_in_menu" value="1" <?php checked($value, '1'); ?>>
        <?php _e('Afficher automatiquement les catégories de produits dans le menu principal', 'parisii-optique'); ?>
    </label>
    <p class="description">
        <?php _e('Les catégories de produits seront automatiquement ajoutées au menu principal.', 'parisii-optique'); ?>
    </p>
    <?php
}

/**
 * Add product categories to menu based on settings
 */
function parisii_optique_menu_product_categories_conditional($items, $args) {
    $show_categories = get_option('parisii_product_categories_in_menu', '1');
    
    if ($show_categories !== '1') {
        return $items;
    }
    
    return parisii_optique_add_product_categories_to_menu($items, $args);
}
add_filter('wp_nav_menu_items', 'parisii_optique_menu_product_categories_conditional', 10, 2);
