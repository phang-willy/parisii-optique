<?php
/**
 * The navbar template part
 *
 * @package Parisii_Optique
 */
?>

<header id="header" class="navbar left-0 z-50 border-b border-gray-200 overflow-visible top-0 fixed will-change-transform w-full bg-white/80 dark:bg-black/80 dark:border-gray-800 backdrop-blur-md">
    <a class="skip-link screen-reader-text" href="#main"><?php esc_html_e('Aller au contenu', 'parisii-optique'); ?></a>
    <div class="max-w-7xl mx-auto px-4 md:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <div class="flex-shrink-0">
                <?php if (has_custom_logo()) : ?>
                    <div class="w-16 lg:w-32">
                        <?php the_custom_logo(); ?>
                        <span class="sr-only"><?php echo esc_html(get_bloginfo('name')); ?> - <?php echo esc_html(get_bloginfo('description')); ?></span>
                    </div>
                <?php else : ?>
                    <a href="<?php echo esc_url(home_url('/')); ?>" class="text-2xl font-heading font-bold bg-secondary">
                        <?php echo esc_html(get_bloginfo('name')); ?>
                    </a>
                    <span class="sr-only"><?php echo esc_html(get_bloginfo('name')); ?> - <?php echo esc_html(get_bloginfo('description')); ?></span>
                <?php endif; ?>
            </div>
            
            <nav class="nav-desktop hidden md:flex items-center flex-row gap-8" role="navigation" aria-label="<?= esc_attr_e('Menu principal desktop', 'parisii-optique'); ?>">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_class' => 'flex flex-row',
                    'container' => false,
                    'fallback_cb' => false,
                    'walker' => new Parisii_Optique_Walker_Nav_Menu(),
                ]);
                ?>
            </nav>
            
            <div class="flex items-center flex-row gap-4">
                <?php echo do_shortcode('[theme_switcher style="dropdown" size="medium" show_label="false"]'); ?>
                <button id="mobile-menu-button" class="md:hidden cursor-pointer p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white" aria-label="<?php esc_attr_e('Ouvrir le menu', 'parisii-optique'); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                    </svg>
                </button>
            </div>
        </div>
    </div>
    
    <div id="mobile-menu-overlay" class="fixed inset-0 bg-black bg-opacity-60 z-40 hidden md:hidden" style="background-color: rgba(0, 0, 0, 0.7) !important;"></div>
    
    <nav id="mobile-menu" class="fixed top-0 right-0 bottom-0 h-screen bg-white dark:bg-gray-900 z-50 md:hidden overflow-y-auto" role="navigation" aria-label="<?php esc_attr_e('Menu principal mobile', 'parisii-optique'); ?>" aria-hidden="true" inert>
        <div class="container flex flex-col h-full">
            <div class="flex items-center justify-between py-3 px-4 border-b border-gray-200 dark:border-gray-700 flex-shrink-0 h-16">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white"><?php _e('Menu', 'parisii-optique'); ?></h2>
                <button id="mobile-menu-close" class="cursor-pointer p-2 rounded-lg text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white" aria-label="<?php esc_attr_e('Fermer le menu', 'parisii-optique'); ?>">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="flex-1 overflow-y-auto">
                <?php
                wp_nav_menu([
                    'theme_location' => 'primary',
                    'menu_class' => 'flex flex-col',
                    'container' => false,
                    'fallback_cb' => false,
                    'walker' => new Parisii_Optique_Walker_Nav_Menu_Mobile(),
                ]);
                ?>
            </div>
        </div>
    </nav>
</header>