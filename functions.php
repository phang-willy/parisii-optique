<?php
/**
 * Parisii Optique Theme Functions
 *
 * @package Parisii_Optique
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Theme setup
function parisii_optique_setup() {
    // Add theme support
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('custom-logo');
    add_theme_support('customize-selective-refresh-widgets');
    add_theme_support('responsive-embeds');
    add_theme_support('wp-block-styles');
    add_theme_support('align-wide');
    add_theme_support('block-patterns');
    add_theme_support('block-templates');
    add_theme_support('block-template-parts');
    
    // Add support for WooCommerce (only if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        add_theme_support('woocommerce');
        add_theme_support('wc-product-gallery-zoom');
        add_theme_support('wc-product-gallery-lightbox');
        add_theme_support('wc-product-gallery-slider');
    }
    
    // Add editor color palette
    add_theme_support('editor-color-palette', [
        [
            'name' => __('Principal', 'parisii-optique'),
            'slug' => 'main',
            'color' => '#c5b68d',
        ],
        [
            'name' => __('Principal Clair', 'parisii-optique'),
            'slug' => 'main-light',
            'color' => '#d9c7a6',
        ],
        [
            'name' => __('Principal Très Clair', 'parisii-optique'),
            'slug' => 'main-lighter',
            'color' => '#e7ddc9',
        ],
        [
            'name' => __('Secondaire', 'parisii-optique'),
            'slug' => 'secondary',
            'color' => '#5C442F',
        ],
        [
            'name' => __('Secondaire Clair', 'parisii-optique'),
            'slug' => 'secondary-light',
            'color' => '#9F7550',
        ],
        [
            'name' => __('Secondaire Très Clair', 'parisii-optique'),
            'slug' => 'secondary-lighter',
            'color' => '#c4a88a',
        ],
        [
            'name' => __('Blanc', 'parisii-optique'),
            'slug' => 'white',
            'color' => '#ffffff',
        ],
        [
            'name' => __('Gris Très Clair', 'parisii-optique'),
            'slug' => 'gray-50',
            'color' => '#f9fafb',
        ],
        [
            'name' => __('Gris Clair', 'parisii-optique'),
            'slug' => 'gray-100',
            'color' => '#f3f4f6',
        ],
        [
            'name' => __('Gris', 'parisii-optique'),
            'slug' => 'gray-500',
            'color' => '#6b7280',
        ],
        [
            'name' => __('Gris Foncé', 'parisii-optique'),
            'slug' => 'gray-700',
            'color' => '#374151',
        ],
        [
            'name' => __('Gris Très Foncé', 'parisii-optique'),
            'slug' => 'gray-900',
            'color' => '#111827',
        ],
        [
            'name' => __('Noir', 'parisii-optique'),
            'slug' => 'black',
            'color' => '#000000',
        ],
    ]);
    
    // Register navigation menus
    register_nav_menus([
        'primary' => __('Menu Principal', 'parisii-optique'),
        'footer' => __('Menu Footer', 'parisii-optique'),
    ]);
    
    // Add image sizes
    add_image_size('card-image', 400, 300, true);
    add_image_size('thumbnail-large', 300, 200, true);
}
add_action('after_setup_theme', 'parisii_optique_setup');

/**
 * Customize document title format
 * Format: Site Title - Page Title (with hierarchy support)
 */
function parisii_optique_document_title_parts($title) {
    global $post;
    
    // Pour les pages et posts, utiliser notre système SEO
    if (is_singular() && $post) {
        $custom_meta_title = get_post_meta($post->ID, '_parisii_meta_title', true);
        
        if (!empty($custom_meta_title)) {
            // Utiliser le titre SEO personnalisé
            $title['title'] = wp_strip_all_tags($custom_meta_title);
            unset($title['site']); // Enlever le nom du site pour éviter la duplication
        } else {
            // Utiliser le système de hiérarchie par défaut
            $site_title = get_bloginfo('name');
            $hierarchy_title = parisii_optique_build_hierarchy_title($post);
            $title['title'] = $site_title . ' - ' . $hierarchy_title;
            unset($title['site']);
        }
    } elseif (is_front_page()) {
        // Page d'accueil : Site Title | Tagline
        return $title;
    } else {
        // Autres pages : Site Title - Page Title
        $site_title = get_bloginfo('name');
        
        if (!empty($title['title'])) {
            $title['title'] = $site_title . ' - ' . $title['title'];
            unset($title['site']);
        }
    }
    
    return $title;
}
add_filter('document_title_parts', 'parisii_optique_document_title_parts');

/**
 * Change document title separator
 */
function parisii_optique_document_title_separator($separator) {
    return '-';
}
add_filter('document_title_separator', 'parisii_optique_document_title_separator');

