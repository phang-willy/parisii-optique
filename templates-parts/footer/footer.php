<?php
/**
 * The footer template part
 *
 * @package Parisii_Optique
 */
?>

<footer class="footer bg-white dark:bg-black text-black dark:text-white border-t border-t-gray-200 dark:border-t-gray-800">
    <div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
        <div class="nav-footer grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12 py-12">
            <div class="flex flex-col gap-4">
                <?php if (has_custom_logo()) : ?>
                    <div class="text-white">
                        <?= the_custom_logo(); ?>
                        <span class="sr-only"><?= bloginfo('name'); ?> - <?= bloginfo('description'); ?></span>
                    </div>
                <?php else : ?>
                    <h3 class="text-2xl font-heading font-bold text-white dark:text-white">
                        <?= bloginfo('name'); ?>
                    </h3>
                <?php endif; ?>
                
                <p class="text-gray-300 dark:text-gray-300">
                    <?php echo get_theme_mod('footer_description', 'Votre partenaire de confiance pour tous vos besoins en optique. Un service personnalisé et des conseils d\'experts.'); ?>
                </p>
            </div>
            <div>
                <h4 class="text-lg font-heading font-semibold text-white dark:text-white mb-4">Adresse</h4>
                <div class="flex flex-col gap-2 text-gray-300 dark:text-gray-300">
                    <p><?php echo get_theme_mod('address_street', '100 route de Seine'); ?></p>
                    <p><?php echo get_theme_mod('address_city', '95249 Cormeilles-en-Parisis'); ?></p>
                </div>
                <?php 
                $google_maps_embed = get_theme_mod('google_maps_embed');
                if ($google_maps_embed) : 
                ?>
                    <div class="mt-4">
                        <?php echo wp_kses_post($google_maps_embed); ?>
                    </div>
                <?php endif; ?>
            </div>
            <div>
                <h4 class="text-lg font-heading font-semibold text-white dark:text-white mb-4">Horaires d'ouverture</h4>
                <div class="flex flex-col gap-2 text-gray-300 dark:text-gray-300">
                    <div class="flex justify-between gap-4">
                        <span>Lun - Ven</span>
                        <span><?php echo get_theme_mod('hours_weekdays', '9h30 - 19h00'); ?></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span>Samedi</span>
                        <span><?php echo get_theme_mod('hours_saturday', '10h00 - 19h30'); ?></span>
                    </div>
                    <div class="flex justify-between gap-4">
                        <span>Dimanche</span>
                        <span><?php echo get_theme_mod('hours_sunday', 'Fermé'); ?></span>
                    </div>
                </div>
            </div>
            <div>
                <h4 class="text-lg font-heading font-semibold text-white dark:text-white mb-4">Contact</h4>
                <div class="flex flex-col gap-3 text-gray-300">
                    <div class="flex items-center flex-row gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                        </svg>
                        <?php 
                            $tel = get_theme_mod('phone_number', '01 34 29 05 05');
                            $link_tel = function_exists('parisii_optique_normalize_phone_for_tel') ? parisii_optique_normalize_phone_for_tel($tel) : preg_replace('/[^0-9+]/', '', $tel); 
                        ?>
                        <a href="tel:<?php echo esc_attr($link_tel); ?>" class="hover:text-white transition-colors">
                            <?php echo esc_html($tel); ?>
                        </a>
                    </div>
                    
                    <div class="flex items-center flex-row gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                        <a href="mailto:<?php echo get_theme_mod('email_address', 'contact@parisii-optique.fr'); ?>" class="hover:text-white transition-colors">
                            <?php echo get_theme_mod('email_address', 'contact@parisii-optique.fr'); ?>
                        </a>
                    </div>
                    
                    <div class="flex items-center flex-row gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/>
                        </svg>
                        <a href="<?php echo esc_url(get_permalink(get_page_by_path('nous-contacter'))); ?>" class="hover:text-white transition-colors">
                            Nous contacter
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="border-t border-gray-200 dark:border-gray-800 py-6">
            <div class="flex flex-col md:flex-row justify-between items-center gap-4 md:gap-0">
                <div class="text-sm text-gray-800 dark:text-gray-400 text-center md:text-left">
                    © <?php echo date('Y'); ?> <?php bloginfo('name'); ?>. Tous droits réservés.
                </div>
                <div class="flex flex-wrap justify-center md:justify-start flex-row gap-6 text-sm text-gray-400 dark:text-gray-400">
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'menu_class' => 'flex flex-row gap-6 nav-footer list-none',
                        'container' => false,
                        'fallback_cb' => false,
                        'walker' => new Parisii_Optique_Walker_Nav_Menu_Footer(),
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>
