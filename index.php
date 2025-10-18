<?php
/**
 * The main template file
 *
 * @package Parisii_Optique
 */

get_header(); ?>

<?php
if (have_posts()) :
    the_content();
else :
    ?>
    <section class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto py-16 text-center">
        <h2 class="text-3xl font-bold text-gray-900 dark:text-white mb-4">Aucun contenu trouvé</h2>
        <p class="text-gray-600 dark:text-gray-400">Désolé, aucun contenu n'est disponible.</p>
    </section>
    <?php
endif;
?>

<?php get_footer(); ?>
