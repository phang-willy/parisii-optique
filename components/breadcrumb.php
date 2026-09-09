<?php
/**
 * Breadcrumb component
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Render the site breadcrumb (Yoast if available, otherwise page hierarchy).
 *
 * @param array $args {
 *     Optional. Arguments to customize the breadcrumb.
 *
 *     @type string $class Nav class attribute.
 *     @type string $home_label Label for the home link. Default: Accueil.
 *     @type int    $post_id    Post ID. Default: current post.
 *     @type bool   $echo       Whether to echo the markup. Default: true.
 * }
 * @return string|void HTML markup when $echo is false.
 */
function parisii_optique_breadcrumb($args = []) {
    $args = wp_parse_args($args, [
        'class'      => 'breadcrumb text-sm text-gray-600 dark:text-gray-400 flex flex-wrap gap-1 items-center mb-6',
        'home_label' => __('Accueil', 'parisii-optique'),
        'post_id'    => get_the_ID(),
        'echo'       => true,
    ]);

    ob_start();
    ?>
    <div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
        <?php
        if (function_exists('yoast_breadcrumb')) {
            yoast_breadcrumb(
                '<nav class="' . esc_attr($args['class']) . '" aria-label="breadcrumb">',
                '</nav>'
            );
        } else {
            $post_id = absint($args['post_id']);
            $parents = [];
            $parent_id = $post_id ? wp_get_post_parent_id($post_id) : 0;

            while ($parent_id) {
                $parents[] = $parent_id;
                $parent_id = wp_get_post_parent_id($parent_id);
            }

            $parents = array_reverse($parents);
            ?>
            <nav class="<?php echo esc_attr($args['class']); ?>" aria-label="breadcrumb">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php echo esc_html($args['home_label']); ?>
                </a>

                <?php foreach ($parents as $parent) : ?>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-4 text-gray-400" aria-hidden="true">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                    <a href="<?php echo esc_url(get_permalink($parent)); ?>">
                        <?php echo esc_html(get_the_title($parent)); ?>
                    </a>
                <?php endforeach; ?>

                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right w-4 text-gray-400" aria-hidden="true">
                    <path d="m9 18 6-6-6-6"/>
                </svg>

                <span class="text-gray-900 dark:text-white">
                    <?php echo esc_html(get_the_title($post_id)); ?>
                </span>
            </nav>

            <h1 class="text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white"><?php the_title(); ?></h1>
            <?php
        }
        ?>
    </div>
    <?php
    $html = ob_get_clean();

    if ($args['echo']) {
        echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped above / Yoast
        return;
    }

    return $html;
}
