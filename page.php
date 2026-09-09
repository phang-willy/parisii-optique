<?php
/**
 * The template for displaying all pages
 *
 * @package Parisii_Optique
 */
?>

<?php get_header(); ?>

<?php if ( is_front_page() ) : ?>
  <?php the_content(); ?>
<?php else : ?>
  <?php parisii_optique_breadcrumb(); ?>
  <div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
    <h1 class="text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white">
      <?php the_title(); ?>
    </h1>
  </div>

  <div class="prose prose-lg dark:prose-invert max-w-none">
    <?php the_content(); ?>
  </div>
<?php endif ; ?>

<?php get_footer(); ?>