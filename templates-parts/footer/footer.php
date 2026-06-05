<?php
/**
 * The footer template part
 *
 * @package Parisii_Optique
 */
?>

<footer class="footer bg-white dark:bg-black text-black dark:text-white border-t border-t-gray-200 dark:border-t-gray-800">
    <div class="max-w-7xl px-4 md:px-6 lg:px-8 mx-auto">
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
                    <p><?php echo get_theme_mod('address_street', '12 Véloroute Sequana'); ?></p>
                    <p><?php echo get_theme_mod('address_city', '95240 Cormeilles-en-Parisis'); ?></p>
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

                    <div class="flex items-center flex-row gap-2">
                        <?php
                            $instagram_account = function_exists('parisii_optique_sanitize_instagram_account')
                                ? parisii_optique_sanitize_instagram_account(get_theme_mod('instagram_account', 'parisii.optique'))
                                : 'parisii.optique';
                            $instagram_url = 'https://www.instagram.com/' . rawurlencode($instagram_account) . '/';
                        ?>
                        <svg class="h-4 w-4 fill-black dark:fill-white" stroke="currentColor" stroke-width="0" viewBox="0 0 448 512" height="200px" width="200px" xmlns="http://www.w3.org/2000/svg"><path d="M224.1 141c-63.6 0-114.9 51.3-114.9 114.9s51.3 114.9 114.9 114.9S339 319.5 339 255.9 287.7 141 224.1 141zm0 189.6c-41.1 0-74.7-33.5-74.7-74.7s33.5-74.7 74.7-74.7 74.7 33.5 74.7 74.7-33.6 74.7-74.7 74.7zm146.4-194.3c0 14.9-12 26.8-26.8 26.8-14.9 0-26.8-12-26.8-26.8s12-26.8 26.8-26.8 26.8 12 26.8 26.8zm76.1 27.2c-1.7-35.9-9.9-67.7-36.2-93.9-26.2-26.2-58-34.4-93.9-36.2-37-2.1-147.9-2.1-184.9 0-35.8 1.7-67.6 9.9-93.9 36.1s-34.4 58-36.2 93.9c-2.1 37-2.1 147.9 0 184.9 1.7 35.9 9.9 67.7 36.2 93.9s58 34.4 93.9 36.2c37 2.1 147.9 2.1 184.9 0 35.9-1.7 67.7-9.9 93.9-36.2 26.2-26.2 34.4-58 36.2-93.9 2.1-37 2.1-147.8 0-184.8zM398.8 388c-7.8 19.6-22.9 34.7-42.6 42.6-29.5 11.7-99.5 9-132.1 9s-102.7 2.6-132.1-9c-19.6-7.8-34.7-22.9-42.6-42.6-11.7-29.5-9-99.5-9-132.1s-2.6-102.7 9-132.1c7.8-19.6 22.9-34.7 42.6-42.6 29.5-11.7 99.5-9 132.1-9s102.7-2.6 132.1 9c19.6 7.8 34.7 22.9 42.6 42.6 11.7 29.5 9 99.5 9 132.1s2.7 102.7-9 132.1z"></path></svg>
                        <a href="<?php echo esc_url($instagram_url); ?>" class="hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($instagram_account); ?>
                        </a>
                    </div>

                    <div class="flex items-center flex-row gap-2">
                        <?php
                            $facebook_account = function_exists('parisii_optique_sanitize_facebook_account')
                                ? parisii_optique_sanitize_facebook_account(get_theme_mod('facebook_account', 'Parisii Optique'))
                                : 'Parisii Optique';
                            $facebook_url = function_exists('parisii_optique_sanitize_facebook_url')
                                ? parisii_optique_sanitize_facebook_url(get_theme_mod('facebook_url', 'https://www.facebook.com/profile.php?id=61574336251763'))
                                : 'https://www.facebook.com/profile.php?id=61574336251763';
                        ?>
                        <svg class="h-4 w-4 fill-black dark:fill-white" stroke="currentColor" stroke-width="0" viewBox="0 0 512 512" height="200px" width="200px" aria-hidden="true" focusable="false" xmlns="http://www.w3.org/2000/svg"><path d="M512 256C512 114.6 397.4 0 256 0S0 114.6 0 256C0 376 82.7 476.8 194.2 504.5V334.2H141.4V256h52.8V222.3c0-87.1 39.4-127.5 125-127.5c16.2 0 44.2 3.2 55.7 6.4V172c-6-.6-16.5-1-29.6-1c-42 0-58.2 15.9-58.2 57.2V256h83.6l-14.4 78.2H287V510.1C413.8 494.8 512 386.9 512 256h0z"></path></svg>
                        <a href="<?php echo esc_url($facebook_url); ?>" class="hover:text-white transition-colors" target="_blank" rel="noopener noreferrer">
                            <?php echo esc_html($facebook_account); ?>
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
