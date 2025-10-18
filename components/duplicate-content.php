<?php
/**
 * Duplicate Content Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add duplicate action to post row actions
 */
function parisii_optique_duplicate_post_link($actions, $post) {
    if (current_user_can('edit_posts')) {
        $duplicate_url = wp_nonce_url(
            add_query_arg([
                'action' => 'parisii_duplicate_post',
                'post_id' => $post->ID,
            ], admin_url('admin.php')),
            'parisii_duplicate_post_' . $post->ID,
            'duplicate_nonce'
        );
        
        $actions['duplicate'] = sprintf(
            '<a href="%s" title="%s" rel="permalink">%s</a>',
            $duplicate_url,
            esc_attr__('Dupliquer cet élément', 'parisii-optique'),
            __('Dupliquer', 'parisii-optique')
        );
    }
    
    return $actions;
}

// Add to different post types
add_filter('post_row_actions', 'parisii_optique_duplicate_post_link', 10, 2);
add_filter('page_row_actions', 'parisii_optique_duplicate_post_link', 10, 2);

// Add to WooCommerce products
add_filter('woocommerce_product_row_actions', 'parisii_optique_duplicate_post_link', 10, 2);

/**
 * Handle duplicate post action
 */
function parisii_optique_handle_duplicate_post() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'parisii_duplicate_post') {
        return;
    }
    
    if (!isset($_GET['post_id']) || !isset($_GET['duplicate_nonce'])) {
        wp_die(__('Paramètres manquants', 'parisii-optique'));
    }
    
    $post_id = intval($_GET['post_id']);
    $nonce = sanitize_text_field($_GET['duplicate_nonce']);
    
    if (!wp_verify_nonce($nonce, 'parisii_duplicate_post_' . $post_id)) {
        wp_die(__('Nonce invalide', 'parisii-optique'));
    }
    
    if (!current_user_can('edit_posts')) {
        wp_die(__('Permissions insuffisantes', 'parisii-optique'));
    }
    
    $original_post = get_post($post_id);
    if (!$original_post) {
        wp_die(__('Élément introuvable', 'parisii-optique'));
    }
    
    // Create duplicate
    $duplicate_id = parisii_optique_duplicate_post($original_post);
    
    if ($duplicate_id) {
        wp_redirect(admin_url('post.php?action=edit&post=' . $duplicate_id . '&duplicated=1'));
        exit;
    } else {
        wp_die(__('Erreur lors de la duplication', 'parisii-optique'));
    }
}
add_action('admin_action_parisii_duplicate_post', 'parisii_optique_handle_duplicate_post');

/**
 * Duplicate post function
 */
function parisii_optique_duplicate_post($post) {
    $new_post_author = wp_get_current_user();
    $new_post_date = current_time('mysql');
    $new_post_date_gmt = get_gmt_from_date($new_post_date);
    
    // Create new post
    $new_post = [
        'post_author' => $new_post_author->ID,
        'post_date' => $new_post_date,
        'post_date_gmt' => $new_post_date_gmt,
        'post_content' => $post->post_content,
        'post_content_filtered' => $post->post_content_filtered,
        'post_title' => $post->post_title . ' (Copie)',
        'post_excerpt' => $post->post_excerpt,
        'post_status' => 'draft',
        'post_type' => $post->post_type,
        'comment_status' => $post->comment_status,
        'ping_status' => $post->ping_status,
        'post_password' => $post->post_password,
        'post_name' => $post->post_name . '-copy',
        'to_ping' => $post->to_ping,
        'pinged' => $post->pinged,
        'post_modified' => $new_post_date,
        'post_modified_gmt' => $new_post_date_gmt,
        'post_parent' => $post->post_parent,
        'menu_order' => $post->menu_order,
        'post_mime_type' => $post->post_mime_type
    ];
    
    $new_post_id = wp_insert_post($new_post);
    
    if (is_wp_error($new_post_id)) {
        return false;
    }
    
    // Duplicate post meta
    $post_meta = get_post_meta($post->ID);
    foreach ($post_meta as $key => $values) {
        foreach ($values as $value) {
            add_post_meta($new_post_id, $key, maybe_unserialize($value));
        }
    }
    
    // Duplicate taxonomies
    $taxonomies = get_object_taxonomies($post->post_type);
    foreach ($taxonomies as $taxonomy) {
        $post_terms = wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'slugs']);
        wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
    }
    
    // Duplicate featured image
    if (has_post_thumbnail($post->ID)) {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        set_post_thumbnail($new_post_id, $thumbnail_id);
    }
    
    // Duplicate WooCommerce product data
    if ($post->post_type === 'product' && class_exists('WooCommerce')) {
        parisii_optique_duplicate_woocommerce_product($post->ID, $new_post_id);
    }
    
    return $new_post_id;
}