// Register template parts and shortcode patterns
function parisii_optique_register_template_parts() {
    // Register header template part
    register_block_pattern(
        'parisii-optique/header',
        [
            'title' => __('Header Parisii Optique', 'parisii-optique'),
            'description' => __('Header avec navigation et logo', 'parisii-optique'),
            'content' => '<!-- wp:template-part {"slug":"header","theme":"parisii-optique","tagName":"header"} /-->',
            'categories' => ['header'],
        ]
    );
    
    // Register footer template part
    register_block_pattern(
        'parisii-optique/footer',
        [
            'title' => __('Footer Parisii Optique', 'parisii-optique'),
            'description' => __('Footer avec informations de contact', 'parisii-optique'),
            'content' => '<!-- wp:template-part {"slug":"footer","theme":"parisii-optique","tagName":"footer"} /-->',
            'categories' => ['footer'],
        ]
    );
    
    // Register newsletter section pattern
    register_block_pattern(
        'parisii-optique/newsletter-section',
        [
            'title' => __('Section Newsletter', 'parisii-optique'),
            'description' => __('Section d\'inscription à la newsletter', 'parisii-optique'),
            'content' => '<!-- wp:shortcode -->[newsletter_section title="Restez informé" subtitle="Inscrivez-vous à notre newsletter"]
Conditions d\'utilisation
[/newsletter_section]<!-- /wp:shortcode -->',
            'categories' => ['parisii-sections'],
        ]
    );
    
    // Register product categories pattern
    // WooCommerce patterns (only if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        register_block_pattern(
            'parisii-optique/product-categories',
            [
                'title' => __('Catégories Produits', 'parisii-optique'),
                'description' => __('Affichage des catégories de produits', 'parisii-optique'),
                'content' => '<!-- wp:shortcode -->[product_categories style="grid" columns="3" show_icons="true"]<!-- /wp:shortcode -->',
                'categories' => ['parisii-woocommerce'],
            ]
        );
    }
    
    // Register theme switcher pattern
    register_block_pattern(
        'parisii-optique/theme-switcher',
        [
            'title' => __('Sélecteur de Thème', 'parisii-optique'),
            'description' => __('Bouton de changement de thème clair/sombre', 'parisii-optique'),
            'content' => '<!-- wp:shortcode -->[theme_switcher]<!-- /wp:shortcode -->',
            'categories' => ['theme'],
        ]
    );
}
add_action('init', 'parisii_optique_register_template_parts');

// Register custom pattern categories
function parisii_optique_register_pattern_categories() {
    register_block_pattern_category(
        'parisii-sections',
        [
            'label' => __('Sections Parisii', 'parisii-optique'),
            'description' => __('Sections personnalisées du thème', 'parisii-optique'),
        ]
    );
    
    register_block_pattern_category(
        'parisii-components',
        [
            'label' => __('Composants Parisii', 'parisii-optique'),
            'description' => __('Composants interactifs du thème', 'parisii-optique'),
        ]
    );
    
    // WooCommerce category (only if WooCommerce is active)
    if (class_exists('WooCommerce')) {
        register_block_pattern_category(
            'parisii-woocommerce',
            [
                'label' => __('WooCommerce Parisii', 'parisii-optique'),
                'description' => __('Composants WooCommerce personnalisés', 'parisii-optique'),
            ]
        );
    }
}
add_action('init', 'parisii_optique_register_pattern_categories');

// Remove default WordPress block patterns to avoid conflicts
function parisii_optique_remove_default_patterns() {
    remove_theme_support('core-block-patterns');
}
add_action('after_setup_theme', 'parisii_optique_remove_default_patterns');

// Enqueue scripts and styles
function parisii_optique_scripts() {
    // Enqueue Tailwind CSS
    wp_enqueue_style('tailwind-css', get_template_directory_uri() . '/dist/style.css', [], '1.0.0');
    
    // Enqueue theme styles
    wp_enqueue_style('parisii-optique-style', get_stylesheet_uri(), ['tailwind-css'], '1.0.0');
    
    // Enqueue theme script
    wp_enqueue_script('parisii-optique-script', get_template_directory_uri() . '/js/theme.js', ['jquery'], '1.0.0', true);
    
    // Localize script for AJAX
    wp_localize_script('parisii-optique-script', 'parisii_ajax', [
        'ajax_url' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('parisii_nonce'),
    ]);
}
add_action('wp_enqueue_scripts', 'parisii_optique_scripts');

