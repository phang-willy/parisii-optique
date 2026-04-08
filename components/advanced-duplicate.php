<?php
/**
 * Advanced Duplicate Content Component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Add duplicate page to admin menu
 */
function parisii_optique_add_duplicate_admin_menu() {
    add_management_page(
        __('Dupliquer le contenu', 'parisii-optique'),
        __('Dupliquer', 'parisii-optique'),
        'edit_posts',
        'parisii-duplicate',
        'parisii_optique_duplicate_admin_page'
    );
}
add_action('admin_menu', 'parisii_optique_add_duplicate_admin_menu');

/**
 * Duplicate admin page
 */
function parisii_optique_duplicate_admin_page() {
    if (isset($_POST['duplicate_action'])) {
        parisii_optique_handle_bulk_duplicate();
    }
    
    $post_types = get_post_types(['public' => true], 'objects');
    $taxonomies = get_taxonomies(['public' => true], 'objects');
    
    ?>
    <div class="wrap">
        <h1><?php _e('Dupliquer le contenu', 'parisii-optique'); ?></h1>
        
        <div class="card">
            <h2><?php _e('Duplication en lot', 'parisii-optique'); ?></h2>
            <p><?php _e('Sélectionnez le type de contenu et les éléments à dupliquer.', 'parisii-optique'); ?></p>
            
            <form method="post" action="">
                <?php wp_nonce_field('parisii_bulk_duplicate', 'bulk_duplicate_nonce'); ?>
                
                <table class="form-table">
                    <tr>
                        <th scope="row">
                            <label for="content_type"><?php _e('Type de contenu', 'parisii-optique'); ?></label>
                        </th>
                        <td>
                            <select name="content_type" id="content_type" required>
                                <option value=""><?php _e('Sélectionner un type', 'parisii-optique'); ?></option>
                                <?php foreach ($post_types as $post_type) : ?>
                                    <option value="post_type_<?php echo $post_type->name; ?>">
                                        <?php echo $post_type->label; ?>
                                    </option>
                                <?php endforeach; ?>
                                <?php foreach ($taxonomies as $taxonomy) : ?>
                                    <option value="taxonomy_<?php echo $taxonomy->name; ?>">
                                        <?php echo $taxonomy->label; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="items_to_duplicate"><?php _e('Éléments à dupliquer', 'parisii-optique'); ?></label>
                        </th>
                        <td>
                            <div id="items_list" style="max-height: 300px; overflow-y: auto; border: 1px solid #ddd; padding: 10px;">
                                <p><?php _e('Sélectionnez d\'abord un type de contenu', 'parisii-optique'); ?></p>
                            </div>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="duplicate_options"><?php _e('Options de duplication', 'parisii-optique'); ?></label>
                        </th>
                        <td>
                            <fieldset>
                                <label>
                                    <input type="checkbox" name="duplicate_meta" value="1" checked>
                                    <?php _e('Dupliquer les métadonnées', 'parisii-optique'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="duplicate_taxonomies" value="1" checked>
                                    <?php _e('Dupliquer les taxonomies', 'parisii-optique'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="duplicate_media" value="1">
                                    <?php _e('Dupliquer les médias', 'parisii-optique'); ?>
                                </label><br>
                                <label>
                                    <input type="checkbox" name="duplicate_comments" value="1">
                                    <?php _e('Dupliquer les commentaires', 'parisii-optique'); ?>
                                </label>
                            </fieldset>
                        </td>
                    </tr>
                    
                    <tr>
                        <th scope="row">
                            <label for="new_status"><?php _e('Statut des nouveaux éléments', 'parisii-optique'); ?></label>
                        </th>
                        <td>
                            <select name="new_status" id="new_status">
                                <option value="draft"><?php _e('Brouillon', 'parisii-optique'); ?></option>
                                <option value="publish"><?php _e('Publié', 'parisii-optique'); ?></option>
                                <option value="private"><?php _e('Privé', 'parisii-optique'); ?></option>
                            </select>
                        </td>
                    </tr>
                </table>
                
                <?php submit_button(__('Dupliquer les éléments sélectionnés', 'parisii-optique'), 'primary', 'duplicate_action'); ?>
            </form>
        </div>
        
        <div class="card">
            <h2><?php _e('Duplication rapide', 'parisii-optique'); ?></h2>
            <p><?php _e('Dupliquez rapidement des éléments individuels depuis leurs listes respectives.', 'parisii-optique'); ?></p>
            
            <div class="duplicate-links">
                <a href="<?php echo admin_url('edit.php'); ?>" class="button">
                    <?php _e('Pages et Articles', 'parisii-optique'); ?>
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=product'); ?>" class="button">
                    <?php _e('Produits', 'parisii-optique'); ?>
                </a>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=category'); ?>" class="button">
                    <?php _e('Catégories', 'parisii-optique'); ?>
                </a>
                <a href="<?php echo admin_url('edit-tags.php?taxonomy=product_cat'); ?>" class="button">
                    <?php _e('Catégories de produits', 'parisii-optique'); ?>
                </a>
            </div>
        </div>
    </div>
    
    <script>
    jQuery(document).ready(function($) {
        $('#content_type').change(function() {
            var contentType = $(this).val();
            if (!contentType) {
                $('#items_list').html('<p><?php echo esc_js(__('Sélectionnez d\'abord un type de contenu', 'parisii-optique')); ?></p>');
                return;
            }
            
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                data: {
                    action: 'parisii_get_content_items',
                    content_type: contentType,
                    nonce: '<?php echo wp_create_nonce('parisii_get_items'); ?>'
                },
                success: function(response) {
                    $('#items_list').html(response);
                }
            });
        });
    });
    </script>
    <?php
}

