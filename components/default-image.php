<?php
/**
 * Default / placeholder image component (editable)
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Post meta key for a default-image slot.
 *
 * @param string $id Slot id.
 * @return string
 */
function parisii_optique_default_image_meta_key($id) {
    return '_parisii_default_image_' . sanitize_key($id);
}

/**
 * Get saved attachment ID for a slot.
 *
 * @param string $id      Slot id.
 * @param int    $post_id Post ID (0 = current queried object).
 * @return int
 */
function parisii_optique_get_default_image_id($id, $post_id = 0) {
    $id = sanitize_key($id);
    if (!$id) {
        return 0;
    }

    if (!$post_id) {
        $post_id = (int) get_queried_object_id();
    }

    if ($post_id > 0) {
        return absint(get_post_meta($post_id, parisii_optique_default_image_meta_key($id), true));
    }

    return absint(get_theme_mod('parisii_default_image_' . $id, 0));
}

/**
 * Whether the current user can replace default images on this view.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function parisii_optique_can_edit_default_image($post_id = 0) {
    if (!$post_id) {
        $post_id = (int) get_queried_object_id();
    }

    return $post_id > 0 && is_user_logged_in() && current_user_can('edit_post', $post_id);
}

/**
 * Build WooCommerce placeholder src / srcset.
 *
 * @return array{src:string,srcset:string,width:int,height:int}
 */
function parisii_optique_default_image_placeholder_attrs() {
    if (!function_exists('parisii_get_placeholder_image')) {
        return [
            'src'    => '',
            'srcset' => '',
            'width'  => 0,
            'height' => 0,
        ];
    }

    return parisii_get_placeholder_image();
}

/**
 * Render the default image (placeholder or uploaded attachment).
 *
 * @param array $args {
 *     Optional. Arguments to customize the image.
 *
 *     @type string $id           Unique slot id (required to persist a replacement).
 *     @type string $alt          Image alt text. Default: site name / attachment alt.
 *     @type string $class        Img class attribute. Default: 'wp-image-40 mx-auto'.
 *     @type string $figure_class Figure class attribute. Default: 'wp-block-image size-large aligncenter'.
 *     @type string $size         Attachment size when a custom image is set. Default: 'large'.
 *     @type int    $width        Img width attribute. Default: 1024.
 *     @type int    $height       Img height attribute. Default: 1024.
 *     @type string $sizes        Sizes attribute for the placeholder. Default: '(max-width: 1024px) 100vw, 1024px'.
 *     @type int    $post_id      Post that stores the image. Default: current page.
 *     @type bool   $editable     Allow front-end replace for editors. Default: true.
 *     @type bool   $echo         Whether to echo the markup. Default: true.
 * }
 * @return string|void HTML markup when $echo is false.
 */
function parisii_optique_default_image($args = []) {
    $args = wp_parse_args($args, [
        'id'           => '',
        'alt'          => '',
        'class'        => 'wp-image-40 mx-auto',
        'figure_class' => 'wp-block-image size-large aligncenter',
        'size'         => 'large',
        'width'        => 1024,
        'height'       => 1024,
        'sizes'        => '(max-width: 1024px) 100vw, 1024px',
        'loading'      => 'lazy',
        'fetchpriority'=> '',
        'post_id'      => (int) get_queried_object_id(),
        'editable'     => true,
        'echo'         => true,
    ]);

    $slot_id      = sanitize_key($args['id']);
    $post_id      = absint($args['post_id']);
    $attachment_id = $slot_id ? parisii_optique_get_default_image_id($slot_id, $post_id) : 0;
    $editable     = !empty($args['editable']) && $slot_id && parisii_optique_can_edit_default_image($post_id);
    $alt          = $args['alt'] !== '' ? $args['alt'] : get_bloginfo('name');
    $width        = absint($args['width']) ?: 1024;
    $height       = absint($args['height']) ?: 1024;
    $loading      = in_array($args['loading'], ['lazy', 'eager'], true) ? $args['loading'] : 'lazy';
    $fetchpriority = in_array($args['fetchpriority'], ['high', 'low', 'auto'], true) ? $args['fetchpriority'] : '';

    $figure_classes = trim($args['figure_class'] . ' parisii-default-image' . ($editable ? ' is-editable' : ''));

    ob_start();
    ?>
    <figure
        class="<?php echo esc_attr($figure_classes); ?>"
        <?php if ($slot_id) : ?>
            data-image-slot="<?php echo esc_attr($slot_id); ?>"
            data-post-id="<?php echo esc_attr((string) $post_id); ?>"
            data-attachment-id="<?php echo esc_attr((string) $attachment_id); ?>"
            data-width="<?php echo esc_attr((string) $width); ?>"
            data-height="<?php echo esc_attr((string) $height); ?>"
            data-size="<?php echo esc_attr($args['size']); ?>"
            data-class="<?php echo esc_attr($args['class']); ?>"
            data-figure-class="<?php echo esc_attr($args['figure_class']); ?>"
            data-alt="<?php echo esc_attr($alt); ?>"
        <?php endif; ?>
    >
        <?php if ($attachment_id && wp_attachment_is_image($attachment_id)) : ?>
            <?php
            $image_attrs = [
                'class'    => $args['class'],
                'alt'      => $alt !== '' ? $alt : (string) get_post_meta($attachment_id, '_wp_attachment_image_alt', true),
                'decoding' => 'async',
                'loading'  => $loading,
            ];
            if ((int) $args['width'] !== 1024 || (int) $args['height'] !== 1024) {
                $image_attrs['width'] = $width;
                $image_attrs['height'] = $height;
            }
            if ($fetchpriority !== '') {
                $image_attrs['fetchpriority'] = $fetchpriority;
            }
            echo wp_get_attachment_image(
                $attachment_id,
                $args['size'],
                false,
                $image_attrs
            );
            ?>
        <?php else : ?>
            <?php
            $placeholder = parisii_optique_default_image_placeholder_attrs();
            $placeholder_width = ((int) $args['width'] === 1024 && (int) $args['height'] === 1024 && !empty($placeholder['width']))
                ? (int) $placeholder['width']
                : $width;
            $placeholder_height = ((int) $args['width'] === 1024 && (int) $args['height'] === 1024 && !empty($placeholder['height']))
                ? (int) $placeholder['height']
                : $height;
            ?>
            <?php if (!empty($placeholder['src'])) : ?>
            <img
                decoding="async"
                loading="<?php echo esc_attr($loading); ?>"
                <?php if ($fetchpriority !== '') : ?>
                    fetchpriority="<?php echo esc_attr($fetchpriority); ?>"
                <?php endif; ?>
                <?php if ($placeholder_width > 0 && $placeholder_height > 0) : ?>
                    width="<?php echo esc_attr((string) $placeholder_width); ?>"
                    height="<?php echo esc_attr((string) $placeholder_height); ?>"
                <?php endif; ?>
                src="<?php echo esc_url($placeholder['src']); ?>"
                alt="<?php echo esc_attr($alt); ?>"
                class="<?php echo esc_attr($args['class']); ?>"
                <?php if (!empty($placeholder['srcset'])) : ?>
                    srcset="<?php echo esc_attr($placeholder['srcset']); ?>"
                    sizes="<?php echo esc_attr($args['sizes']); ?>"
                <?php endif; ?>
            >
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($editable) : ?>
            <div class="parisii-default-image__toolbar">
                <button type="button" class="parisii-default-image__btn parisii-default-image__replace">
                    <?php echo esc_html($attachment_id ? __('Remplacer', 'parisii-optique') : __('Choisir une image', 'parisii-optique')); ?>
                </button>
                <?php if ($attachment_id) : ?>
                    <button type="button" class="parisii-default-image__btn parisii-default-image__reset">
                        <?php esc_html_e('Réinitialiser', 'parisii-optique'); ?>
                    </button>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </figure>
    <?php
    $html = ob_get_clean();

    if ($args['echo']) {
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above
        return;
    }

    return $html;
}