/**
 * Duplicate WooCommerce product specific data
 */
function parisii_optique_duplicate_woocommerce_product($original_id, $duplicate_id) {
    $product = wc_get_product($original_id);
    if (!$product) {
        return;
    }
    
    $duplicate = wc_get_product($duplicate_id);
    if (!$duplicate) {
        return;
    }
    
    // Copy product data
    $duplicate->set_name($product->get_name() . ' (Copie)');
    $duplicate->set_sku($product->get_sku() . '-copy');
    $duplicate->set_regular_price($product->get_regular_price());
    $duplicate->set_sale_price($product->get_sale_price());
    $duplicate->set_manage_stock($product->get_manage_stock());
    $duplicate->set_stock_quantity($product->get_stock_quantity());
    $duplicate->set_stock_status($product->get_stock_status());
    $duplicate->set_weight($product->get_weight());
    $duplicate->set_length($product->get_length());
    $duplicate->set_width($product->get_width());
    $duplicate->set_height($product->get_height());
    $duplicate->set_shipping_class_id($product->get_shipping_class_id());
    $duplicate->set_tax_class($product->get_tax_class());
    $duplicate->set_tax_status($product->get_tax_status());
    $duplicate->set_catalog_visibility($product->get_catalog_visibility());
    $duplicate->set_featured($product->get_featured());
    $duplicate->set_sold_individually($product->get_sold_individually());
    $duplicate->set_purchase_note($product->get_purchase_note());
    $duplicate->set_reviews_allowed($product->get_reviews_allowed());
    $duplicate->set_menu_order($product->get_menu_order());
    
    // Copy product attributes
    $attributes = $product->get_attributes();
    $duplicate_attributes = [];
    foreach ($attributes as $attribute) {
        $duplicate_attributes[] = $attribute;
    }
    $duplicate->set_attributes($duplicate_attributes);
    
    // Copy product categories and tags
    $product_categories = wp_get_post_terms($original_id, 'product_cat', ['fields' => 'ids']);
    $product_tags = wp_get_post_terms($original_id, 'product_tag', ['fields' => 'ids']);
    
    if (!is_wp_error($product_categories)) {
        wp_set_post_terms($duplicate_id, $product_categories, 'product_cat');
    }
    if (!is_wp_error($product_tags)) {
        wp_set_post_terms($duplicate_id, $product_tags, 'product_tag');
    }
    
    $duplicate->save();
}

/**
 * Add duplicate action to category row actions
 */
function parisii_optique_duplicate_category_link($actions, $term) {
    if (current_user_can('manage_categories')) {
        $duplicate_url = wp_nonce_url(
            add_query_arg([
                'action' => 'parisii_duplicate_category',
                'term_id' => $term->term_id,
                'taxonomy' => $term->taxonomy,
            ], admin_url('admin.php')),
            'parisii_duplicate_category_' . $term->term_id,
            'duplicate_nonce'
        );
        
        $actions['duplicate'] = sprintf(
            '<a href="%s" title="%s" rel="permalink">%s</a>',
            $duplicate_url,
            esc_attr__('Dupliquer cette catégorie', 'parisii-optique'),
            __('Dupliquer', 'parisii-optique')
        );
    }
    
    return $actions;
}

// Add to different taxonomies
add_filter('category_row_actions', 'parisii_optique_duplicate_category_link', 10, 2);
add_filter('post_tag_row_actions', 'parisii_optique_duplicate_category_link', 10, 2);
add_filter('product_cat_row_actions', 'parisii_optique_duplicate_category_link', 10, 2);
add_filter('product_tag_row_actions', 'parisii_optique_duplicate_category_link', 10, 2);

/**
 * Handle duplicate category action
 */