/**
 * AJAX handler to get content items
 */
function parisii_optique_get_content_items() {
    check_ajax_referer('parisii_get_items', 'nonce');

    if (!current_user_can('edit_posts')) {
        wp_die(esc_html__('Permissions insuffisantes', 'parisii-optique'));
    }

    $content_type = isset($_POST['content_type']) ? sanitize_text_field(wp_unslash($_POST['content_type'])) : '';
    $items_html = '';
    
    if (strpos($content_type, 'post_type_') === 0) {
        $post_type = str_replace('post_type_', '', $content_type);
        $posts = get_posts([
            'post_type' => $post_type,
            'numberposts' => -1,
            'post_status' => 'any'
        ]);
        
        foreach ($posts as $post) {
            $items_html .= sprintf(
                '<label><input type="checkbox" name="items[]" value="%d"> %s (%s)</label><br>',
                $post->ID,
                esc_html($post->post_title),
                esc_html($post->post_status)
            );
        }
    } elseif (strpos($content_type, 'taxonomy_') === 0) {
        $taxonomy = str_replace('taxonomy_', '', $content_type);
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false
        ]);
        
        if (!is_wp_error($terms)) {
            foreach ($terms as $term) {
                $items_html .= sprintf(
                    '<label><input type="checkbox" name="items[]" value="%d"> %s</label><br>',
                    $term->term_id,
                    esc_html($term->name)
                );
            }
        }
    }
    
    if (empty($items_html)) {
        $items_html = '<p>' . esc_html__('Aucun élément trouvé', 'parisii-optique') . '</p>';
    }
    
    wp_die($items_html);
}
add_action('wp_ajax_parisii_get_content_items', 'parisii_optique_get_content_items');

/**
 * Handle bulk duplicate action
 */
function parisii_optique_handle_bulk_duplicate() {
    $nonce = isset($_POST['bulk_duplicate_nonce']) ? sanitize_text_field(wp_unslash($_POST['bulk_duplicate_nonce'])) : '';
    if (!wp_verify_nonce($nonce, 'parisii_bulk_duplicate')) {
        wp_die(__('Nonce invalide', 'parisii-optique'));
    }
    
    if (!current_user_can('edit_posts')) {
        wp_die(__('Permissions insuffisantes', 'parisii-optique'));
    }
    
    $content_type = isset($_POST['content_type']) ? sanitize_text_field(wp_unslash($_POST['content_type'])) : '';
    $items = isset($_POST['items']) && is_array($_POST['items']) ? array_map('intval', wp_unslash($_POST['items'])) : [];
    $duplicate_meta = isset($_POST['duplicate_meta']);
    $duplicate_taxonomies = isset($_POST['duplicate_taxonomies']);
    $duplicate_media = isset($_POST['duplicate_media']);
    $duplicate_comments = isset($_POST['duplicate_comments']);
    $new_status = isset($_POST['new_status']) ? sanitize_text_field(wp_unslash($_POST['new_status'])) : 'draft';
    
    $duplicated_count = 0;
    $errors = [];
    
    foreach ($items as $item_id) {
        if (strpos($content_type, 'post_type_') === 0) {
            $post = get_post($item_id);
            if ($post) {
                $duplicate_id = parisii_optique_duplicate_post_advanced($post, [
                    'status' => $new_status,
                    'duplicate_meta' => $duplicate_meta,
                    'duplicate_taxonomies' => $duplicate_taxonomies,
                    'duplicate_media' => $duplicate_media,
                    'duplicate_comments' => $duplicate_comments
                ]);
                
                if ($duplicate_id) {
                    $duplicated_count++;
                } else {
                    $errors[] = sprintf(__('Erreur lors de la duplication de "%s"', 'parisii-optique'), $post->post_title);
                }
            }
        } elseif (strpos($content_type, 'taxonomy_') === 0) {
            $taxonomy = str_replace('taxonomy_', '', $content_type);
            $term = get_term($item_id, $taxonomy);
            if ($term && !is_wp_error($term)) {
                $duplicate_id = parisii_optique_duplicate_category_advanced($term, [
                    'duplicate_meta' => $duplicate_meta
                ]);
                
                if ($duplicate_id) {
                    $duplicated_count++;
                } else {
                    $errors[] = sprintf(__('Erreur lors de la duplication de "%s"', 'parisii-optique'), $term->name);
                }
            }
        }
    }
    
    $message_lines = [sprintf(__('%d éléments dupliqués avec succès', 'parisii-optique'), $duplicated_count)];
    if (!empty($errors)) {
        foreach ($errors as $error) {
            $message_lines[] = $error;
        }
    }
    
    add_action('admin_notices', function() use ($message_lines) {
        echo '<div class="notice notice-success is-dismissible"><p>' . wp_kses_post(implode('<br>', array_map('esc_html', $message_lines))) . '</p></div>';
    });
}

