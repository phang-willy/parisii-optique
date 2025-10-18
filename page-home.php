<?php
/**
 * Template Name: Page d'accueil
 * Description: Template personnalisé pour la page d'accueil
 *
 * @package Parisii_Optique
 */

get_header();
?>
<h1 class="sr-only"><?= bloginfo('name'); ?> - <?= bloginfo('description'); ?></h1>
<section id="hello">
    <div class="w-full max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
        <div class="grid md:grid-cols-2 gap-6">
            <div class="flex flex-col gap-6 justify-center">
                <h1><?= bloginfo('description'); ?></h1>
                <div class="flex flex-col gap-4">
                    <p>Bienvenue chez <?= bloginfo('name'); ?>, votre opticien de référence situé au cœur du nouveau quartier Seine Parisii à Cormeilles-en-Parisis.</p>
                    <p>Notre magasin est l’endroit idéal pour trouver un spécialiste en santé visuelle et choisir la paire de lunettes parfaite.</p>
                </div>
                <div class="flex md:flex-row flex-col gap-4">
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('nous-contacter'))); ?>" class="btn btn-primary">Prendre Rendez-vous</a>
                    <a href="<?php echo esc_url(get_permalink(get_page_by_path('collections-et-marques'))); ?>" class="btn btn-secondary">Voir nos collections</a>
                </div>
            </div>
            <div>
               <?php
                $uploads_base = untrailingslashit( home_url( '/wp-content/uploads' ) );
                $placeholder_files = array(
                    '1024x1024' => 'woocommerce-placeholder-1024x1024.webp',
                    '768' => 'woocommerce-placeholder-768x768.webp',
                    '600' => 'woocommerce-placeholder-600x600.webp',
                    '300' => 'woocommerce-placeholder-300x300.webp',
                    '150' => 'woocommerce-placeholder-150x150.webp',
                    '100' => 'woocommerce-placeholder-100x100.webp',
                    'full' => 'woocommerce-placeholder.webp',
                );
                $src = $uploads_base . '/' . $placeholder_files['1024x1024'];
                $srcset_parts = array();
                $srcset_parts[] = $uploads_base . '/' . $placeholder_files['1024x1024'] . ' 1024w';
                $srcset_parts[] = $uploads_base . '/' . $placeholder_files['300'] . ' 300w';
                $srcset_parts[] = $uploads_base . '/' . $placeholder_files['100'] . ' 100w';
                $srcset_parts[] = $uploads_base . '/' . $placeholder_files['600'] . ' 600w';
                $srcset_parts[] = $uploads_base . '/' . $placeholder_files['150'] . ' 150w';
                $srcset_parts[] = $uploads_base . '/' . $placeholder_files['768'] . ' 768w';
                $srcset_parts[] = $uploads_base . '/' . $placeholder_files['full'] . ' 1200w';
                $srcset = implode( ', ', $srcset_parts );
               ?>
               <figure class="wp-block-image size-large">
                   <img decoding="async" width="1024" height="1024" src="<?php echo esc_url( $src ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>" class="wp-image-40" srcset="<?php echo esc_attr( $srcset ); ?>" sizes="(max-width: 1024px) 100vw, 1024px">
               </figure>
            </div>
        </div>
    </div>
</section>
<section id="infos" class="bg-main-400">
    <div class="w-full max-w-7xl mx-auto p-4 md:p-6 lg:p-8">
        <div class="grid lg:grid-cols-3 gap-6 place-items-center text-center rounded-md p-4">
            <article class="text-black">
                <h3>Adresse</h3>
                <a href="https://maps.app.goo.gl/mndu7PdqxZ4xdYTh8" class="external-link">
                    <span class="text-sm"><?= get_theme_mod('address_street', '100 route de Seine') . '<br>' . get_theme_mod('address_city', '95249 Cormeilles-en-Parisis'); ?></span>
                </a>
            </article>
            <article class="text-black">
                <h3>Horaires</h3>
                <p><span class="text-sm">Du Lundi au Vendredi : <?= get_theme_mod('hours_weekdays', '9h30 - 19h00'); ?></span><br><span class="text-sm">et le Samedi : <?= get_theme_mod('hours_saturday', '10h00 à 19h30'); ?></span></p>
            </article>
            <article class="text-black">
                <h3>Contact</h3>
                <?php 
                    $tel = get_theme_mod('phone_number', '01 34 29 05 05');
                    $link_tel = function_exists('parisii_optique_normalize_phone_for_tel') ? parisii_optique_normalize_phone_for_tel($tel) : preg_replace('/[^0-9+]/', '', $tel);
                    $email = get_theme_mod('email_address', 'contact@parisii-optique.fr');
                ?>
                <a href="tel:<?= $link_tel; ?>" class="text-sm"><?= $tel; ?></a><br><a href="mailto:<?= $email; ?>" class="text-sm"><?= $email; ?></a>
            </article>
        </div>
    </div>
</section>

<?php
get_footer();
