<?php
/**
 * The Template for displaying product archives, including the main shop page
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
    ?>

    <header class="woocommerce-products-header mb-12">
        <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
            <h1 class="woocommerce-products-header__title page-title text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white mb-4">
                <?php woocommerce_page_title(); ?>
            </h1>
        <?php endif; ?>

        <?php
        /**
         * Hook: woocommerce_archive_description.
         *
         * @hooked woocommerce_taxonomy_archive_description - 10
         * @hooked woocommerce_product_archive_description - 10
         */
        do_action('woocommerce_archive_description');
        ?>
    </header>

    <?php
    if (woocommerce_product_loop()) {
        /**
         * Hook: woocommerce_before_shop_loop.
         *
         * @hooked woocommerce_output_all_notices - 10
         * @hooked woocommerce_result_count - 20
         * @hooked woocommerce_catalog_ordering - 30
         */
        do_action('woocommerce_before_shop_loop');

        woocommerce_product_loop_start();

        if (wc_get_loop_prop('is_shortcode')) {
            $columns = absint(wc_get_loop_prop('columns'));
            $GLOBALS['woocommerce_loop']['columns'] = $columns;
        }

        while (have_posts()) {
            the_post();

            /**
             * Hook: woocommerce_shop_loop.
             */
            do_action('woocommerce_shop_loop');

            wc_get_template_part('content', 'product');
        }

        woocommerce_product_loop_end();

        /**
         * Hook: woocommerce_after_shop_loop.
         *
         * @hooked woocommerce_pagination - 10
         */
        do_action('woocommerce_after_shop_loop');
    } else {
        /**
         * Hook: woocommerce_no_products_found.
         *
         * @hooked wc_no_products_found - 10
         */
        do_action('woocommerce_no_products_found');
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

get_footer('shop');