// Register widget areas
function parisii_optique_widgets_init() {
    register_sidebar([
        'name' => __('Sidebar', 'parisii-optique'),
        'id' => 'sidebar-1',
        'description' => __('Add widgets here.', 'parisii-optique'),
        'before_widget' => '<section id="%1$s" class="widget %2$s">',
        'after_widget' => '</section>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
    
    register_sidebar([
        'name' => __('Footer 1', 'parisii-optique'),
        'id' => 'footer-1',
        'description' => __('Footer section 1', 'parisii-optique'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
    
    register_sidebar([
        'name' => __('Footer 2', 'parisii-optique'),
        'id' => 'footer-2',
        'description' => __('Footer section 2', 'parisii-optique'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
    
    register_sidebar([
        'name' => __('Footer 3', 'parisii-optique'),
        'id' => 'footer-3',
        'description' => __('Footer section 3', 'parisii-optique'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
    
    register_sidebar([
        'name' => __('Footer 4', 'parisii-optique'),
        'id' => 'footer-4',
        'description' => __('Footer section 4', 'parisii-optique'),
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget' => '</div>',
        'before_title' => '<h3 class="widget-title">',
        'after_title' => '</h3>',
    ]);
}
add_action('widgets_init', 'parisii_optique_widgets_init');

// Customizer settings
function parisii_optique_customize_register($wp_customize) {
    // Colors section
    $wp_customize->add_section('parisii_colors', [
        'title' => __('Couleurs du thème', 'parisii-optique'),
        'priority' => 25,
        'description' => __('Personnalisez les couleurs principales du thème', 'parisii-optique'),
    ]);
    
    // Main color
    $wp_customize->add_setting('color_main', [
        'default' => '#558763',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_main', [
        'label' => __('Couleur principale', 'parisii-optique'),
        'section' => 'parisii_colors',
        'description' => __('Vert - Couleur principale du thème (#558763)', 'parisii-optique'),
    ]));
    
    // Main hover color
    $wp_customize->add_setting('color_main_hover', [
        'default' => '#64BE7D',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_main_hover', [
        'label' => __('Couleur principale (hover)', 'parisii-optique'),
        'section' => 'parisii_colors',
        'description' => __('Vert clair au survol (#64BE7D)', 'parisii-optique'),
    ]));
    
    // Secondary color
    $wp_customize->add_setting('color_secondary', [
        'default' => '#5C442F',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_secondary', [
        'label' => __('Couleur secondaire', 'parisii-optique'),
        'section' => 'parisii_colors',
        'description' => __('Marron - Couleur secondaire du thème (#5C442F)', 'parisii-optique'),
    ]));
    
    // Secondary hover color
    $wp_customize->add_setting('color_secondary_hover', [
        'default' => '#9F7550',
        'sanitize_callback' => 'sanitize_hex_color',
        'transport' => 'refresh',
    ]);
    
    $wp_customize->add_control(new WP_Customize_Color_Control($wp_customize, 'color_secondary_hover', [
        'label' => __('Couleur secondaire (hover)', 'parisii-optique'),
        'section' => 'parisii_colors',
        'description' => __('Marron clair au survol (#9F7550)', 'parisii-optique'),
    ]));
    
    // Theme switcher section
    $wp_customize->add_section('theme_switcher', [
        'title' => __('Thème', 'parisii-optique'),
        'priority' => 30,
    ]);
    
    $wp_customize->add_setting('default_theme_mode', [
        'default' => 'system',
        'sanitize_callback' => 'sanitize_text_field',
    ]);
    
    $wp_customize->add_control('default_theme_mode', [
        'label' => __('Mode de thème par défaut', 'parisii-optique'),
        'section' => 'theme_switcher',
        'type' => 'select',
        'choices' => [
            'light' => __('Clair', 'parisii-optique'),
            'dark' => __('Sombre', 'parisii-optique'),
            'system' => __('Système', 'parisii-optique'),
        ],
    ]);
}
add_action('customize_register', 'parisii_optique_customize_register');

/**
 * Output custom colors CSS
 */
function parisii_optique_custom_colors_css() {
    $color_main = get_theme_mod('color_main', '#558763');
    $color_main_hover = get_theme_mod('color_main_hover', '#64BE7D');
    $color_secondary = get_theme_mod('color_secondary', '#5C442F');
    $color_secondary_hover = get_theme_mod('color_secondary_hover', '#9F7550');
    
    ?>
    <style type="text/css" id="parisii-custom-colors">
        :root {
            --color-main: <?php echo esc_html($color_main); ?> !important;
            --color-main-hover: <?php echo esc_html($color_main_hover); ?> !important;
            --color-secondary: <?php echo esc_html($color_secondary); ?> !important;
            --color-secondary-hover: <?php echo esc_html($color_secondary_hover); ?> !important;
        }
        
        [data-theme="dark"], .dark {
            --color-main: <?php echo esc_html($color_main_hover); ?> !important;
            --color-main-hover: <?php echo esc_html($color_main); ?> !important;
            --color-secondary: <?php echo esc_html($color_secondary_hover); ?> !important;
            --color-secondary-hover: <?php echo esc_html($color_secondary); ?> !important;
        }
        
        /* Debug - Afficher les couleurs */
        /* Main: <?php echo $color_main; ?>, Hover: <?php echo $color_main_hover; ?> */
        /* Secondary: <?php echo $color_secondary; ?>, Hover: <?php echo $color_secondary_hover; ?> */
    </style>
    <?php
}
add_action('wp_head', 'parisii_optique_custom_colors_css', 100);

// Add body classes for theme mode
function parisii_optique_body_classes($classes) {
    $theme_mode = get_theme_mod('default_theme_mode', 'system');
    $classes[] = 'theme-' . $theme_mode;
    return $classes;
}
add_filter('body_class', 'parisii_optique_body_classes');

// Include additional files
require_once get_template_directory() . '/inc/theme-functions.php';

// Include WooCommerce functions (only if WooCommerce is active)
if (class_exists('WooCommerce')) {
    require_once get_template_directory() . '/inc/woocommerce-functions.php';
}

require_once get_template_directory() . '/inc/theme-switcher.php';
require_once get_template_directory() . '/inc/deploy-purge.php';

// Include plugin activation class
require_once get_template_directory() . '/inc/class-tgm-plugin-activation.php';

// Include plugin sync (development mode only)
require_once get_template_directory() . '/inc/plugin-sync.php';

// Include components
require_once get_template_directory() . '/components/sections.php';
require_once get_template_directory() . '/components/theme-switcher.php';
require_once get_template_directory() . '/components/duplicate-content.php';
require_once get_template_directory() . '/components/advanced-duplicate.php';
require_once get_template_directory() . '/components/duplicate-shortcode.php';
// require_once get_template_directory() . '/components/menu-product-categories.php'; // Désactivé - walker avec icônes
// require_once get_template_directory() . '/components/advanced-menu-categories.php'; // Désactivé - walker avec icônes
require_once get_template_directory() . '/components/menu-categories-shortcode.php';
// require_once get_template_directory() . '/components/menu-admin-categories.php';
// require_once get_template_directory() . '/components/menu-admin-walker.php';
require_once get_template_directory() . '/components/menu-admin-simple.php';

// Include custom blocks
require_once get_template_directory() . '/blocks/blocks.php';

/**
 * Customize product category URLs
 * Remove /categorie-produit/ prefix from category URLs
 * DÉSACTIVÉ - URLs WooCommerce par défaut
 */
/*
function parisii_optique_customize_product_category_urls() {
    // Only run if WooCommerce is active
    if (!class_exists('WooCommerce')) {
        return;
    }
    
    // Get product category slugs to make rules more specific
    $categories = get_terms([
        'taxonomy' => 'product_cat',
        'hide_empty' => false,
        'fields' => 'slugs'
    ]);
    
    if (empty($categories) || is_wp_error($categories)) {
        return;
    }
    
    // Create a pattern that matches only product category slugs
    $category_slugs = implode('|', array_map('preg_quote', $categories));
    
    // Add rewrite rules for product categories (more specific)
    add_rewrite_rule(
        '^(' . $category_slugs . ')/?$',
        'index.php?product_cat=$matches[1]',
        'top'
    );
    
    // Add rewrite rules for product category pages with pagination
    add_rewrite_rule(
        '^(' . $category_slugs . ')/page/([0-9]+)/?$',
        'index.php?product_cat=$matches[1]&paged=$matches[2]',
        'top'
    );
}
add_action('init', 'parisii_optique_customize_product_category_urls');
*/

/**
 * Modify product category permalinks
 * DÉSACTIVÉ - URLs WooCommerce par défaut
 */
/*
function parisii_optique_modify_product_category_permalinks($permalink, $term, $taxonomy) {
    // Only modify product category URLs
    if ($taxonomy !== 'product_cat') {
        return $permalink;
    }
    
    // Remove the base slug from the permalink
    $base_slug = 'categorie-produit';
    $permalink = str_replace('/' . $base_slug . '/', '/', $permalink);
    
    return $permalink;
}
add_filter('term_link', 'parisii_optique_modify_product_category_permalinks', 10, 3);
*/

/**
 * Handle custom category URL requests
 * DÉSACTIVÉ - URLs WooCommerce par défaut
 */
/*
function parisii_optique_handle_custom_category_requests() {
    global $wp_query;
    
    // Check if this is a product category request
    if (is_tax('product_cat')) {
        return;
    }
    
    // Skip if it's the homepage
    if (is_home() || is_front_page()) {
        return;
    }
    
    // Get the current request URI
    $request_uri = $_SERVER['REQUEST_URI'];
    $request_uri = trim($request_uri, '/');
    
    // Skip if it's an admin request or contains query parameters
    if (is_admin() || strpos($request_uri, '?') !== false) {
        return;
    }
    
    // Remove index.php from the request URI if present
    $request_uri = str_replace('index.php/', '', $request_uri);
    $request_uri = trim($request_uri, '/');
    
    // Skip empty requests (homepage)
    if (empty($request_uri)) {
        return;
    }
    
    // Skip if it's a known WordPress path
    $wp_paths = ['wp-admin', 'wp-content', 'wp-includes', 'wp-json', 'xmlrpc.php'];
    foreach ($wp_paths as $path) {
        if (strpos($request_uri, $path) === 0) {
            return;
        }
    }
    
    // Check if this matches a product category slug
    $term = get_term_by('slug', $request_uri, 'product_cat');
    
    if ($term && !is_wp_error($term)) {
        // Redirect to clean URL if we're on an index.php URL
        if (strpos($_SERVER['REQUEST_URI'], 'index.php/') !== false) {
            $clean_url = home_url('/' . $term->slug . '/');
            wp_redirect($clean_url, 301);
            exit;
        }
        
        // Set up the query for this category
        $wp_query->is_tax = true;
        $wp_query->is_archive = true;
        $wp_query->is_home = false;
        $wp_query->is_single = false;
        $wp_query->is_page = false;
        $wp_query->queried_object = $term;
        $wp_query->queried_object_id = $term->term_id;
        $wp_query->set('product_cat', $term->slug);
        $wp_query->set('post_type', 'product');
        $wp_query->set('posts_per_page', get_option('posts_per_page'));
        
        // Set the page title
        add_filter('wp_title', function($title) use ($term) {
            return $term->name . ' - ' . get_bloginfo('name');
        });
        
        // Set the page description
        add_filter('wp_head', function() use ($term) {
            if ($term->description) {
                echo '<meta name="description" content="' . esc_attr($term->description) . '">' . "\n";
            }
        });
    }
}
add_action('template_redirect', 'parisii_optique_handle_custom_category_requests');
*/

/**
 * Flush rewrite rules when theme is activated
 * DÉSACTIVÉ - URLs WooCommerce par défaut
 */
/*
function parisii_optique_flush_rewrite_rules() {
    parisii_optique_customize_product_category_urls();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'parisii_optique_flush_rewrite_rules');
*/

/**
 * Add admin notice to flush rewrite rules
 * DÉSACTIVÉ - URLs WooCommerce par défaut
 */
/*
function parisii_optique_admin_notice_rewrite_rules() {
    if (get_option('parisii_optique_flush_rewrite_rules') !== '1') {
        ?>
        <div class="notice notice-warning is-dismissible">
            <p>
                <strong>Parisii Optique:</strong> 
                Les URLs des catégories de produits ont été personnalisées. 
                <a href="<?php echo admin_url('options-permalink.php'); ?>" class="button button-primary">
                    Mettre à jour les permaliens
                </a>
            </p>
        </div>
        <?php
    }
}
add_action('admin_notices', 'parisii_optique_admin_notice_rewrite_rules');
*/

/**
 * Mark rewrite rules as flushed
 * DÉSACTIVÉ - URLs WooCommerce par défaut
 */
/*
function parisii_optique_mark_rewrite_rules_flushed() {
    if (isset($_GET['settings-updated']) && $_GET['settings-updated'] === 'true') {
        update_option('parisii_optique_flush_rewrite_rules', '1');
    }
}
add_action('admin_init', 'parisii_optique_mark_rewrite_rules_flushed');
*/

/**
 * Add theme initialization script to prevent FOUC
 */
function parisii_optique_theme_init_script() {
    ?>
    <script>
        (function() {
            const savedTheme = localStorage.getItem('parisii-theme');
            const html = document.documentElement;
            
            // Remove any existing theme classes first
            html.classList.remove('light', 'dark');
            
            if (savedTheme && savedTheme !== 'system') {
                // Use saved theme (light or dark)
                html.classList.add(savedTheme);
            } else {
                // No saved theme or system theme - detect device preference
                const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                html.classList.add(prefersDark ? 'dark' : 'light');
                
                // If no saved theme, save as 'system'
                if (!savedTheme) {
                    localStorage.setItem('parisii-theme', 'system');
                }
            }
        })();
    </script>
    <?php
}
add_action('wp_head', 'parisii_optique_theme_init_script', 1);

/**
 * Get product category URL by slug
 * 
 * @param string $slug Category slug
 * @param string $fallback_url Fallback URL if category doesn't exist
 * @return string Category URL or fallback URL
 */
function parisii_optique_get_category_url($slug, $fallback_url = '') {
    $term = get_term_by('slug', $slug, 'product_cat');
    
    if ($term && !is_wp_error($term)) {
        return get_term_link($term);
    }
    
    return $fallback_url ?: home_url('/');
}

/**
 * Normalize a phone number for use in a tel: href.
 *
 * Rules:
 * - Remove all characters except digits and leading +
 * - Convert leading 00 to + (international prefix)
 * - If starts with 0 (national FR), replace leading 0 by +33
 * - If starts with 33 (without +), prefix with +
 * - If already starts with +, keep as-is (after cleanup)
 *
 * @param string $phone Raw phone string
 * @return string Normalized phone suitable for tel: links (e.g. +33612345678)
 */
function parisii_optique_normalize_phone_for_tel($phone) {
    $phone = trim((string) $phone);

    // Keep only digits and plus
    $clean = preg_replace('/[^0-9+]/', '', $phone);

    if ($clean === '') {
        return '';
    }

    // Handle leading international 00 -> +
    if (strpos($clean, '00') === 0) {
        $clean = '+' . substr($clean, 2);
    }

    // If it already starts with +, ensure single leading + and return
    if (strpos($clean, '+') === 0) {
        // Remove any accidental additional pluses
        $clean = '+' . ltrim($clean, '+');
        return $clean;
    }

    // National French number starting with 0 -> replace by +33
    if (strpos($clean, '0') === 0) {
        return '+33' . substr($clean, 1);
    }

    // Starts with 33 without plus -> add plus
    if (strpos($clean, '33') === 0) {
        return '+' . $clean;
    }

    // Fallback: return cleaned number
    return $clean;
}

/**
 * Wrap top-level blocks in sections - SIMPLIFIÉ
 * Wrappe chaque bloc de premier niveau, même s'ils sont dans un conteneur
 */
function parisii_optique_wrap_blocks_in_sections($content) {
    // Only for pages and posts, not in admin
    if (is_admin() || !is_singular()) {
        return $content;
    }
    
    // Parse blocks from the original content
    $blocks = parse_blocks($content);
    
    if (empty($blocks)) {
        return $content;
    }
    
    // Render content normally first to get the full HTML
    $rendered_content = '';
    foreach ($blocks as $block) {
        $rendered_content .= render_block($block);
    }
    
    // Now wrap each top-level element in a section using DOMDocument
    $dom = new DOMDocument();
    @$dom->loadHTML('<?xml encoding="utf-8" ?><body>' . $rendered_content . '</body>', LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
    
    $body = $dom->getElementsByTagName('body')->item(0);
    if (!$body) {
        return $rendered_content;
    }
    
    $output = '';
    
    foreach ($body->childNodes as $node) {
        // Skip text nodes that are just whitespace
        if ($node->nodeType === XML_TEXT_NODE && trim($node->nodeValue) === '') {
            continue;
        }
        
        // Vérifie si le noeud a une classe alignfull pour ajuster les classes de section
        $has_alignfull = false;
        if ($node->nodeType === XML_ELEMENT_NODE && $node->hasAttributes()) {
            $classAttr = $node->attributes->getNamedItem('class');
            if ($classAttr && strpos($classAttr->nodeValue, 'alignfull') !== false) {
                $has_alignfull = true;
            }
        }
        $section_classes = 'wp-block-section';
        $paddings  = "";
        if (!$has_alignfull) {
            $section_classes .= ' max-w-7xl mx-auto';
            $paddings = "p-4 md:p-6 lg:p-8";
        }
        
        // Ajouter les paddings au premier élément enfant (le nœud lui-même s'il est un élément)
        if ($node->nodeType === XML_ELEMENT_NODE) {
            $existingClass = $node->getAttribute('class');
            $newClass = $existingClass ? $existingClass . ' ' . $paddings : $paddings;
            $node->setAttribute('class', $newClass);
        }
        
        $node_html = $dom->saveHTML($node);
        
        $output .= '<section class="' . $section_classes . '">';
        $output .= $node_html;
        $output .= '</section>' . "\n";
    }
    
    return $output;
}
add_filter('the_content', 'parisii_optique_wrap_blocks_in_sections', 9);

/**
 * Ajouter une icône de redirection externe aux liens externes
 */
function parisii_optique_add_external_link_icon($content) {
    // Récupérer l'URL du site
    $site_url = home_url();
    $site_domain = parse_url($site_url, PHP_URL_HOST);
    
    // Pattern pour trouver les liens <a href="...">
    $pattern = '/<a\s+([^>]*?)href=["\']([^"\']*?)["\']([^>]*?)>/i';
    
    $content = preg_replace_callback($pattern, function($matches) use ($site_domain) {
        $before_href = $matches[1];
        $url = $matches[2];
        $after_href = $matches[3];
        
        // Vérifier si c'est un lien externe
        $is_external = false;
        
        // Si l'URL commence par http/https et ne contient pas le domaine du site
        if (preg_match('/^https?:\/\//', $url)) {
            $url_domain = parse_url($url, PHP_URL_HOST);
            if ($url_domain && $url_domain !== $site_domain) {
                $is_external = true;
            }
        }
        
        // Si c'est un lien externe, ajouter l'icône
        if ($is_external) {
            // Vérifier si l'icône n'est pas déjà présente
            if (strpos($matches[0], 'external-link') === false) {
                $external_icon = '<svg class="ml-1 w-3 h-3 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>';
                return '<a ' . $before_href . 'href="' . $url . '"' . $after_href . '><span class="inline-flex items-center">' . 
                       strip_tags($matches[0], '<a>') . 
                       $external_icon . '</span></a>';
            }
        }
        
        return $matches[0];
    }, $content);
    
    return $content;
}
add_filter('the_content', 'parisii_optique_add_external_link_icon', 10);
add_filter('widget_text', 'parisii_optique_add_external_link_icon', 10);
add_filter('comment_text', 'parisii_optique_add_external_link_icon', 10);

/**
 * SEO Meta Box for pages and posts
 */
function parisii_optique_add_seo_meta_box() {
    $post_types = ['page', 'post'];
    
    foreach ($post_types as $post_type) {
        add_meta_box(
            'parisii-seo-meta-box',
            __('SEO - Optimisation', 'parisii-optique'),
            'parisii_optique_seo_meta_box_callback',
            $post_type,
            'side',
            'high'
        );
    }
}
add_action('add_meta_boxes', 'parisii_optique_add_seo_meta_box');

/**
 * SEO Meta Box Callback
 */
function parisii_optique_seo_meta_box_callback($post) {
    wp_nonce_field('parisii_seo_meta_box', 'parisii_seo_meta_box_nonce');
    
    $meta_title = get_post_meta($post->ID, '_parisii_meta_title', true);
    $meta_description = get_post_meta($post->ID, '_parisii_meta_description', true);
    $og_image = get_post_meta($post->ID, '_parisii_og_image', true);
    
    $site_title = get_bloginfo('name');
    $site_description = get_bloginfo('description');
    $page_title = $post->post_title;
    
    // Construire le titre hiérarchique
    $hierarchy_title = parisii_optique_build_hierarchy_title($post);
    $default_title = $site_title . ' - ' . $hierarchy_title;
    
    // Valeurs par défaut si vides
    if (empty($meta_title)) {
        $meta_title = $default_title;
    }
    if (empty($meta_description)) {
        $meta_description = $site_title . ' - ' . $hierarchy_title . ' - ' . $site_description;
    }
    
    // Image par défaut (featured image ou logo du site)
    $default_og_image = '';
    if (has_post_thumbnail($post->ID)) {
        $default_og_image = get_the_post_thumbnail_url($post->ID, 'large');
    } elseif (has_custom_logo()) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $default_og_image = wp_get_attachment_image_url($custom_logo_id, 'large');
    }
    
    ?>
    <div class="parisii-seo-meta-box">
        <p>
            <label for="parisii_meta_title" class="screen-reader-text">
                <?php _e('Meta Title', 'parisii-optique'); ?>
            </label>
            <input 
                type="text" 
                id="parisii_meta_title" 
                name="parisii_meta_title" 
                value="<?php echo esc_attr($meta_title); ?>"
                placeholder="<?php echo esc_attr($default_title); ?>"
                style="width: 100%;"
            />
            <p class="description">
                <strong><?php _e('Longueur:', 'parisii-optique'); ?></strong> 
                <span id="meta-title-length"><?php echo strlen($meta_title); ?></span>/60 
                <?php _e('caractères', 'parisii-optique'); ?>
            </p>
        </p>
        
        <p>
            <label for="parisii_meta_description" class="screen-reader-text">
                <?php _e('Meta Description', 'parisii-optique'); ?>
            </label>
            <textarea 
                id="parisii_meta_description" 
                name="parisii_meta_description" 
                rows="4" 
                style="width: 100%;"
                placeholder="<?php echo esc_attr($site_title . ' - ' . $hierarchy_title . ' - ' . $site_description); ?>"
            ><?php echo esc_textarea($meta_description); ?></textarea>
            <p class="description">
                <strong><?php _e('Longueur:', 'parisii-optique'); ?></strong> 
                <span id="meta-desc-length"><?php echo strlen($meta_description); ?></span>/160 
                <?php _e('caractères', 'parisii-optique'); ?>
            </p>
        </p>
        
        <p>
            <label for="parisii_og_image" class="screen-reader-text">
                <?php _e('Image Open Graph', 'parisii-optique'); ?>
            </label>
            <input 
                type="url" 
                id="parisii_og_image" 
                name="parisii_og_image" 
                value="<?php echo esc_url($og_image); ?>"
                placeholder="<?php echo esc_attr($default_og_image); ?>"
                style="width: 100%;"
            />
            <p class="description">
                <?php _e('Image pour réseaux sociaux (1200x630px)', 'parisii-optique'); ?>
            </p>
        </p>
        
        <div class="seo-info">
            <p class="description">
                <strong><?php _e('Valeurs par défaut:', 'parisii-optique'); ?></strong><br>
                <code><?php echo esc_html($default_title); ?></code>
            </p>
            
            <p class="description">
                <button type="button" id="use-default-meta-title" class="button button-small">
                    <?php _e('Titre par défaut', 'parisii-optique'); ?>
                </button>
                <button type="button" id="use-default-meta-desc" class="button button-small">
                    <?php _e('Description par défaut', 'parisii-optique'); ?>
                </button>
                <?php if ($default_og_image): ?>
                <button type="button" id="use-default-og-image" class="button button-small">
                    <?php _e('Image par défaut', 'parisii-optique'); ?>
                </button>
                <?php endif; ?>
            </p>
        </div>
    </div>
    
    <style>
    .parisii-seo-meta-box input[type="text"],
    .parisii-seo-meta-box input[type="url"],
    .parisii-seo-meta-box textarea {
        font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
        font-size: 13px;
        line-height: 1.4;
        resize: vertical;
        min-height: 30px;
    }
    
    .parisii-seo-meta-box textarea {
        min-height: 80px;
    }
    
    .parisii-seo-meta-box .seo-info {
        margin-top: 15px;
        padding: 10px;
        background: #f9f9f9;
        border: 1px solid #ddd;
        border-radius: 4px;
    }
    
    .parisii-seo-meta-box .seo-info .description {
        margin: 5px 0;
        font-size: 12px;
    }
    
    .parisii-seo-meta-box .seo-info code {
        background: #fff;
        padding: 2px 4px;
        border-radius: 3px;
        font-size: 11px;
        word-break: break-all;
    }
    
    .parisii-seo-meta-box .seo-info .button {
        margin-right: 5px;
        margin-bottom: 5px;
        font-size: 11px;
        padding: 2px 6px;
        height: auto;
    }
    
    #meta-title-length,
    #meta-desc-length {
        font-weight: bold;
        color: #0073aa;
    }
    
    #meta-title-length.warning,
    #meta-desc-length.warning {
        color: #d63638;
    }
    
    #meta-title-length.good,
    #meta-desc-length.good {
        color: #00a32a;
    }
    </style>
    
    <script>
    jQuery(document).ready(function($) {
        const titleInput = $('#parisii_meta_title');
        const descTextarea = $('#parisii_meta_description');
        const ogImageInput = $('#parisii_og_image');
        
        const titleLengthSpan = $('#meta-title-length');
        const descLengthSpan = $('#meta-desc-length');
        
        const useDefaultTitleBtn = $('#use-default-meta-title');
        const useDefaultDescBtn = $('#use-default-meta-desc');
        const useDefaultOgImageBtn = $('#use-default-og-image');
        
        // Mise à jour du compteur de caractères pour le titre
        function updateTitleCharCount() {
            const length = titleInput.val().length;
            titleLengthSpan.text(length);
            
            titleLengthSpan.removeClass('warning good');
            if (length > 60) {
                titleLengthSpan.addClass('warning');
            } else if (length >= 50 && length <= 60) {
                titleLengthSpan.addClass('good');
            }
        }
        
        // Mise à jour du compteur de caractères pour la description
        function updateDescCharCount() {
            const length = descTextarea.val().length;
            descLengthSpan.text(length);
            
            descLengthSpan.removeClass('warning good');
            if (length > 160) {
                descLengthSpan.addClass('warning');
            } else if (length >= 120 && length <= 160) {
                descLengthSpan.addClass('good');
            }
        }
        
        // Événements sur les champs
        titleInput.on('input keyup', updateTitleCharCount);
        descTextarea.on('input keyup', updateDescCharCount);
        
        // Boutons pour utiliser les valeurs par défaut
        useDefaultTitleBtn.on('click', function() {
            const defaultValue = '<?php echo esc_js($default_title); ?>';
            titleInput.val(defaultValue);
            updateTitleCharCount();
        });
        
        useDefaultDescBtn.on('click', function() {
            const defaultValue = '<?php echo esc_js($site_title . ' - ' . $hierarchy_title . ' - ' . $site_description); ?>';
            descTextarea.val(defaultValue);
            updateDescCharCount();
        });
        
        useDefaultOgImageBtn.on('click', function() {
            const defaultValue = '<?php echo esc_js($default_og_image); ?>';
            ogImageInput.val(defaultValue);
        });
        
        // Initialiser les compteurs
        updateTitleCharCount();
        updateDescCharCount();
    });
    </script>
    <?php
}

/**
 * Build hierarchy title for SEO
 */
function parisii_optique_build_hierarchy_title($post) {
    $title_parts = [];
    
    // Si c'est une page avec un parent
    if ($post->post_type === 'page' && $post->post_parent > 0) {
        $ancestors = get_post_ancestors($post->ID);
        $ancestors = array_reverse($ancestors);
        
        foreach ($ancestors as $ancestor_id) {
            $ancestor = get_post($ancestor_id);
            if ($ancestor) {
                $title_parts[] = $ancestor->post_title;
            }
        }
    }
    
    // Ajouter le titre de la page actuelle
    $title_parts[] = $post->post_title;
    
    return implode(' - ', $title_parts);
}

/**
 * Save SEO Meta Box Data
 */
function parisii_optique_save_seo_meta_box($post_id) {
    // Vérifier le nonce
    if (!isset($_POST['parisii_seo_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['parisii_seo_meta_box_nonce'], 'parisii_seo_meta_box')) {
        return;
    }
    
    // Vérifier les permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Vérifier l'autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Sauvegarder les champs SEO
    $fields = [
        'parisii_meta_title' => 'sanitize_text_field',
        'parisii_meta_description' => 'sanitize_textarea_field',
        'parisii_og_image' => 'esc_url_raw'
    ];
    
    foreach ($fields as $field => $sanitize_function) {
        if (isset($_POST[$field])) {
            $value = call_user_func($sanitize_function, $_POST[$field]);
            update_post_meta($post_id, '_' . $field, $value);
        }
    }
}
add_action('save_post', 'parisii_optique_save_seo_meta_box');

/**
 * Output complete SEO meta tags in frontend head
 */
function parisii_optique_output_seo_meta_tags() {
    global $post;
    
    $site_title = get_bloginfo('name');
    $site_description = get_bloginfo('description');
    $site_url = home_url('/');
    
    // Variables par défaut
    $meta_title = $site_title;
    $meta_description = $site_description;
    $og_image = '';
    
    // Si c'est une page ou un post
    if (is_singular() && $post) {
        $hierarchy_title = parisii_optique_build_hierarchy_title($post);
        
        // Meta title
        $custom_meta_title = get_post_meta($post->ID, '_parisii_meta_title', true);
        if (!empty($custom_meta_title)) {
            $meta_title = $custom_meta_title;
        } else {
            $meta_title = $site_title . ' - ' . $hierarchy_title;
        }
        
        // Meta description
        $custom_meta_description = get_post_meta($post->ID, '_parisii_meta_description', true);
        if (!empty($custom_meta_description)) {
            $meta_description = $custom_meta_description;
        } else {
            $meta_description = $site_title . ' - ' . $hierarchy_title . ' - ' . $site_description;
        }
        
        // OG Image
        $custom_og_image = get_post_meta($post->ID, '_parisii_og_image', true);
        if (!empty($custom_og_image)) {
            $og_image = $custom_og_image;
        } elseif (has_post_thumbnail($post->ID)) {
            $og_image = get_the_post_thumbnail_url($post->ID, 'large');
        }
    }
    
    // Nettoyer et échapper les valeurs
    $meta_title = wp_strip_all_tags($meta_title);
    $meta_description = wp_strip_all_tags($meta_description);
    
    $meta_title = esc_attr($meta_title);
    $meta_description = esc_attr($meta_description);
    $og_image = esc_url($og_image);
    
    // Si pas d'image OG, utiliser le logo du site
    if (empty($og_image) && has_custom_logo()) {
        $custom_logo_id = get_theme_mod('custom_logo');
        $og_image = wp_get_attachment_image_url($custom_logo_id, 'large');
        $og_image = esc_url($og_image);
    }
    
    // Page URL
    $page_url = is_singular() ? get_permalink($post->ID) : $site_url;
    $page_url = esc_url($page_url);
    
    // Output meta tags
    echo "\n<!-- SEO Meta Tags -->\n";
    
    // Meta description
    echo '<meta name="description" content="' . $meta_description . '">' . "\n";
    
    // Open Graph tags
    echo '<meta property="og:type" content="' . (is_singular() ? 'article' : 'website') . '">' . "\n";
    echo '<meta property="og:title" content="' . $meta_title . '">' . "\n";
    echo '<meta property="og:description" content="' . $meta_description . '">' . "\n";
    echo '<meta property="og:url" content="' . $page_url . '">' . "\n";
    echo '<meta property="og:site_name" content="' . esc_attr($site_title) . '">' . "\n";
    
    if (!empty($og_image)) {
        echo '<meta property="og:image" content="' . $og_image . '">' . "\n";
        echo '<meta property="og:image:width" content="1200">' . "\n";
        echo '<meta property="og:image:height" content="630">' . "\n";
        echo '<meta property="og:image:type" content="image/jpeg">' . "\n";
    }
    
    // Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . $meta_title . '">' . "\n";
    echo '<meta name="twitter:description" content="' . $meta_description . '">' . "\n";
    if (!empty($og_image)) {
        echo '<meta name="twitter:image" content="' . $og_image . '">' . "\n";
    }
    
    // Additional meta tags
    echo '<meta name="robots" content="index, follow">' . "\n";
    echo '<link rel="canonical" href="' . $page_url . '">' . "\n";
    
    echo "<!-- End SEO Meta Tags -->\n\n";
}
add_action('wp_head', 'parisii_optique_output_seo_meta_tags', 1);

/**
 * Increase upload file size limit to 10MB
 */
function parisii_optique_increase_upload_limit() {
    // Increase PHP upload limits
    @ini_set('upload_max_filesize', '10M');
    @ini_set('post_max_size', '10M');
    @ini_set('max_execution_time', '300');
    @ini_set('memory_limit', '256M');
}
add_action('init', 'parisii_optique_increase_upload_limit');

/**
 * Set WordPress upload size limit
 */
function parisii_optique_set_upload_size_limit($file) {
    $size = $file['size'];
    $size = $size / 1024 / 1024; // Convert to MB
    
    if ($size > 10) {
        $file['error'] = 'Le fichier est trop volumineux. Taille maximale autorisée : 10 Mo.';
    }
    
    return $file;
}
add_filter('wp_handle_upload_prefilter', 'parisii_optique_set_upload_size_limit');

/**
 * Add admin notice about upload limits
 */
function parisii_optique_upload_limits_notice() {
    if (current_user_can('manage_options')) {
        $max_upload = wp_max_upload_size();
        $max_upload_mb = round($max_upload / 1024 / 1024, 2);
        
        if ($max_upload_mb < 10) {
            echo '<div class="notice notice-warning is-dismissible">';
            echo '<p><strong>Parisii Optique:</strong> La limite d\'upload est actuellement de ' . $max_upload_mb . ' Mo. Pour permettre l\'upload de fichiers jusqu\'à 10 Mo, contactez votre hébergeur pour modifier les paramètres PHP suivants :</p>';
            echo '<ul style="margin-left: 20px;">';
            echo '<li><code>upload_max_filesize = 10M</code></li>';
            echo '<li><code>post_max_size = 10M</code></li>';
            echo '<li><code>max_execution_time = 300</code></li>';
            echo '<li><code>memory_limit = 256M</code></li>';
            echo '</ul>';
            echo '</div>';
        }
    }
}
add_action('admin_notices', 'parisii_optique_upload_limits_notice');

/**
 * Display current upload limits in media library
 */
function parisii_optique_media_upload_limits() {
    if (current_user_can('upload_files')) {
        $max_upload = wp_max_upload_size();
        $max_upload_mb = round($max_upload / 1024 / 1024, 2);
        
        echo '<div class="upload-limit-info" style="background: #f0f0f1; padding: 10px; margin: 10px 0; border-radius: 4px; font-size: 13px;">';
        echo '<strong>Limite d\'upload actuelle :</strong> ' . $max_upload_mb . ' Mo';
        if ($max_upload_mb >= 10) {
            echo ' <span style="color: #00a32a;">✓</span>';
        } else {
            echo ' <span style="color: #d63638;">⚠</span>';
        }
        echo '</div>';
    }
}
add_action('post-upload-ui', 'parisii_optique_media_upload_limits');

/**
 * Add upload size info to media library
 */
function parisii_optique_media_library_upload_info() {
    if (current_user_can('upload_files')) {
        $max_upload = wp_max_upload_size();
        $max_upload_mb = round($max_upload / 1024 / 1024, 2);
        
        echo '<script>
        jQuery(document).ready(function($) {
            if ($("#media-upload-form").length) {
                var info = $("<div class=\"upload-limit-info\" style=\"background: #f0f0f1; padding: 10px; margin: 10px 0; border-radius: 4px; font-size: 13px;\">");
                info.html("<strong>Limite d\'upload :</strong> ' . $max_upload_mb . ' Mo' . ($max_upload_mb >= 10 ? ' <span style=\"color: #00a32a;\">✓</span>' : ' <span style=\"color: #d63638;\">⚠</span>') . '");
                $("#media-upload-form").prepend(info);
            }
        });
        </script>';
    }
}
add_action('admin_footer', 'parisii_optique_media_library_upload_info');