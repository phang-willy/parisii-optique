<?php
/**
 * Duplicate Content Shortcode Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Duplicate content shortcode
 */
function parisii_optique_duplicate_shortcode($atts) {
    // N'afficher que dans l'admin ou pour les administrateurs
    if (!is_admin() && !current_user_can('manage_options')) {
        return '';
    }
    
    $atts = shortcode_atts([
        'id' => '',
        'type' => 'post', // post, page, product, category, tag
        'text' => 'Dupliquer',
        'class' => 'btn btn-outline',
        'redirect' => 'edit', // edit, list, current
        'confirm' => 'true',
    ], $atts);

    if (empty($atts['id'])) {
        return '<p>' . __('ID manquant', 'parisii-optique') . '</p>';
    }

    if (!current_user_can('edit_posts')) {
        return '';
    }

    $item_id = intval($atts['id']);
    $duplicate_url = '';
    $nonce_action = '';
    $redirect_url = '';

    // Determine duplicate URL and nonce based on type
    switch ($atts['type']) {
        case 'post':
        case 'page':
        case 'product':
            $duplicate_url = wp_nonce_url(
                add_query_arg([
                    'action' => 'parisii_duplicate_post',
                    'post_id' => $item_id,
                ], admin_url('admin.php')),
                'parisii_duplicate_post_' . $item_id,
                'duplicate_nonce'
            );
            $nonce_action = 'parisii_duplicate_post_' . $item_id;
            break;

        case 'category':
        case 'tag':
        case 'product_cat':
        case 'product_tag':
            $taxonomy = $atts['type'];
            $duplicate_url = wp_nonce_url(
                add_query_arg([
                    'action' => 'parisii_duplicate_category',
                    'term_id' => $item_id,
                    'taxonomy' => $taxonomy,
                ], admin_url('admin.php')),
                'parisii_duplicate_category_' . $item_id,
                'duplicate_nonce'
            );
            $nonce_action = 'parisii_duplicate_category_' . $item_id;
            break;

        default:
            return '<p>' . __('Type non supporté', 'parisii-optique') . '</p>';
    }

    // Determine redirect URL
    switch ($atts['redirect']) {
        case 'edit':
            if (in_array($atts['type'], ['post', 'page', 'product'])) {
                $redirect_url = admin_url('post.php?action=edit&post=' . $item_id);
            } else {
                $redirect_url = admin_url('edit-tags.php?taxonomy=' . $atts['type'] . '&tag_ID=' . $item_id);
            }
            break;
        case 'list':
            if (in_array($atts['type'], ['post', 'page', 'product'])) {
                $post_type = $atts['type'] === 'product' ? 'product' : 'post';
                $redirect_url = admin_url('edit.php?post_type=' . $post_type);
            } else {
                $redirect_url = admin_url('edit-tags.php?taxonomy=' . $atts['type']);
            }
            break;
        case 'current':
        default:
            $redirect_url = get_permalink();
            break;
    }

    $confirm_text = $atts['confirm'] === 'true' ? 
        __('Êtes-vous sûr de vouloir dupliquer cet élément ?', 'parisii-optique') : '';

    ob_start();
    ?>
    <a href="<?php echo esc_url($duplicate_url); ?>" 
       class="duplicate-link <?php echo esc_attr($atts['class']); ?>"
       data-confirm="<?php echo esc_attr($confirm_text); ?>"
       data-redirect="<?php echo esc_url($redirect_url); ?>"
       data-nonce="<?php echo wp_create_nonce($nonce_action); ?>">
        <span class="dashicons dashicons-admin-page" style="font-size: 16px; vertical-align: middle;"></span>
        <?php echo esc_html($atts['text']); ?>
    </a>
    <?php
    return ob_get_clean();
}
add_shortcode('duplicate', 'parisii_optique_duplicate_shortcode');

/**
 * Duplicate multiple items shortcode
 */
