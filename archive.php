<?php
/**
 * The template for displaying archive pages
 *
 * @package Parisii_Optique
 */

get_header(); ?>

<main id="main" class="site-main">
    <section class="max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
        <header class="page-header mb-12">
            <h1 class="page-title text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white mb-4">
                <?php
                if (is_category()) {
                    single_cat_title();
                } elseif (is_tag()) {
                    single_tag_title();
                } elseif (is_author()) {
                    echo 'Articles de ' . get_the_author();
                } elseif (is_date()) {
                    if (is_year()) {
                        echo 'Articles de ' . get_the_date('Y');
                    } elseif (is_month()) {
                        echo 'Articles de ' . get_the_date('F Y');
                    } elseif (is_day()) {
                        echo 'Articles du ' . get_the_date('j F Y');
                    }
                } else {
                    echo 'Archives';
                }
                ?>
            </h1>
            
            <?php if (is_category() && category_description()) : ?>
                <div class="page-description text-lg text-gray-600 dark:text-gray-400">
                    <?php echo category_description(); ?>
                </div>
            <?php endif; ?>
        </header>

        <?php if (have_posts()) : ?>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <?php while (have_posts()) : the_post(); ?>
                    <article id="post-<?php the_ID(); ?>" <?php post_class('card group hover:shadow-soft-lg transition-shadow duration-300'); ?>>
                        <?php if (has_post_thumbnail()) : ?>
                            <div class="aspect-w-16 aspect-h-9 overflow-hidden">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_post_thumbnail('card-image', ['class' => 'w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300']); ?>
                                </a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="p-6">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400 mb-3">
                                <time datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                                <?php if (has_category()) : ?>
                                    <span class="mx-2">•</span>
                                    <span><?php the_category(', '); ?></span>
                                <?php endif; ?>
                            </div>
                            
                            <h2 class="text-xl font-heading font-semibold text-gray-900 dark:text-white mb-3 group-hover:text-main-600 dark:group-hover:text-main-400 transition-colors">
                                <a href="<?php the_permalink(); ?>">
                                    <?php the_title(); ?>
                                </a>
                            </h2>
                            
                            <div class="text-gray-700 dark:text-gray-300 mb-4">
                                <?php the_excerpt(); ?>
                            </div>
                            
                            <a href="<?php the_permalink(); ?>" class="inline-flex items-center text-main-600 dark:text-main-400 hover:text-main-700 dark:hover:text-main-300 font-medium transition-colors">
                                Lire la suite
                                <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        </div>
                    </article>
                <?php endwhile; ?>
            </div>
            
            <?php
            // Pagination
            the_posts_pagination([
                'mid_size' => 2,
                'prev_text' => '← Précédent',
                'next_text' => 'Suivant →',
                'class' => 'mt-12 flex justify-center',
            ]);
            ?>
        <?php else : ?>
            <section class="text-center py-16">
                <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Aucun contenu trouvé</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-8">Désolé, aucun contenu ne correspond à votre recherche.</p>
                <a href="<?php echo esc_url(home_url('/')); ?>" class="btn btn-primary">
                    Retour à l'accueil
                </a>
            </section>
        <?php endif; ?>
    </section>
</main>

<?php get_footer(); ?>
