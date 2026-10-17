<?php
/**
 * WooCommerce integration functions
 *
 * @package Parisii_Optique
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/helpers-pure.php';

/**
 * Placeholder image for the current WordPress installation.
 *
 * Prefers /wp-content/uploads/woocommerce-placeholder.webp when that file
 * exists. Production still serves its dated upload until that file is copied
 * there. Domains are never hardcoded: the host comes from wp_get_upload_dir().
 *
 * @return array{src:string,srcset:string,width:int,height:int}
 */
function parisii_get_placeholder_image() {
    static $image = null;
    if (is_array($image)) {
        return $image;
    }

    $image = [
        'src'    => '',
        'srcset' => '',
        'width'  => 0,
        'height' => 0,
    ];

    $uploads = wp_get_upload_dir();
    $basedir = isset($uploads['basedir']) ? (string) $uploads['basedir'] : '';
    $baseurl = isset($uploads['baseurl']) ? (string) $uploads['baseurl'] : '';
    $available = [];

    if ($basedir !== '' && empty($uploads['error'])) {
        $names = array_unique(array_merge(
            parisii_optique_placeholder_candidates(),
            array_keys(parisii_optique_placeholder_srcset_files()),
            ['woocommerce-placeholder.webp']
        ));
        foreach ($names as $relative) {
            if (is_file(trailingslashit($basedir) . $relative)) {
                $available[] = $relative;
            }
        }
    }

    $relative = parisii_optique_choose_placeholder_relative($available);
    if ($relative !== '' && $baseurl !== '') {
        $path = trailingslashit($basedir) . $relative;
        $size = is_file($path) ? @getimagesize($path) : false;
        $image['src'] = parisii_optique_join_upload_url($baseurl, $relative);
        $image['width'] = is_array($size) ? (int) $size[0] : 0;
        $image['height'] = is_array($size) ? (int) $size[1] : 0;
        if ($relative === 'woocommerce-placeholder.webp') {
            $image['srcset'] = parisii_optique_build_placeholder_srcset($baseurl, $available, $image['width']);
        }

        return $image;
    }

    $image['src'] = parisii_optique_native_placeholder_url();

    return $image;
}

/**
 * URL of the fallback placeholder on the current site.
 *
 * @return string
 */
function parisii_get_placeholder_url() {
    return parisii_get_placeholder_image()['src'];
}

/**
 * WooCommerce bundled placeholder, used when no custom file is on disk.
 *
 * @return string
 */
function parisii_optique_native_placeholder_url() {
    if (!defined('WC_PLUGIN_FILE')) {
        return '';
    }

    $directory = dirname(WC_PLUGIN_FILE);
    foreach (['assets/images/placeholder.webp', 'assets/images/placeholder.png'] as $relative) {
        if (is_file($directory . '/' . $relative)) {
            return plugins_url($relative, WC_PLUGIN_FILE);
        }
    }

    return '';
}

/**
 * Point WooCommerce product placeholders at the same file.
 *
 * @param string $src Original placeholder URL.
 * @return string
 */
function parisii_optique_filter_placeholder_img_src($src) {
    $url = parisii_get_placeholder_url();

    return $url !== '' ? $url : $src;
}

/**
 * Keep the product placeholder srcset on files that exist here.
 *
 * @param string $srcset Original srcset.
 * @return string
 */
function parisii_optique_filter_placeholder_img_srcset($srcset) {
    $image = parisii_get_placeholder_image();
    if ($image['src'] === '') {
        return $srcset;
    }

    return $image['srcset'];
}

if (class_exists('WooCommerce')) {
    add_filter('woocommerce_placeholder_img_src', 'parisii_optique_filter_placeholder_img_src');
    add_filter('woocommerce_placeholder_img_srcset', 'parisii_optique_filter_placeholder_img_srcset');
}

// The hooks below need WooCommerce. The placeholder API above stays available without it.
if (!class_exists('WooCommerce')) {
    return;
}

/**
 * Remove WooCommerce default styles
 */
add_filter('woocommerce_enqueue_styles', '__return_empty_array');

/**
 * Add theme support for WooCommerce
 */
function parisii_optique_woocommerce_support() {
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'parisii_optique_woocommerce_support');

/**
 * Customize WooCommerce product loop
 */
function parisii_optique_woocommerce_product_loop_start() {
    echo '<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">';
}
add_action('woocommerce_before_shop_loop', 'parisii_optique_woocommerce_product_loop_start', 5);

function parisii_optique_woocommerce_product_loop_end() {
    echo '</div>';
}
add_action('woocommerce_after_shop_loop', 'parisii_optique_woocommerce_product_loop_end', 25);

/**
 * Customize product card wrapper
 */
function parisii_optique_woocommerce_before_shop_loop_item() {
    echo '<div class="card group hover:shadow-soft-lg transition-shadow duration-300">';
}
add_action('woocommerce_before_shop_loop_item', 'parisii_optique_woocommerce_before_shop_loop_item', 5);

