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

// Vérifier que WooCommerce est installé et activé
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
    
    if ($product->get_image_id()) {
        echo '<div class="aspect-w-16 aspect-h-12 overflow-hidden rounded-t-xl">';
        echo '<a href="' . get_permalink() . '">';
        echo woocommerce_get_product_thumbnail('card-image', ['class' => 'w-full h-64 object-cover group-hover:scale-105 transition-transform duration-300']);
        echo '</a>';
        echo '</div>';
    }
}
remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
add_action('woocommerce_before_shop_loop_item_title', 'parisii_optique_woocommerce_product_thumbnail', 10);

/**
 * Customize product title
 */
function parisii_optique_woocommerce_shop_loop_item_title() {
    echo '<div class="p-6">';
    echo '<h3 class="text-lg font-heading font-semibold text-gray-900 dark:text-white mb-2 group-hover:text-main-600 dark:group-hover:text-main-400 transition-colors">';
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
        echo '<div class="text-main-600 dark:text-main-400 font-semibold text-lg mb-4">';
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
