<?php
/**
 * Template Name: Page de verres
 * Description: Template personnalisé pour la page de verres
 *
 * @package Parisii_Optique
 */

get_header();
?>

<?php parisii_optique_breadcrumb(); ?>

<?php
  get_template_part('parts/verres/hello');
  get_template_part('parts/verres/incontournables');
  get_template_part('parts/verres/solaires-et-adaptatives');
  get_template_part('parts/verres/anti-reflets');
  get_template_part('parts/verres/anti-lumiere-bleue');
  get_template_part('parts/verres/teintes-polarisants');
  get_template_part('parts/verres/photochromatiques-transitions');
  get_template_part('parts/verres/filtre-medical');
?>

<?php get_footer(); ?>