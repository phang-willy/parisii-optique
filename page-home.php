<?php
/**
 * Template Name: Page d'accueil
 * Description: Template personnalisé pour la page d'accueil
 *
 * @package Parisii_Optique
 */

get_header();

get_template_part('parts/home/hello');
  get_template_part('parts/home/what-we-do');
  get_template_part('parts/home/get-estimation');
  get_template_part('parts/home/services');
  get_template_part('parts/home/engagements');
  get_template_part('parts/home/partners');
  get_template_part('parts/home/solars');
  get_template_part('parts/home/kids');
  get_template_part('parts/home/where-we-are');
?>

<?php
get_footer();