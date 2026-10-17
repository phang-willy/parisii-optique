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

  <div class="prose prose-lg dark:prose-invert max-w-none">
    <?php the_content(); ?>
  </div>
<?php endif ; ?>

<?php get_footer(); ?>