function parisii_optique_handle_duplicate_category() {
    if (!isset($_GET['action']) || $_GET['action'] !== 'parisii_duplicate_category') {
        return;
    }
    
    if (!isset($_GET['term_id']) || !isset($_GET['taxonomy']) || !isset($_GET['duplicate_nonce'])) {
        wp_die(__('Paramètres manquants', 'parisii-optique'));
    }
    
    $term_id = intval($_GET['term_id']);
    $taxonomy = sanitize_text_field($_GET['taxonomy']);
    $nonce = sanitize_text_field($_GET['duplicate_nonce']);
    
    if (!wp_verify_nonce($nonce, 'parisii_duplicate_category_' . $term_id)) {
        wp_die(__('Nonce invalide', 'parisii-optique'));
    }
    
    if (!current_user_can('manage_categories')) {
        wp_die(__('Permissions insuffisantes', 'parisii-optique'));
    }
    
    $original_term = get_term($term_id, $taxonomy);
    if (is_wp_error($original_term) || !$original_term) {
        wp_die(__('Catégorie introuvable', 'parisii-optique'));
    }
    
    // Create duplicate
    $duplicate_id = parisii_optique_duplicate_category($original_term);
    
    if ($duplicate_id) {
        wp_redirect(admin_url('edit-tags.php?taxonomy=' . $taxonomy . '&duplicated=1'));
        exit;
    } else {
        wp_die(__('Erreur lors de la duplication', 'parisii-optique'));
    }
}
add_action('admin_action_parisii_duplicate_category', 'parisii_optique_handle_duplicate_category');

/**
 * Duplicate category function
 */
function parisii_optique_duplicate_category($term) {
    $new_term = wp_insert_term(
        $term->name . ' (Copie)',
        $term->taxonomy,
        [
            'description' => $term->description,
            'slug' => $term->slug . '-copy',
            'parent' => $term->parent
        ]
    );
    
    if (is_wp_error($new_term)) {
        return false;
    }
    
    $new_term_id = $new_term['term_id'];
    
    // Duplicate term meta
    $term_meta = get_term_meta($term->term_id);
    foreach ($term_meta as $key => $values) {
        foreach ($values as $value) {
            add_term_meta($new_term_id, $key, maybe_unserialize($value));
        }
    }
    
    return $new_term_id;
}

/**
 * Add admin notices for successful duplication
 */
function parisii_optique_duplicate_admin_notices() {
    if (isset($_GET['duplicated']) && $_GET['duplicated'] == '1') {
        echo '<div class="notice notice-success is-dismissible"><p>' . 
             __('Élément dupliqué avec succès !', 'parisii-optique') . 
             '</p></div>';
    }
}
add_action('admin_notices', 'parisii_optique_duplicate_admin_notices');

/**
 * Add duplicate button to post edit screen
 */
function parisii_optique_add_duplicate_button() {
    global $post;
    
    if (!$post || !current_user_can('edit_posts')) {
        return;
    }
    
    $duplicate_url = wp_nonce_url(
        add_query_arg([
            'action' => 'parisii_duplicate_post',
            'post_id' => $post->ID,
        ], admin_url('admin.php')),
        'parisii_duplicate_post_' . $post->ID,
        'duplicate_nonce'
    );
    
    echo '<div id="duplicate-action" class="misc-pub-section">';
    echo '<a href="' . esc_url($duplicate_url) . '" class="button button-secondary">';
    echo '<span class="dashicons dashicons-admin-page" style="font-size: 16px; vertical-align: middle;"></span> ';
    echo __('Dupliquer', 'parisii-optique');
    echo '</a>';
    echo '</div>';
}

// Add to different post types
add_action('post_submitbox_misc_actions', 'parisii_optique_add_duplicate_button');

/**
 * Add duplicate button to category edit screen
 */
function parisii_optique_add_duplicate_category_button() {
    global $tag;
    
    if (!$tag || !current_user_can('manage_categories')) {
        return;
    }
    
    $duplicate_url = wp_nonce_url(
        add_query_arg([
            'action' => 'parisii_duplicate_category',
            'term_id' => $tag->term_id,
            'taxonomy' => $tag->taxonomy,
        ], admin_url('admin.php')),
        'parisii_duplicate_category_' . $tag->term_id,
        'duplicate_nonce'
    );
    
    echo '<div class="form-field">';
    echo '<a href="' . esc_url($duplicate_url) . '" class="button button-secondary">';
    echo '<span class="dashicons dashicons-admin-page" style="font-size: 16px; vertical-align: middle;"></span> ';
    echo __('Dupliquer cette catégorie', 'parisii-optique');
    echo '</a>';
    echo '</div>';
}

// Add to different taxonomies
add_action('category_edit_form', 'parisii_optique_add_duplicate_category_button');
add_action('post_tag_edit_form', 'parisii_optique_add_duplicate_category_button');
add_action('product_cat_edit_form', 'parisii_optique_add_duplicate_category_button');
add_action('product_tag_edit_form', 'parisii_optique_add_duplicate_category_button');
