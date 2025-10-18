<?php
/**
 * Menu Categories Shortcode Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Product categories shortcode
 */
function parisii_optique_product_categories_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 'product-categories-' . uniqid(),
        'style' => 'list', // list, grid, dropdown, tabs
        'columns' => 3,
        'show_icons' => 'true',
        'show_counts' => 'true',
        'show_descriptions' => 'false',
        'hide_empty' => 'true',
        'parent' => '0',
        'number' => '10',
        'orderby' => 'name',
        'order' => 'ASC',
        'exclude' => '',
        'include' => '',
        'class' => '',
        'item_class' => '',
        'link_class' => '',
    ], $atts);

    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '<p>' . __('WooCommerce n\'est pas installé', 'parisii-optique') . '</p>';
    }

    // Parse boolean values
    $show_icons = $atts['show_icons'] === 'true';
    $show_counts = $atts['show_counts'] === 'true';
    $show_descriptions = $atts['show_descriptions'] === 'true';
    $hide_empty = $atts['hide_empty'] === 'true';

    // Parse exclude and include
    $exclude = !empty($atts['exclude']) ? array_map('intval', explode(',', $atts['exclude'])) : [];
    $include = !empty($atts['include']) ? array_map('intval', explode(',', $atts['include'])) : [];

    // Get product categories
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => $hide_empty,
        'parent' => intval($atts['parent']),
        'number' => intval($atts['number']),
        'orderby' => $atts['orderby'],
        'order' => $atts['order'],
        'exclude' => $exclude,
        'include' => $include,
    ]);

    if (is_wp_error($categories) || empty($categories)) {
        return '<p>' . __('Aucune catégorie de produit trouvée', 'parisii-optique') . '</p>';
    }

    ob_start();
    ?>
    <div id="<?php echo esc_attr($atts['id']); ?>" class="product-categories-shortcode <?php echo esc_attr($atts['class']); ?>">
        <?php if ($atts['style'] === 'list') : ?>
            <ul class="product-categories-list">
                <?php foreach ($categories as $category) : ?>
                    <li class="product-category-item <?php echo esc_attr($atts['item_class']); ?>">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="product-category-link <?php echo esc_attr($atts['link_class']); ?>">
                            <?php if ($show_icons) : ?>
                                <span class="category-icon <?php echo esc_attr(parisii_optique_get_category_icon_admin($category->term_id)); ?>"></span>
                            <?php endif; ?>
                            <span class="category-name"><?php echo esc_html($category->name); ?></span>
                            <?php if ($show_counts && $category->count > 0) : ?>
                                <span class="category-count">(<?php echo $category->count; ?>)</span>
                            <?php endif; ?>
                        </a>
                        <?php if ($show_descriptions && !empty($category->description)) : ?>
                            <div class="category-description"><?php echo esc_html($category->description); ?></div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ul>
            
        <?php elseif ($atts['style'] === 'grid') : ?>
            <div class="product-categories-grid" style="display: grid; grid-template-columns: repeat(<?php echo intval($atts['columns']); ?>, 1fr); gap: 1rem;">
                <?php foreach ($categories as $category) : ?>
                    <div class="product-category-item <?php echo esc_attr($atts['item_class']); ?>">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="product-category-link <?php echo esc_attr($atts['link_class']); ?>">
                            <?php if ($show_icons) : ?>
                                <span class="category-icon <?php echo esc_attr(parisii_optique_get_category_icon_admin($category->term_id)); ?>"></span>
                            <?php endif; ?>
                            <span class="category-name"><?php echo esc_html($category->name); ?></span>
                            <?php if ($show_counts && $category->count > 0) : ?>
                                <span class="category-count">(<?php echo $category->count; ?>)</span>
                            <?php endif; ?>
                        </a>
                        <?php if ($show_descriptions && !empty($category->description)) : ?>
                            <div class="category-description"><?php echo esc_html($category->description); ?></div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
            
        <?php elseif ($atts['style'] === 'dropdown') : ?>
            <select class="product-categories-dropdown <?php echo esc_attr($atts['class']); ?>" onchange="window.location.href=this.value">
                <option value=""><?php _e('Sélectionner une catégorie', 'parisii-optique'); ?></option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo esc_url(get_term_link($category)); ?>">
                        <?php echo esc_html($category->name); ?>
                        <?php if ($show_counts && $category->count > 0) : ?>
                            (<?php echo $category->count; ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
            
        <?php elseif ($atts['style'] === 'tabs') : ?>
            <div class="product-categories-tabs">
                <ul class="tabs-nav">
                    <?php foreach ($categories as $index => $category) : ?>
                        <li class="tab-nav-item <?php echo $index === 0 ? 'active' : ''; ?>">
                            <a href="#tab-<?php echo $category->term_id; ?>" class="tab-nav-link">
                                <?php if ($show_icons) : ?>
                                    <span class="category-icon <?php echo esc_attr(parisii_optique_get_category_icon_admin($category->term_id)); ?>"></span>
                                <?php endif; ?>
                                <?php echo esc_html($category->name); ?>
                                <?php if ($show_counts && $category->count > 0) : ?>
                                    <span class="category-count">(<?php echo $category->count; ?>)</span>
                                <?php endif; ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="tabs-content">
                    <?php foreach ($categories as $index => $category) : ?>
                        <div id="tab-<?php echo $category->term_id; ?>" class="tab-content <?php echo $index === 0 ? 'active' : ''; ?>">
                            <h3><?php echo esc_html($category->name); ?></h3>
                            <?php if ($show_descriptions && !empty($category->description)) : ?>
                                <p><?php echo esc_html($category->description); ?></p>
                            <?php endif; ?>
                            <a href="<?php echo esc_url(get_term_link($category)); ?>" class="btn btn-primary">
                                <?php _e('Voir les produits', 'parisii-optique'); ?>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('product_categories', 'parisii_optique_product_categories_shortcode');

/**
 * Product categories menu shortcode
 */
function parisii_optique_product_categories_menu_shortcode($atts) {
    $atts = shortcode_atts([
        'id' => 'product-categories-menu-' . uniqid(),
        'style' => 'horizontal', // horizontal, vertical, dropdown
        'show_icons' => 'true',
        'show_counts' => 'true',
        'class' => 'product-categories-menu',
        'item_class' => 'menu-item',
        'link_class' => 'menu-link',
    ], $atts);

    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '<p>' . __('WooCommerce n\'est pas installé', 'parisii-optique') . '</p>';
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
        return '<p>' . __('Aucune catégorie de produit trouvée', 'parisii-optique') . '</p>';
    }

    $show_icons = $atts['show_icons'] === 'true';
    $show_counts = $atts['show_counts'] === 'true';

    ob_start();
    ?>
    <nav id="<?php echo esc_attr($atts['id']); ?>" class="<?php echo esc_attr($atts['class']); ?>">
        <?php if ($atts['style'] === 'horizontal') : ?>
            <ul class="menu menu-horizontal">
                <?php foreach ($categories as $category) : ?>
                    <li class="<?php echo esc_attr($atts['item_class']); ?>">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="<?php echo esc_attr($atts['link_class']); ?>">
                            <?php if ($show_icons) : ?>
                                <span class="menu-icon <?php echo esc_attr(parisii_optique_get_category_icon_admin($category->term_id)); ?>"></span>
                            <?php endif; ?>
                            <span class="menu-text"><?php echo esc_html($category->name); ?></span>
                            <?php if ($show_counts && $category->count > 0) : ?>
                                <span class="menu-count">(<?php echo $category->count; ?>)</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            
        <?php elseif ($atts['style'] === 'vertical') : ?>
            <ul class="menu menu-vertical">
                <?php foreach ($categories as $category) : ?>
                    <li class="<?php echo esc_attr($atts['item_class']); ?>">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="<?php echo esc_attr($atts['link_class']); ?>">
                            <?php if ($show_icons) : ?>
                                <span class="menu-icon <?php echo esc_attr(parisii_optique_get_category_icon_admin($category->term_id)); ?>"></span>
                            <?php endif; ?>
                            <span class="menu-text"><?php echo esc_html($category->name); ?></span>
                            <?php if ($show_counts && $category->count > 0) : ?>
                                <span class="menu-count">(<?php echo $category->count; ?>)</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
            
        <?php elseif ($atts['style'] === 'dropdown') : ?>
            <select class="menu-dropdown" onchange="window.location.href=this.value">
                <option value=""><?php _e('Sélectionner une catégorie', 'parisii-optique'); ?></option>
                <?php foreach ($categories as $category) : ?>
                    <option value="<?php echo esc_url(get_term_link($category)); ?>">
                        <?php echo esc_html($category->name); ?>
                        <?php if ($show_counts && $category->count > 0) : ?>
                            (<?php echo $category->count; ?>)
                        <?php endif; ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </nav>
    <?php
    return ob_get_clean();
}
add_shortcode('product_categories_menu', 'parisii_optique_product_categories_menu_shortcode');

/**
 * Enqueue menu categories scripts
 */
function parisii_optique_menu_categories_scripts() {
    $post = get_post();
    if ($post && (has_shortcode($post->post_content, 'product_categories') || 
        has_shortcode($post->post_content, 'product_categories_menu'))) {
        wp_enqueue_script('parisii-menu-categories', get_template_directory_uri() . '/js/menu-categories.js', ['jquery'], '1.0.0', true);
        wp_enqueue_style('parisii-menu-categories', get_template_directory_uri() . '/css/menu-categories.css', [], '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'parisii_optique_menu_categories_scripts');
