<section id="where-we-are" class="bg-[#c5b68d]">
  <div class="w-full max-w-7xl mx-auto px-4 py-8 md:px-6 md:py-12 lg:px-8 lg:py-16 text-black">
    <h2 class="text-center mb-8">Où nous trouver ?</h2>
    <div class="grid md:grid-cols-2 gap-6">
      <article>
        <div id="google-maps-embed-wrapper" class="w-full h-112.5 overflow-hidden">
          <?php
          $parisii_maps_iframe = parisii_optique_maps_iframe_markup();
          if ($parisii_maps_iframe !== '') {
              echo $parisii_maps_iframe; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- sanitized iframe.
          }
          ?>
        </div>
        <?php parisii_optique_render_maps_link(); ?>
      </article>
      <div class="flex flex-col gap-16 justify-center">
        <article class="flex flex-col gap-1">
          <h3>Adresse :</h3>
          <p><?php echo esc_html(get_theme_mod('address_street', '12 Véloroute Sequana')); ?>, <?php echo esc_html(get_theme_mod('address_city', '95240 Cormeilles-en-Parisis')); ?></p>
        </article>
        <article class="flex flex-col gap-1">
          <h3>Horaires :</h3>
          <?php parisii_optique_render_opening_hours('home'); ?>
        </article>
      </div>
    </div>
  </div>
</section>
