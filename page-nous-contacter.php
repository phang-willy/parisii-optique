<?php
/**
 * Template Name: Page Contact
 * Template pour la page Nous contacter (slug: nous-contacter)
 *
 * @package Parisii_Optique
 */

get_header();
?>

<?php parisii_optique_breadcrumb(); ?>

<div class="max-w-7xl px-4 py-8 md:px-6 md:py-12 lg:px-8lg:py-16 mx-auto">
  <div id="parisii-contact-content" class="w-full">
    <div class="mb-8">
      <p class="text-gray-700 dark:text-gray-300"><strong>Une question</strong> sur vos lunettes, vos lentilles ou votre suivi visuel ?</p>
      <p class="text-gray-700 dark:text-gray-300">L'équipe de Parisii Optique <strong>vous accueille</strong> à Cormeilles-en-Parisis et <strong>vous accompagne</strong> avec des conseils personnalisés.</p>
      <p class="text-gray-700 dark:text-gray-300">Contactez-nous pour <strong>prendre rendez-vous</strong>, <strong>préparer votre visite</strong> ou <strong>obtenir une information</strong> sur nos services.</p>
    </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 md:gap-12 lg:gap-16">
        <div id="google-maps-embed-wrapper">
          <?php $google_maps_embed = get_theme_mod('google_maps_embed'); ?>
          <?php if ($google_maps_embed) : ?>
            <?php echo $google_maps_embed; ?>
          <?php endif; ?>
        </div>
          <div id="parisii-contact-form-wrapper" class="w-full">
              <div id="parisii-contact-alert" class="hidden rounded-lg p-4 mb-6" role="alert" aria-live="polite"></div>

              <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-lg border border-gray-200 dark:border-gray-700 p-6 md:p-8 w-full">
                  <form id="parisii-contact-form" class="grid grid-cols-1 gap-5" novalidate>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                          <label for="contact-nom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Nom', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                          <input type="text" id="contact-nom" name="nom" required autocomplete="family-name"
                                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-main/20 transition-colors"
                                  data-format="uppercase">
                          <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="nom"></p>
                      </div>

                      <div>
                          <label for="contact-prenom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Prénom', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                          <input type="text" id="contact-prenom" name="prenom" required autocomplete="given-name"
                                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-main/20 transition-colors"
                                  data-format="capitalize">
                          <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="prenom"></p>
                      </div>
                  </div>

                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                          <label for="contact-email" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Email', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                          <input type="email" id="contact-email" name="email" required autocomplete="email"
                                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-main/20 transition-colors">
                          <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="email"></p>
                      </div>

                      <div>
                          <label for="contact-tel" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Téléphone', 'parisii-optique'); ?></label>
                          <input type="tel" id="contact-tel" name="tel" autocomplete="tel"
                                  class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-main/20 transition-colors">
                          <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="tel"></p>
                      </div>
                  </div>

                  <div>
                      <label for="contact-sujet" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Sujet', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                      <input type="text" id="contact-sujet" name="sujet" required
                              class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-main/20 transition-colors">
                      <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="sujet"></p>
                  </div>

                  <div>
                      <label for="contact-message" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1 cursor-pointer"><?php esc_html_e('Message', 'parisii-optique'); ?> <span class="text-red-500">*</span></label>
                      <textarea id="contact-message" name="message" rows="5" required
                                class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-gray-100 px-4 py-2.5 focus:ring-2 focus:ring-main/20 transition-colors resize-y"></textarea>
                      <p class="contact-field-error mt-1 text-sm text-red-600 dark:text-red-400 hidden" data-for="message"></p>
                  </div>

                  <div class="hidden" aria-hidden="true">
                      <label for="contact-website"><?php esc_html_e('Site web', 'parisii-optique'); ?></label>
                      <input type="text" id="contact-website" name="website" value="" autocomplete="off" tabindex="-1">
                  </div>
                  <input type="hidden" id="contact-form-started-at" name="form_started_at" value="<?php echo esc_attr((string) time()); ?>">

                  <div class="pt-2">
                      <button type="submit" id="contact-submit" disabled
                              class="w-full px-6 py-3 rounded-lg bg-main hover:bg-main-hover focus:bg-main-focus text-white font-medium focus:ring-2 focus:ring-main focus:ring-offset-2 dark:focus:ring-offset-gray-900 transition-colors disabled:bg-gray-400 disabled:hover:bg-gray-400 dark:disabled:bg-gray-600 dark:disabled:hover:bg-gray-600 disabled:cursor-not-allowed disabled:opacity-90">
                          <?php esc_html_e('Nous contacter', 'parisii-optique'); ?>
                      </button>
                  </div>
                  </form>
              </div>
          </div>
      </div>
  </div>
</div>

<?php get_footer(); ?>
