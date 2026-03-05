<?php
/**
 * Template Name: Page Contact
 * Template pour la page Nous contacter (slug: nous-contacter)
 *
 * @package Parisii_Optique
 */

get_header();
?>

<div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto">
    <?php
    if (function_exists('yoast_breadcrumb')) {
        yoast_breadcrumb('<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 mb-6" aria-label="breadcrumb">', '</nav>');
    } else {
        echo '<nav class="breadcrumb text-sm text-gray-600 dark:text-gray-400 flex flex-wrap gap-1 items-center" aria-label="breadcrumb">';
        echo '<a href="' . esc_url(home_url('/')) . '" class="hover:text-main-500">' . esc_html__('Accueil', 'parisii-optique') . '</a>';
        ?>
        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide w-4 text-gray-400"><path d="m9 18 6-6-6-6"/></svg>
        <?php
        echo '<span class="current font-medium">' . get_the_title() . '</span>';
        echo '</nav>';
    }
    ?>
    <h1 class="text-4xl md:text-5xl font-heading font-bold text-gray-900 dark:text-white mb-8"><?php the_title(); ?></h1>
</div>

<div class="max-w-7xl p-4 md:p-6 lg:p-8 mx-auto flex justify-center">
    <div class="w-full max-w-2xl">
        <div id="parisii-contact-form-wrapper">
            <div id="parisii-contact-alert" class="hidden rounded-lg p-4 mb-6" role="alert" aria-live="polite"></div>

            <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8">
                <form id="parisii-contact-form" class="grid grid-cols-1 gap-5" novalidate>
                <div>
                    <label for="contact-nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Nom', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                    <input type="text" id="contact-nom" name="nom" required autocomplete="family-name"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:border-main-500 focus:ring-2 focus:ring-main-500/20 transition-colors"
                           autocomplete="family-name" data-format="uppercase">
                    <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="nom"></p>
                </div>

                <div>
                    <label for="contact-prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Prénom', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                    <input type="text" id="contact-prenom" name="prenom" required autocomplete="given-name"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:border-main-500 focus:ring-2 focus:ring-main-500/20 transition-colors"
                           autocomplete="given-name" data-format="capitalize">
                    <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="prenom"></p>
                </div>

                <div>
                    <label for="contact-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Email', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                    <input type="email" id="contact-email" name="email" required autocomplete="email"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:border-main-500 focus:ring-2 focus:ring-main-500/20 transition-colors"
                           autocomplete="email">
                    <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="email"></p>
                </div>

                <div>
                    <label for="contact-tel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Téléphone', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                    <input type="tel" id="contact-tel" name="tel" required autocomplete="tel"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:border-main-500 focus:ring-2 focus:ring-main-500/20 transition-colors"
                           autocomplete="tel">
                    <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="tel"></p>
                </div>

                <div>
                    <label for="contact-sujet" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Sujet', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                    <input type="text" id="contact-sujet" name="sujet" required
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:border-main-500 focus:ring-2 focus:ring-main-500/20 transition-colors">
                    <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="sujet"></p>
                </div>

                <div>
                    <label for="contact-message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Message', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                    <textarea id="contact-message" name="message" rows="5" required
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:border-main-500 focus:ring-2 focus:ring-main-500/20 transition-colors resize-y"></textarea>
                    <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="message"></p>
                </div>

                <div>
                    <label for="contact-captcha" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer">
                        <span id="contact-captcha-label"><?php esc_html_e('Recopiez le code ci-dessous', 'parisii-optique'); ?></span> <span class="text-red-500">*</span>
                    </label>
                    <div class="flex flex-wrap items-center gap-3 mb-3">
                        <code id="contact-captcha-code" class="text-lg font-mono tracking-wider px-3 py-2 bg-gray-100 dark:bg-gray-800 rounded border border-gray-300 dark:border-gray-600 select-none" aria-hidden="true"></code>
                        <button type="button" id="contact-captcha-refresh" class="text-sm text-main-600 dark:text-main-400 hover:underline focus:outline-none focus:underline" title="<?php esc_attr_e('Rafraîchir le code', 'parisii-optique'); ?>"><?php esc_html_e('Rafraîchir', 'parisii-optique'); ?></button>
                    </div>
                    <input type="text" id="contact-captcha" name="captcha" required autocomplete="off"
                           class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:border-main-500 focus:ring-2 focus:ring-main-500/20 transition-colors"
                           placeholder="<?php esc_attr_e('Code', 'parisii-optique'); ?>">
                    <input type="hidden" id="contact-captcha-key" name="captcha_key" value="">
                    <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="captcha"></p>
                </div>

                <div class="pt-2">
                    <button type="submit" id="contact-submit" disabled
                            class="w-full px-6 py-3 rounded-lg bg-main-500 hover:bg-main-600 text-white font-medium focus:ring-2 focus:ring-main-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors disabled:bg-gray-400 disabled:hover:bg-gray-400 dark:disabled:bg-gray-600 dark:disabled:hover:bg-gray-600 disabled:cursor-not-allowed disabled:opacity-90">
                        <?php esc_html_e('Nous contacter', 'parisii-optique'); ?>
                    </button>
                </div>
                </form>
            </div>
        </div>

        <div id="parisii-contact-success" class="hidden bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 text-center">
            <p id="parisii-contact-success-message" class="mb-6 rounded-lg p-4 bg-green-100 dark:bg-green-900/40 text-green-800 dark:text-green-100 border border-green-300 dark:border-green-700"></p>
            <button type="button" id="parisii-contact-refresh-btn" class="px-6 py-3 rounded-lg bg-main-500 hover:bg-main-600 text-white font-medium focus:ring-2 focus:ring-main-500 focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors cursor-pointer">
                <?php esc_html_e('Rafraîchir la page', 'parisii-optique'); ?>
            </button>
        </div>
    </div>
</div>

<?php get_footer(); ?>
