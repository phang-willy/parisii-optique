<?php
/**
 * Menu Admin Walker Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

// Only load this in admin area where Walker_Nav_Menu_Checklist is available
if (!is_admin()) {
    return;
}

// Ensure the Walker_Nav_Menu_Checklist class is available
if (!class_exists('Walker_Nav_Menu_Checklist')) {
    // Include the necessary admin files
    if (file_exists(ABSPATH . 'wp-admin/includes/nav-menu.php')) {
        require_once ABSPATH . 'wp-admin/includes/nav-menu.php';
    }
}

// If the class is still not available, create a fallback
if (!class_exists('Walker_Nav_Menu_Checklist')) {
    return;
}

// Only define the class if we're in admin and the parent class exists
if (is_admin() && class_exists('Walker_Nav_Menu_Checklist')) {
    /**
     * Custom walker for menu admin
     */
    class Parisii_Menu_Admin_Walker extends Walker_Nav_Menu_Checklist {
    
    /**
     * Start the list before the elements are added
     */
    function start_lvl(&$output, $depth = 0, $args = null) {
        $indent = str_repeat("\t", $depth);
        $output .= "\n$indent<ul class=\"children\">\n";
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
        
        $item_output = '<label class="menu-item-title">';
        $item_output .= '<input type="checkbox" class="menu-item-checkbox" name="menu-item[' . $item->ID . '][menu-item-object-id]" value="' . $item->object_id . '" /> ';
        
        // Add product category icon
        if ($item->object === 'product_cat') {
            $icon = parisii_optique_get_category_icon_admin($item->object_id);
            $item_output .= '<span class="menu-item-icon ' . esc_attr($icon) . '"></span>';
        }
        
        $item_output .= esc_html($item->title);
        
        // Add product count
        if ($item->object === 'product_cat' && isset($item->count) && $item->count > 0) {
            $item_output .= ' <span class="menu-item-count">(' . $item->count . ')</span>';
        }
        
        $item_output .= '</label>';
        
        // Add hidden fields
        $item_output .= '<input type="hidden" class="menu-item-db-id" name="menu-item[' . $item->ID . '][menu-item-db-id]" value="' . $item->ID . '" />';
        $item_output .= '<input type="hidden" class="menu-item-object" name="menu-item[' . $item->ID . '][menu-item-object]" value="' . esc_attr($item->object) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-parent-id" name="menu-item[' . $item->ID . '][menu-item-parent-id]" value="' . esc_attr($item->menu_item_parent) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-type" name="menu-item[' . $item->ID . '][menu-item-type]" value="' . esc_attr($item->type) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-title" name="menu-item[' . $item->ID . '][menu-item-title]" value="' . esc_attr($item->title) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-url" name="menu-item[' . $item->ID . '][menu-item-url]" value="' . esc_attr($item->url) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-target" name="menu-item[' . $item->ID . '][menu-item-target]" value="' . esc_attr($item->target) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-attr-title" name="menu-item[' . $item->ID . '][menu-item-attr-title]" value="' . esc_attr($item->attr_title) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-classes" name="menu-item[' . $item->ID . '][menu-item-classes]" value="' . esc_attr(implode(' ', $item->classes)) . '" />';
        $item_output .= '<input type="hidden" class="menu-item-xfn" name="menu-item[' . $item->ID . '][menu-item-xfn]" value="' . esc_attr($item->xfn) . '" />';
        
        $output .= apply_filters('walker_nav_menu_start_el', $item_output, $item, $depth, $args);
    }
    
    /**
     * End the element output
     */
    function end_el(&$output, $item, $depth = 0, $args = null) {
        $output .= "</li>\n";
    }
    } // End of class definition
} // End of admin check

/**
 * Add product categories to menu admin meta box
 */
function parisii_optique_add_product_categories_meta_box() {
    // Only add meta box if we're in admin and the walker class is available
    if (!is_admin() || !class_exists('Parisii_Menu_Admin_Walker')) {
        return;
    }
    
    add_meta_box(
        'parisii-product-categories',
        __('Catégories de Produits', 'parisii-optique'),
        'parisii_optique_product_categories_meta_box_callback',
        'nav-menus',
        'side',
        'default'
    );
}
add_action('admin_init', 'parisii_optique_add_product_categories_meta_box');

