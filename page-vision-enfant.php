<?php
/**
 * Template Name: Vision de l'enfant
 * Description: Page Vision de l'enfant avec section marques.
 *
 * @package Parisii_Optique
 */

get_header();
?>

<div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
    <?php
    if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 mb-6" aria-label="breadcrumb">', '</nav>');
    } else {
        echo '<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 flex flex-wrap gap-1 items-center" aria-label="breadcrumb">';
        echo '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Accueil', 'parisii-optique') . '</a>';
        ?>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide w-4 text-gray-400"><path d="m9 18 6-6-6-6"/></svg>
        <?php
        echo '<span class="current font-medium">' . get_the_title() . '</span>';
        echo '</nav>';
    }
    ?>
    <h1 class="text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white mb-8"><?php the_title(); ?></h1>

    <?php while (have_posts()) : the_post(); ?>
        <?php if (get_the_content()) : ?>
            <section class="prose prose-lg dark:prose-invert max-w-none mb-10">
                <?php the_content(); ?>
            </section>
        <?php endif; ?>
    <?php endwhile; ?>

    <section>
        <header class="mb-6">
            <h2 class="text-3xl md:text-4xl font-heading font-bold text-gray-900 dark:text-white">
                Marques pour enfants
            </h2>
            <p class="text-gray-700 dark:text-gray-300 mt-2">
                Une selection de marques pour enfants alliant confort, resistance et style.
            </p>
        </header>

        <?php if (class_exists('Parisii_Optique_Brand_Display')) : ?>
            <?php Parisii_Optique_Brand_Display::render_brands_grid(['kids' => true]); ?>
        <?php else : ?>
            <div class="card text-center py-8">
                <p class="text-gray-500 dark:text-gray-400">
                    Les marques pour enfants ne sont pas disponibles pour le moment.
                </p>
            </div>
        <?php endif; ?>
    </section>
</div>

<?php get_footer(); ?>
