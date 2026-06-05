<?php
/**
 * Template Name: Page des marques
 * Description: Template pour afficher les marques avec filtres
 *
 * @package Parisii_Optique_Plugin
 */

get_header();
?>

    <div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
        <header class="grid grid-cols-1 gap-4">
        <?php
        if (function_exists('yoast_breadcrumb')) {
            yoast_breadcrumb('<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 mb-6" aria-label="breadcrumb">', '</nav>');
        } else {
            echo '<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 flex flex-wrap gap-1" aria-label="breadcrumb">';
            echo '<a href="' . esc_url(home_url('/')) . '">' . __('Accueil', 'parisii-optique-plugin') . '</a>';
            ?>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-chevron-right-icon lucide-chevron-right w-4 text-gray-400">
                <path d="m9 18 6-6-6-6"/>
            </svg>
            <?php
            echo '<span class="current font-medium">' . esc_html(get_the_title()) . '</span>';
            echo '</nav>';
        }
        ?>
            <h1 class="text-4xl lg:text-5xl font-heading font-bold"><?php the_title(); ?></h1>
        </header>
    </div>
    <?php while (have_posts()) : the_post(); ?>
        <?php if (get_the_content()) : ?>
            <?php the_content(); ?>
        <?php endif; ?>
    <?php endwhile; ?>
    <section id="marques" class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto parisii-optique-brands-content">
        <?php Parisii_Optique_Brand_Display::render_brands_grid(); ?>
    </section>
<?php
get_footer();

