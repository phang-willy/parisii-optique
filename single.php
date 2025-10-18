<?php
/**
 * The template for displaying all single posts
 *
 * @package Parisii_Optique
 */

get_header(); ?>

<main id="main" class="site-main">
    <?php while (have_posts()) : the_post(); ?>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <?php if (has_post_thumbnail()) : ?>
                <section class="post-thumbnail">
                    <?php the_post_thumbnail('card-image', ['class' => 'w-full h-64 md:h-96 object-cover']); ?>
                </section>
            <?php endif; ?>
            
            <section class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
                <div class="max-w-4xl mx-auto">
                    <header class="entry-header mb-8">
                        <h1 class="entry-title text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white mb-4">
                            <?php the_title(); ?>
                        </h1>
                        
                        <div class="entry-meta text-gray-600 dark:text-gray-400 mb-6">
                            <div class="flex flex-wrap items-center gap-4">
                                <time class="published" datetime="<?php echo get_the_date('c'); ?>">
                                    <?php echo get_the_date(); ?>
                                </time>
                                
                                <?php if (get_the_author()) : ?>
                                    <span class="byline">
                                        par <span class="author vcard">
                                            <a class="url fn n" href="<?php echo esc_url(get_author_posts_url(get_the_author_meta('ID'))); ?>">
                                                <?php echo get_the_author(); ?>
                                            </a>
                                        </span>
                                    </span>
                                <?php endif; ?>
                                
                                <?php if (has_category()) : ?>
                                    <div class="categories">
                                        <?php the_category(', '); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </header>

                    <div class="entry-content prose prose-lg max-w-none">
                        <?php
                        the_content();

                        wp_link_pages([
                            'before' => '<div class="page-links">',
                            'after'  => '</div>',
                        ]);
                        ?>
                    </div>

                    <?php if (has_tag()) : ?>
                        <footer class="entry-footer mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
                            <div class="tags">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Tags</h3>
                                <?php the_tags('<div class="flex flex-wrap gap-2">', '', '</div>'); ?>
                            </div>
                        </footer>
                    <?php endif; ?>
                </div>
            </section>
        </article>

        <?php
        // If comments are open or we have at least one comment, load up the comment template.
        if (comments_open() || get_comments_number()) :
            comments_template();
        endif;
        ?>

    <?php endwhile; ?>
</main>

<?php get_footer(); ?>
