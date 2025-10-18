<?php
/**
 * The Template for displaying all single products
 *
 * @package Parisii_Optique
 */

if (!defined('ABSPATH')) {
    exit;
}

get_header('shop'); ?>

<section class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
    <?php
    /**
     * Breadcrumb
     */
    if (function_exists('woocommerce_breadcrumb')) {
        woocommerce_breadcrumb(array(
            'delimiter'   => '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right w-4 text-gray-400"><path d="m9 18 6-6-6-6"/></svg>',
            'wrap_before' => '<nav class="woocommerce-breadcrumb" aria-label="breadcrumb">',
            'wrap_after'  => '</nav>',
            'before'      => '',
            'after'       => '',
            'home'        => __('Accueil', 'parisii-optique'),
        ));
    }

    /**
     * Hook: woocommerce_before_main_content.
     *
     * @hooked woocommerce_output_content_wrapper - 10 (outputs opening divs for the content)
     * @hooked woocommerce_breadcrumb - 20
     */
    do_action('woocommerce_before_main_content');

    while (have_posts()) {
        the_post();

        wc_get_template_part('content', 'single-product');
    }

    /**
     * Hook: woocommerce_after_main_content.
     *
     * @hooked woocommerce_output_content_wrapper_end - 10 (outputs closing divs for the content)
     */
    do_action('woocommerce_after_main_content');
    ?>
</section>

<?php
/**
 * Hook: woocommerce_sidebar.
 *
 * @hooked woocommerce_get_sidebar - 10
 */
do_action('woocommerce_sidebar');

get_footer('shop');