/**
 * Product categories meta box callback
 */
function parisii_optique_product_categories_meta_box_callback() {
    // Check if we're in admin and the walker class is available
    if (!is_admin() || !class_exists('Parisii_Menu_Admin_Walker')) {
        echo '<p>' . __('Fonctionnalité non disponible', 'parisii-optique') . '</p>';
        return;
    }
    
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        echo '<p>' . __('WooCommerce n\'est pas installé', 'parisii-optique') . '</p>';
        return;
    }
    
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
    
    // Convert to menu items
    $menu_items = [];
    foreach ($categories as $category) {
        $menu_items[] = (object) [
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
            'menu_item_parent' => 0,
        ];
    }
    
    ?>
    <div id="parisii-product-categories" class="posttypediv">
        <div id="tabs-panel-parisii-product-categories" class="tabs-panel tabs-panel-active">
            <ul id="parisii-product-categories-checklist" class="categorychecklist form-no-clear">
                <?php
                $walker = new Parisii_Menu_Admin_Walker();
                echo walk_nav_menu_tree($menu_items, 0, (object) ['walker' => $walker]);
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
 * Add product categories to menu admin CSS
 */
function parisii_optique_menu_admin_walker_css() {
    ?>
    <style>
    #parisii-product-categories .categorychecklist {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 10px;
        background: #fff;
    }
    
    #parisii-product-categories .categorychecklist li {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    
    #parisii-product-categories .categorychecklist label {
        display: block;
        padding: 8px 12px;
        margin: 0;
        cursor: pointer;
        border-radius: 3px;
        transition: background-color 0.2s;
    }
    
    #parisii-product-categories .categorychecklist label:hover {
        background-color: #f0f0f0;
    }
    
    #parisii-product-categories .categorychecklist input[type="checkbox"] {
        margin-right: 8px;
        vertical-align: middle;
    }
    
    #parisii-product-categories .menu-item-icon {
        margin-right: 8px;
        color: #0073aa;
        vertical-align: middle;
    }
    
    #parisii-product-categories .menu-item-count {
        color: #666;
        font-size: 12px;
        margin-left: 5px;
    }
    
    .menu-item-product-category {
        background-color: #f0f8ff;
        border-left: 3px solid #0073aa;
        margin: 2px 0;
    }
    
    .menu-item-product-category .item-title {
        font-weight: 600;
        color: #0073aa;
    }
    
    .menu-item-product-category .item-type {
        color: #0073aa;
        font-style: italic;
        font-size: 11px;
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
    
    .menu-item-product-category .menu-item-handle {
        border-left: 3px solid #0073aa;
    }
    
    .menu-item-product-category .menu-item-title {
        color: #0073aa;
        font-weight: 600;
    }
    
    .menu-item-product-category .menu-item-type {
        color: #0073aa;
        font-style: italic;
    }
    </style>
    <?php
}
add_action('admin_head', 'parisii_optique_menu_admin_walker_css');

/**
 * Add product categories to menu admin JavaScript
 */
function parisii_optique_menu_admin_walker_js() {
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
        
        // Add category search
        $('#parisii-product-categories').prepend('<input type="text" id="category-search" placeholder="Rechercher une catégorie..." style="width: 100%; padding: 5px; margin-bottom: 10px; border: 1px solid #ddd; border-radius: 3px;">');
        
        $('#category-search').on('input', function() {
            var searchTerm = $(this).val().toLowerCase();
            $('#parisii-product-categories .categorychecklist li').each(function() {
                var $item = $(this);
                var categoryName = $item.find('label').text().toLowerCase();
                
                if (categoryName.includes(searchTerm)) {
                    $item.show();
                } else {
                    $item.hide();
                }
            });
        });
    });
    </script>
    <?php
}
add_action('admin_footer', 'parisii_optique_menu_admin_walker_js');
