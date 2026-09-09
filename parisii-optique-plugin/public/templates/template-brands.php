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
                <p>Venez découvrir nos collections en magasin.</p>
            </article>
            <article>
                <h3>Pour les sportifs et sportives :</h3>
                <p>Les amateurs de sport et athlètes ne sont pas en reste ! <?php echo get_the_title(); ?> met à votre disposition une gamme de lunettes spécialement conçues pour les activités physiques.</p>
                <p>Pour vous permettre d'effectuer votre activité favorite sans faire l'impasse sur votre confort visuel, notre collection sport inclut des modèles robustes et performants, adaptés à diverses disciplines telles que la course, le cyclisme, ou les sports aquatiques.</p>
                <p>Ces lunettes sont conçues pour offrir un confort optimal, une résistance accrue, et une visibilité parfaite, même dans les conditions les plus exigeantes.</p>
            </article>
        </div>
        <?php Parisii_Optique_Brand_Display::render_brands_grid(); ?>
    </section>
<?php
get_footer();