/**
 * Enqueue media library assets for front-end image editing.
 */
function parisii_optique_default_image_assets() {
    if (!is_singular() || !parisii_optique_can_edit_default_image()) {
        return;
    }

    wp_enqueue_media();

    $script_path = get_template_directory() . '/js/default-image.js';
    $version     = file_exists($script_path) ? (string) filemtime($script_path) : wp_get_theme()->get('Version');

    wp_enqueue_script(
        'parisii-default-image',
        get_template_directory_uri() . '/js/default-image.js',
        ['jquery'],
        $version,
        true
    );

    wp_localize_script('parisii-default-image', 'parisiiDefaultImage', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('parisii_default_image'),
        'i18n'    => [
            'title'  => __('Choisir une image', 'parisii-optique'),
            'button' => __('Utiliser cette image', 'parisii-optique'),
            'error'  => __('Impossible d’enregistrer l’image.', 'parisii-optique'),
        ],
    ]);
}
add_action('wp_enqueue_scripts', 'parisii_optique_default_image_assets');

/**
 * AJAX: save or reset a default-image slot.
 */
function parisii_optique_ajax_save_default_image() {
    check_ajax_referer('parisii_default_image', 'nonce');

    $post_id       = isset($_POST['post_id']) ? absint($_POST['post_id']) : 0;
    $slot_id       = isset($_POST['slot']) ? sanitize_key(wp_unslash($_POST['slot'])) : '';
    $attachment_id = isset($_POST['attachment_id']) ? absint($_POST['attachment_id']) : 0;

    if (!$post_id || !$slot_id || !current_user_can('edit_post', $post_id)) {
        wp_send_json_error(['message' => __('Permission refusée.', 'parisii-optique')], 403);
    }

    $meta_key = parisii_optique_default_image_meta_key($slot_id);

    if ($attachment_id > 0) {
        if (!wp_attachment_is_image($attachment_id)) {
            wp_send_json_error(['message' => __('Fichier invalide.', 'parisii-optique')], 400);
        }
        update_post_meta($post_id, $meta_key, $attachment_id);
    } else {
        delete_post_meta($post_id, $meta_key);
    }

    $html = parisii_optique_default_image([
        'id'           => $slot_id,
        'post_id'      => $post_id,
        'width'        => isset($_POST['width']) ? absint($_POST['width']) : 1024,
        'height'       => isset($_POST['height']) ? absint($_POST['height']) : 1024,
        'size'         => isset($_POST['size']) ? sanitize_key(wp_unslash($_POST['size'])) : 'large',
        'class'        => isset($_POST['class']) ? sanitize_text_field(wp_unslash($_POST['class'])) : 'wp-image-40 mx-auto',
        'figure_class' => isset($_POST['figure_class']) ? sanitize_text_field(wp_unslash($_POST['figure_class'])) : 'wp-block-image size-large aligncenter',
        'alt'          => isset($_POST['alt']) ? sanitize_text_field(wp_unslash($_POST['alt'])) : '',
        'editable'     => true,
        'echo'         => false,
    ]);

    wp_send_json_success([
        'attachment_id' => $attachment_id,
        'html'          => $html,
    ]);
}
add_action('wp_ajax_parisii_save_default_image', 'parisii_optique_ajax_save_default_image');