/**
 * Advanced duplicate post function
 */
function parisii_optique_duplicate_post_advanced($post, $options = []) {
    $defaults = [
        'status' => 'draft',
        'duplicate_meta' => true,
        'duplicate_taxonomies' => true,
        'duplicate_media' => false,
        'duplicate_comments' => false
    ];
    
    $options = wp_parse_args($options, $defaults);
    
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
        'post_status' => $options['status'],
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
    if ($options['duplicate_meta']) {
        $post_meta = get_post_meta($post->ID);
        foreach ($post_meta as $key => $values) {
            foreach ($values as $value) {
                add_post_meta($new_post_id, $key, maybe_unserialize($value));
            }
        }
    }
    
    // Duplicate taxonomies
    if ($options['duplicate_taxonomies']) {
        $taxonomies = get_object_taxonomies($post->post_type);
        foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post->ID, $taxonomy, ['fields' => 'slugs']);
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
        }
    }
    
    // Duplicate featured image
    if (has_post_thumbnail($post->ID)) {
        $thumbnail_id = get_post_thumbnail_id($post->ID);
        if ($options['duplicate_media']) {
            // Duplicate the media file
            $new_thumbnail_id = parisii_optique_duplicate_media($thumbnail_id);
            if ($new_thumbnail_id) {
                set_post_thumbnail($new_post_id, $new_thumbnail_id);
            }
        } else {
            set_post_thumbnail($new_post_id, $thumbnail_id);
        }
    }
    
    // Duplicate comments
    if ($options['duplicate_comments']) {
        $comments = get_comments(['post_id' => $post->ID]);
        foreach ($comments as $comment) {
            $new_comment = [
                'comment_post_ID' => $new_post_id,
                'comment_author' => $comment->comment_author,
                'comment_author_email' => $comment->comment_author_email,
                'comment_author_url' => $comment->comment_author_url,
                'comment_content' => $comment->comment_content,
                'comment_type' => $comment->comment_type,
                'comment_parent' => $comment->comment_parent,
                'user_id' => $comment->user_id,
                'comment_approved' => $comment->comment_approved,
                'comment_meta' => get_comment_meta($comment->comment_ID)
            ];
            wp_insert_comment($new_comment);
        }
    }
    
    // Duplicate WooCommerce product data
    if ($post->post_type === 'product' && class_exists('WooCommerce')) {
        parisii_optique_duplicate_woocommerce_product($post->ID, $new_post_id);
    }
    
    return $new_post_id;
}

/**
 * Advanced duplicate category function
 */
function parisii_optique_duplicate_category_advanced($term, $options = []) {
    $defaults = [
        'duplicate_meta' => true
    ];
    
    $options = wp_parse_args($options, $defaults);
    
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
    if ($options['duplicate_meta']) {
        $term_meta = get_term_meta($term->term_id);
        foreach ($term_meta as $key => $values) {
            foreach ($values as $value) {
                add_term_meta($new_term_id, $key, maybe_unserialize($value));
            }
        }
    }
    
    return $new_term_id;
}

/**
 * Duplicate media file
 */
function parisii_optique_duplicate_media($media_id) {
    $media = get_post($media_id);
    if (!$media || $media->post_type !== 'attachment') {
        return false;
    }
    
    $file_path = get_attached_file($media_id);
    if (!file_exists($file_path)) {
        return false;
    }
    
    $upload_dir = wp_upload_dir();
    $file_info = pathinfo($file_path);
    $new_filename = $file_info['filename'] . '-copy.' . $file_info['extension'];
    $new_file_path = $upload_dir['path'] . '/' . $new_filename;
    
    if (copy($file_path, $new_file_path)) {
        $new_media = [
            'post_title' => $media->post_title . ' (Copie)',
            'post_content' => $media->post_content,
            'post_excerpt' => $media->post_excerpt,
            'post_status' => 'inherit',
            'post_mime_type' => $media->post_mime_type,
            'post_author' => get_current_user_id()
        ];
        
        $new_media_id = wp_insert_attachment($new_media, $new_file_path);
        
        if (!is_wp_error($new_media_id)) {
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            $metadata = wp_generate_attachment_metadata($new_media_id, $new_file_path);
            wp_update_attachment_metadata($new_media_id, $metadata);
            
            return $new_media_id;
        }
    }
    
    return false;
}