function parisii_optique_duplicate_multiple_shortcode($atts) {
    $atts = shortcode_atts([
        'ids' => '',
        'type' => 'post',
        'text' => 'Dupliquer tout',
        'class' => 'btn btn-primary',
        'redirect' => 'list',
    ], $atts);

    if (empty($atts['ids'])) {
        return '<p>' . __('IDs manquants', 'parisii-optique') . '</p>';
    }

    if (!current_user_can('edit_posts')) {
        return '';
    }

    $ids = array_map('intval', explode(',', $atts['ids']));
    $duplicate_urls = [];

    foreach ($ids as $id) {
        switch ($atts['type']) {
            case 'post':
            case 'page':
            case 'product':
                $duplicate_urls[] = wp_nonce_url(
                    add_query_arg([
                        'action' => 'parisii_duplicate_post',
                        'post_id' => $id,
                    ], admin_url('admin.php')),
                    'parisii_duplicate_post_' . $id,
                    'duplicate_nonce'
                );
                break;

            case 'category':
            case 'tag':
            case 'product_cat':
            case 'product_tag':
                $taxonomy = $atts['type'];
                $duplicate_urls[] = wp_nonce_url(
                    add_query_arg([
                        'action' => 'parisii_duplicate_category',
                        'term_id' => $id,
                        'taxonomy' => $taxonomy,
                    ], admin_url('admin.php')),
                    'parisii_duplicate_category_' . $id,
                    'duplicate_nonce'
                );
                break;
        }
    }

    $redirect_url = '';
    switch ($atts['redirect']) {
        case 'edit':
            $redirect_url = get_permalink();
            break;
        case 'list':
            if (in_array($atts['type'], ['post', 'page', 'product'])) {
                $post_type = $atts['type'] === 'product' ? 'product' : 'post';
                $redirect_url = admin_url('edit.php?post_type=' . $post_type);
            } else {
                $redirect_url = admin_url('edit-tags.php?taxonomy=' . $atts['type']);
            }
            break;
        case 'current':
        default:
            $redirect_url = get_permalink();
            break;
    }

    ob_start();
    ?>
    <div class="duplicate-multiple">
        <button class="duplicate-multiple-btn <?php echo esc_attr($atts['class']); ?>"
                data-urls="<?php echo esc_attr(json_encode($duplicate_urls)); ?>"
                data-redirect="<?php echo esc_url($redirect_url); ?>">
            <span class="dashicons dashicons-admin-page" style="font-size: 16px; vertical-align: middle;"></span>
            <?php echo esc_html($atts['text']); ?>
        </button>
        <div class="duplicate-progress" style="display: none;">
            <div class="progress-bar">
                <div class="progress-fill" style="width: 0%;"></div>
            </div>
            <span class="progress-text">0%</span>
        </div>
    </div>
    <?php
    return ob_get_clean();
}
add_shortcode('duplicate_multiple', 'parisii_optique_duplicate_multiple_shortcode');

/**
 * Enqueue duplicate scripts
 */
function parisii_optique_duplicate_scripts() {
    $post = get_post();
    if ($post && (has_shortcode($post->post_content, 'duplicate') || 
        has_shortcode($post->post_content, 'duplicate_multiple'))) {
        wp_enqueue_script('parisii-duplicate', get_template_directory_uri() . '/js/duplicate.js', ['jquery'], '1.0.0', true);
        wp_enqueue_style('parisii-duplicate', get_template_directory_uri() . '/css/duplicate.css', [], '1.0.0');
    }
}
add_action('wp_enqueue_scripts', 'parisii_optique_duplicate_scripts');

/**
 * Add duplicate button to post content (for logged-in users)
 */
function parisii_optique_add_duplicate_to_content($content) {
    if (!is_singular() || !current_user_can('edit_posts')) {
        return $content;
    }

    global $post;
    $post_type = $post->post_type;
    
    // Only add to certain post types
    if (!in_array($post_type, ['post', 'page', 'product'])) {
        return $content;
    }

    $duplicate_button = do_shortcode('[duplicate id="' . $post->ID . '" type="' . $post_type . '" text="Dupliquer cette page" class="btn btn-outline mt-4"]');
    
    return $content . $duplicate_button;
}
// Désactivé - ne pas afficher les boutons de duplication côté front
// add_filter('the_content', 'parisii_optique_add_duplicate_to_content');

/**
 * Add duplicate button to category pages
 */
function parisii_optique_add_duplicate_to_category() {
    if (!is_category() || !current_user_can('manage_categories')) {
        return;
    }

    $category = get_queried_object();
    if (!$category) {
        return;
    }

    $duplicate_button = do_shortcode('[duplicate id="' . $category->term_id . '" type="category" text="Dupliquer cette catégorie" class="btn btn-outline mt-4"]');
    
    echo '<div class="category-duplicate">' . $duplicate_button . '</div>';
}
// Désactivé - ne pas afficher les boutons de duplication côté front
// add_action('woocommerce_archive_description', 'parisii_optique_add_duplicate_to_category');
// add_action('woocommerce_after_shop_loop', 'parisii_optique_add_duplicate_to_category');

/**
 * Add duplicate button to product pages
 */
function parisii_optique_add_duplicate_to_product() {
    if (!is_product() || !current_user_can('edit_products')) {
        return;
    }

    global $product;
    if (!$product) {
        return;
    }

    $duplicate_button = do_shortcode('[duplicate id="' . $product->get_id() . '" type="product" text="Dupliquer ce produit" class="btn btn-outline mt-4"]');
    
    echo '<div class="product-duplicate">' . $duplicate_button . '</div>';
}
// Désactivé - ne pas afficher les boutons de duplication côté front
// add_action('woocommerce_single_product_summary', 'parisii_optique_add_duplicate_to_product', 25);
