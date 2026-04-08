<?php
/**
 * Custom Gutenberg Blocks
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Register custom blocks
 */
function parisii_optique_register_blocks() {
    // Load block assets
    $asset_file_path = get_template_directory() . '/blocks/dist/blocks/index.asset.php';
    $asset_file = file_exists($asset_file_path) ? include $asset_file_path : [
        'dependencies' => ['wp-blocks', 'wp-element', 'wp-editor', 'wp-components', 'wp-i18n'],
        'version' => '1.0.0'
    ];
    
    // Register block scripts
    wp_register_script(
        'parisii-blocks',
        get_template_directory_uri() . '/blocks/dist/blocks/index.js',
        $asset_file['dependencies'],
        $asset_file['version'],
        true
    );

    // Register block styles
    wp_register_style(
        'parisii-blocks-editor',
        get_template_directory_uri() . '/blocks/editor.css',
        ['wp-edit-blocks'],
        '1.0.0'
    );

    wp_register_style(
        'parisii-blocks-style',
        get_template_directory_uri() . '/blocks/style.css',
        [],
        '1.0.0'
    );

    // Scripts supprimés - accordion et carousel retirés
    // wp_register_script('parisii-blocks-frontend', ...);
    // wp_register_script('parisii-embla-carousel', ...);

    // Blocks supprimés - accordion, carousel, image-text-section retirés
    // register_block_type('parisii-optique/carousel', ...);
    // register_block_type('parisii-optique/carousel-item', ...);
    // register_block_type('parisii-optique/accordion', ...);
    // register_block_type('parisii-optique/accordion-item', ...);
    // register_block_type('parisii-optique/image-text-section', ...);

    // Register newsletter section block
    register_block_type('parisii-optique/newsletter-section', [
        'editor_script' => 'parisii-blocks',
        'editor_style' => 'parisii-blocks-editor',
        'style' => 'parisii-blocks-style',
    ]);

    // Register blank section block
    register_block_type('parisii-optique/blank-section', [
        'editor_script' => 'parisii-blocks',
        'editor_style' => 'parisii-blocks-editor',
        'style' => 'parisii-blocks-style',
    ]);

    // Register product categories block
    register_block_type('parisii-optique/product-categories', [
        'editor_script' => 'parisii-blocks',
        'editor_style' => 'parisii-blocks-editor',
        'style' => 'parisii-blocks-style',
        'render_callback' => 'parisii_optique_render_product_categories_block',
    ]);

    // Register theme switcher block
    register_block_type('parisii-optique/theme-switcher', [
        'editor_script' => 'parisii-blocks',
        'editor_style' => 'parisii-blocks-editor',
        'style' => 'parisii-blocks-style',
    ]);

    // Register duplicate block
    register_block_type('parisii-optique/duplicate', [
        'editor_script' => 'parisii-blocks',
        'editor_style' => 'parisii-blocks-editor',
        'style' => 'parisii-blocks-style',
    ]);
}
add_action('init', 'parisii_optique_register_blocks');

/**
 * Render product categories block on frontend
 */
function parisii_optique_render_product_categories_block($attributes) {
    // Check if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return '<p>' . __('WooCommerce n\'est pas installé', 'parisii-optique') . '</p>';
    }

    $style = $attributes['style'] ?? 'grid';
    $columns = $attributes['columns'] ?? 3;
    $showIcons = $attributes['showIcons'] ?? true;
    $showCounts = $attributes['showCounts'] ?? true;
    $showDescriptions = $attributes['showDescriptions'] ?? false;
    $hideEmpty = $attributes['hideEmpty'] ?? true;
    $number = $attributes['number'] ?? 10;
    $orderby = $attributes['orderby'] ?? 'name';
    $order = $attributes['order'] ?? 'ASC';

    // Get product categories
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => $hideEmpty,
        'number' => $number,
        'orderby' => $orderby,
        'order' => $order,
    ]);

    if (is_wp_error($categories) || empty($categories)) {
        return '<p>' . __('Aucune catégorie trouvée', 'parisii-optique') . '</p>';
    }

    // Enqueue scripts and styles
    wp_enqueue_script('parisii-menu-categories', get_template_directory_uri() . '/js/menu-categories.js', ['jquery'], '1.0.0', true);
    wp_enqueue_style('parisii-menu-categories', get_template_directory_uri() . '/css/menu-categories.css', [], '1.0.0');

    ob_start();
    ?>
    <div class="parisii-product-categories" data-style="<?php echo esc_attr($style); ?>" data-columns="<?php echo esc_attr($columns); ?>">
        <?php if ($style === 'grid') : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-<?php echo esc_attr($columns); ?> gap-6">
                <?php foreach ($categories as $category) : ?>
                    <div class="category-item bg-white dark:bg-gray-800 rounded-lg shadow-md overflow-hidden hover:shadow-lg transition-shadow">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="block">
                            <?php if ($showIcons) : ?>
                                <div class="category-icon p-4 text-center">
                                    <?php
                                    $thumbnail_id = get_term_meta($category->term_id, 'thumbnail_id', true);
                                    if ($thumbnail_id) {
                                        echo wp_get_attachment_image($thumbnail_id, 'medium', false, ['class' => 'w-16 h-16 mx-auto rounded-full object-cover']);
                                    } else {
                                        echo '<div class="w-16 h-16 mx-auto bg-secondary rounded-full flex items-center justify-center text-white text-2xl">' . substr($category->name, 0, 1) . '</div>';
                                    }
                                    ?>
                                </div>
                            <?php endif; ?>
                            <div class="category-content p-4">
                                <h3 class="category-title text-lg font-semibold text-gray-900 dark:text-white mb-2">
                                    <?php echo esc_html($category->name); ?>
                                </h3>
                                <?php if ($showDescriptions && $category->description) : ?>
                                    <p class="category-description text-sm text-gray-600 dark:text-gray-400 mb-2">
                                        <?php echo esc_html($category->description); ?>
                                    </p>
                                <?php endif; ?>
                                <?php if ($showCounts) : ?>
                                    <span class="category-count text-xs text-gray-500 dark:text-gray-400">
                                        <?php echo esc_html($category->count); ?> produit<?php echo $category->count > 1 ? 's' : ''; ?>
                                    </span>
                                <?php endif; ?>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <!-- Other styles can be implemented here -->
            <div class="category-list">
                <?php foreach ($categories as $category) : ?>
                    <div class="category-item">
                        <a href="<?php echo esc_url(get_term_link($category)); ?>" class="flex items-center justify-between p-3 hover:bg-gray-50 dark:hover:bg-gray-700 rounded">
                            <span class="font-medium"><?php echo esc_html($category->name); ?></span>
                            <?php if ($showCounts) : ?>
                                <span class="text-sm text-gray-500">(<?php echo esc_html($category->count); ?>)</span>
                            <?php endif; ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Add block categories
 */
function parisii_optique_add_block_categories($categories) {
    return array_merge($categories, [
        [
            'slug' => 'parisii-components',
            'title' => __('Composants Parisii', 'parisii-optique'),
            'icon' => 'admin-tools',
        ],
        [
            'slug' => 'parisii-sections',
            'title' => __('Sections Parisii', 'parisii-optique'),
            'icon' => 'layout',
        ],
        [
            'slug' => 'parisii-woocommerce',
            'title' => __('WooCommerce Parisii', 'parisii-optique'),
            'icon' => 'products',
        ],
    ]);
}
add_filter('block_categories_all', 'parisii_optique_add_block_categories');