function parisii_optique_woocommerce_after_shop_loop_item() {
    echo '</div>';
}
add_action('woocommerce_after_shop_loop_item', 'parisii_optique_woocommerce_after_shop_loop_item', 25);

/**
 * Customize product image
 */
function parisii_optique_woocommerce_product_thumbnail() {
    global $product;
    
    if (!$product instanceof WC_Product) {
        return;
    }

    echo '<div class="aspect-w-16 aspect-h-12 overflow-hidden rounded-t-xl">';
    echo '<a href="' . esc_url(get_permalink()) . '">';
    if ($product->get_image_id()) {
        echo woocommerce_get_product_thumbnail('card-image', ['class' => 'w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300']);
    } else {
        echo wc_placeholder_img('card-image', [
            'class' => 'w-full h-64 object-cover',
            'alt'   => sprintf(__('Image à venir : %s', 'parisii-optique'), $product->get_name()),
        ]);
    }
    echo '</a>';
    echo '</div>';
}
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'parisii_optique_woocommerce_product_thumbnail', 10);

/**
 * Customize product title
 */
function parisii_optique_woocommerce_shop_loop_item_title() {
    echo '<div class="p-6">';
    echo '<h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-2 bg-main transition-colors">';
    echo '<a href="' . get_permalink() . '">' . get_the_title() . '</a>';
    echo '</h3>';
}
remove_action('woocommerce_shop_loop_item_title', 'woocommerce_template_loop_product_title', 10);
add_action('woocommerce_shop_loop_item_title', 'parisii_optique_woocommerce_shop_loop_item_title', 10);

/**
 * Customize product price
 */
function parisii_optique_woocommerce_after_shop_loop_item_title() {
    global $product;
    
    if ($product->get_price_html()) {
        echo '<div class="bg-main font-semibold text-lg mb-4">';
        echo $product->get_price_html();
        echo '</div>';
    }
    
    // Product description/excerpt
    if ($product->get_short_description()) {
        echo '<div class="text-gray-600 dark:text-gray-400 text-sm mb-4">';
        echo wp_trim_words($product->get_short_description(), 15);
        echo '</div>';
    }
    
    echo '<a href="' . get_permalink() . '" class="btn btn-outline w-full text-center">Voir le produit</a>';
    echo '</div>'; // Close p-6 div
}
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
add_action('woocommerce_after_shop_loop_item_title', 'parisii_optique_woocommerce_after_shop_loop_item_title', 10);

/**
 * Remove add to cart button from shop loop
 */
remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);

/**
 * Customize WooCommerce pagination
 */
function parisii_optique_woocommerce_pagination_args($args) {
    $args['prev_text'] = '← Précédent';
    $args['next_text'] = 'Suivant →';
    $args['class'] = 'mt-12 flex justify-center';
    return $args;
}
add_filter('woocommerce_pagination_args', 'parisii_optique_woocommerce_pagination_args');

/**
 * Customize WooCommerce breadcrumbs
 */
function parisii_optique_woocommerce_breadcrumb_defaults($defaults) {
    $defaults['delimiter'] = '<span class="mx-2 text-gray-400">/</span>';
    $defaults['wrap_before'] = '<nav class="woocommerce-breadcrumb text-sm text-gray-600 dark:text-gray-400 mb-8" aria-label="' . esc_attr__('Breadcrumb', 'woocommerce') . '">';
    $defaults['wrap_after'] = '</nav>';
    return $defaults;
}
add_filter('woocommerce_breadcrumb_defaults', 'parisii_optique_woocommerce_breadcrumb_defaults');

/**
 * Add custom WooCommerce body classes
 */
function parisii_optique_woocommerce_body_classes($classes) {
    // Vérification supplémentaire au cas où
    if (!function_exists('is_woocommerce')) {
        return $classes;
    }
    
    if (is_woocommerce() || is_cart() || is_checkout() || is_account_page()) {
        $classes[] = 'woocommerce-page';
    }
    return $classes;
}
add_filter('body_class', 'parisii_optique_woocommerce_body_classes');

/**
 * Customize WooCommerce shop page title
 */
function parisii_optique_woocommerce_shop_page_title($title) {
    // Vérification supplémentaire au cas où
    if (!function_exists('is_shop')) {
        return $title;
    }
    
    if (is_shop()) {
        return 'Nos Produits';
    }
    return $title;
}
add_filter('woocommerce_page_title', 'parisii_optique_woocommerce_shop_page_title');

/**
 * Add custom WooCommerce templates
 */
function parisii_optique_woocommerce_locate_template($template, $template_name, $template_path) {
    // Vérification supplémentaire au cas où
    if (!function_exists('WC') || !WC()) {
        return $template;
    }
    
    $plugin_path = WC()->plugin_path() . '/templates/';
    $theme_path = get_template_directory() . '/woocommerce/';
    
    if (file_exists($theme_path . $template_name)) {
        return $theme_path . $template_name;
    }
    
    return $template;
}
add_filter('woocommerce_locate_template', 'parisii_optique_woocommerce_locate_template', 10, 3);
