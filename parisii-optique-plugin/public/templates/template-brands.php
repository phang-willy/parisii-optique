<?php
/**
 * Template Name: Page des marques
 * Description: Template pour afficher les marques avec filtres
 *
 * @package Parisii_Optique_Plugin
 */

get_header();
?>

    <?php if (function_exists('parisii_optique_breadcrumb')) : ?>
        <?php parisii_optique_breadcrumb(); ?>
    <?php endif; ?>
    <section id="marques" class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto parisii-optique-brands-content">
        <div class="grid grid-cols-1 gap-8 mb-8">
            <article>
                <h3>Pour tout le monde :</h3>
                <p>Nous proposons un large éventail de marques de lunettes pour hommes, femmes et enfants.</p>
                <p>Venez découvrir vos marques préférées en magasin.</p>
            </article>
        </div>
        <?php Parisii_Optique_Brand_Display::render_brands_grid(); ?>
    </section>
<?php
get_footer();

