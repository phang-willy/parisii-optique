<?php
/**
 * Template Name: Page de santé visuelle
 * Description: Template personnalisé pour la page de santé visuelle
 *
 * @package Parisii_Optique
 */

get_header();
?>

<?php parisii_optique_breadcrumb(); ?>

<?php
  get_template_part('parts/sante-visuelle/hello');
  get_template_part('parts/sante-visuelle/myopie');
  get_template_part('parts/sante-visuelle/hypermetropie');
  get_template_part('parts/sante-visuelle/astigmatisme');
  get_template_part('parts/sante-visuelle/presbytie');
  get_template_part('parts/sante-visuelle/pathologies');
?>

<?php get_footer(); ?>